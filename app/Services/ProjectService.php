<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
            $filename = time().'_'.uniqid().'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        }

        return Project::create($data);
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
            $filename = time().'_'.uniqid().'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        } else {
            // Pertahankan gambar lama jika tidak di-upload gambar baru
            unset($data['image']);
        }

        return $project->update($data);
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
        // Hapus file gambar dari disk
        if ($project->image) {
            Storage::disk('public')->delete('projects/'.$project->image);
        }

        return $project->forceDelete();
    }
}
