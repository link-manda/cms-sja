<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $categories = Category::orderBy('name')->get();

        return view('projects.create', compact('categories'));
    }

    /**
     * Menyimpan project baru ke database.
     *
     * @return RedirectResponse
     */
    public function store(StoreProjectRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['is_for_sale_or_rent'] = $request->boolean('is_for_sale_or_rent');

        $this->projectService->createProject($validatedData);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * Mengunggah satu gambar galeri sementara secara asynchronous (staged upload).
     */
    public function uploadTempGallery(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:10240',
        ], [
            'file.required' => 'No image file was provided.',
            'file.image' => 'The uploaded file must be a valid image.',
            'file.mimes' => 'The image format must be JPG, PNG, or WEBP.',
            'file.extensions' => 'The image extension must be .jpg, .jpeg, .png, or .webp.',
            'file.max' => 'The image may not be greater than 10 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first('file'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $filename = Str::random(40).'.'.$file->extension();
        $path = $file->storeAs('projects/temp-gallery', $filename, 'public');

        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'Failed to store temporary image.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'temp_path' => $path,
            'filename' => $filename,
        ]);
    }

    /**
     * Menghasilkan presigned S3 PUT URL untuk unggah video showcase langsung ke Cloudflare R2.
     */
    public function presignVideoUpload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'filename' => 'required|string|max:255',
            'file_size' => 'required|integer|min:1|max:52428800', // max 50 MB (50 * 1024 * 1024 bytes)
            'mime_type' => 'required|string|in:video/mp4,video/webm',
        ], [
            'filename.required' => 'Nama berkas video wajib disediakan.',
            'file_size.required' => 'Ukuran berkas video wajib disediakan.',
            'file_size.max' => 'Ukuran berkas video maksimal 50 MB.',
            'mime_type.required' => 'Tipe format video wajib disediakan.',
            'mime_type.in' => 'Format video yang didukung hanya MP4 dan WebM.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $extension = strtolower(pathinfo($request->input('filename'), PATHINFO_EXTENSION));
        if (! in_array($extension, ['mp4', 'webm'], true)) {
            $extension = $request->input('mime_type') === 'video/webm' ? 'webm' : 'mp4';
        }

        $key = 'projects/videos/'.Str::random(40).'.'.$extension;
        $mimeType = $request->input('mime_type');

        try {
            $res = Storage::disk('r2')->temporaryUploadUrl(
                $key,
                now()->addMinutes(20),
                ['ContentType' => $mimeType]
            );

            $uploadUrl = is_array($res) ? ($res['url'] ?? '') : (string) $res;
            $uploadHeaders = is_array($res) ? ($res['headers'] ?? []) : [];
        } catch (\Throwable $e) {
            Log::error('Gagal membuat presigned upload URL R2: '.$e->getMessage(), [
                'exception' => $e,
                'key' => $key,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate presigned upload URL for Cloudflare R2. Please ensure R2 storage is properly configured.',
            ], 500);
        }

        $baseUrl = rtrim(config('filesystems.disks.r2.url', ''), '/');
        $publicUrl = ! empty($baseUrl) ? $baseUrl.'/'.$key : Storage::disk('r2')->url($key);

        return response()->json([
            'status' => 'success',
            'upload_url' => $uploadUrl,
            'upload_headers' => $uploadHeaders,
            'key' => $key,
            'public_url' => $publicUrl,
        ]);
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
        $categories = Category::orderBy('name')->get();

        return view('projects.edit', compact('project', 'categories'));
    }

    /**
     * Memperbarui project spesifik di database.
     *
     * @return RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validatedData = $request->validated();
        $validatedData['is_for_sale_or_rent'] = $request->boolean('is_for_sale_or_rent');

        $this->projectService->updateProject($project, $validatedData);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
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
