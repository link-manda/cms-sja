<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $filename = Str::random(40).'.'.$data['image']->extension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        }

        $galleryImages = $data['gallery_images'] ?? [];
        $galleryVideos = $data['gallery_videos'] ?? [];
        unset($data['gallery_images'], $data['gallery_videos']);

        return DB::transaction(function () use ($data, $galleryImages, $galleryVideos) {
            $project = Project::create($data);
            $this->storeGalleryMedia($project, $galleryImages, $galleryVideos);

            return $project;
        });
    }

    /**
     * Memperbarui data project beserta file gambarnya (jika di-upload yang baru) dan video URLs.
     */
    public function updateProject(Project $project, array $data): bool
    {
        $data = $this->normalizePropertyOfferData($data);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($project->image && Storage::disk('public')->exists('projects/'.$project->image)) {
                Storage::disk('public')->delete('projects/'.$project->image);
            }

            $filename = Str::random(40).'.'.$data['image']->extension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        } else {
            unset($data['image']);
        }

        $galleryImages = $data['gallery_images'] ?? [];
        $galleryVideos = $data['gallery_videos'] ?? [];
        unset($data['gallery_images'], $data['gallery_videos']);

        return DB::transaction(function () use ($project, $data, $galleryImages, $galleryVideos) {
            $updated = $project->update($data);
            $this->storeGalleryMedia($project, $galleryImages, $galleryVideos);

            return $updated;
        });
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

    private function storeGalleryMedia(Project $project, array $images, array $videoUrls = []): void
    {
        $storedPaths = [];

        try {
            // 1. Process uploaded image files
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
                    'image_path' => $path,
                    'video_url' => null,
                ]);
            }

            // 2. Process video URLs (filter empty/whitespace)
            foreach ($videoUrls as $videoUrl) {
                $videoUrl = is_string($videoUrl) ? trim($videoUrl) : '';
                if (empty($videoUrl)) {
                    continue;
                }

                $project->images()->create([
                    'type' => 'video',
                    'image_path' => null,
                    'video_url' => $videoUrl,
                ]);
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

        if ($image->type === 'image' && ! empty($image->image_path) && Storage::disk('public')->exists($image->image_path)) {
            if (! Storage::disk('public')->delete($image->image_path)) {
                return false;
            }
        }

        return (bool) $image->delete();
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

        // Hapus file gambar galeri dari disk (hanya untuk tipe image dengan image_path valid)
        foreach ($project->images as $galleryImage) {
            if ($galleryImage->type === 'image' && ! empty($galleryImage->image_path) && Storage::disk('public')->exists($galleryImage->image_path)) {
                Storage::disk('public')->delete($galleryImage->image_path);
            }
        }

        // Hapus record gallery (jika cascade on delete belum diset di database)
        $project->images()->delete();

        return $project->forceDelete();
    }
}
