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
            $imagePath = str_starts_with($project->image, 'http')
                ? $project->image
                : (file_exists(public_path('assets/' . $project->image))
                    ? asset('assets/' . $project->image)
                    : (str_starts_with($project->image, 'projects/')
                        ? asset('storage/' . $project->image)
                        : asset('storage/projects/' . $project->image)));

            $allImages = [$imagePath];
            if ($project->images) {
                foreach ($project->images as $img) {
                    $allImages[] = asset('storage/' . $img->image_path);
                }
            }
        @endphp

        <!-- 2. Master Gallery Carousel & Cinema Display -->
        <div class="mb-16 animate-reveal-up" style="animation-delay: 150ms;" id="project-carousel"
            data-images="{{ json_encode($allImages) }}">
            
            <!-- Main Cinema Frame -->
            <div class="relative rounded-[2.5rem] overflow-hidden glass-card shadow-2xl border border-black/5 w-full aspect-video md:aspect-[21/9] bg-primary group">
                <img id="main-carousel-img" src="{{ $allImages[0] }}" alt="{{ $project->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 ease-haptic cursor-pointer"
                    onclick="openLightbox(this.src)" decoding="async">

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-primary/60 via-transparent to-transparent opacity-60 pointer-events-none"></div>

                <!-- Status Pill on Cinema Frame -->
                <div class="absolute top-6 right-6 glass-card px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-white/40 shadow-sm z-10 pointer-events-none {{ $project->status === 'Completed' ? 'text-emerald-400' : 'text-secondary' }}">
                    {{ $project->status }}
                </div>

                <!-- Click-to-Zoom Helper Pill -->
                <div class="absolute bottom-6 left-6 glass-card px-3.5 py-1.5 rounded-full text-[10px] font-bold text-white tracking-wider uppercase border border-white/20 shadow-sm z-10 pointer-events-none hidden sm:flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">fullscreen</span>
                    <span>Click Image to Expand</span>
                </div>

                @if (count($allImages) > 1)
                    <!-- Navigation Arrows -->
                    <button onclick="prevImage(event)" aria-label="Previous image"
                        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md hover:bg-secondary transition-all opacity-0 group-hover:opacity-100 z-10 border border-white/20 hover:scale-110 shadow-lg">
                        <span class="material-symbols-outlined text-3xl font-light">chevron_left</span>
                    </button>
                    <button onclick="nextImage(event)" aria-label="Next image"
                        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md hover:bg-secondary transition-all opacity-0 group-hover:opacity-100 z-10 border border-white/20 hover:scale-110 shadow-lg">
                        <span class="material-symbols-outlined text-3xl font-light">chevron_right</span>
                    </button>
                @endif
            </div>

            <!-- Carousel Thumbnails Strip -->
            @if (count($allImages) > 1)
                <div class="flex gap-4 overflow-x-auto py-4 scrollbar-none mt-4 snap-x">
                    @foreach ($allImages as $index => $img)
                        <button onclick="setImage({{ $index }})" id="thumb-{{ $index }}"
                            class="carousel-thumb snap-start relative flex-shrink-0 w-28 h-20 md:w-36 md:h-24 rounded-2xl overflow-hidden border-2 {{ $index === 0 ? 'border-secondary opacity-100 scale-100 shadow-md' : 'border-transparent opacity-50 hover:opacity-100 scale-95 hover:scale-100' }} transition-all duration-300">
                            <img src="{{ $img }}" class="w-full h-full object-cover"
                                alt="{{ $project->title }} preview {{ $index + 1 }}" loading="lazy" decoding="async">
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
                    <div class="glass-card rounded-[2rem] p-8 sm:p-10 border border-emerald-500/20 shadow-2xl relative overflow-hidden bg-gradient-to-br from-white via-white to-emerald-50/30">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                <span class="material-symbols-outlined text-2xl">real_estate_agent</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-1 block">Property Investment</span>
                                <h3 class="font-display text-2xl font-bold text-primary tracking-tight">Commercial &amp; Property Offering</h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div class="bg-white/90 p-4 rounded-2xl border border-black/5 shadow-sm">
                                <span class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-1">Listing Type</span>
                                <span class="font-display font-bold text-primary text-lg">For {{ $project->property_type }}</span>
                            </div>
                            <div class="bg-white/90 p-4 rounded-2xl border border-secondary/20 shadow-sm">
                                <span class="block text-[10px] font-bold text-muted uppercase tracking-wider mb-1">Pricing Valuation</span>
                                <span class="font-display font-bold text-secondary text-lg sm:text-xl">Rp {{ number_format($project->price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if ($project->roi_estimation)
                            <div class="bg-emerald-500/10 p-5 rounded-2xl border border-emerald-500/20 mb-6">
                                <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1.5">Projected ROI &amp; Feasibility</span>
                                <p class="text-xs sm:text-sm text-primary/80 leading-relaxed">{{ $project->roi_estimation }}</p>
                            </div>
                        @endif

                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20am%20interested%20in%20the%20property%20investment%20for%20{{ urlencode($project->title) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="w-full text-center bg-secondary hover:bg-secondary-hover text-white font-bold text-xs uppercase tracking-wider py-4 rounded-xl shadow-glow active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            <span>Inquire Investment Specifications</span>
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
        class="fixed inset-0 z-[100] bg-primary/95 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4 sm:p-10"
        onclick="closeLightbox(event)">
        <!-- Close Button -->
        <button type="button"
            class="absolute top-6 right-6 sm:top-10 sm:right-10 text-white/60 hover:text-white transition-colors p-2.5 rounded-full hover:bg-white/10 z-[101]"
            onclick="closeLightbox(event)" aria-label="Close fullscreen preview">
            <span class="material-symbols-outlined text-3xl font-light">close</span>
        </button>

        <!-- Image Container -->
        <div class="relative max-w-6xl w-full h-full flex items-center justify-center">
            <div class="absolute inset-0 flex items-center justify-center">
                <span id="lightbox-loader"
                    class="material-symbols-outlined text-white/50 text-4xl animate-spin hidden">progress_activity</span>
            </div>
            <img id="lightbox-image" src="" alt="Gallery Preview"
                class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl scale-95 opacity-0 transition-all duration-300 relative z-10">
        </div>
    </div>

    <!-- Interactive Carousel & Lightbox Logic -->
    <script>
        let currentImageIndex = 0;
        let isTransitioning = false;
        const carouselEl = document.getElementById('project-carousel');
        let galleryImages = [];
        if (carouselEl) {
            galleryImages = JSON.parse(carouselEl.getAttribute('data-images') || '[]');
        }

        function setImage(index) {
            if (galleryImages.length === 0 || isTransitioning || index === currentImageIndex) return;

            const mainImg = document.getElementById('main-carousel-img');
            const nextImage = new Image();
            isTransitioning = true;

            nextImage.onload = () => {
                currentImageIndex = index;
                mainImg.style.opacity = '0.35';
                mainImg.style.transform = 'scale(0.985)';

                setTimeout(() => {
                    mainImg.src = nextImage.src;
                    mainImg.style.opacity = '1';
                    mainImg.style.transform = 'scale(1)';
                    updateThumbnails();
                    isTransitioning = false;
                }, 180);
            };

            nextImage.onerror = () => {
                isTransitioning = false;
            };

            nextImage.src = galleryImages[index];
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
            if (galleryImages.length <= 1) return;
            let newIndex = currentImageIndex + 1;
            if (newIndex >= galleryImages.length) newIndex = 0;
            setImage(newIndex);
        }

        function prevImage(e) {
            if (e) e.stopPropagation();
            if (galleryImages.length <= 1) return;
            let newIndex = currentImageIndex - 1;
            if (newIndex < 0) newIndex = galleryImages.length - 1;
            setImage(newIndex);
        }

        function openLightbox(imgSrc) {
            const lightbox = document.getElementById('gallery-lightbox');
            const img = document.getElementById('lightbox-image');
            const loader = document.getElementById('lightbox-loader');

            lightbox.classList.remove('hidden');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightbox.classList.add('opacity-100');
            }, 10);

            img.classList.remove('scale-100', 'opacity-100');
            img.classList.add('scale-95', 'opacity-0');
            loader.classList.remove('hidden');

            img.src = imgSrc;

            img.onload = () => {
                loader.classList.add('hidden');
                img.classList.remove('scale-95', 'opacity-0');
                img.classList.add('scale-100', 'opacity-100');
            };

            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            const lightbox = document.getElementById('gallery-lightbox');
            const img = document.getElementById('lightbox-image');

            lightbox.classList.remove('opacity-100');
            lightbox.classList.add('opacity-0');

            img.classList.remove('scale-100', 'opacity-100');
            img.classList.add('scale-95', 'opacity-0');

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
