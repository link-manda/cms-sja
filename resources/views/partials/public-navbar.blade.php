<!-- Floating Island Navigation (Architectural Edition) -->
<header class="fixed top-4 inset-x-0 z-50 transition-all duration-500 ease-haptic" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-pill rounded-full px-5 sm:px-6 py-3 flex justify-between items-center transition-all duration-300">
            <!-- Brand Identity -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary rounded-xl">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-sm border border-black/5 group-hover:shadow-md group-hover:scale-105 transition-all duration-300">
                    <img src="{{ asset('assets/logo.png') }}" alt="PT Sistem Jaya Abadi Logo" class="w-full h-full object-contain"
                        onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=11161B&color=FAF9F6'">
                </div>
                <div class="flex flex-col">
                    <span class="font-display font-bold text-primary tracking-tight text-base sm:text-lg group-hover:text-secondary transition-colors duration-200">
                        Sistem Jaya Abadi
                    </span>
                    <span class="text-[9px] uppercase tracking-[0.2em] font-medium text-muted -mt-1 hidden sm:block">
                        General Contractor
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-1 sm:space-x-2">
                <a href="{{ url('/') }}#home"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider text-muted hover:text-primary hover:bg-black/5 transition-all duration-200">
                    Home
                </a>
                <a href="{{ url('/') }}#about"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider text-muted hover:text-primary hover:bg-black/5 transition-all duration-200">
                    About Us
                </a>
                <a href="{{ url('/') }}#services"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider text-muted hover:text-primary hover:bg-black/5 transition-all duration-200">
                    Services
                </a>
                <a href="{{ route('public.projects.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('public.projects.*') ? 'bg-primary text-white shadow-sm' : 'text-muted hover:text-primary hover:bg-black/5' }}">
                    Projects
                </a>
                <a href="{{ route('public.calculator.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('public.calculator.*') ? 'bg-primary text-white shadow-sm' : 'text-muted hover:text-primary hover:bg-black/5' }}">
                    Price Calculator
                </a>
            </nav>

            <!-- Right Actions: Auth & Button-in-Button CTA -->
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-xs font-semibold uppercase tracking-wider text-primary hover:text-secondary px-3 py-2 rounded-full hover:bg-black/5 transition flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                @endauth

                <!-- Nested Island CTA with Kinetic Motion -->
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20consult%20about%20a%20construction%20project."
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-secondary hover:bg-secondary-hover text-white pl-4 sm:pl-5 pr-2 py-2 flex items-center gap-2.5 font-semibold text-xs uppercase tracking-wider shadow-sm hover:shadow-glow active:scale-[0.98] transition-all duration-300 group">
                    <span>Project Consultation</span>
                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-sm font-bold">north_east</span>
                    </span>
                </a>
            </div>

            <!-- Mobile Hamburger Morph Trigger -->
            <button class="lg:hidden p-2 text-primary focus:outline-none rounded-full hover:bg-black/5 transition"
                id="mobile-menu-btn"
                aria-label="Toggle navigation menu"
                onclick="toggleMobileMenu()">
                <div class="w-6 h-5 flex flex-col justify-between items-center relative" id="hamburger-icon">
                    <span class="w-full h-0.5 bg-primary rounded-full transition-all duration-300 ease-haptic origin-left" id="line-1"></span>
                    <span class="w-full h-0.5 bg-primary rounded-full transition-all duration-200" id="line-2"></span>
                    <span class="w-full h-0.5 bg-primary rounded-full transition-all duration-300 ease-haptic origin-left" id="line-3"></span>
                </div>
            </button>
        </div>

        <!-- Mobile Full-Screen Glass Menu Modal -->
        <div id="mobile-menu"
            class="hidden lg:hidden glass-card mt-3 rounded-3xl p-6 flex flex-col space-y-4 shadow-ambient transition-all duration-500 ease-haptic border border-white/80">
            <div class="flex flex-col space-y-2">
                <a href="{{ url('/') }}#home" onclick="toggleMobileMenu()"
                    class="text-sm font-semibold uppercase tracking-wider text-muted hover:text-primary p-2.5 rounded-xl hover:bg-black/5 transition">
                    Home
                </a>
                <a href="{{ url('/') }}#about" onclick="toggleMobileMenu()"
                    class="text-sm font-semibold uppercase tracking-wider text-muted hover:text-primary p-2.5 rounded-xl hover:bg-black/5 transition">
                    About Us
                </a>
                <a href="{{ url('/') }}#services" onclick="toggleMobileMenu()"
                    class="text-sm font-semibold uppercase tracking-wider text-muted hover:text-primary p-2.5 rounded-xl hover:bg-black/5 transition">
                    Services
                </a>
                <a href="{{ route('public.projects.index') }}" onclick="toggleMobileMenu()"
                    class="text-sm font-semibold uppercase tracking-wider p-2.5 rounded-xl transition {{ request()->routeIs('public.projects.*') ? 'bg-primary text-white font-bold' : 'text-muted hover:text-primary hover:bg-black/5' }}">
                    Projects Portfolio
                </a>
                <a href="{{ route('public.calculator.index') }}" onclick="toggleMobileMenu()"
                    class="text-sm font-semibold uppercase tracking-wider p-2.5 rounded-xl transition {{ request()->routeIs('public.calculator.*') ? 'bg-primary text-white font-bold' : 'text-muted hover:text-primary hover:bg-black/5' }}">
                    Price Calculator
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-semibold uppercase tracking-wider text-primary p-2.5 rounded-xl hover:bg-black/5 transition flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">dashboard</span>
                        <span>Admin Dashboard</span>
                    </a>
                @endauth
            </div>

            <div class="pt-2 border-t border-black/5">
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20consult%20about%20a%20construction%20project."
                    target="_blank" rel="noopener noreferrer"
                    class="w-full rounded-2xl bg-secondary hover:bg-secondary-hover text-white py-3.5 px-5 flex items-center justify-center gap-2 font-bold text-xs uppercase tracking-wider shadow-sm transition-all duration-200">
                    <span>Project Consultation</span>
                    <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const line1 = document.getElementById('line-1');
        const line2 = document.getElementById('line-2');
        const line3 = document.getElementById('line-3');

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            line1.classList.add('rotate-45', 'translate-x-0.5', '-translate-y-0.5');
            line2.classList.add('opacity-0');
            line3.classList.add('-rotate-45', 'translate-x-0.5', 'translate-y-0.5');
        } else {
            menu.classList.add('hidden');
            line1.classList.remove('rotate-45', 'translate-x-0.5', '-translate-y-0.5');
            line2.classList.remove('opacity-0');
            line3.classList.remove('-rotate-45', 'translate-x-0.5', 'translate-y-0.5');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.getElementById('navbar');
        if (nav) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    nav.classList.add('top-2');
                    nav.classList.remove('top-4');
                } else {
                    nav.classList.add('top-4');
                    nav.classList.remove('top-2');
                }
            }, { passive: true });
        }
    });
</script>
