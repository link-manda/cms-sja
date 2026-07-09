<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProjectController extends Controller
{
    protected $projectService;

    /**
     * Inisialisasi controller dengan menginjeksi ProjectService.
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Menampilkan daftar project dengan pagination.
     *
     * @return View
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Menampilkan form untuk membuat project baru.
     *
     * @return View
     */
    public function create()
    {
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('projects.create', compact('categories'));
    }

    /**
     * Menyimpan project baru ke database.
     *
     * @return RedirectResponse
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->createProject($request->validated());

        Log::channel('audit')->info('Project created', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'title' => $project->title,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Menampilkan detail project spesifik (jika diperlukan).
     *
     * @return View
     */
    public function show(Project $project)
    {
        $project->load(['category', 'images']);

        return view('projects.show', compact('project'));
    }

    /**
     * Menampilkan form edit untuk project spesifik.
     *
     * @return View
     */
    public function edit(Project $project)
    {
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('projects.edit', compact('project', 'categories'));
    }

    /**
     * Memperbarui project spesifik di database.
     *
     * @return RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->projectService->updateProject($project, $request->validated());

        Log::channel('audit')->info('Project updated', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'title' => $project->title,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Menghapus project spesifik dari database.
     *
     * @return RedirectResponse
     */
    public function destroy(Project $project)
    {
        $this->projectService->deleteProject($project);

        Log::channel('audit')->info('Project soft-deleted', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'title' => $project->title,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project moved to trash successfully!');
    }

    /**
     * Menampilkan daftar project yang terhapus (Soft Deletes).
     *
     * @return View
     */
    public function archive()
    {
        $projects = Project::onlyTrashed()->latest()->paginate(10);

        return view('projects.archive', compact('projects'));
    }

    /**
     * Mengembalikan project yang terhapus.
     *
     * @return RedirectResponse
     */
    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return redirect()->route('projects.archive')
            ->with('success', 'Project restored successfully!');
    }

    /**
     * Menghapus project secara permanen.
     *
     * @return RedirectResponse
     */
    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $this->projectService->forceDeleteProject($project);

        return redirect()->route('projects.archive')
            ->with('success', 'Project deleted permanently!');
    }

    /**
     * Menghapus 1 gambar spesifik dari galeri.
     *
     * @return RedirectResponse
     */
    public function deleteGalleryImage(Project $project, int $image): RedirectResponse
    {
        if (! $this->projectService->deleteGalleryImage($project, $image)) {
            return back()->with('error', 'Failed to delete gallery image file.');
        }

        Log::channel('audit')->info('Gallery image deleted', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'project_image_id' => $image,
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Gallery image deleted successfully!');
    }
}
