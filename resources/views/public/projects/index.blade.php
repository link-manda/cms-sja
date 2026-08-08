<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => 'Our Projects - PT Sistem Jaya Abadi',
        'seoTitle' => 'Our Projects - PT Sistem Jaya Abadi',
        'seoDescription' => 'Explore the portfolio of PT Sistem Jaya Abadi. View our completed and ongoing construction projects across Indonesia.',
        'seoUrl' => route('public.projects.index'),
    ])
</head>

<body
    class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen bg-background">

    <!-- Ambient Background Meshes -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <!-- Floating Island Navbar -->
    @include('partials.public-navbar')

    <!-- 1. Editorial Page Header -->
    <section class="pt-36 sm:pt-44 md:pt-48 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative w-full">
        <div class="text-center max-w-3xl mx-auto animate-reveal-up mb-12 sm:mb-16">
            <!-- Total Counter & Trust Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-black/5 shadow-ambient text-xs font-semibold text-primary uppercase tracking-widest mb-4 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $projects->total() }} Projects Portfolio</span>
            </div>

            <!-- Syne Headline -->
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary tracking-tight leading-[1.1] mb-6">
                Curated Works &amp; <span class="text-gradient">Architectural Milestones</span>
            </h1>

            <p class="text-muted text-base sm:text-lg leading-relaxed">
                Explore our portfolio of completed and ongoing commercial complexes, luxury residential developments, heavy civil works, and interior fit-outs across Indonesia.
            </p>
        </div>

        <!-- 2. Interactive Multi-Dimensional Filter Bar -->
        <div class="glass-card rounded-[2rem] p-6 sm:p-8 mb-12 sm:mb-16 shadow-ambient border border-black/5">
            <!-- Category Quick-Filter Pills -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-muted uppercase tracking-[0.2em] mb-3">Filter by Sector</p>
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <!-- All Categories Pill -->
                    <a href="{{ route('public.projects.index', array_filter(request()->except(['category', 'page']))) }}"
                        class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-all duration-200 {{ !request('category') ? 'bg-primary text-white shadow-sm' : 'bg-black/5 text-muted hover:text-primary hover:bg-black/10' }}">
                        All Categories
                    </a>

                    <!-- Category Loop Pills -->
                    @foreach ($categories as $category)
                        <a href="{{ route('public.projects.index', array_merge(request()->except('page'), ['category' => $category->id])) }}"
                            class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-all duration-200 {{ request('category') == $category->id ? 'bg-primary text-white shadow-sm' : 'bg-black/5 text-muted hover:text-primary hover:bg-black/10' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Secondary Dropdown Filters -->
            <form action="{{ route('public.projects.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end pt-5 border-t border-black/5">
                
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <!-- Status Filter Dropdown -->
                <div class="lg:col-span-5">
                    <label for="status" class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-2">Project Status</label>
                    <div class="relative">
                        <select name="status" id="status"
                            class="w-full bg-white/80 backdrop-blur-md border border-black/10 text-primary text-xs font-semibold rounded-xl focus:ring-secondary focus:border-secondary p-3 appearance-none shadow-sm cursor-pointer">
                            <option value="">All Project Statuses</option>
                            <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>Ongoing Projects</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed Projects</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-muted text-lg">expand_more</span>
                    </div>
                </div>

                <!-- Province/Location Dropdown -->
                <div class="lg:col-span-5">
                    <label for="province" class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-2">Location / Province</label>
                    <div class="relative">
                        <select name="province" id="province"
                            class="w-full bg-white/80 backdrop-blur-md border border-black/10 text-primary text-xs font-semibold rounded-xl focus:ring-secondary focus:border-secondary p-3 appearance-none shadow-sm cursor-pointer">
                            <option value="">All Provinces &amp; Cities</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-muted text-lg">expand_more</span>
                    </div>
                </div>

                <!-- Action Button Cluster -->
                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit"
                        class="w-full bg-primary hover:bg-secondary text-white p-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors duration-200 flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">filter_list</span>
                        <span>Filter</span>
                    </button>

                    @if (request()->hasAny(['category', 'status', 'province']))
                        <a href="{{ route('public.projects.index') }}"
                            class="p-3 bg-black/5 hover:bg-black/10 text-muted hover:text-primary rounded-xl font-bold text-xs uppercase transition-colors flex items-center justify-center shrink-0"
                            title="Clear All Filters">
                            <span class="material-symbols-outlined text-base">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Asymmetric Architectural Project Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($projects as $index => $project)
                @php
                    $imagePath = str_starts_with($project->image, 'http')
                        ? $project->image
                        : (file_exists(public_path('assets/' . $project->image))
                            ? asset('assets/' . $project->image)
                            : (str_starts_with($project->image, 'projects/')
                                ? asset('storage/' . $project->image)
                                : asset('storage/projects/' . $project->image)));
                @endphp

                <a href="{{ route('public.projects.show', $project->slug) }}"
                    class="group relative overflow-hidden rounded-[2.5rem] glass-card h-[440px] sm:h-[480px] block border border-black/5 shadow-ambient hover:shadow-2xl transition-all duration-500">
                    
                    <!-- High-Resolution Project Photo -->
                    <img src="{{ $imagePath }}" alt="{{ $project->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-haptic"
                        onerror="this.src='https://placehold.co/800x600/11161B/FAF9F6?text=Architectural+Case+Study'">

                    <!-- Multi-stop Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>

                    <!-- Top Meta Badges -->
                    <div class="absolute top-6 inset-x-6 flex items-center justify-between z-10">
                        <span class="px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest border border-white/20">
                            {{ $project->category->name ?? 'Architecture' }}
                        </span>
                        <span class="px-3.5 py-1.5 rounded-full backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest {{ $project->status === 'Completed' ? 'bg-emerald-500/80' : 'bg-secondary/80' }}">
                            {{ $project->status }}
                        </span>
                    </div>

                    <!-- Bottom Content Overlay -->
                    <div class="absolute bottom-6 inset-x-6 text-white z-10 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs text-white/80 font-medium flex items-center gap-1.5 mb-1.5">
                                <span class="material-symbols-outlined text-sm text-secondary">location_on</span>
                                <span>{{ $project->location ?? 'Indonesia' }}</span>
                            </p>
                            <h3 class="font-display text-xl sm:text-2xl font-bold tracking-tight line-clamp-2 group-hover:text-secondary transition-colors duration-200">
                                {{ $project->title }}
                            </h3>
                        </div>

                        <!-- Kinetic Arrow Button -->
                        <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 shrink-0">
                            <span class="material-symbols-outlined text-base">north_east</span>
                        </div>
                    </div>
                </a>
            @empty
                <!-- 4. Artistic Empty State -->
                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-20 text-center glass-card rounded-[2.5rem] border border-black/5 p-8 max-w-2xl mx-auto shadow-ambient">
                    <div class="w-16 h-16 bg-black/5 text-muted rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">domain_disabled</span>
                    </div>
                    <h3 class="font-display text-xl font-bold text-primary mb-2">No Projects Found Matching Your Criteria</h3>
                    <p class="text-muted text-sm max-w-md mx-auto mb-6">
                        We couldn't find any architectural records matching your active filters. Try broadening your filter selection.
                    </p>
                    <a href="{{ route('public.projects.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-secondary hover:bg-secondary-hover text-white px-6 py-3 text-xs font-bold uppercase tracking-wider shadow-sm transition-all">
                        <span>Clear All Filters</span>
                        <span class="material-symbols-outlined text-sm">refresh</span>
                    </a>
                </div>
            @endforelse
        </div>

        <!-- 5. Modern Architectural Pagination -->
        <div class="mt-16 flex justify-center">
            @if ($projects->hasPages())
                <div class="glass-card px-6 py-3 rounded-full shadow-ambient border border-black/5 inline-block">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Master Architectural Slate Footer -->
    @include('partials.public-footer')

</body>

</html>
