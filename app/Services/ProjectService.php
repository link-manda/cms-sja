<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        // Kelola upload gambar jika ada
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        }
        // Tangkap gallery_images sebelum dikirim ke ::create
        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        $project = Project::create($data);

        // Kelola upload gambar galeri jika ada
        if (!empty($galleryImages)) {
            foreach ($galleryImages as $image) {
                if ($image instanceof UploadedFile) {
                    $filename = Str::random(40).'.'.$image->getClientOriginalExtension();
                    $image->storeAs('projects/gallery', $filename, 'public');
                    $project->images()->create(['image_path' => 'projects/gallery/' . $filename]);
                }
            }
        }

        return $project;
    }

    /**
     * Memperbarui data project beserta file gambarnya (jika di-upload yang baru).
     */
    public function updateProject(Project $project, array $data): bool
    {
        // Kelola upload gambar baru
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Hapus gambar lama jika ada
            if ($project->image) {
                Storage::disk('public')->delete('projects/'.$project->image);
            }

            // Simpan gambar baru
            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        } else {
            // Pertahankan gambar lama jika tidak di-upload gambar baru
            unset($data['image']);
        }
        // Tangkap gallery_images jika ada upload baru
        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        $updated = $project->update($data);

        // Kelola upload gambar galeri baru
        if (!empty($galleryImages)) {
            foreach ($galleryImages as $image) {
                if ($image instanceof UploadedFile) {
                    $filename = Str::random(40).'.'.$image->getClientOriginalExtension();
                    $image->storeAs('projects/gallery', $filename, 'public');
                    $project->images()->create(['image_path' => 'projects/gallery/' . $filename]);
                }
            }
        }

        return $updated;
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
