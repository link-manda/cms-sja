<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @php
        $seoTitle = $project->meta_title ?? $project->title . ' - Case Study | PT Sistem Jaya Abadi';
        $seoDescription = $project->meta_description ?? Str::limit($project->description, 150);
        $seoImage = str_starts_with($project->image, 'http')
            ? $project->image
            : (file_exists(public_path('assets/' . $project->image))
                ? asset('assets/' . $project->image)
                : (str_starts_with($project->image, 'projects/')
                    ? asset('storage/' . $project->image)
                    : asset('storage/projects/' . $project->image)));
    @endphp
    @include('partials.public-head', [
        'pageTitle' => $seoTitle,
        'seoTitle' => $seoTitle,
        'seoDescription' => $seoDescription,
        'seoUrl' => route('public.projects.show', $project->slug),
        'seoImage' => $seoImage,
        'seoType' => 'article',
    ])
</head>

<body
    class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen bg-background">

    <!-- Ambient Background Meshes -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <!-- Floating Island Navbar -->
    @include('partials.public-navbar')

    <!-- 1. Case Study Hero & Breadcrumb -->
    <section class="pt-36 sm:pt-44 md:pt-48 pb-12 sm:pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative w-full">
        <!-- Breadcrumbs -->
        <div class="mb-8 animate-reveal-up">
            <a href="{{ route('public.projects.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-all group px-4 py-2 rounded-full glass-card border border-black/5 shadow-ambient">
                <span class="material-symbols-outlined text-sm transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span>Back to Projects Portfolio</span>
            </a>
        </div>

        <!-- Case Study Header Banner -->
        <div class="max-w-4xl mb-12 animate-reveal-up" style="animation-delay: 80ms;">
            <div class="flex items-center gap-3 mb-4 flex-wrap">
                <span class="px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-widest border border-primary/10">
                    {{ $project->category->name ?? 'General Contractor' }}
                </span>
                <span class="px-3.5 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $project->status === 'Completed' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-secondary/10 text-secondary border border-secondary/20' }}">
                    {{ $project->status }}
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary leading-[1.1] tracking-tight mb-4">
                {{ $project->title }}
            </h1>

            <p class="text-sm sm:text-base text-muted flex items-center gap-2 font-medium">
                <span class="material-symbols-outlined text-secondary text-lg">location_on</span>
                <span>{{ $project->location ?? 'Indonesia' }}</span>
                @if ($project->year)
                    <span class="text-black/20">&bull;</span>
                    <span>Year {{ $project->year }}</span>
                @endif
            </p>
        </div>

        @php
            if (!isset($allMediaItems)) {
                $imagePath = str_starts_with($project->image, 'http')
                    ? $project->image
                    : (file_exists(public_path('assets/' . $project->image))
                        ? asset('assets/' . $project->image)
                        : (str_starts_with($project->image, 'projects/')
                            ? asset('storage/' . $project->image)
                            : asset('storage/projects/' . $project->image)));

                $allMediaItems = [
                    [
                        'type' => 'image',
                        'src' => $imagePath,
                        'embedUrl' => null,
                        'thumb' => $imagePath,
                    ],
                ];

                if ($project->images) {
                    foreach ($project->images as $img) {
                        $allMediaItems[] = [
                            'type' => $img->type ?? 'image',
                            'src' => ($img->type === 'video') ? $img->embed_url : asset('storage/' . $img->image_path),
                            'embedUrl' => ($img->type === 'video') ? $img->embed_url : null,
                            'thumb' => ($img->type === 'video') ? $img->thumbnail_url : asset('storage/' . $img->image_path),
                        ];
                    }
                }
            }
        @endphp

        <!-- 2. Master Gallery Carousel & Cinema Display -->
        <div class="mb-16 animate-reveal-up" style="animation-delay: 150ms;" id="project-carousel"
            data-media="{{ json_encode($allMediaItems, JSON_UNESCAPED_SLASHES) }}"
            data-images="{{ json_encode(array_column($allMediaItems, 'src'), JSON_UNESCAPED_SLASHES) }}">
            
            <!-- Main Cinema Frame -->
            <div class="relative rounded-[2.5rem] overflow-hidden glass-card shadow-2xl border border-black/5 w-full aspect-video md:aspect-[21/9] bg-neutral-950 group">
                
                <!-- Main Image Element -->
                <img id="main-carousel-img" src="{{ $allMediaItems[0]['src'] }}" alt="{{ $project->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 ease-haptic cursor-pointer"
                    onclick="openLightbox(currentImageIndex)" decoding="async">

                <!-- Main Video Element Container -->
                <div id="main-video-container" class="hidden absolute inset-0 w-full h-full flex items-center justify-center bg-neutral-950 p-4 sm:p-8">
                    <!-- Video Facade (Poster + Play Button) -->
                    <div id="video-facade" class="relative w-full h-full max-w-5xl aspect-video mx-auto flex items-center justify-center cursor-pointer group/facade overflow-hidden rounded-2xl shadow-2xl border border-white/10" onclick="playCurrentVideo()">
                        <img id="video-facade-thumb" src="" alt="Video thumbnail" class="w-full h-full object-cover group-hover/facade:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover/facade:bg-black/30 transition-colors flex items-center justify-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-600/90 hover:bg-red-600 text-white flex items-center justify-center shadow-2xl group-hover/facade:scale-110 transition-all">
                                <span class="material-symbols-outlined text-4xl sm:text-5xl ml-1">play_arrow</span>
                            </div>
                        </div>
                    </div>
                    <!-- Active Video Iframe Container -->
                    <div id="video-iframe-wrapper" class="hidden w-full h-full max-w-5xl aspect-video mx-auto rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-black">
                        <iframe id="main-video-iframe" class="w-full h-full" src="" title="Project Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Gradient Overlay -->
                <div id="cinema-gradient" class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60 pointer-events-none"></div>

                <!-- Status Pill on Cinema Frame -->
                <div class="absolute top-6 right-6 glass-card px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-white/40 shadow-sm z-20 pointer-events-none {{ $project->status === 'Completed' ? 'text-emerald-400' : 'text-secondary' }}">
                    {{ $project->status }}
                </div>

                <!-- Fullscreen Cinema / Expand Button Overlay -->
                <button type="button" onclick="openLightbox(currentImageIndex)" class="absolute bottom-6 left-6 glass-card px-3.5 py-1.5 rounded-full text-[10px] font-bold text-white tracking-wider uppercase border border-white/20 shadow-sm z-20 hover:bg-white/20 transition-all flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-sm">fullscreen</span>
                    <span id="expand-label">Click Image to Expand</span>
                </button>

                @if (count($allMediaItems) > 1)
                    <!-- Navigation Arrows -->
                    <button onclick="prevImage(event)" aria-label="Previous media"
                        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-md hover:bg-secondary transition-all opacity-0 group-hover:opacity-100 z-20 border border-white/20 hover:scale-110 shadow-lg cursor-pointer">
                        <span class="material-symbols-outlined text-3xl font-light">chevron_left</span>
                    </button>
                    <button onclick="nextImage(event)" aria-label="Next media"
                        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-md hover:bg-secondary transition-all opacity-0 group-hover:opacity-100 z-20 border border-white/20 hover:scale-110 shadow-lg cursor-pointer">
                        <span class="material-symbols-outlined text-3xl font-light">chevron_right</span>
                    </button>
                @endif
            </div>

            <!-- Carousel Thumbnails Strip -->
            @if (count($allMediaItems) > 1)
                <div class="flex gap-4 overflow-x-auto py-4 scrollbar-none mt-4 snap-x">
                    @foreach ($allMediaItems as $index => $item)
                        <button onclick="setImage({{ $index }})" id="thumb-{{ $index }}"
                            class="carousel-thumb snap-start relative flex-shrink-0 w-28 h-20 md:w-36 md:h-24 rounded-2xl overflow-hidden border-2 {{ $index === 0 ? 'border-secondary opacity-100 scale-100 shadow-md' : 'border-transparent opacity-50 hover:opacity-100 scale-95 hover:scale-100' }} transition-all duration-300 cursor-pointer">
                            <img src="{{ $item['thumb'] }}" class="w-full h-full object-cover"
                                alt="{{ $project->title }} preview {{ $index + 1 }}" loading="lazy" decoding="async">
                            @if ($item['type'] === 'video')
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-red-600 text-white rounded flex items-center gap-0.5 shadow pointer-events-none">
                                    <span class="material-symbols-outlined text-[11px]">play_arrow</span>
                                    VIDEO
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 3. Technical Specifications & Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">

            <!-- Left Column: Overview & Investment Details (8 Cols) -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Overview Card -->
                <div class="glass-card rounded-[2rem] p-8 sm:p-10 border border-black/5 shadow-ambient">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-secondary mb-2 inline-block">Scope &amp; Narrative</span>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-primary mb-6 border-b border-black/5 pb-4 tracking-tight">
                        Project Overview &amp; Execution
                    </h2>
                    <div class="text-muted text-sm sm:text-base leading-relaxed whitespace-pre-line space-y-4">
                        {{ $project->description }}
                    </div>
                </div>

                <!-- Investment Opportunity Card (If Enabled) -->
                @if ($project->is_for_sale_or_rent)
                    @php
                        $listingTitle = match($project->property_type) {
                            'Rent' => 'Rental Property Offering',
                            'Sale' => 'Property For Sale',
                            'Investment' => 'Commercial & Property Investment',
                            default => 'Commercial & Property Offering',
                        };
                        $listingBadge = match($project->property_type) {
                            'Rent' => 'For Rent',
                            'Sale' => 'For Sale',
                            'Investment' => 'Investment Opportunity',
                            default => !empty($project->property_type) ? 'For ' . $project->property_type : 'Property Offering',
                        };
                        $waBtnLabel = match($project->property_type) {
                            'Rent' => 'Inquire Rental Availability',
                            'Sale' => 'Inquire Property Purchase',
                            'Investment' => 'Inquire Investment Specifications',
                            default => 'Inquire About This Property',
                        };
                        $waText = match($project->property_type) {
                            'Rent' => 'Hello PT Sistem Jaya Abadi, I am interested in renting / boarding room for ' . $project->title,
                            'Sale' => 'Hello PT Sistem Jaya Abadi, I am interested in purchasing property ' . $project->title,
                            'Investment' => 'Hello PT Sistem Jaya Abadi, I am interested in the property investment opportunity for ' . $project->title,
                            default => 'Hello PT Sistem Jaya Abadi, I am interested in the property for ' . $project->title,
                        };
                    @endphp
                    <div class="glass-card rounded-[2rem] p-8 sm:p-10 border border-emerald-500/20 shadow-2xl relative overflow-hidden bg-gradient-to-br from-white via-white to-emerald-50/30">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-2xl">real_estate_agent</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-1 block">{{ $listingBadge }}</span>
                                <h3 class="font-display text-2xl font-bold text-primary tracking-tight">{{ $listingTitle }}</h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div class="bg-white/90 p-4 rounded-2xl border border-black/5 shadow-sm">
                                <span class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-1">Listing Type</span>
                                <span class="font-display font-bold text-primary text-lg">{{ $listingBadge }}</span>
                            </div>
                            <div class="bg-white/90 p-4 rounded-2xl border border-secondary/20 shadow-sm">
                                <span class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-1">Pricing Valuation</span>
                                @if ($project->price !== null)
                                    <span class="font-display font-bold text-secondary text-lg sm:text-xl">Rp {{ number_format((float) $project->price, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-display font-bold text-slate-500 text-base italic">Price on Request</span>
                                @endif
                            </div>
                        </div>

                        @if (strcasecmp($project->property_type ?? '', 'Rent') !== 0 && filled($project->roi_estimation))
                            <div class="bg-emerald-500/10 p-5 rounded-2xl border border-emerald-500/20 mb-6">
                                <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1.5">Projected ROI &amp; Feasibility</span>
                                <p class="text-xs sm:text-sm text-primary/80 leading-relaxed">{{ $project->roi_estimation }}</p>
                            </div>
                        @endif

                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text={{ urlencode($waText) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="w-full text-center bg-secondary hover:bg-secondary-hover text-white font-bold text-xs uppercase tracking-wider py-4 rounded-xl shadow-glow active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            <span>{{ $waBtnLabel }}</span>
                            <span class="material-symbols-outlined text-sm">north_east</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Right Column: Obsidian Technical Specs & CTA (5 Cols) -->
            <div class="lg:col-span-5 space-y-8">
                <!-- Obsidian Project Specs Card -->
                <div class="bg-primary text-white rounded-[2rem] p-8 sm:p-10 shadow-2xl border border-white/10 relative overflow-hidden">
                    <!-- Subtle Glow Circle -->
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-secondary/20 blur-3xl rounded-full pointer-events-none"></div>

                    <h3 class="font-display text-xl font-bold text-white border-b border-white/10 pb-4 mb-6 tracking-tight relative z-10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-2xl">tune</span>
                        <span>Technical Architectural Specs</span>
                    </h3>

                    <div class="space-y-4 relative z-10 text-xs sm:text-sm">
                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/50 uppercase tracking-wider font-semibold">Category</span>
                            <span class="font-semibold text-white">{{ $project->category->name ?? 'General' }}</span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-white/5">
                            <span class="text-white/50 uppercase tracking-wider font-semibold">Location</span>
                            <span class="font-semibold text-white">{{ $project->location ?? 'Indonesia' }}</span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-white/5 items-center">
                            <span class="text-white/50 uppercase tracking-wider font-semibold">Status</span>
                            <span class="font-semibold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $project->status === 'Completed' ? 'bg-emerald-400' : 'bg-secondary animate-pulse' }}"></span>
                                <span>{{ $project->status }}</span>
                            </span>
                        </div>

                        @if ($project->client)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <span class="text-white/50 uppercase tracking-wider font-semibold">Client</span>
                                <span class="font-semibold text-white">{{ $project->client }}</span>
                            </div>
                        @endif

                        @if ($project->year)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <span class="text-white/50 uppercase tracking-wider font-semibold">Handover Year</span>
                                <span class="font-semibold text-white">{{ $project->year }}</span>
                            </div>
                        @endif

                        @if ($project->building_area)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <span class="text-white/50 uppercase tracking-wider font-semibold">Building Area</span>
                                <span class="font-semibold text-secondary">{{ $project->building_area }}</span>
                            </div>
                        @endif

                        @if ($project->land_area)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <span class="text-white/50 uppercase tracking-wider font-semibold">Land Area</span>
                                <span class="font-semibold text-secondary">{{ $project->land_area }}</span>
                            </div>
                        @endif

                        @if ($project->execution_team)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <span class="text-white/50 uppercase tracking-wider font-semibold">Engineering Team</span>
                                <span class="font-semibold text-white">{{ $project->execution_team }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Direct Consultation CTA Card -->
                <div class="glass-card rounded-[2rem] p-8 border border-black/5 shadow-ambient text-center space-y-5">
                    <div class="w-14 h-14 bg-secondary/10 text-secondary rounded-2xl flex items-center justify-center mx-auto shadow-sm">
                        <span class="material-symbols-outlined text-3xl">support_agent</span>
                    </div>

                    <div>
                        <h4 class="font-display text-xl font-bold text-primary mb-2">Build Your Vision with Us</h4>
                        <p class="text-xs sm:text-sm text-muted leading-relaxed">
                            Schedule a technical discussion with our project leaders for custom structural planning, scheduling, and budgeting.
                        </p>
                    </div>

                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20consult%20about%20a%20project%20similar%20to%20{{ urlencode($project->title) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="w-full bg-secondary hover:bg-secondary-hover text-white py-4 rounded-xl font-bold text-xs uppercase tracking-wider shadow-glow active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span>Consult via WhatsApp</span>
                        <span class="material-symbols-outlined text-sm">north_east</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- 4. Related Projects Showcase -->
        @if ($relatedProjects->count() > 0)
            <div class="mt-24 pt-16 border-t border-black/5">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-secondary mb-2 block">Portfolio Insights</span>
                        <h2 class="font-display text-3xl font-extrabold text-primary tracking-tight">More Featured Projects</h2>
                    </div>
                    <a href="{{ route('public.projects.index') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary transition-colors">
                        <span>View All</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($relatedProjects as $relProject)
                        @php
                            $relImgPath = str_starts_with($relProject->image, 'http')
                                ? $relProject->image
                                : (file_exists(public_path('assets/' . $relProject->image))
                                    ? asset('assets/' . $relProject->image)
                                    : (str_starts_with($relProject->image, 'projects/')
                                        ? asset('storage/' . $relProject->image)
                                        : asset('storage/projects/' . $relProject->image)));
                        @endphp
                        <a href="{{ route('public.projects.show', $relProject->slug) }}"
                            class="group relative overflow-hidden rounded-[2rem] glass-card h-[380px] block border border-black/5 shadow-ambient hover:shadow-2xl transition-all duration-500">
                            <img src="{{ $relImgPath }}" alt="{{ $relProject->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-haptic">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>

                            <div class="absolute top-4 right-4 z-10">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest backdrop-blur-md text-white {{ $relProject->status === 'Completed' ? 'bg-emerald-500/80' : 'bg-secondary/80' }}">
                                    {{ $relProject->status }}
                                </span>
                            </div>

                            <div class="absolute bottom-5 inset-x-5 text-white z-10 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs text-white/80 font-medium flex items-center gap-1 mb-1">
                                        <span class="material-symbols-outlined text-xs text-secondary">location_on</span>
                                        <span>{{ $relProject->location }}</span>
                                    </p>
                                    <h4 class="font-display text-lg font-bold tracking-tight line-clamp-2 group-hover:text-secondary transition-colors">
                                        {{ $relProject->title }}
                                    </h4>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0 group-hover:bg-secondary transition-colors">
                                    <span class="material-symbols-outlined text-sm">north_east</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </section>

    <!-- Master Architectural Slate Footer -->
    @include('partials.public-footer')

    <!-- Custom Full-Screen Lightbox Modal -->
    <div id="gallery-lightbox"
        class="fixed inset-0 z-[100] bg-neutral-950/95 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4 sm:p-10"
        onclick="closeLightbox(event)">
        <!-- Close Button -->
        <button type="button"
            class="absolute top-6 right-6 sm:top-10 sm:right-10 text-white/70 hover:text-white transition-colors p-2.5 rounded-full hover:bg-white/10 z-[102] cursor-pointer"
            onclick="closeLightbox(event)" aria-label="Close fullscreen preview">
            <span class="material-symbols-outlined text-3xl font-light">close</span>
        </button>

        <!-- Media Container -->
        <div class="relative max-w-6xl w-full h-full flex items-center justify-center" onclick="event.stopPropagation()">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span id="lightbox-loader"
                    class="material-symbols-outlined text-white/50 text-4xl animate-spin hidden">progress_activity</span>
            </div>
            <!-- Fullscreen Image -->
            <img id="lightbox-image" src="" alt="Gallery Preview"
                class="hidden max-w-full max-h-full object-contain rounded-2xl shadow-2xl scale-95 opacity-0 transition-all duration-300 relative z-10">

            <!-- Fullscreen Video Iframe Container -->
            <div id="lightbox-video-container" class="hidden w-full max-w-5xl aspect-video rounded-2xl overflow-hidden shadow-2xl border border-white/10 relative z-10 bg-black">
                <iframe id="lightbox-video-iframe" class="w-full h-full" src="" title="Fullscreen Project Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Interactive Carousel & Lightbox Logic -->
    <script>
        let currentImageIndex = 0;
        let isTransitioning = false;
        const carouselEl = document.getElementById('project-carousel');
        let allMediaItems = [];

        if (carouselEl) {
            try {
                allMediaItems = JSON.parse(carouselEl.getAttribute('data-media') || '[]');
            } catch (e) {
                allMediaItems = [];
            }
            // Fallback to data-images if data-media is empty
            if (allMediaItems.length === 0) {
                const legacyImages = JSON.parse(carouselEl.getAttribute('data-images') || '[]');
                allMediaItems = legacyImages.map(url => ({ type: 'image', src: url, embedUrl: null, thumb: url }));
            }
        }

        function stopMainVideo() {
            const mainVideoIframe = document.getElementById('main-video-iframe');
            const videoIframeWrapper = document.getElementById('video-iframe-wrapper');
            const videoFacade = document.getElementById('video-facade');

            if (mainVideoIframe) mainVideoIframe.src = '';
            if (videoIframeWrapper) videoIframeWrapper.classList.add('hidden');
            if (videoFacade) videoFacade.classList.remove('hidden');
        }

        function playCurrentVideo() {
            const media = allMediaItems[currentImageIndex];
            if (!media || media.type !== 'video' || !media.embedUrl) return;

            const videoFacade = document.getElementById('video-facade');
            const videoIframeWrapper = document.getElementById('video-iframe-wrapper');
            const mainVideoIframe = document.getElementById('main-video-iframe');

            if (videoFacade) videoFacade.classList.add('hidden');
            if (videoIframeWrapper) videoIframeWrapper.classList.remove('hidden');

            const sep = media.embedUrl.includes('?') ? '&' : '?';
            if (mainVideoIframe) {
                mainVideoIframe.src = media.embedUrl + sep + 'autoplay=1';
            }
        }

        function setImage(index) {
            if (allMediaItems.length === 0 || isTransitioning || index === currentImageIndex) return;

            // Always stop any playing video before switching slides to prevent audio leak
            stopMainVideo();

            const media = allMediaItems[index];
            const mainImg = document.getElementById('main-carousel-img');
            const videoContainer = document.getElementById('main-video-container');
            const videoFacadeThumb = document.getElementById('video-facade-thumb');
            const cinemaGradient = document.getElementById('cinema-gradient');
            const expandLabel = document.getElementById('expand-label');

            isTransitioning = true;

            if (media.type === 'video') {
                currentImageIndex = index;
                if (mainImg) mainImg.classList.add('hidden');
                if (cinemaGradient) cinemaGradient.classList.add('hidden');
                if (videoContainer) videoContainer.classList.remove('hidden');
                if (videoFacadeThumb) videoFacadeThumb.src = media.thumb;
                if (expandLabel) expandLabel.textContent = 'Fullscreen Cinema';

                updateThumbnails();
                isTransitioning = false;
            } else {
                if (videoContainer) videoContainer.classList.add('hidden');
                if (cinemaGradient) cinemaGradient.classList.remove('hidden');
                if (mainImg) mainImg.classList.remove('hidden');

                const nextImage = new Image();
                nextImage.onload = () => {
                    currentImageIndex = index;
                    mainImg.style.opacity = '0.35';
                    mainImg.style.transform = 'scale(0.985)';

                    setTimeout(() => {
                        mainImg.src = nextImage.src;
                        mainImg.style.opacity = '1';
                        mainImg.style.transform = 'scale(1)';
                        if (expandLabel) expandLabel.textContent = 'Click Image to Expand';
                        updateThumbnails();
                        isTransitioning = false;
                    }, 180);
                };

                nextImage.onerror = () => {
                    isTransitioning = false;
                };

                nextImage.src = media.src;
            }
        }

        function updateThumbnails() {
            document.querySelectorAll('.carousel-thumb').forEach((thumb, i) => {
                if (i === currentImageIndex) {
                    thumb.classList.remove('border-transparent', 'opacity-50', 'scale-95');
                    thumb.classList.add('border-secondary', 'opacity-100', 'scale-100', 'shadow-md');

                    const container = thumb.parentElement;
                    const scrollLeft = thumb.offsetLeft - (container.clientWidth / 2) + (thumb.clientWidth / 2);
                    container.scrollTo({
                        left: scrollLeft,
                        behavior: 'smooth'
                    });
                } else {
                    thumb.classList.add('border-transparent', 'opacity-50', 'scale-95');
                    thumb.classList.remove('border-secondary', 'opacity-100', 'scale-100', 'shadow-md');
                }
            });
        }

        function nextImage(e) {
            if (e) e.stopPropagation();
            if (allMediaItems.length <= 1) return;
            let newIndex = currentImageIndex + 1;
            if (newIndex >= allMediaItems.length) newIndex = 0;
            setImage(newIndex);
        }

        function prevImage(e) {
            if (e) e.stopPropagation();
            if (allMediaItems.length <= 1) return;
            let newIndex = currentImageIndex - 1;
            if (newIndex < 0) newIndex = allMediaItems.length - 1;
            setImage(newIndex);
        }

        function openLightbox(target) {
            let index = currentImageIndex;
            if (typeof target === 'number' && target >= 0 && target < allMediaItems.length) {
                index = target;
            }

            // Stop carousel video if playing to avoid audio overlap
            stopMainVideo();

            const lightbox = document.getElementById('gallery-lightbox');
            const img = document.getElementById('lightbox-image');
            const videoContainer = document.getElementById('lightbox-video-container');
            const videoIframe = document.getElementById('lightbox-video-iframe');
            const loader = document.getElementById('lightbox-loader');

            lightbox.classList.remove('hidden');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightbox.classList.add('opacity-100');
            }, 10);

            const media = allMediaItems[index] || { type: 'image', src: target };

            if (media.type === 'video') {
                if (img) img.classList.add('hidden');
                if (loader) loader.classList.add('hidden');
                if (videoContainer) videoContainer.classList.remove('hidden');

                const sep = media.embedUrl.includes('?') ? '&' : '?';
                if (videoIframe) {
                    videoIframe.src = media.embedUrl + sep + 'autoplay=1';
                }
            } else {
                if (videoContainer) videoContainer.classList.add('hidden');
                if (videoIframe) videoIframe.src = '';
                if (img) {
                    img.classList.remove('hidden', 'scale-100', 'opacity-100');
                    img.classList.add('scale-95', 'opacity-0');
                }
                if (loader) loader.classList.remove('hidden');

                img.src = media.src;
                img.onload = () => {
                    if (loader) loader.classList.add('hidden');
                    img.classList.remove('scale-95', 'opacity-0');
                    img.classList.add('scale-100', 'opacity-100');
                };
            }

            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            const lightbox = document.getElementById('gallery-lightbox');
            const img = document.getElementById('lightbox-image');
            const videoContainer = document.getElementById('lightbox-video-container');
            const videoIframe = document.getElementById('lightbox-video-iframe');

            lightbox.classList.remove('opacity-100');
            lightbox.classList.add('opacity-0');

            if (img) {
                img.classList.remove('scale-100', 'opacity-100');
                img.classList.add('scale-95', 'opacity-0');
            }

            // Immediately cut off video audio on close
            if (videoIframe) videoIframe.src = '';
            if (videoContainer) videoContainer.classList.add('hidden');

            setTimeout(() => {
                lightbox.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const lightbox = document.getElementById('gallery-lightbox');
                if (lightbox && !lightbox.classList.contains('hidden')) {
                    closeLightbox();
                }
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'ArrowLeft') {
                prevImage();
            }
        });
    </script>
</body>

</html>
