<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor'),
    ])
</head>

<body
    class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen bg-background">

    <!-- Ambient Background Meshes -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <!-- Floating Island Navbar -->
    @include('partials.public-navbar')

    <!-- 1. Hero Section (Architectural Editorial Split) -->
    <section id="home" class="relative pt-36 sm:pt-44 md:pt-48 pb-20 md:pb-28 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            <!-- Left Column: Copy & Value Proposition -->
            <div class="lg:col-span-7 animate-reveal-up">
                <!-- Trust & Certification Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-black/5 shadow-ambient text-xs font-semibold text-primary uppercase tracking-widest mb-6 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>ISO 9001:2015 &amp; LPJK Certified General Contractor</span>
                </div>

                <!-- Editorial Headline -->
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary leading-[1.08] tracking-tight mb-6">
                    Mastering <span class="text-gradient">Architectural Precision</span>, Delivering Lasting Legacy.
                </h1>

                <!-- Lead Paragraph -->
                <p class="text-base sm:text-lg text-muted mb-8 max-w-2xl leading-relaxed">
                    We orchestrate end-to-end commercial, industrial, and bespoke residential developments with unwavering structural integrity, transparent milestones, and uncompromised craftsmanship.
                </p>

                <!-- Action Button Cluster -->
                <div class="flex flex-col sm:flex-row gap-4 mb-10 items-stretch sm:items-center">
                    <!-- Primary WhatsApp Consultation CTA -->
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20consult%20about%20a%20construction%20project."
                        target="_blank" rel="noopener noreferrer"
                        class="rounded-full bg-secondary hover:bg-secondary-hover text-white pl-7 pr-3 py-3.5 flex items-center justify-between sm:justify-start gap-4 font-bold text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-glow active:scale-[0.98] transition-all duration-300 group">
                        <span>Consult Your Project</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-base">north_east</span>
                        </span>
                    </a>

                    <!-- Secondary Price Calculator Trigger -->
                    <a href="{{ route('public.calculator.index') }}"
                        class="rounded-full glass-card hover:bg-white text-primary px-6 py-3.5 font-bold text-xs sm:text-sm uppercase tracking-wider border border-black/5 shadow-ambient hover:shadow-md active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2.5">
                        <span class="material-symbols-outlined text-secondary text-lg">calculate</span>
                        <span>Price Calculator</span>
                    </a>
                </div>

                <!-- Credibility & Experience Counter Bar -->
                <div class="grid grid-cols-3 gap-4 sm:gap-8 pt-8 mt-10 border-t border-black/5">
                    <div>
                        <p class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-primary tracking-tight">15<span class="text-secondary">+</span></p>
                        <p class="text-xs sm:text-sm font-medium text-muted mt-1">Years of Mastery</p>
                    </div>
                    <div>
                        <p class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-primary tracking-tight">500<span class="text-secondary">+</span></p>
                        <p class="text-xs sm:text-sm font-medium text-muted mt-1">Completed Projects</p>
                    </div>
                    <div>
                        <p class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-primary tracking-tight">100<span class="text-secondary">%</span></p>
                        <p class="text-xs sm:text-sm font-medium text-muted mt-1">On-Time SLA Guarantee</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Layered Architectural Visual Showcase -->
            <div class="lg:col-span-5 relative">
                <!-- Outer Glass Showcase Frame -->
                <div class="relative rounded-[2.5rem] overflow-hidden glass-card p-2.5 shadow-2xl border border-white/80 group">
                    <div class="aspect-[4/5] w-full rounded-[2rem] overflow-hidden relative bg-primary">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=1200&auto=format&fit=crop"
                            alt="Modern Architectural Engineering"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-haptic">
                        
                        <!-- Subtle Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/20 to-transparent"></div>

                        <!-- Inner Floating Tag -->
                        <div class="absolute bottom-6 left-6 right-6 text-white">
                            <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest mb-2 border border-white/20">
                                Featured Engineering
                            </span>
                            <p class="font-display text-xl font-bold tracking-tight">Precision Structural Mastery</p>
                            <p class="text-xs text-white/80 mt-1">Commercial &bull; Industrial &bull; Residential</p>
                        </div>
                    </div>
                </div>

                <!-- Floating Experience Badge (Top-Right) -->
                <div class="absolute -top-4 -right-4 glass-card rounded-2xl p-4 shadow-ambient border border-white/80 flex items-center gap-3 hidden sm:flex animate-float">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-xl">verified</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary">Certified LPJK K3</p>
                        <p class="text-[10px] text-muted">Safety &amp; Quality Assured</p>
                    </div>
                </div>

                <!-- Floating Showcase Card (Bottom-Left) -->
                <div class="absolute -bottom-6 -left-6 glass-card rounded-2xl p-4 sm:p-5 shadow-2xl border border-white/80 flex items-center gap-4 max-w-xs hidden sm:flex">
                    <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center font-bold shrink-0">
                        <span class="material-symbols-outlined text-2xl">architecture</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary">Structural Integrity</p>
                        <p class="text-[11px] text-muted leading-tight mt-0.5">Laboratory tested concrete &amp; seismic-grade steel frameworks.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Why Choose Us / Value Proposition (4-Column Bento Grid) -->
    <section id="about" class="py-24 sm:py-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative">
        <div class="text-center mb-16 sm:mb-20">
            <span class="text-xs font-bold uppercase tracking-[0.25em] text-secondary mb-3 inline-block">
                Why PT Sistem Jaya Abadi
            </span>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-primary tracking-tight">
                Engineering Excellence with Integrity
            </h2>
            <p class="text-muted max-w-2xl mx-auto text-base sm:text-lg mt-4 leading-relaxed">
                We combine rigorous engineering protocols, high-grade certified materials, and transparent milestones to deliver structures that stand the test of time.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <!-- Pillar 1 -->
            <div class="glass-card p-8 rounded-3xl border border-black/5 hover:-translate-y-1.5 transition-all duration-300 ease-haptic group shadow-ambient hover:shadow-xl">
                <div class="w-14 h-14 bg-primary text-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-secondary transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">engineering</span>
                </div>
                <h3 class="font-display text-lg font-bold text-primary mb-2.5">Licensed Engineering Mastery</h3>
                <p class="text-muted text-xs sm:text-sm leading-relaxed">
                    Staffed by certified structural, civil, and MEP engineers with deep technical acumen and LPJK K3 compliance.
                </p>
            </div>

            <!-- Pillar 2 -->
            <div class="glass-card p-8 rounded-3xl border border-black/5 hover:-translate-y-1.5 transition-all duration-300 ease-haptic group shadow-ambient hover:shadow-xl">
                <div class="w-14 h-14 bg-primary text-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-secondary transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <h3 class="font-display text-lg font-bold text-primary mb-2.5">Architectural Grade Materials</h3>
                <p class="text-muted text-xs sm:text-sm leading-relaxed">
                    Strict adherence to SNI standards with laboratory-tested reinforced concrete, premium steel, and durable finishes.
                </p>
            </div>

            <!-- Pillar 3 -->
            <div class="glass-card p-8 rounded-3xl border border-black/5 hover:-translate-y-1.5 transition-all duration-300 ease-haptic group shadow-ambient hover:shadow-xl">
                <div class="w-14 h-14 bg-primary text-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-secondary transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">schedule</span>
                </div>
                <h3 class="font-display text-lg font-bold text-primary mb-2.5">Strict Milestone SLA</h3>
                <p class="text-muted text-xs sm:text-sm leading-relaxed">
                    Digital timeline tracking and transparent phase reporting to guarantee punctual handover without compromising quality.
                </p>
            </div>

            <!-- Pillar 4 -->
            <div class="glass-card p-8 rounded-3xl border border-black/5 hover:-translate-y-1.5 transition-all duration-300 ease-haptic group shadow-ambient hover:shadow-xl">
                <div class="w-14 h-14 bg-primary text-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-secondary transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">handshake</span>
                </div>
                <h3 class="font-display text-lg font-bold text-primary mb-2.5">Long-Term Structural Warranty</h3>
                <p class="text-muted text-xs sm:text-sm leading-relaxed">
                    Comprehensive post-handover warranty and dedicated structural maintenance support to safeguard your property value.
                </p>
            </div>
        </div>
    </section>

    <!-- 3. Integrated Construction Services Grid (6 Bespoke Cards) -->
    <section id="services" class="py-24 sm:py-32 bg-white border-y border-black/5 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 sm:mb-20">
                <span class="text-xs font-bold uppercase tracking-[0.25em] text-secondary mb-3 inline-block">
                    Integrated Capabilities
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-primary tracking-tight">
                    Full-Spectrum Construction Solutions
                </h2>
                <p class="text-muted max-w-2xl mx-auto text-base sm:text-lg mt-4 leading-relaxed">
                    From conceptual blueprints and heavy foundation engineering to luxury interior fit-outs and infrastructure development.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">apartment</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Contractor</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">General Building Construction</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            Turnkey construction for multi-story offices, commercial retail hubs, industrial warehouses, and luxury residential estates.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20General%20Building%20Construction."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">construction</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Renovation</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">Building Renovation &amp; Retrofitting</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            Structural reinforcement, facade modernization, adaptive reuse, and total spatial upgrades for existing buildings.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20Building%20Renovation."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">architecture</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Integrated</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">Design &amp; Build Architecture</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            Unified architectural planning, 3D realistic rendering, structural calculation, and seamless execution under one roof.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20Design%20and%20Build."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">foundation</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Structural</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">Heavy Structural Engineering</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            Deep foundation bore-piling, heavy reinforced concrete frameworks, structural steel fabrication, and retaining walls.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20Heavy%20Structural%20Works."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Service 5 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">chair</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Interior</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">Interior Architecture &amp; Fit-Out</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            High-end interior finishing, custom joinery, acoustic ceilings, MEP lighting integration, and corporate ergonomics.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20Interior%20Fit%20Out."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Service 6 -->
                <div class="bg-background p-8 rounded-3xl border border-black/5 hover:border-secondary/40 hover:shadow-xl transition-all duration-300 ease-haptic group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-2xl">grid_view</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded-full">Civil Works</span>
                        <h3 class="font-display text-xl font-bold text-primary mt-3 mb-2.5">Civil Infrastructure &amp; Utilities</h3>
                        <p class="text-muted text-xs sm:text-sm leading-relaxed">
                            Site grading, regional drainage masterplans, access road paving, and underground electrical &amp; plumbing utilities.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-black/5">
                        <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20Civil%20Infrastructure."
                            target="_blank" rel="noopener noreferrer"
                            class="text-xs font-bold uppercase tracking-wider text-primary hover:text-secondary flex items-center gap-1.5 transition-colors">
                            <span>Consult This Service</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Curated Project Portfolio Showcase (Asymmetric Architectural Grid) -->
    <section id="projects" class="py-24 sm:py-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 sm:mb-20 gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.25em] text-secondary mb-3 inline-block">
                    Curated Portfolio
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-primary tracking-tight">
                    Proven Results, Precision Craft
                </h2>
                <p class="text-muted max-w-2xl text-base sm:text-lg mt-3 leading-relaxed">
                    Explore our recent completed and ongoing construction milestones across Indonesia.
                </p>
            </div>

            <!-- View Complete Catalogue Button -->
            <a href="{{ route('public.projects.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full glass-card hover:bg-white text-primary font-bold text-xs uppercase tracking-wider border border-black/5 shadow-ambient hover:shadow-md transition-all self-start md:self-auto">
                <span>All Projects Catalogue</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        @if (isset($projects) && $projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($projects as $index => $project)
                    @php
                        $imagePath = str_starts_with($project->image, 'http')
                            ? $project->image
                            : (file_exists(public_path('assets/' . $project->image))
                                ? asset('assets/' . $project->image)
                                : (str_starts_with($project->image, 'projects/')
                                    ? asset('storage/' . $project->image)
                                    : asset('storage/projects/' . $project->image)));

                        // Asymmetric editorial layout: card index 0 spans 2 columns on desktop if multiple projects exist
                        $colSpan = ($index === 0 && $projects->count() >= 3) ? 'lg:col-span-2' : '';
                    @endphp

                    <a href="{{ route('public.projects.show', $project->slug) }}"
                        class="group relative overflow-hidden rounded-[2.5rem] glass-card h-[420px] sm:h-[480px] block border border-black/5 shadow-ambient hover:shadow-2xl transition-all duration-500 {{ $colSpan }}">
                        
                        <!-- Background Image with smooth hover scale -->
                        <img src="{{ $imagePath }}" alt="{{ $project->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-haptic">

                        <!-- Multi-stop Gradient Shade -->
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>

                        <!-- Top Status & Category Badges -->
                        <div class="absolute top-6 inset-x-6 flex items-center justify-between z-10">
                            <span class="px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest border border-white/20">
                                {{ $project->category->name ?? 'Architecture' }}
                            </span>
                            <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/80 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest">
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
                                <h3 class="font-display text-xl sm:text-2xl font-bold tracking-tight group-hover:text-secondary transition-colors duration-200">
                                    {{ $project->title }}
                                </h3>
                            </div>

                            <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 shrink-0">
                                <span class="material-symbols-outlined text-base">north_east</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- Graceful Empty State -->
            <div class="py-20 text-center glass-card rounded-[2.5rem] border border-black/5 p-8 max-w-2xl mx-auto shadow-ambient">
                <div class="w-16 h-16 bg-black/5 text-muted rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">domain_disabled</span>
                </div>
                <h3 class="font-display text-xl font-bold text-primary mb-2">Projects Catalogue Updating</h3>
                <p class="text-muted text-sm max-w-md mx-auto mb-6">
                    Our team is currently curating new architectural case studies. You can consult directly for portfolio credentials.
                </p>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-secondary hover:bg-secondary-hover text-white px-6 py-3 text-xs font-bold uppercase tracking-wider shadow-sm transition-all">
                    <span>Contact For Portfolio Deck</span>
                    <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>
        @endif
    </section>

    <!-- 5. Interactive Price Calculator Teaser Card (High-Conversion Bento) -->
    <section class="py-12 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative z-10">
        <div class="rounded-[2.5rem] bg-gradient-to-br from-primary via-[#161D24] to-primary p-8 sm:p-12 lg:p-16 text-white relative overflow-hidden shadow-2xl border border-white/10">
            <!-- Ambient Glow Mesh in Card -->
            <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-secondary/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -top-20 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
                <!-- Left: Headline & Feature Highlights -->
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-secondary border border-white/10 text-[10px] font-bold uppercase tracking-widest mb-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-sm">calculate</span>
                        <span>Transparent Building Estimation</span>
                    </span>

                    <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-4 text-white">
                        Simulate Your Construction Investment in Real-Time
                    </h2>

                    <p class="text-white/70 text-sm sm:text-base leading-relaxed mb-8 max-w-xl">
                        Plan with confidence. Select your building specifications, review estimated cost ranges, explore 2D/3D visual blueprints, and understand each construction phase with complete transparency.
                    </p>

                    <!-- Feature Check-Pill List -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
                        <div class="flex items-center gap-2.5 text-xs font-semibold text-white/90">
                            <span class="material-symbols-outlined text-emerald-400 text-base">check_circle</span>
                            <span>Transparent Price Ranges</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs font-semibold text-white/90">
                            <span class="material-symbols-outlined text-emerald-400 text-base">check_circle</span>
                            <span>2D &amp; 3D Architectural Visuals</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs font-semibold text-white/90">
                            <span class="material-symbols-outlined text-emerald-400 text-base">check_circle</span>
                            <span>Step-by-Step Construction Stages</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs font-semibold text-white/90">
                            <span class="material-symbols-outlined text-emerald-400 text-base">check_circle</span>
                            <span>No Hidden Costs or Surprises</span>
                        </div>
                    </div>

                    <!-- Direct Launch Action Button -->
                    <a href="{{ route('public.calculator.index') }}"
                        class="inline-flex items-center gap-3 rounded-full bg-secondary hover:bg-secondary-hover text-white pl-7 pr-3 py-3.5 font-bold text-xs sm:text-sm uppercase tracking-wider shadow-glow hover:scale-105 active:scale-[0.98] transition-all duration-300 group">
                        <span>Launch Price Calculator</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-300">
                            <span class="material-symbols-outlined text-base">north_east</span>
                        </span>
                    </a>
                </div>

                <!-- Right: High-End Interactive Mockup Card -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-white/5 border border-white/10 p-6 backdrop-blur-xl shadow-2xl">
                        <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full bg-secondary"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-white">Interactive Calculator</span>
                            </div>
                            <span class="text-[10px] text-white/60 font-mono">LIVE PREVIEW</span>
                        </div>

                        <!-- Mockup Select Box -->
                        <div class="space-y-3 mb-6">
                            <div class="bg-white/10 rounded-xl p-3.5 flex items-center justify-between border border-white/10">
                                <div>
                                    <p class="text-[10px] text-white/60 uppercase">Selected Model</p>
                                    <p class="text-xs font-bold text-white">Luxury 2-Story Residential</p>
                                </div>
                                <span class="material-symbols-outlined text-secondary text-base">home</span>
                            </div>

                            <div class="bg-white/10 rounded-xl p-3.5 flex items-center justify-between border border-white/10">
                                <div>
                                    <p class="text-[10px] text-white/60 uppercase">Estimated Budget Range</p>
                                    <p class="text-xs font-bold text-emerald-400">Rp 5.500.000 - Rp 7.500.000 / m²</p>
                                </div>
                                <span class="material-symbols-outlined text-emerald-400 text-base">payments</span>
                            </div>
                        </div>

                        <!-- CTA to Test -->
                        <a href="{{ route('public.calculator.index') }}"
                            class="w-full rounded-xl bg-white text-primary py-3 px-4 flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider hover:bg-white/90 transition-colors">
                            <span>Open Full Estimator</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Grand Architectural Final CTA Section -->
    <section class="py-24 sm:py-32 bg-primary relative overflow-hidden text-center z-10">
        <!-- Ambient Radial Mesh in CTA -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-full opacity-15 pointer-events-none">
            <div class="w-full h-full rounded-full bg-secondary blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <span class="text-xs font-bold uppercase tracking-[0.25em] text-secondary mb-4 inline-block">
                Start Your Construction Journey
            </span>

            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight mb-6">
                Ready to Build Your Architectural Vision with Industry Leaders?
            </h2>

            <p class="text-white/70 text-base sm:text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
                Schedule a consultation with our senior project managers to discuss structural plans, budgeting, and timelines today.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <!-- WhatsApp CTA -->
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20discuss%20a%20new%20construction%20project."
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-secondary hover:bg-secondary-hover text-white pl-7 pr-3 py-3.5 flex items-center gap-3 font-bold text-xs sm:text-sm uppercase tracking-wider shadow-glow hover:scale-105 active:scale-[0.98] transition-all duration-300 group">
                    <span>Contact Us on WhatsApp</span>
                    <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-300">
                        <span class="material-symbols-outlined text-base">call</span>
                    </span>
                </a>

                <!-- Request Quote CTA -->
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello,%20I%20would%20like%20to%20request%20a%20quotation%20(RAB)%20for%20a%20project."
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-white/10 hover:bg-white text-white hover:text-primary px-7 py-3.5 font-bold text-xs sm:text-sm uppercase tracking-wider border border-white/20 shadow-sm transition-all duration-200">
                    <span>Request Quotation (RAB)</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Master Architectural Slate Footer -->
    @include('partials.public-footer')

</body>

</html>
