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
                : asset('storage/projects/' . $project->image));
    @endphp
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $seoTitle }}</title>
    @include('partials.public-seo', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'url' => route('public.projects.show', $project->slug),
        'image' => $seoImage,
        'type' => 'article',
    ])
    <link rel="icon" type="image/png" href="/assets/logo.png" />
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
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
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        revealUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8F9FA; }
        
        /* Subtle ambient glows */
        .ambient-glow-1 {
            position: fixed; top: -10%; left: -10%; width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(219,89,22,0.05) 0%, rgba(255,255,255,0) 70%);
            z-index: -1; pointer-events: none;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
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
            background-image: linear-gradient(90deg, #141B23, #4A5568);
        }
    </style>
</head>
<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen">

    <div class="ambient-glow-1"></div>

    <!-- TopNavBar (Glassmorphism) -->
    <header class="fixed top-0 inset-x-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="glass-panel rounded-2xl px-6 py-4 flex justify-between items-center transition-all duration-300">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain" onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
                    </div>
                    <span class="text-xl font-bold text-primary tracking-tight">Sistem Jaya Abadi</span>
                </a>
                
                <nav class="hidden md:flex space-x-8 items-center">
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#home">Home</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#about">About Us</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#services">Services</a>
                    <a class="text-sm font-semibold text-primary border-b-2 border-secondary pb-1" href="{{ route('public.projects.index') }}">Projects</a>
                </nav>

                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-primary hover:text-secondary transition flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">dashboard</span> Dashboard
                        </a>
                    @endauth
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services." target="_blank" class="bg-secondary text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary-hover hover:shadow-glow transition-all active:scale-95 duration-150">
                        Project Consultation
                    </a>
                </div>

                <button class="md:hidden text-primary focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
            </div>
            
            <div id="mobile-menu" class="hidden md:hidden glass-panel mt-2 rounded-2xl p-4 flex flex-col space-y-4">
                <a href="{{ url('/') }}#home" class="text-muted font-medium">Home</a>
                <a href="{{ url('/') }}#about" class="text-muted font-medium">About Us</a>
                <a href="{{ url('/') }}#services" class="text-muted font-medium">Services</a>
                <a href="{{ route('public.projects.index') }}" class="text-primary font-medium">Projects</a>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services." class="bg-secondary text-white text-center py-3 rounded-xl font-semibold">
                    Let's Talk
                </a>
            </div>
        </div>
    </header>

    <!-- Case Study Hero Section -->
    <section class="pt-32 pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative">
        <div class="mb-8 animate-reveal-up">
            <a href="{{ route('public.projects.index') }}" class="inline-flex items-center gap-2 text-muted hover:text-primary font-semibold transition group">
                <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition">arrow_back</span>
                <span>Back to Projects</span>
            </a>
        </div>

        <div class="max-w-4xl mb-12 animate-reveal-up" style="animation-delay: 100ms;">
            <span class="inline-block px-3 py-1 bg-primary/5 text-primary text-xs font-semibold tracking-wider uppercase rounded-full mb-4 border border-primary/10">
                Case Study
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-primary leading-tight mb-4 tracking-tight">
                {{ $project->title }}
            </h1>
            <p class="text-lg text-muted flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-xl">location_on</span>
                <span>{{ $project->location }}</span>
            </p>
        </div>

        @php
            $imagePath = (str_starts_with($project->image, 'http')) 
                ? $project->image 
                : (file_exists(public_path('assets/' . $project->image)) 
                    ? asset('assets/' . $project->image) 
                    : asset('storage/projects/' . $project->image));
        @endphp
        
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl mb-16 border border-white/50 w-full aspect-video md:aspect-[21/9] animate-reveal-up" style="animation-delay: 200ms;">
            <img src="{{ $imagePath }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            
            @if ($project->status === 'Completed')
                <div class="absolute top-6 right-6 glass-panel px-5 py-2 rounded-full text-sm font-semibold text-success tracking-wider uppercase border border-success/20">
                    Completed
                </div>
            @else
                <div class="absolute top-6 right-6 bg-secondary text-white px-5 py-2 rounded-full text-sm font-semibold tracking-wider uppercase shadow-glow">
                    Ongoing
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            
            <!-- Left: Description -->
            <div class="lg:col-span-2 space-y-8 reveal-on-scroll">
                <div class="glass-panel rounded-3xl p-8 md:p-12">
                    <h2 class="text-2xl font-bold text-primary mb-6 border-b border-gray-200 pb-4">
                        Project Overview & Details
                    </h2>
                    <div class="text-muted text-base md:text-lg leading-relaxed whitespace-pre-line space-y-6">
                        {{ $project->description }}
                    </div>
                </div>
            </div>

            <!-- Right: Metadata & CTA -->
            <div class="space-y-8 reveal-on-scroll" style="animation-delay: 150ms;">
                
                <div class="bg-primary text-white rounded-3xl p-8 shadow-2xl border border-white/10 space-y-6 relative overflow-hidden">
                    <!-- Subtle glow inside dark card -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/20 blur-3xl rounded-full"></div>
                    
                    <h3 class="text-xl font-bold text-white border-b border-white/10 pb-3 relative z-10">Project Specs</h3>
                    
                    <div class="space-y-5 relative z-10">
                        <div>
                            <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Category</span>
                            <span class="text-base font-medium text-white">{{ $project->category->name ?? 'General Project' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Location</span>
                            <span class="text-base font-medium text-white">{{ $project->location }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Status</span>
                            <span class="text-base font-medium flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $project->status === 'Completed' ? 'bg-success' : 'bg-secondary animate-pulse' }}"></span>
                                {{ $project->status }}
                            </span>
                        </div>
                        @if ($project->client)
                            <div>
                                <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Client</span>
                                <span class="text-base font-medium text-white">{{ $project->client }}</span>
                            </div>
                        @endif
                        @if ($project->year)
                            <div>
                                <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Year</span>
                                <span class="text-base font-medium text-white">{{ $project->year }}</span>
                            </div>
                        @endif
                        @if ($project->building_area)
                            <div>
                                <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Building Area</span>
                                <span class="text-base font-medium text-white">{{ $project->building_area }}</span>
                            </div>
                        @endif
                        @if ($project->land_area)
                            <div>
                                <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Land Area</span>
                                <span class="text-base font-medium text-white">{{ $project->land_area }}</span>
                            </div>
                        @endif
                        @if ($project->execution_team)
                            <div>
                                <span class="block text-xs font-semibold tracking-wider text-white/50 uppercase mb-1">Execution Team</span>
                                <span class="text-base font-medium text-white">{{ $project->execution_team }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CTA -->
                <div class="glass-panel rounded-3xl p-8 text-center space-y-6">
                    <div class="w-16 h-16 bg-success/10 rounded-2xl flex items-center justify-center text-success mx-auto shadow-sm">
                        <span class="material-symbols-outlined text-3xl">support_agent</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-primary mb-2">Build Your Dream Project</h4>
                        <p class="text-sm text-muted leading-relaxed">
                            Get a direct consultation with our engineers for custom structures, budgets, and permits.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hi,%20I'm%20interested%20in%20building%20a%20project%20similar%20to%20{{ urlencode($project->title) }}" target="_blank" class="block w-full bg-secondary text-white text-center py-4 rounded-xl font-bold hover:bg-secondary-hover hover:shadow-glow transition-all active:scale-95">
                        Consult via WhatsApp
                    </a>
                </div>

            </div>
        </div>

        <!-- More Projects Section -->
        @if ($relatedProjects->count() > 0)
            <div class="mt-24 border-t border-gray-200 pt-16 reveal-on-scroll">
                <h2 class="text-3xl font-bold text-primary mb-12">More Featured Projects</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedProjects as $relProject)
                        @php
                            $relImgPath = (str_starts_with($relProject->image, 'http')) 
                                ? $relProject->image 
                                : (file_exists(public_path('assets/' . $relProject->image)) 
                                    ? asset('assets/' . $relProject->image) 
                                    : asset('storage/projects/' . $relProject->image));
                        @endphp
                        <a href="{{ route('public.projects.show', $relProject->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 block">
                            <div class="relative h-56 overflow-hidden">
                                <img src="{{ $relImgPath }}" alt="{{ $relProject->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute top-3 right-3 glass-panel px-3 py-1 rounded-full text-[10px] font-semibold text-primary tracking-wider uppercase shadow-sm">
                                    {{ $relProject->status }}
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="text-secondary text-[10px] font-bold tracking-wider uppercase mb-2">{{ $relProject->location }}</p>
                                <h4 class="text-lg font-bold text-primary mb-2 group-hover:text-success transition-colors">{{ $relProject->title }}</h4>
                                <span class="text-success font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">Read Case Study <span class="material-symbols-outlined text-sm">arrow_forward</span></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-20 pb-10 z-10 relative mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">
                <!-- Company Info -->
                <div class="space-y-6 text-center md:text-left">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 border border-gray-100 shadow-sm">
                            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain" onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
                        </div>
                        <span class="text-xl font-bold text-primary tracking-tight">Sistem Jaya Abadi</span>
                    </a>
                    <p class="text-sm text-muted leading-relaxed">
                        Premium construction and engineering services. We build with uncompromised quality, absolute precision, and integrity.
                    </p>
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-lg mt-0.5 hidden md:block">location_on</span>
                        <p class="text-sm text-muted leading-relaxed">Jl. Raya Sesetan, Denpasar Selatan,<br>Bali - Indonesia</p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#home" class="text-sm text-muted hover:text-secondary transition-colors">Home</a></li>
                        <li><a href="{{ url('/') }}#about" class="text-sm text-muted hover:text-secondary transition-colors">About Us</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Services</a></li>
                        <li><a href="{{ route('public.projects.index') }}" class="text-sm text-muted hover:text-secondary transition-colors">Projects</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Our Services</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Building Contractor</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Building Renovation</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Design & Build</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Interior & Fit Out</a></li>
                    </ul>
                </div>

                <!-- Contact & Socials -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Connect With Us</h4>
                    <ul class="space-y-3 mb-6 flex flex-col items-center md:items-start">
                        <li>
                            <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services." class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">call</span> {{ setting('contact_whatsapp', '+62 812-3456-7890') }}
                            </a>
                        </li>
                        <li>
                            <a href="mailto:info@sistemjayaabadi.com" class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">mail</span> info@sistemjayaabadi.com
                            </a>
                        </li>
                    </ul>
                    <div class="flex gap-4">
                        <a href="javascript:void(0)" class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">IN</span>
                        </a>
                        <a href="javascript:void(0)" class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">FB</span>
                        </a>
                        <a href="javascript:void(0)" class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">IG</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-muted text-center md:text-left">© {{ date('Y') }} PT Sistem Jaya Abadi. All rights reserved.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Privacy Policy</a>
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1 });

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
        });
    </script>
</body>
</html>
