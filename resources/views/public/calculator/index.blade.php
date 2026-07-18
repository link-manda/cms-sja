<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Building Price Calculator - PT Sistem Jaya Abadi</title>
    @include('partials.public-seo', [
        'title' => 'Building Price Calculator - PT Sistem Jaya Abadi',
        'description' => 'Estimate your building cost with PT Sistem Jaya Abadi. Select a building type to view price ranges, 2D & 3D designs, and construction process visuals.',
        'url' => route('public.calculator.index'),
    ])
    <link rel="icon" type="image/png" href="/assets/logo.png" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

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
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                    boxShadow: {
                        glass: "0 8px 32px 0 rgba(20, 27, 35, 0.05)",
                        glow: "0 0 20px rgba(219, 89, 22, 0.3)",
                    },
                    animation: {
                        'reveal-up': 'revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
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
</head>

<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen">

    <div class="ambient-glow-1"></div>

    <!-- TopNavBar -->
    <header class="fixed top-0 inset-x-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="glass-panel rounded-2xl px-6 py-4 flex justify-between items-center transition-all duration-300">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
                    </div>
                    <span class="text-xl font-bold text-primary tracking-tight">Sistem Jaya Abadi</span>
                </a>

                <nav class="hidden md:flex space-x-8 items-center">
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#home">Home</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#about">About Us</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ url('/') }}#services">Services</a>
                    <a class="text-sm font-medium text-muted hover:text-primary transition-colors pb-1" href="{{ route('public.projects.index') }}">Projects</a>
                    <a class="text-sm font-semibold text-primary border-b-2 border-secondary pb-1" href="{{ route('public.calculator.index') }}">Price Calculator</a>
                </nav>

                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-primary hover:text-secondary transition flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">dashboard</span> Dashboard
                        </a>
                    @endauth
                    <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                        target="_blank"
                        class="bg-secondary text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary-hover hover:shadow-glow transition-all active:scale-95 duration-150">
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
                <a href="{{ route('public.projects.index') }}" class="text-muted font-medium">Projects</a>
                <a href="{{ route('public.calculator.index') }}" class="text-primary font-medium">Price Calculator</a>
            </div>
        </div>
    </header>

    <!-- Calculator Section -->
    <section class="pt-40 pb-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto z-10 relative w-full">
        <div class="text-center mb-12 animate-reveal-up">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6 tracking-tight">Building Price Calculator</h1>
            <p class="text-muted max-w-2xl mx-auto text-lg">Select a building type to view its estimated price range, design visuals, and construction process.</p>
        </div>

        @if ($options->isEmpty())
            <div class="py-20 text-center glass-panel rounded-3xl">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-muted mx-auto mb-4 border border-gray-100">
                    <span class="material-symbols-outlined text-4xl">calculate</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-2">No Options Available</h3>
                <p class="text-muted">Calculator options are being prepared. Please check back soon.</p>
            </div>
        @else
            <!-- Dropdown -->
            <div class="glass-panel rounded-2xl p-6 mb-10 animate-reveal-up" style="animation-delay: 100ms;">
                <label for="option-select" class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Choose Building Type</label>
                <select id="option-select" class="w-full bg-white border border-gray-200 text-primary text-base rounded-xl focus:ring-secondary focus:border-secondary block p-4">
                    <option value="">-- Select an option --</option>
                    @foreach ($options as $option)
                        <option value="{{ $option['id'] }}">{{ $option['name'] }} — {{ $option['price_range'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Result (hidden until selection) -->
            <div id="calc-result" class="hidden space-y-10">
                <!-- Price + Name -->
                <div class="glass-panel rounded-2xl p-8 text-center">
                    <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-2">Estimated Price Range</p>
                    <p id="result-price" class="text-3xl md:text-4xl font-bold text-secondary mb-2"></p>
                    <h2 id="result-name" class="text-lg font-semibold text-primary"></h2>
                </div>

                <!-- Galleries -->
                <div id="gallery-2d" class="hidden">
                    <h3 class="text-xl font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">architecture</span> 2D Design</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" data-gallery="2d"></div>
                </div>
                <div id="gallery-3d" class="hidden">
                    <h3 class="text-xl font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">view_in_ar</span> 3D Design</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" data-gallery="3d"></div>
                </div>
                <div id="gallery-proses" class="hidden">
                    <h3 class="text-xl font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">construction</span> Construction Process</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" data-gallery="proses"></div>
                </div>

                <!-- Description -->
                <div class="glass-panel rounded-2xl p-8">
                    <h3 class="text-xl font-bold text-primary mb-4">Details</h3>
                    <p id="result-description" class="text-muted leading-relaxed whitespace-pre-line"></p>
                </div>
            </div>
        @endif
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-20 pb-10 z-10 relative mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">
                <div class="space-y-6 text-center md:text-left">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 border border-gray-100 shadow-sm">
                            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain"
                                onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=141B23&color=fff'">
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

                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#home" class="text-sm text-muted hover:text-secondary transition-colors">Home</a></li>
                        <li><a href="{{ url('/') }}#about" class="text-sm text-muted hover:text-secondary transition-colors">About Us</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Services</a></li>
                        <li><a href="{{ route('public.projects.index') }}" class="text-sm text-muted hover:text-secondary transition-colors">Projects</a></li>
                        <li><a href="{{ route('public.calculator.index') }}" class="text-sm text-muted hover:text-secondary transition-colors">Price Calculator</a></li>
                    </ul>
                </div>

                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-primary mb-6">Our Services</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Building Contractor</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Building Renovation</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Design & Build</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-sm text-muted hover:text-secondary transition-colors">Interior & Fit Out</a></li>
                    </ul>
                </div>

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
                            <a href="mailto:info@sistemjayaabadi.biz.id" class="flex items-center gap-2 text-sm text-muted hover:text-secondary transition-colors">
                                <span class="material-symbols-outlined text-lg">mail</span> info@sistemjayaabadi.biz.id
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-muted text-center md:text-left">© {{ date('Y') }} PT Sistem Jaya Abadi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @if ($options->isNotEmpty())
        <script id="calc-data" type="application/json">@json($options)</script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const data = JSON.parse(document.getElementById('calc-data').textContent);
                const byId = Object.fromEntries(data.map(o => [String(o.id), o]));

                const select = document.getElementById('option-select');
                const result = document.getElementById('calc-result');
                const priceEl = document.getElementById('result-price');
                const nameEl = document.getElementById('result-name');
                const descEl = document.getElementById('result-description');

                const renderGallery = (type, urls) => {
                    const wrapper = document.getElementById('gallery-' + type);
                    const grid = wrapper.querySelector('[data-gallery]');
                    grid.innerHTML = '';
                    if (!urls || !urls.length) { wrapper.classList.add('hidden'); return; }
                    wrapper.classList.remove('hidden');
                    urls.forEach(url => {
                        const div = document.createElement('div');
                        div.className = 'rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-white aspect-[4/3]';
                        const img = document.createElement('img');
                        img.src = url;
                        img.loading = 'lazy';
                        img.className = 'w-full h-full object-cover hover:scale-105 transition-transform duration-500';
                        img.onerror = () => { img.src = 'https://placehold.co/800x600/141B23/EEEDE6?text=Image+Not+Found'; };
                        div.appendChild(img);
                        grid.appendChild(div);
                    });
                };

                select.addEventListener('change', function () {
                    const option = byId[this.value];
                    if (!option) { result.classList.add('hidden'); return; }
                    priceEl.textContent = option.price_range;
                    nameEl.textContent = option.name;
                    descEl.textContent = option.description;
                    renderGallery('2d', option.images['2d']);
                    renderGallery('3d', option.images['3d']);
                    renderGallery('proses', option.images['proses']);
                    result.classList.remove('hidden');
                });
            });
        </script>
    @endif

</body>

</html>
