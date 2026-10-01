<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\R2StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RoutingController extends Controller
{
    /**
     * Whitelist of allowed view namespaces for dynamic routing.
     * Prevents access to sensitive internal views (auth, layouts, components, etc.)
     */
    private const ALLOWED_VIEW_NAMESPACES = [
        'dashboards',
        'apps',
        'ecommerce',
        'hr',
        'landing',
        'layouts-eg',
        'pages',
    ];

    public function index(Request $request, R2StorageService $r2StorageService)
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
        $completedPercentage = $totalProjects > 0 ? (int) round(($completedProjects / $totalProjects) * 100) : 0;
        $ongoingPercentage = $totalProjects > 0 ? (int) round(($ongoingProjects / $totalProjects) * 100) : 0;
        $r2Storage = $r2StorageService->getStorageUsage();

        return view('dashboards.index', compact(
            'totalProjects',
            'completedProjects',
            'ongoingProjects',
            'completedPercentage',
            'ongoingPercentage',
            'recentProjects',
            'seoPercentage',
            'r2Storage'
        ));
    }

    /**
     * Endpoint asinkron untuk sinkronisasi on-demand metadata Cloudflare R2.
     */
    public function syncR2Storage(Request $request, R2StorageService $r2StorageService): JsonResponse
    {
        try {
            $result = $r2StorageService->reconcileBucket();

            return response()->json([
                'success' => true,
                'message' => 'Cloudflare R2 storage synchronized successfully.',
                'data' => $result['usage'],
                'updated_records' => $result['updated_records'],
                'deleted_orphans' => $result['deleted_orphans'],
                'physical_count' => $result['physical_count'],
            ]);
        } catch (Throwable $e) {
            Log::error('Gagal sinkronisasi R2 Storage via dashboard: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize data with Cloudflare R2. Please try again later.',
            ], 500);
        }
    }

    public function root(Request $request, $first)
    {
        if (! in_array($first, self::ALLOWED_VIEW_NAMESPACES)) {
            abort(404);
        }

        return view($first);
    }

    public function secondLevel(Request $request, $first, $second)
    {
        if (! in_array($first, self::ALLOWED_VIEW_NAMESPACES)) {
            abort(404);
        }

        return view($first.'.'.$second);
    }

    public function thirdLevel(Request $request, $first, $second, $third)
    {
        if (! in_array($first, self::ALLOWED_VIEW_NAMESPACES)) {
            abort(404);
        }

        return view($first.'.'.$second.'.'.$third);
    }
}
