<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Service Class untuk mengelola logika bisnis Model Project.
 * Menerapkan prinsip "Fat Models, Skinny Controllers".
 */
class ProjectService
{
    /**
     * Menyimpan project baru ke dalam database beserta file gambarnya dan video URLs.
     */
    public function createProject(array $data): Project
    {
        $data = $this->normalizePropertyOfferData($data);

        $newStoredImage = null;
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $filename = Str::random(40).'.'.$data['image']->extension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
            $newStoredImage = $filename;
        }

        $galleryImages = $data['gallery_images'] ?? [];
        $tempGalleryImages = $data['temp_gallery_images'] ?? [];
        $galleryVideos = $data['gallery_videos'] ?? [];
        $videoKey = $data['video_key'] ?? null;
        $videoFileSize = isset($data['video_file_size']) ? (int) $data['video_file_size'] : null;
        $videoMimeType = $data['video_mime_type'] ?? null;
        unset($data['gallery_images'], $data['temp_gallery_images'], $data['gallery_videos'], $data['video_key'], $data['video_file_size'], $data['video_mime_type']);

        try {
            return DB::transaction(function () use ($data, $galleryImages, $tempGalleryImages, $galleryVideos, $videoKey, $videoFileSize, $videoMimeType) {
                $project = Project::create($data);
                $this->storeGalleryMedia($project, $galleryImages, $galleryVideos, $tempGalleryImages, $videoKey, $videoFileSize, $videoMimeType);

                return $project;
            });
        } catch (Throwable $e) {
            if ($newStoredImage && Storage::disk('public')->exists('projects/'.$newStoredImage)) {
                Storage::disk('public')->delete('projects/'.$newStoredImage);
            }
            throw $e;
        }
    }

    /**
     * Memperbarui data project beserta file gambarnya (jika di-upload yang baru) dan video URLs.
     */
    public function updateProject(Project $project, array $data): bool
    {
        $data = $this->normalizePropertyOfferData($data);

        $oldImageToDelete = null;
        $newStoredImage = null;

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $oldImageToDelete = $project->image;
            $filename = Str::random(40).'.'.$data['image']->extension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
            $newStoredImage = $filename;
        } else {
            unset($data['image']);
        }

        $galleryImages = $data['gallery_images'] ?? [];
        $tempGalleryImages = $data['temp_gallery_images'] ?? [];
        $galleryVideos = $data['gallery_videos'] ?? [];
        $videoKey = $data['video_key'] ?? null;
        $videoFileSize = isset($data['video_file_size']) ? (int) $data['video_file_size'] : null;
        $videoMimeType = $data['video_mime_type'] ?? null;
        unset($data['gallery_images'], $data['temp_gallery_images'], $data['gallery_videos'], $data['video_key'], $data['video_file_size'], $data['video_mime_type']);

        try {
            return DB::transaction(function () use ($project, $data, $galleryImages, $tempGalleryImages, $galleryVideos, $oldImageToDelete, $videoKey, $videoFileSize, $videoMimeType) {
                $updated = $project->update($data);
                $this->storeGalleryMedia($project, $galleryImages, $galleryVideos, $tempGalleryImages, $videoKey, $videoFileSize, $videoMimeType);

                if ($oldImageToDelete) {
                    DB::afterCommit(function () use ($oldImageToDelete) {
                        if (Storage::disk('public')->exists('projects/'.$oldImageToDelete)) {
                            Storage::disk('public')->delete('projects/'.$oldImageToDelete);
                        }
                    });
                }

                return $updated;
            });
        } catch (Throwable $e) {
            if ($newStoredImage && Storage::disk('public')->exists('projects/'.$newStoredImage)) {
                Storage::disk('public')->delete('projects/'.$newStoredImage);
            }
            throw $e;
        }
    }

    /**
     * Menormalkan data penawaran properti dan estimasi ROI.
     */
    private function normalizePropertyOfferData(array $data): array
    {
        if (array_key_exists('is_for_sale_or_rent', $data)) {
            $isEnabled = filter_var($data['is_for_sale_or_rent'], FILTER_VALIDATE_BOOLEAN);
            $data['is_for_sale_or_rent'] = $isEnabled;

            if ($isEnabled) {
                $propertyType = isset($data['property_type']) ? ucfirst(strtolower(trim((string) $data['property_type']))) : null;
                $data['property_type'] = $propertyType;
                if ($propertyType === 'Rent') {
                    $data['roi_estimation'] = null;
                }
            } else {
                $data['property_type'] = null;
                $data['price'] = null;
                $data['roi_estimation'] = null;
            }
        } elseif (isset($data['property_type'])) {
            $propertyType = ucfirst(strtolower(trim((string) $data['property_type'])));
            $data['property_type'] = $propertyType;
            if ($propertyType === 'Rent') {
                $data['roi_estimation'] = null;
            }
        }

        return $data;
    }

    private function storeGalleryMedia(Project $project, array $images, array $videoUrls = [], array $tempImages = [], ?string $videoKey = null, ?int $videoFileSize = null, ?string $videoMimeType = null): void
    {
        $storedPaths = [];

        try {
            // 1. Process uploaded image files (direct multipart)
            foreach ($images as $image) {
                if (! $image instanceof UploadedFile) {
                    continue;
                }

                $filename = Str::random(40).'.'.$image->extension();
                $path = 'projects/gallery/'.$filename;

                if (! $image->storeAs('projects/gallery', $filename, 'public')) {
                    throw new RuntimeException('Failed to store gallery image.');
                }

                $storedPaths[] = $path;

                $project->images()->create([
                    'type' => 'image',
                    'storage_disk' => 'public',
                    'image_path' => $path,
                    'video_url' => null,
                ]);
            }

            // 2. Process temporary uploaded image files (asynchronous staged upload)
            foreach ($tempImages as $tempPath) {
                if (! is_string($tempPath) || empty($tempPath)) {
                    continue;
                }

                $cleanTempPath = str_replace('\\', '/', $tempPath);
                $filename = basename($cleanTempPath);
                $sourcePath = 'projects/temp-gallery/'.$filename;

                if (Storage::disk('public')->exists($sourcePath)) {
                    $targetPath = 'projects/gallery/'.$filename;
                    Storage::disk('public')->move($sourcePath, $targetPath);
                    $storedPaths[] = $targetPath;

                    $project->images()->create([
                        'type' => 'image',
                        'storage_disk' => 'public',
                        'image_path' => $targetPath,
                        'video_url' => null,
                    ]);
                }
            }

            // 3. Process video URLs (filter empty/whitespace)
            foreach ($videoUrls as $videoUrl) {
                $videoUrl = is_string($videoUrl) ? trim($videoUrl) : '';
                if (empty($videoUrl)) {
                    continue;
                }

                $project->images()->create([
                    'type' => 'video',
                    'storage_disk' => 'public',
                    'image_path' => null,
                    'video_url' => $videoUrl,
                ]);
            }

            // 4. Process Cloudflare R2 uploaded video showcase
            if (! empty($videoKey)) {
                $cleanVideoKey = str_replace('\\', '/', trim($videoKey));
                $baseUrl = rtrim(config('filesystems.disks.r2.url', ''), '/');
                $publicVideoUrl = ! empty($baseUrl) ? $baseUrl.'/'.$cleanVideoKey : Storage::disk('r2')->url($cleanVideoKey);

                $project->images()->create([
                    'type' => 'video',
                    'storage_disk' => 'r2',
                    'image_path' => $cleanVideoKey,
                    'video_url' => $publicVideoUrl,
                    'file_size' => $videoFileSize,
                    'mime_type' => $videoMimeType ?: 'video/mp4',
                ]);

                app(R2StorageService::class)->invalidateCache();
            }
        } catch (Throwable $exception) {
            if (! empty($storedPaths)) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }
    }

    public function deleteGalleryImage(Project $project, int $imageId): bool
    {
        $image = $project->images()->findOrFail($imageId);
        $isR2 = $image->storage_disk === 'r2';

        if ($isR2 && ! empty($image->image_path)) {
            try {
                if (Storage::disk('r2')->exists($image->image_path)) {
                    Storage::disk('r2')->delete($image->image_path);
                }
            } catch (Throwable $e) {
                Log::warning('Gagal menghapus file video dari Cloudflare R2: '.$e->getMessage(), [
                    'image_id' => $imageId,
                    'path' => $image->image_path,
                ]);
            }
        } elseif ($image->type === 'image' && ! empty($image->image_path) && Storage::disk('public')->exists($image->image_path)) {
            if (! Storage::disk('public')->delete($image->image_path)) {
                return false;
            }
        }

        $deleted = (bool) $image->delete();

        if ($deleted && $isR2) {
            app(R2StorageService::class)->invalidateCache();
        }

        return $deleted;
    }

    /**
     * Menghapus sementara project (Soft Delete).
     */
    public function deleteProject(Project $project): ?bool
    {
        // Jangan hapus gambar saat soft delete
        return $project->delete();
    }

    /**
     * Menghapus project secara permanen beserta gambarnya.
     */
    public function forceDeleteProject(Project $project): ?bool
    {
        // Hapus file gambar utama dari disk
        if ($project->image && Storage::disk('public')->exists('projects/'.$project->image)) {
            Storage::disk('public')->delete('projects/'.$project->image);
        }

        $hasR2 = false;

        // Hapus file media galeri dari disk
        foreach ($project->images as $galleryMedia) {
            if ($galleryMedia->storage_disk === 'r2' && ! empty($galleryMedia->image_path)) {
                $hasR2 = true;
                try {
                    if (Storage::disk('r2')->exists($galleryMedia->image_path)) {
                        Storage::disk('r2')->delete($galleryMedia->image_path);
                    }
                } catch (Throwable $e) {
                    Log::warning('Gagal menghapus file R2 saat force delete: '.$e->getMessage());
                }
            } elseif ($galleryMedia->type === 'image' && ! empty($galleryMedia->image_path) && Storage::disk('public')->exists($galleryMedia->image_path)) {
                Storage::disk('public')->delete($galleryMedia->image_path);
            }
        }

        // Hapus record gallery (jika cascade on delete belum diset di database)
        $project->images()->delete();

        $result = $project->forceDelete();

        if ($result && $hasR2) {
            app(R2StorageService::class)->invalidateCache();
        }

        return $result;
    }
}
