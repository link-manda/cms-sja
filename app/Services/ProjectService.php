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
     * Menyimpan project baru ke dalam database beserta file gambarnya.
     */
    public function createProject(array $data): Project
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        return DB::transaction(function () use ($data, $galleryImages) {
            $project = Project::create($data);
            $this->storeGalleryImages($project, $galleryImages);

            return $project;
        });
    }

    /**
     * Memperbarui data project beserta file gambarnya (jika di-upload yang baru).
     */
    public function updateProject(Project $project, array $data): bool
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($project->image) {
                Storage::disk('public')->delete('projects/'.$project->image);
            }

            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        } else {
            unset($data['image']);
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        return DB::transaction(function () use ($project, $data, $galleryImages) {
            $updated = $project->update($data);
            $this->storeGalleryImages($project, $galleryImages);

            return $updated;
        });
    }

    private function storeGalleryImages(Project $project, array $images): void
    {
        $storedPaths = [];

        try {
            foreach ($images as $image) {
                if (! $image instanceof UploadedFile) {
                    continue;
                }

                $filename = Str::random(40).'.'.$image->getClientOriginalExtension();
                $path = 'projects/gallery/'.$filename;

                if (! $image->storeAs('projects/gallery', $filename, 'public')) {
                    throw new RuntimeException('Failed to store gallery image.');
                }

                $storedPaths[] = $path;

                $project->images()->create(['image_path' => $path]);
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }
    }

    public function deleteGalleryImage(Project $project, int $imageId): bool
    {
        $image = $project->images()->findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image_path) && ! Storage::disk('public')->delete($image->image_path)) {
            return false;
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
        if ($project->image) {
            Storage::disk('public')->delete('projects/'.$project->image);
        }

        // Hapus file gambar galeri dari disk
        foreach ($project->images as $galleryImage) {
            Storage::disk('public')->delete($galleryImage->image_path);
        }

        // Hapus record gallery (jika cascade on delete belum diset di database)
        $project->images()->delete();

        return $project->forceDelete();
    }
}
