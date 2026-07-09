@extends('layouts.vertical', ['title' => 'Project Details'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Project Details'])

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h6 class="card-title text-base font-semibold text-default-800">Project Details: {{ $project->title }}</h6>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm bg-primary text-white cursor-pointer">
                    Edit Project
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-sm border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">
                    Back
                </a>
            </div>
         </div>
         <div class="card-body">
             <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <!-- Main Photo -->
                 <div class="md:col-span-1">
                     <p class="text-xs font-semibold text-default-500 mb-2">Main Project Photo</p>
                     @if ($project->image)
                         <img src="{{ asset('storage/projects/' . $project->image) }}" alt="{{ $project->title }}" class="w-full object-cover rounded border border-default-200 shadow">
                     @else
                         <div class="aspect-video w-full bg-default-100 dark:bg-zinc-800 flex items-center justify-center rounded text-sm text-default-400">No Image</div>
                     @endif
                 </div>

                 <!-- Project Info -->
                 <div class="md:col-span-2 space-y-4">
                     <div>
                         <h4 class="text-xl font-bold text-default-900">{{ $project->title }}</h4>
                         <p class="text-xs text-default-400">Slug: <span class="font-mono bg-default-100 dark:bg-zinc-800 px-1 py-0.5 rounded">{{ $project->slug }}</span></p>
                     </div>

                     <div class="grid grid-cols-2 md:grid-cols-3 gap-4 border-y border-default-200 py-3">
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Location</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="map-pin"></i> {{ $project->location }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Status</p>
                            <div>
                                @if ($project->status === 'Completed')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-success/10 text-success">Completed</span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-warning/10 text-warning">Ongoing</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Category</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="tag"></i> {{ $project->category->name ?? 'Uncategorized' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Client</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="user"></i> {{ $project->client ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Year of Completion</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="calendar"></i> {{ $project->year ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Execution Team</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="users"></i> {{ $project->execution_team ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Building Area</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="home"></i> {{ $project->building_area ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-default-500 mb-0.5">Land Area</p>
                            <p class="text-sm font-medium text-default-900 flex items-center gap-1">
                                <i class="size-4 text-primary" data-lucide="maximize"></i> {{ $project->land_area ?? '-' }}
                            </p>
                        </div>
                    </div>

                     <div>
                         <p class="text-xs font-semibold text-default-500 mb-2">Project Description</p>
                         <p class="text-sm text-default-700 leading-relaxed whitespace-pre-line">{{ $project->description }}</p>
                     </div>

                     <!-- SEO Section -->
                     @if ($project->meta_title || $project->meta_description)
                         <div class="bg-default-50 dark:bg-zinc-900 p-4 rounded border border-default-200 space-y-2">
                             <h6 class="text-xs font-bold text-default-800 flex items-center gap-1">
                                 <i class="size-3.5" data-lucide="search"></i> Search Engine Appearance (SEO)
                             </h6>
                             @if ($project->meta_title)
                                 <div>
                                     <p class="text-[11px] font-semibold text-default-500">Meta Title</p>
                                     <p class="text-xs text-primary font-medium">{{ $project->meta_title }}</p>
                                 </div>
                             @endif
                             @if ($project->meta_description)
                                 <div>
                                     <p class="text-[11px] font-semibold text-default-500">Meta Description</p>
                                     <p class="text-xs text-default-600 leading-normal">{{ $project->meta_description }}</p>
                                 </div>
                             @endif
                         </div>
                     @endif
                 </div>
             </div>
         </div>
     </div>

    <div class="card mt-6">
        <div class="card-header flex justify-between items-center">
            <div>
                <h6 class="card-title text-base font-semibold text-default-800">Project Gallery</h6>
                <p class="text-sm text-default-500 mt-1">Read-only preview of photos shown on the public case study page.</p>
            </div>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">
                Manage Gallery
            </a>
        </div>
        <div class="card-body">
            @if ($project->images->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($project->images as $image)
                        <div class="rounded-lg overflow-hidden border border-default-200 shadow-sm">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-32 object-cover" alt="{{ $project->title }} gallery image" loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-default-300 bg-default-50 dark:bg-zinc-900 p-8 text-center text-sm text-default-500">
                    No gallery photos uploaded yet.
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
@endsection
