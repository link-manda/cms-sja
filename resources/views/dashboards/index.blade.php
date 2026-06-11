@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Dashboard'])

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <!-- Total Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-primary/10 text-primary">
                    <i class="size-7" data-lucide="folder-kanban"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Total Projects</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $totalProjects }}
                    </h5>
                </div>
                <a href="{{ route('projects.index') }}" class="ms-auto btn size-8 flex items-center justify-center bg-default-100 hover:bg-default-200 text-default-700 rounded-full transition-all cursor-pointer" title="Manage Projects">
                    <i class="size-4.5" data-lucide="arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-success/10 text-success">
                    <i class="size-7" data-lucide="check-circle-2"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Completed</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $completedProjects }}
                    </h5>
                </div>
                <span class="ms-auto px-2.5 py-0.5 text-xs font-semibold rounded bg-success/15 text-success">Finished</span>
            </div>
        </div>

        <!-- Ongoing Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-warning/10 text-warning">
                    <i class="size-7" data-lucide="clock"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Ongoing</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $ongoingProjects }}
                    </h5>
                </div>
                <span class="ms-auto px-2.5 py-0.5 text-xs font-semibold rounded bg-warning/15 text-warning animate-pulse">Active</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left: Recent Projects Table -->
        <div class="lg:col-span-2 col-span-1">
            <div class="card h-full">
                <div class="card-header flex justify-between items-center">
                    <h6 class="card-title text-base font-semibold text-default-800">Recent Projects</h6>
                    <a href="{{ route('projects.index') }}" class="text-xs text-primary hover:underline font-medium">View All Projects</a>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-default-200">
                            <thead class="bg-default-100">
                                <tr class="text-xs font-semibold text-default-500 uppercase">
                                    <th class="px-5 py-3 text-start" scope="col">Project Name</th>
                                    <th class="px-5 py-3 text-start" scope="col">Location</th>
                                    <th class="px-5 py-3 text-start" scope="col">Status</th>
                                    <th class="px-5 py-3 text-center" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-default-200">
                                @forelse ($recentProjects as $project)
                                    <tr class="text-default-800 hover:bg-default-50 transition duration-150">
                                        <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-default-900">
                                            {{ $project->title }}
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-sm">
                                            {{ $project->location }}
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-sm">
                                            @if ($project->status === 'Completed')
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-success/10 text-success">Completed</span>
                                            @else
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-warning/10 text-warning">Ongoing</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-sm text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('public.projects.show', $project->slug) }}" target="_blank" class="btn size-7 flex items-center justify-center bg-info/10 text-info hover:bg-info hover:text-white rounded transition cursor-pointer" title="Preview Case Study">
                                                    <i class="size-4" data-lucide="external-link"></i>
                                                </a>
                                                <a href="{{ route('projects.edit', $project->id) }}" class="btn size-7 flex items-center justify-center bg-primary/10 text-primary hover:bg-primary hover:text-white rounded transition cursor-pointer" title="Edit">
                                                    <i class="size-4" data-lucide="edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-10 text-center text-default-500 text-sm">
                                            No projects registered yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: SEO Health & Quick Actions -->
        <div class="col-span-1 space-y-5">
            <!-- SEO Health Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title text-base font-semibold text-default-800">Google SEO Coverage</h6>
                </div>
                <div class="card-body">
                    <div class="flex flex-col items-center py-4">
                        <!-- Simple Progress Ring / Stats -->
                        <div class="relative flex items-center justify-center size-28 mb-4">
                            <svg class="size-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-default-150" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="text-primary" stroke-width="3" stroke-dasharray="{{ $seoPercentage }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <span class="absolute text-xl font-bold text-default-900">{{ $seoPercentage }}%</span>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-default-800">SEO Health Index</p>
                            <p class="text-xs text-default-500 mt-1 leading-normal max-w-[220px] mx-auto">
                                Percentage of projects equipped with Meta Title & Meta Description tags.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title text-base font-semibold text-default-800">Quick Shortcuts</h6>
                </div>
                <div class="card-body space-y-3">
                    <a href="{{ route('projects.create') }}" class="btn w-full bg-primary text-white flex items-center justify-center gap-2 cursor-pointer py-2.5 rounded-xl font-semibold shadow-sm hover:shadow-md transition">
                        <i class="size-4" data-lucide="plus"></i> Add New Project
                    </a>
                    <a href="{{ url('/') }}" target="_blank" class="btn w-full border border-default-300 text-default-700 hover:bg-default-150 flex items-center justify-center gap-2 cursor-pointer py-2.5 rounded-xl font-semibold transition">
                        <i class="size-4" data-lucide="globe"></i> Preview Website
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
@endsection
