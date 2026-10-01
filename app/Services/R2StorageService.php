<?php

namespace App\Services;

use App\Models\ProjectImage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class R2StorageService
{
    public const CACHE_KEY = 'r2_storage_usage';

    public const CACHE_TTL_HOURS = 6;

    /**
     * Cloudflare R2 standard decimal metrics: 1 GB = 10^9 bytes, 1 MB = 10^6 bytes.
     */
    public const BYTES_PER_GB = 1000000000;

    public const BYTES_PER_MB = 1000000;

    public const BYTES_PER_KB = 1000;

    /**
     * Get storage usage metrics, cached for performance.
     */
    public function getStorageUsage(bool $forceFresh = false): array
    {
        if ($forceFresh) {
            $this->invalidateCache();
        }

        return Cache::remember(self::CACHE_KEY, now()->addHours(self::CACHE_TTL_HOURS), function () {
            return $this->calculateUsage();
        });
    }

    /**
     * Calculate storage usage directly from the database metadata.
     */
    public function calculateUsage(): array
    {
        $totalBytes = (int) ProjectImage::where('storage_disk', 'r2')->sum('file_size');
        $videoCount = (int) ProjectImage::where('storage_disk', 'r2')->count();

        $limitGb = (float) config('filesystems.disks.r2.storage_limit_gb', 10);
        $limitBytes = (int) round($limitGb * self::BYTES_PER_GB);

        if ($limitBytes <= 0) {
            $percentage = 0.0;
        } else {
            $percentage = min(100.0, round(($totalBytes / $limitBytes) * 100, 2));
        }

        $statusInfo = $this->resolveStatus($percentage);

        return [
            'total_bytes' => $totalBytes,
            'total_formatted' => $this->formatBytes($totalBytes),
            'limit_bytes' => $limitBytes,
            'limit_gb' => $limitGb,
            'limit_formatted' => $this->formatBytes($limitBytes),
            'percentage' => $percentage,
            'video_count' => $videoCount,
            'status' => $statusInfo['status'],
            'status_label' => $statusInfo['label'],
            'color' => $statusInfo['color'],
            'bg_color' => $statusInfo['bg_color'],
            'text_color' => $statusInfo['text_color'],
            'bar_color' => $statusInfo['bar_color'],
            'icon' => $statusInfo['icon'],
            'cached_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Invalidate cached usage metrics.
     */
    public function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Perform live bucket reconciliation against Cloudflare R2.
     * Connects directly to Cloudflare R2 API, scans physical files,
     * updates database records, cleans up orphaned/ghost database records,
     * and caches the verified bucket state.
     *
     * @throws Throwable
     */
    public function reconcileBucket(): array
    {
        $updatedCount = 0;
        $deletedOrphans = 0;

        try {
            $files = Storage::disk('r2')->allFiles('projects/videos');
            $physicalFiles = [];
            $totalPhysicalBytes = 0;

            foreach ($files as $filePath) {
                $cleanPath = str_replace('\\', '/', $filePath);
                try {
                    $size = Storage::disk('r2')->fileSize($cleanPath);
                    if ($size !== null) {
                        $physicalFiles[$cleanPath] = (int) $size;
                        $totalPhysicalBytes += (int) $size;
                    }
                } catch (Throwable $e) {
                    Log::warning('Gagal membaca ukuran file R2 untuk path: '.$cleanPath, [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // 1. Update existing project_images matching physical files
            foreach ($physicalFiles as $path => $size) {
                $images = ProjectImage::where('storage_disk', 'r2')
                    ->where('image_path', $path)
                    ->get();

                foreach ($images as $image) {
                    if ($image->file_size !== $size) {
                        $image->update(['file_size' => $size]);
                        $updatedCount++;
                    }
                }
            }

            // 2. Detect and remove orphaned project_images where physical file is missing from R2
            $existingDbRecords = ProjectImage::where('storage_disk', 'r2')
                ->whereNotNull('image_path')
                ->get();

            foreach ($existingDbRecords as $dbRecord) {
                $cleanDbPath = str_replace('\\', '/', $dbRecord->image_path);
                if (! array_key_exists($cleanDbPath, $physicalFiles)) {
                    Log::info('Menghapus record ProjectImage yatim karena file tidak ada di R2: '.$cleanDbPath, [
                        'id' => $dbRecord->id,
                        'project_id' => $dbRecord->project_id,
                    ]);
                    $dbRecord->delete();
                    $deletedOrphans++;
                }
            }

            // 3. Cache the verified physical R2 bucket usage
            $limitGb = (float) config('filesystems.disks.r2.storage_limit_gb', 10);
            $limitBytes = (int) round($limitGb * self::BYTES_PER_GB);
            $physicalCount = count($physicalFiles);

            $percentage = $limitBytes > 0
                ? min(100.0, round(($totalPhysicalBytes / $limitBytes) * 100, 2))
                : 0.0;

            $statusInfo = $this->resolveStatus($percentage);

            $usage = [
                'total_bytes' => $totalPhysicalBytes,
                'total_formatted' => $this->formatBytes($totalPhysicalBytes),
                'limit_bytes' => $limitBytes,
                'limit_gb' => $limitGb,
                'limit_formatted' => $this->formatBytes($limitBytes),
                'percentage' => $percentage,
                'video_count' => $physicalCount,
                'status' => $statusInfo['status'],
                'status_label' => $statusInfo['label'],
                'color' => $statusInfo['color'],
                'bg_color' => $statusInfo['bg_color'],
                'text_color' => $statusInfo['text_color'],
                'bar_color' => $statusInfo['bar_color'],
                'icon' => $statusInfo['icon'],
                'cached_at' => now()->toIso8601String(),
            ];

            Cache::put(self::CACHE_KEY, $usage, now()->addHours(self::CACHE_TTL_HOURS));

            return [
                'updated_records' => $updatedCount,
                'deleted_orphans' => $deletedOrphans,
                'physical_count' => $physicalCount,
                'physical_bytes' => $totalPhysicalBytes,
                'usage' => $usage,
            ];
        } catch (Throwable $exception) {
            Log::error('Gagal menjalankan rekonsiliasi Cloudflare R2: '.$exception->getMessage(), [
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }

    /**
     * Resolve adaptive visual status based on capacity percentage.
     * WCAG compliant contrast and distinct Lucide icons.
     */
    public function resolveStatus(float $percentage): array
    {
        if ($percentage < 70.0) {
            return [
                'status' => 'optimal',
                'label' => 'Optimal',
                'icon' => 'check-circle',
                'color' => 'success',
                'bg_color' => 'bg-success/15',
                'text_color' => 'text-success',
                'bar_color' => 'bg-primary',
            ];
        }

        if ($percentage < 90.0) {
            return [
                'status' => 'warning',
                'label' => 'Warning',
                'icon' => 'alert-triangle',
                'color' => 'warning',
                'bg_color' => 'bg-amber-100 dark:bg-amber-950/40',
                'text_color' => 'text-amber-700 dark:text-amber-400',
                'bar_color' => 'bg-amber-500',
            ];
        }

        return [
            'status' => 'critical',
            'label' => 'Critical',
            'icon' => 'alert-octagon',
            'color' => 'danger',
            'bg_color' => 'bg-danger/15',
            'text_color' => 'text-danger',
            'bar_color' => 'bg-danger',
        ];
    }

    /**
     * Format byte values into human-readable strings according to Cloudflare R2 metric standards.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0.00 MB';
        }

        if ($bytes >= self::BYTES_PER_GB) {
            return number_format($bytes / self::BYTES_PER_GB, $precision, '.', '').' GB';
        }

        if ($bytes >= self::BYTES_PER_MB) {
            return number_format($bytes / self::BYTES_PER_MB, $precision, '.', '').' MB';
        }

        if ($bytes >= self::BYTES_PER_KB) {
            return number_format($bytes / self::BYTES_PER_KB, $precision, '.', '').' KB';
        }

        return $bytes.' B';
    }
}
