<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Our Projects - PT Sistem Jaya Abadi</title>
    @include('partials.public-seo', [
        'title' => 'Our Projects - PT Sistem Jaya Abadi',
        'description' => 'Explore the portfolio of PT Sistem Jaya Abadi. View our completed and ongoing construction projects across Indonesia.',
        'url' => route('public.projects.index'),
    ])
    <link rel="icon" type="image/png" href="/assets/logo.png" />

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
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        glass: "0 8px 32px 0 rgba(20, 27, 35, 0.05)",
                        glow: "0 0 20px rgba(219, 89, 22, 0.3)",
                    },
                    animation: {
                        'reveal-up': 'revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
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

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(20, 27, 35, 0.05);
        }
    </style>
    @include('partials.public-animations')
</head>

<body
    class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen">

    <div class="ambient-glow-1"></div>

    <!-- TopNavBar -->
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
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1"
                        href="{{ url('/') }}#home">Home</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1"
                        href="{{ url('/') }}#about">About Us</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1"
                        href="{{ url('/') }}#services">Services</a>
                    <a class="text-sm font-semibold text-primary border-b-2 border-secondary pb-1"
                        href="{{ route('public.projects.index') }}">Projects</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1"
                        href="{{ route('public.calculator.index') }}">Price Calculator</a>
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

            <div id="mobile-menu" class="hidden md:hidden glass-panel mt-2 rounded-2xl p-4 flex flex-col space-y-4">
                <a href="{{ url('/') }}#home" class="text-muted font-medium">Home</a>
                <a href="{{ url('/') }}#about" class="text-muted font-medium">About Us</a>
                <a href="{{ url('/') }}#services" class="text-muted font-medium">Services</a>
                <a href="{{ route('public.projects.index') }}" class="text-primary font-medium">Projects</a>
                <a href="{{ route('public.calculator.index') }}" class="text-muted font-medium">Price Calculator</a>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                    class="bg-secondary text-white text-center py-3 rounded-xl font-semibold">
                    Let's Talk
                </a>
            </div>
        </div>
    </header>

    <!-- Page Header & Filters -->
    <section class="pt-40 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto z-10 relative w-full">
        <div class="text-center mb-12 animate-reveal-up">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6 tracking-tight">Our Projects</h1>
            <p class="text-muted max-w-2xl mx-auto text-lg">Explore our portfolio of completed and ongoing developments
                across Indonesia. We deliver engineering excellence with integrity.</p>
        </div>

        <!-- Filter Form -->
        <div class="glass-panel rounded-2xl p-6 mb-12 animate-reveal-up" style="animation-delay: 100ms;">
            <form action="{{ route('public.projects.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                <!-- Category Filter -->
                <div>
                    <label for="category"
                        class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Category</label>
                    <select name="category" id="category"
                        class="w-full bg-white border border-gray-200 text-primary text-sm rounded-xl focus:ring-secondary focus:border-secondary block p-3">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status"
                        class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Status</label>
                    <select name="status" id="status"
                        class="w-full bg-white border border-gray-200 text-primary text-sm rounded-xl focus:ring-secondary focus:border-secondary block p-3">
                        <option value="">All Statuses</option>
                        <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed
                        </option>
                    </select>
                </div>

                <!-- Province Filter -->
                <div>
                    <label for="province"
                        class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Province</label>
                    <select name="province" id="province"
                        class="w-full bg-white border border-gray-200 text-primary text-sm rounded-xl focus:ring-secondary focus:border-secondary block p-3">
                        <option value="">All Provinces</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province }}"
                                {{ request('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="submit"
                        class="w-full bg-primary text-white p-3 rounded-xl font-semibold hover:bg-primary-light transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">filter_list</span> Apply
                    </button>
                    @if (request()->hasAny(['category', 'status', 'province']))
                        <a href="{{ route('public.projects.index') }}"
                            class="px-4 py-3 bg-gray-100 text-muted hover:bg-gray-200 rounded-xl font-semibold transition-colors flex items-center justify-center"
                            title="Clear Filters">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($projects as $project)
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
                    class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 block reveal-on-scroll"
                    data-reveal-delay="{{ $loop->index * 80 }}">
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ $imagePath }}" alt="{{ $project->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            onerror="this.src='https://placehold.co/800x600/141B23/EEEDE6?text=Image+Not+Found'">

                        <div
                            class="absolute top-3 right-3 glass-panel px-3 py-1 rounded-full text-[10px] font-semibold tracking-wider uppercase shadow-sm {{ $project->status === 'Completed' ? 'text-success' : 'text-secondary' }}">
                            {{ $project->status }}
                        </div>
                        <div
                            class="absolute top-3 left-3 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-[10px] font-semibold tracking-wider uppercase shadow-sm">
                            {{ $project->category->name ?? 'Project' }}
                        </div>
                    </div>
                    <div class="p-6">
                        <p
                            class="text-muted text-[10px] font-bold tracking-wider uppercase mb-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">location_on</span>
                            {{ $project->location }}
                        </p>
                        <h4 class="text-lg font-bold text-primary mb-3 group-hover:text-secondary transition-colors line-clamp-2"
                            title="{{ $project->title }}">{{ $project->title }}</h4>
                        <span
                            class="text-primary font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">Read
                            Case Study <span class="material-symbols-outlined text-sm">arrow_forward</span></span>
                    </div>
                </a>
            @empty
                <div
                    class="col-span-1 md:col-span-2 lg:col-span-3 py-20 text-center glass-panel rounded-3xl reveal-on-scroll">
                    <div
                        class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-muted mx-auto mb-4 border border-gray-100">
                        <span class="material-symbols-outlined text-4xl">search_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-2">No Projects Found</h3>
                    <p class="text-muted">We couldn't find any projects matching your current filters.</p>
                    <a href="{{ route('public.projects.index') }}"
                        class="inline-block mt-6 px-6 py-2 bg-secondary text-white font-semibold rounded-lg hover:bg-secondary-hover transition">
                        Clear All Filters
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Modern Pagination -->
        <div class="mt-16 flex justify-center">
            @if ($projects->hasPages())
                <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 inline-block">
                    {{ $projects->links() }}
                </div>
            @endif
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
                        <li><a href="{{ route('public.projects.index') }}"
                                class="text-sm text-muted hover:text-secondary transition-colors">Projects</a></li>
                        <li><a href="{{ route('public.calculator.index') }}"
                                class="text-sm text-muted hover:text-secondary transition-colors">Price Calculator</a></li>
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
                            <a href="mailto:info@sistemjayaabadi.biz.id"
                                class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">mail</span> info@sistemjayaabadi.biz.id
                            </a>
                        </li>
                    </ul>
                    <div class="flex gap-4 justify-center md:justify-start">
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">IG</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">FB</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-muted hover:bg-secondary hover:text-white hover:border-secondary transition-all">
                            <span class="text-sm font-bold">IN</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-muted text-center md:text-left">© {{ date('Y') }} PT Sistem Jaya Abadi. All
                    rights reserved.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Privacy
                        Policy</a>
                    <a href="javascript:void(0)" class="text-sm text-muted hover:text-primary transition-colors">Terms of
                        Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
