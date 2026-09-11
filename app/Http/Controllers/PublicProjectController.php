<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProjectController extends Controller
{
    /**
     * Show the case study of a specific project.
     */
    public function show(string $slug): View
    {
        $project = Project::where('slug', $slug)->with('images')->firstOrFail();

        // Fetch related projects (excluding current) to display in the bottom section
        $relatedProjects = Project::where('id', '!=', $project->id)
            ->latest()
            ->take(3)
            ->get();

        $imagePath = str_starts_with($project->image, 'http')
            ? $project->image
            : (file_exists(public_path('assets/'.$project->image))
                ? asset('assets/'.$project->image)
                : (str_starts_with($project->image, 'projects/')
                    ? asset('storage/'.$project->image)
                    : asset('storage/projects/'.$project->image)));

        $allMediaItems = [
            [
                'type' => 'image',
                'src' => $imagePath,
                'embedUrl' => null,
                'thumb' => $imagePath,
            ],
        ];

        foreach ($project->images as $img) {
            $allMediaItems[] = [
                'type' => $img->type ?? 'image',
                'src' => ($img->type === 'video') ? $img->embed_url : asset('storage/'.$img->image_path),
                'embedUrl' => ($img->type === 'video') ? $img->embed_url : null,
                'thumb' => ($img->type === 'video') ? $img->thumbnail_url : asset('storage/'.$img->image_path),
            ];
        }

        return view('public.projects.show', compact('project', 'relatedProjects', 'allMediaItems'));
    }

    /**
     * Display a listing of all projects with filters.
     */
    public function index(Request $request): View
    {
        $query = Project::query();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $query->where('location', 'like', '%'.$request->province.'%');
        }

        $projects = $query->latest()->paginate(9)->withQueryString();

        $categories = Category::all();

        // Extract distinct locations/provinces to build a dropdown.
        // Note: Assumes location field holds values like "Bali", "Jakarta"
        $provinces = Project::select('location')->distinct()->whereNotNull('location')->pluck('location');

        return view('public.projects.index', compact('projects', 'categories', 'provinces'));
    }
}
