@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Dashboard'])

    <!-- KPI Cards Grid (Project Portfolio Domain) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <!-- Total Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-primary/10 text-primary shrink-0">
                    <i class="size-7" data-lucide="folder-kanban"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Total Projects</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $totalProjects }}
                    </h5>
                    <p class="text-xs text-default-400 mt-1">Active portfolio projects</p>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-success/10 text-success shrink-0">
                    <i class="size-7" data-lucide="check-circle-2"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Completed</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $completedProjects }}
                    </h5>
                    <p class="text-xs text-default-400 mt-1"><span class="text-success font-semibold">{{ $completedPercentage }}%</span> completion rate</p>
                </div>
            </div>
        </div>

        <!-- Ongoing Projects -->
        <div class="card">
            <div class="card-body flex items-center gap-4">
                <div class="flex items-center justify-center rounded-full size-14 bg-warning/10 text-warning shrink-0">
                    <i class="size-7" data-lucide="clock"></i>
                </div>
                <div>
                    <p class="text-sm text-default-500 font-medium uppercase tracking-wider">Ongoing</p>
                    <h5 class="text-2xl font-bold text-default-900 mt-1">
                        {{ $ongoingProjects }}
                    </h5>
                    <p class="text-xs text-default-400 mt-1"><span class="text-warning font-semibold">{{ $ongoingPercentage }}%</span> in progress</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Left: Recent Projects Table -->
        <div class="lg:col-span-2 col-span-1">
            <div class="card">
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

        <!-- Right: System & Media Health, Shortcuts -->
        <div class="col-span-1 space-y-5">
            <!-- Cloudflare R2 Storage Card -->
            <div class="card" id="r2-storage-card">
                <div class="card-header flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center rounded-lg size-8 bg-sky-500/10 text-sky-500 shrink-0">
                            <i class="size-4.5" data-lucide="cloud"></i>
                        </div>
                        <h6 class="card-title text-base font-semibold text-default-800">Media Storage</h6>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="r2-status-badge" class="px-2 py-0.5 text-xs font-semibold rounded inline-flex items-center gap-1 {{ $r2Storage['bg_color'] }} {{ $r2Storage['text_color'] }}">
                            <i id="r2-status-icon" class="size-3.5" data-lucide="{{ $r2Storage['icon'] }}"></i>
                            <span id="r2-status-text">{{ $r2Storage['status_label'] }}</span>
                        </span>
                        <button type="button" id="btn-sync-r2" class="btn size-7 flex items-center justify-center bg-default-100 hover:bg-default-200 text-default-700 rounded-full transition cursor-pointer" title="Sync R2 Storage">
                            <i id="r2-sync-icon" class="size-3.5" data-lucide="refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body space-y-3 pt-3">
                    <div class="flex items-baseline justify-between">
                        <h5 id="r2-usage-text" class="text-xl font-bold text-default-900 tracking-tight" aria-live="polite">
                            {{ $r2Storage['total_formatted'] }} <span class="text-xs font-normal text-default-400">/ {{ $r2Storage['limit_formatted'] }}</span>
                        </h5>
                        <span class="text-xs text-default-400 font-medium">Business Pro Tier</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-default-150 rounded-full h-2 overflow-hidden" role="progressbar" id="r2-progressbar-container" aria-valuenow="{{ $r2Storage['percentage'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Cloudflare R2 Storage Capacity">
                        <div id="r2-progressbar" class="h-2 rounded-full transition-all duration-500 {{ $r2Storage['bar_color'] }}" style="width: {{ max(1, $r2Storage['percentage']) }}%;"></div>
                    </div>

                    <!-- Counter Footer -->
                    <div class="flex items-center justify-between text-xs text-default-500 font-medium pt-0.5">
                        <span id="r2-percent-text">{{ $r2Storage['percentage'] }}% Used</span>
                        <span id="r2-video-count">{{ $r2Storage['video_count'] }} {{ $r2Storage['video_count'] === 1 ? 'Active Video' : 'Active Videos' }}</span>
                    </div>
                </div>
            </div>

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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnSync = document.getElementById('btn-sync-r2');
    if (!btnSync) return;

    btnSync.addEventListener('click', function () {
        const syncIcon = document.getElementById('r2-sync-icon');
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

        // Disable button & animate spinner
        btnSync.disabled = true;
        btnSync.classList.add('pointer-events-none', 'opacity-60');
        btnSync.setAttribute('aria-busy', 'true');
        if (syncIcon) {
            syncIcon.classList.add('animate-spin');
        }

        fetch('{{ route('manage.storage.sync-r2') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(async (response) => {
            if (response.status === 419) {
                alert('Your session has expired. Please reload the page to refresh your session.');
                window.location.reload();
                return;
            }
            if (response.status === 429) {
                alert('Too many sync requests. Please wait a moment before trying again.');
                return;
            }

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Failed to synchronize Cloudflare R2 data.');
            }

            const usage = data.data;

            // Update text metrics
            const usageText = document.getElementById('r2-usage-text');
            if (usageText) {
                usageText.innerHTML = `${usage.total_formatted} <span class="text-xs font-normal text-default-400">/ ${usage.limit_formatted}</span>`;
            }

            const percentText = document.getElementById('r2-percent-text');
            if (percentText) {
                percentText.textContent = `${usage.percentage}% Used`;
            }

            const videoCount = document.getElementById('r2-video-count');
            if (videoCount) {
                videoCount.textContent = `${usage.video_count} ${usage.video_count === 1 ? 'Active Video' : 'Active Videos'}`;
            }

            // Update Progress Bar
            const progressContainer = document.getElementById('r2-progressbar-container');
            if (progressContainer) {
                progressContainer.setAttribute('aria-valuenow', usage.percentage);
            }

            const progressBar = document.getElementById('r2-progressbar');
            if (progressBar) {
                progressBar.style.width = `${Math.max(1, usage.percentage)}%`;
                progressBar.className = `h-2 rounded-full transition-all duration-500 ${usage.bar_color}`;
            }

            // Update Status Badge & Icon
            const statusBadge = document.getElementById('r2-status-badge');
            const statusText = document.getElementById('r2-status-text');
            if (statusBadge && statusText) {
                statusText.textContent = usage.status_label;
                statusBadge.className = `px-2 py-0.5 text-xs font-semibold rounded inline-flex items-center gap-1 ${usage.bg_color} ${usage.text_color}`;

                const oldIcon = document.getElementById('r2-status-icon');
                if (oldIcon) {
                    const newIcon = document.createElement('i');
                    newIcon.id = 'r2-status-icon';
                    newIcon.className = 'size-3.5';
                    newIcon.setAttribute('data-lucide', usage.icon);
                    oldIcon.replaceWith(newIcon);

                    // Re-render only the updated status icon inside the status badge container
                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons({
                            root: statusBadge
                        });
                    }
                }
            }
        })
        .catch((error) => {
            alert(error.message || 'An error occurred while synchronizing Cloudflare R2 data.');
        })
        .finally(() => {
            btnSync.disabled = false;
            btnSync.classList.remove('pointer-events-none', 'opacity-60');
            btnSync.removeAttribute('aria-busy');
            const activeSyncIcon = document.getElementById('r2-sync-icon');
            if (activeSyncIcon) {
                activeSyncIcon.classList.remove('animate-spin');
            }
        });
    });
});
</script>
@endsection
