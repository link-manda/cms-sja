<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    public function index(Request $request)
    {
        $totalProjects = Project::count();
        $completedProjects = Project::where('status', 'Completed')->count();
        $ongoingProjects = Project::where('status', 'Ongoing')->count();

        $recentProjects = Project::latest()->take(5)->get();

        $seoOptimizedCount = Project::whereNotNull('meta_title')
            ->where('meta_title', '!=', '')
            ->whereNotNull('meta_description')
            ->where('meta_description', '!=', '')
            ->count();

        $seoPercentage = $totalProjects > 0 ? (int) round(($seoOptimizedCount / $totalProjects) * 100) : 0;

        return view('dashboards.index', compact(
            'totalProjects',
            'completedProjects',
            'ongoingProjects',
            'recentProjects',
            'seoPercentage'
        ));
    }

    public function root(Request $request, $first)
    {
        return view($first);
    }

    public function secondLevel(Request $request, $first, $second)
    {
        return view($first.'.'.$second);
    }

    public function thirdLevel(Request $request, $first, $second, $third)
    {
        return view($first.'.'.$second.'.'.$third);
    }
}
