<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class PublicProjectController extends Controller
{
    /**
     * Show the case study of a specific project.
     */
    public function show(string $slug): View
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        // Fetch related projects (excluding current) to display in the bottom section
        $relatedProjects = Project::where('id', '!=', $project->id)
            ->latest()
            ->take(3)
            ->get();

        return view('public.projects.show', compact('project', 'relatedProjects'));
    }

    /**
     * Display a listing of all projects with filters.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Project::query();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $query->where('location', 'like', '%' . $request->province . '%');
        }

        $projects = $query->latest()->paginate(9)->withQueryString();

        $categories = \App\Models\Category::all();
        
        // Extract distinct locations/provinces to build a dropdown. 
        // Note: Assumes location field holds values like "Bali", "Jakarta"
        $provinces = Project::select('location')->distinct()->whereNotNull('location')->pluck('location');

        return view('public.projects.index', compact('projects', 'categories', 'provinces'));
    }
}
