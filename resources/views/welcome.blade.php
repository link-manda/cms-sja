<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor') }}</title>
    @include('partials.public-seo')
    <link rel="icon" type="image/png" href="/assets/logo.png" />
    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'PT Sistem Jaya Abadi',
            'url' => url('/'),
            'logo' => asset('assets/logo.png'),
            'description' => setting('site_description', 'Professional contractors for premium, on-time construction projects.'),
            'email' => setting('contact_email', ''),
            'address' => setting('company_address', ''),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: "#F8F9FA",
                        surface: "#FFFFFF",
                        primary: "#141B23",
                        "primary-light": "#2A3441",
                        secondary: "#DB5916",
                        "secondary-hover": "#c24e13",
                        success: "#055305",
                        muted: "#7C7C89",
                        "glass-bg": "rgba(255, 255, 255, 0.7)",
                        "glass-border": "rgba(255, 255, 255, 0.4)",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        glass: "0 8px 32px 0 rgba(20, 27, 35, 0.05)",
                        glow: "0 0 20px rgba(219, 89, 22, 0.3)",
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'reveal-up': 'revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            },
                        },
                        revealUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(30px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FA;
        }

        /* Subtle ambient glows */
        .ambient-glow-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(219, 89, 22, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -10%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(5, 83, 5, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(20, 27, 35, 0.05);
        }

        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #055305, #128B12);
        }
    </style>
</head>

<body
    class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen">

    <!-- Ambient Backgrounds -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- TopNavBar (Glassmorphism) -->
    <header class="fixed top-0 inset-x-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                class="glass-panel rounded-2xl px-6 py-4 flex justify-between items-center transition-all duration-300">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
                    </div>
                    <span class="text-xl font-bold text-primary tracking-tight">Sistem Jaya Abadi</span>
                </a>

                <nav class="hidden md:flex space-x-8 items-center">
                    <a class="nav-link-desktop text-sm font-semibold text-primary border-b-2 border-secondary pb-1"
                        href="#home">Home</a>
                    <a class="nav-link-desktop text-sm font-medium text-muted hover:text-primary transition-colors border-b-2 border-transparent pb-1"
                        href="#about">About Us</a>
                    <a class="nav-link-desktop text-sm font-medium text-muted hover:text-primary transition-colors border-b-2 border-transparent pb-1"
                        href="#services">Services</a>
                    <a class="nav-link-desktop text-sm font-medium text-muted hover:text-primary transition-colors border-b-2 border-transparent pb-1"
                        href="#projects">Projects</a>
                </nav>

                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-sm font-medium text-primary hover:text-secondary transition flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">dashboard</span> Dashboard
                        </a>
                    @endauth
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                        target="_blank"
                        class="bg-secondary text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary-hover hover:shadow-glow transition-all active:scale-95 duration-150">
                        Project Consultation
                    </a>
                </div>

                <button class="md:hidden text-primary focus:outline-none"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden glass-panel mt-2 rounded-2xl p-4 flex flex-col space-y-4">
                <a href="#home" class="nav-link-mobile text-primary font-bold">Home</a>
                <a href="#about" class="nav-link-mobile text-muted font-medium hover:text-primary transition-colors">About Us</a>
                <a href="#services" class="nav-link-mobile text-muted font-medium hover:text-primary transition-colors">Services</a>
                <a href="#projects" class="nav-link-mobile text-muted font-medium hover:text-primary transition-colors">Projects</a>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                    class="bg-secondary text-white text-center py-3 rounded-xl font-semibold">
                    Let's Talk
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative pt-40 pb-24 md:pt-48 md:pb-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-reveal-up">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-primary leading-tight mb-6 tracking-tight">
                    <span class="text-gradient">Professional Contractors</span> for Premium, On-Time Projects.
                </h1>
                <p class="text-lg text-muted mb-8 max-w-lg leading-relaxed">
                    Trusted construction solutions for residential, commercial, and industrial developments. We build
                    with uncompromised quality and absolute precision.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                        class="bg-secondary text-white px-8 py-4 rounded-xl font-semibold hover:bg-secondary-hover transition-all hover:shadow-glow active:scale-95 duration-150 text-center flex justify-center items-center gap-2">
                        Get Free Consultation <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    <a href="#projects"
                        class="glass-panel text-primary px-8 py-4 rounded-xl font-semibold hover:bg-white transition-all active:scale-95 duration-150 text-center">
                        View Projects
                    </a>
                </div>

                <div class="flex flex-wrap gap-6 pt-8 border-t border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-success">verified</span>
                        <span class="text-sm font-semibold text-primary">15+ Years Experience</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-success">architecture</span>
                        <span class="text-sm font-semibold text-primary">500+ Projects</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-success">timer</span>
                        <span class="text-sm font-semibold text-primary">100% On-Time</span>
                    </div>
                </div>
            </div>

            <!-- Hero Image with Float & Glass overlays -->
            <div
                class="relative h-[400px] md:h-[600px] rounded-[2rem] overflow-hidden shadow-2xl border border-white/50 animate-float">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC_HXvKla2gCZkpAAL5NK1y2qYlBXXEIuRp6vLJtAURgCV6UbXTHwdII5IvibpbYZqg4a-p5eLc-fobregfqL7wcGpuX0EjuDVcFjWgxdpVQsp6awgyLVoArEbztY3XZLICsSdOpe0A8_bsQEi6eDlUrh4z0mz7PVIwHUJlCQ6VHMX8jrjtOJZ9X457dUVxkufIZS1NC0ZSmNUOxfr7GzUkbZIwb2QdzsNH10Y5NQkv6vimC3VnZ-rISVAb8v6Ffz4OBm17hSk3vaQ"
                    alt="Modern Architecture" class="absolute inset-0 w-full h-full object-cover">
                <!-- Inner Glass Overlay -->
                <div
                    class="absolute bottom-6 left-6 right-6 glass-panel p-6 rounded-2xl flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted font-bold uppercase tracking-wider mb-1">Featured Project</p>
                        <p class="text-primary font-bold">Lumina Office Tower</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-secondary">arrow_outward</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section id="about" class="py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative reveal-on-scroll">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4 tracking-tight">Why Choose Us?</h2>
            <p class="text-muted max-w-2xl mx-auto text-lg">Engineering excellence combined with integrity to deliver
                structures that stand the test of time.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group cursor-default">
                <div
                    class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:shadow-md transition-all text-secondary">
                    <span class="material-symbols-outlined text-3xl">groups</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-3">Expert Team</h3>
                <p class="text-muted text-sm leading-relaxed">Certified professionals with decades of combined
                    experience in the construction industry.</p>
            </div>

            <div
                class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group cursor-default">
                <div
                    class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:shadow-md transition-all text-secondary">
                    <span class="material-symbols-outlined text-3xl">architecture</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-3">Premium Materials</h3>
                <p class="text-muted text-sm leading-relaxed">We source only the highest grade materials to ensure
                    durability and aesthetic perfection.</p>
            </div>

            <div
                class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group cursor-default">
                <div
                    class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:shadow-md transition-all text-secondary">
                    <span class="material-symbols-outlined text-3xl">timer</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-3">On-Time Delivery</h3>
                <p class="text-muted text-sm leading-relaxed">Efficient project management guarantees handover
                    according to the agreed schedule.</p>
            </div>

            <div
                class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group cursor-default">
                <div
                    class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:shadow-md transition-all text-secondary">
                    <span class="material-symbols-outlined text-3xl">verified_user</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-3">Extended Warranty</h3>
                <p class="text-muted text-sm leading-relaxed">Long-term commitment to quality with comprehensive
                    structural and maintenance warranties.</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-white border-y border-gray-100 reveal-on-scroll relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4 tracking-tight">Integrated Construction
                    Services</h2>
                <p class="text-muted max-w-2xl mx-auto text-lg">End-to-end construction solutions from conceptual
                    design to final handover with world-class standards.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Item -->
                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">apartment</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Building Contractor</h3>
                    <p class="text-muted text-sm leading-relaxed">Construction of residential homes, office buildings,
                        retail spaces, and commercial facilities.</p>
                </div>

                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">construction</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Building Renovation</h3>
                    <p class="text-muted text-sm leading-relaxed">Total or partial renovations to enhance the
                        functionality and value of existing structures.</p>
                </div>

                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">architecture</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Design & Build</h3>
                    <p class="text-muted text-sm leading-relaxed">Seamless design and construction services integrated
                        into one efficient system.</p>
                </div>

                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">foundation</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Structural Works</h3>
                    <p class="text-muted text-sm leading-relaxed">Foundations, reinforced concrete, steel frameworks,
                        and critical supporting structures.</p>
                </div>

                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">chair</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Interior & Fit Out</h3>
                    <p class="text-muted text-sm leading-relaxed">Professional interior finishing for offices, retail
                        stores, and commercial buildings.</p>
                </div>

                <div
                    class="bg-[#F8F9FA] p-8 rounded-2xl border border-gray-100 hover:border-success/30 hover:shadow-lg transition-all group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:bg-success group-hover:text-white transition-colors text-success">
                        <span class="material-symbols-outlined">engineering</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Infrastructure & Utilities</h3>
                    <p class="text-muted text-sm leading-relaxed">Roadworks, drainage systems, regional utilities, and
                        supporting facilities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto reveal-on-scroll relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4 tracking-tight">Proven Results Reflecting
                Quality</h2>
            <p class="text-muted max-w-2xl mx-auto text-lg">Our commitment to engineering excellence and aesthetic
                detail is reflected in every structure we build.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if (isset($projects) && $projects->count() > 0)
                @foreach ($projects as $index => $project)
                    @php
                        $imagePath = str_starts_with($project->image, 'http')
                            ? $project->image
                            : (file_exists(public_path('assets/' . $project->image))
                                ? asset('assets/' . $project->image)
                                : asset('storage/projects/' . $project->image));

                        // Make the second item span 2 columns on large screens for dynamic layout
                        $colSpan = $index == 1 && $projects->count() >= 3 ? 'lg:col-span-2' : '';
                    @endphp
                    <a href="{{ route('public.projects.show', $project->slug) }}"
                        class="group relative overflow-hidden rounded-[2rem] shadow-glass h-[400px] block {{ $colSpan }}">
                        <img src="{{ $imagePath }}" alt="{{ $project->title }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                        <!-- Gradient Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300">
                        </div>

                        <div class="absolute inset-0 p-8 flex flex-col justify-end text-white">
                            <div
                                class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <span
                                    class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold tracking-wider uppercase mb-3 border border-white/20">
                                    {{ $project->category->name ?? 'Project' }}
                                </span>
                                <h3 class="text-2xl font-bold mb-2">{{ $project->title }}</h3>
                                <p class="text-white/80 text-sm mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    {{ $project->location ?? 'Indonesia' }} &bull; {{ $project->status }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="flex justify-center mt-12">
            <a href="{{ route('public.projects.index') }}"
                class="glass-panel text-primary px-8 py-4 rounded-xl font-semibold hover:bg-white transition-all active:scale-95 duration-150">
                See All Projects
            </a>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-primary relative overflow-hidden text-center reveal-on-scroll">
        <!-- Abstract shape in background -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-3xl h-full opacity-10 pointer-events-none">
            <div class="w-full h-full rounded-full bg-secondary blur-3xl"></div>
        </div>

        <div class="max-w-3xl mx-auto px-4 relative z-10">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 tracking-tight">Ready to Build Your Project with
                Professionals?</h2>
            <p class="text-white/70 text-lg mb-10 max-w-xl mx-auto">Schedule a consultation with our expert team to
                discuss your vision and construction needs today.</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                    class="bg-secondary text-white px-8 py-4 rounded-xl font-semibold hover:bg-secondary-hover transition-all hover:shadow-glow active:scale-95 duration-150">
                    Contact Us Now
                </a>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello,%20I%20would%20like%20to%20get%20a%20quote%20for%20a%20project."
                    class="bg-white/10 text-white px-8 py-4 rounded-xl font-semibold border border-white/20 hover:bg-white hover:text-primary transition-all active:scale-95 duration-150">
                    Get a Quote
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-20 pb-10 z-10 relative mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">
                <!-- Company Info -->
                <div class="space-y-6 text-center md:text-left">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 border border-gray-100 shadow-sm">
                            <img src="{{ asset('assets/logo.png') }}" alt="Logo"
                                class="w-full h-full object-contain"
                                onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
                        </div>
                        <span class="text-xl font-bold text-primary tracking-tight">Sistem Jaya Abadi</span>
                    </a>
                    <p class="text-sm text-muted leading-relaxed">
                        Premium construction and engineering services. We build with uncompromised quality, absolute
                        precision, and integrity.
                    </p>
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-3">
                        <span
                            class="material-symbols-outlined text-secondary text-lg mt-0.5 hidden md:block">location_on</span>
                        <p class="text-sm text-muted leading-relaxed">Jl. Raya Sesetan, Denpasar Selatan,<br>Bali -
                            Indonesia</p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#home"
                                class="text-sm text-muted hover:text-secondary transition-colors">Home</a></li>
                        <li><a href="{{ url('/') }}#about"
                                class="text-sm text-muted hover:text-secondary transition-colors">About Us</a></li>
                        <li><a href="{{ url('/') }}#services"
                                class="text-sm text-muted hover:text-secondary transition-colors">Services</a></li>
                        <li><a href="{{ url('/') }}#projects"
                                class="text-sm text-muted hover:text-secondary transition-colors">Projects</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Our Services</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#services"
                                class="text-sm text-muted hover:text-secondary transition-colors">Building
                                Contractor</a></li>
                        <li><a href="{{ url('/') }}#services"
                                class="text-sm text-muted hover:text-secondary transition-colors">Building
                                Renovation</a></li>
                        <li><a href="{{ url('/') }}#services"
                                class="text-sm text-muted hover:text-secondary transition-colors">Design & Build</a>
                        </li>
                        <li><a href="{{ url('/') }}#services"
                                class="text-sm text-muted hover:text-secondary transition-colors">Interior & Fit
                                Out</a></li>
                    </ul>
                </div>

                <!-- Contact & Socials -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Connect With Us</h4>
                    <ul class="space-y-3 mb-6 flex flex-col items-center md:items-start">
                        <li>
                            <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                                class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">call</span>
                                {{ setting('contact_whatsapp', '+62 812-3456-7890') }}
                            </a>
                        </li>
                        <li>
                            <a href="mailto:info@sistemjayaabadi.com"
                                class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">mail</span> info@sistemjayaabadi.com
                            </a>
                        </li>
                    </ul>
                    <div class="flex gap-4 justify-center md:justify-start">
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-xs font-bold">IG</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-xs font-bold">FB</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-xs font-bold">IN</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-muted text-center md:text-left">© {{ date('Y') }} PT Sistem Jaya Abadi. All
                    rights reserved.</p>
                <div class="flex gap-6">
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Privacy
                        Policy</a>
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Terms of
                        Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Reveal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
                observer.observe(el);
            });

            // Navbar blur on scroll
            window.addEventListener('scroll', () => {
                const nav = document.getElementById('navbar');
                if (window.scrollY > 20) {
                    nav.classList.add('py-2');
                    nav.classList.remove('mt-4');
                } else {
                    nav.classList.remove('py-2');
                    nav.classList.add('mt-4');
                }
            });

            // Active Navbar Link based on Scroll Section
            const sections = document.querySelectorAll('section[id]');
            const desktopLinks = document.querySelectorAll('.nav-link-desktop');
            const mobileLinks = document.querySelectorAll('.nav-link-mobile');

            const navObserverOptions = {
                root: null,
                rootMargin: '-50px 0px -50% 0px', // Trigger when section is in the top half of viewport
                threshold: 0
            };

            const navObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        
                        // Update Desktop Links
                        desktopLinks.forEach(link => {
                            if (link.getAttribute('href') === `#${id}`) {
                                link.classList.add('font-semibold', 'text-primary', 'border-secondary');
                                link.classList.remove('font-medium', 'text-muted', 'border-transparent');
                            } else {
                                link.classList.remove('font-semibold', 'text-primary', 'border-secondary');
                                link.classList.add('font-medium', 'text-muted', 'border-transparent');
                            }
                        });

                        // Update Mobile Links
                        mobileLinks.forEach(link => {
                            if (link.getAttribute('href') === `#${id}`) {
                                link.classList.add('font-bold', 'text-primary');
                                link.classList.remove('font-medium', 'text-muted');
                            } else {
                                link.classList.remove('font-bold', 'text-primary');
                                link.classList.add('font-medium', 'text-muted');
                            }
                        });
                    }
                });
            }, navObserverOptions);

            sections.forEach(section => {
                navObserver.observe(section);
            });
        });
    </script>
</body>

</html>
