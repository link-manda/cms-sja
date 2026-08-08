<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => 'Admin Portal Login - PT Sistem Jaya Abadi',
        'seoTitle' => 'Admin Portal Login - PT Sistem Jaya Abadi',
        'seoDescription' => 'Centralized Management Console for PT Sistem Jaya Abadi engineers and administrators.',
        'seoUrl' => route('login'),
    ])
</head>

<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden min-h-screen bg-background flex flex-col justify-center">

    <div class="relative min-h-screen w-full flex bg-[#FAF9F6] text-primary antialiased">

        <!-- 1. Left Panel: Management Console Form (50% Desktop, 100% Mobile) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10 min-h-screen bg-[#FAF9F6]">
            <!-- Top Bar: Brand Logo & Public Link -->
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center p-2 shadow-ambient border border-black/5 group-hover:scale-105 transition-transform duration-300">
                        <img alt="SJA Logo" class="w-full h-full object-contain" src="{{ asset('assets/logo.png') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=11161B&color=FAF9F6'"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-primary text-base tracking-tight">PT Sistem Jaya Abadi</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] font-bold text-muted">Management Portal</span>
                    </div>
                </a>

                <a href="/" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Back to Site</span>
                </a>
            </div>

            <!-- Main Form Card Area -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                <!-- Header Typography -->
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/5 border border-primary/10 text-[10px] font-bold text-primary uppercase tracking-widest mb-3 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Internal Operations Console</span>
                    </div>
                    <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-primary tracking-tight leading-tight mb-2">
                        Central Access <span class="text-gradient">Portal</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-muted leading-relaxed">
                        Enter your authorized engineering &amp; operational credentials to manage project archives, inventory, and cost indices.
                    </p>
                </div>

                <!-- Session Flash Message -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-emerald-600 text-lg mt-0.5 shrink-0">check_circle</span>
                        <p class="text-xs font-semibold text-emerald-700 leading-relaxed">{{ session('status') }}</p>
                    </div>
                @endif

                <!-- Global Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-secondary/10 border border-secondary/20 flex items-start gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-secondary text-lg mt-0.5 shrink-0">error</span>
                        <div class="text-xs font-semibold text-secondary leading-relaxed">
                            <p>Authentication credentials did not match our records. Please verify and try again.</p>
                        </div>
                    </div>
                @endif

                <!-- Cloudflare Turnstile API -->
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase tracking-[0.2em] mb-2" for="email">
                            Account Email Address
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-muted text-lg pointer-events-none">mail</span>
                            <input class="w-full bg-white border {{ $errors->has('email') ? 'border-secondary focus:ring-secondary focus:border-secondary' : 'border-black/10 focus:ring-secondary focus:border-secondary' }} text-primary text-xs sm:text-sm font-medium rounded-xl py-3.5 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm transition-all placeholder:text-muted/60"
                                   id="email" name="email" value="{{ old('email') }}" placeholder="admin@sistemjayaabadi.biz.id" type="email" required autofocus autocomplete="username"/>
                        </div>
                    </div>

                    <!-- Password Input with Toggle -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-[10px] font-bold text-muted uppercase tracking-[0.2em]" for="password">
                                Security Password
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-secondary font-bold text-[11px] hover:text-secondary-hover transition-colors"
                                   href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-muted text-lg pointer-events-none">lock</span>
                            <input class="w-full bg-white border {{ $errors->has('password') || $errors->has('email') ? 'border-secondary focus:ring-secondary focus:border-secondary' : 'border-black/10 focus:ring-secondary focus:border-secondary' }} text-primary text-xs sm:text-sm font-medium rounded-xl py-3.5 pl-11 pr-11 focus:outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm transition-all placeholder:text-muted/60"
                                   id="password" name="password" placeholder="••••••••" type="password" required autocomplete="current-password"/>
                            <button type="button" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-primary transition-colors focus:outline-none p-1 rounded-lg">
                                <span id="toggle-password-icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center gap-2.5 pt-1">
                        <input class="w-4 h-4 rounded border-black/20 text-secondary focus:ring-secondary focus:ring-offset-[#FAF9F6] cursor-pointer"
                               id="remember_me" name="remember" type="checkbox"/>
                        <label class="text-muted text-xs font-medium select-none cursor-pointer" for="remember_me">
                            Keep session active on this workstation
                        </label>
                    </div>

                    <!-- Cloudflare Turnstile Container -->
                    <div class="pt-2 flex flex-col items-center sm:items-start">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                        @error('cf-turnstile-response')
                            <p class="text-secondary text-xs font-semibold mt-2 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm">warning</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Submit CTA Button -->
                    <div class="pt-2">
                        <button class="w-full bg-secondary hover:bg-secondary-hover text-white py-4 px-6 rounded-xl font-bold text-xs uppercase tracking-wider shadow-glow active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group cursor-pointer"
                                type="submit">
                            <span>Authenticate &amp; Access Dashboard</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Meta & Encryption Info -->
            <div class="pt-6 border-t border-black/5 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-muted font-medium">
                <span>&copy; {{ date('Y') }} PT Sistem Jaya Abadi. All rights reserved.</span>
                <span class="inline-flex items-center gap-1.5 text-[10px] uppercase font-bold tracking-wider text-muted">
                    <span class="material-symbols-outlined text-xs text-emerald-600">verified_user</span>
                    <span>256-Bit TLS Secured</span>
                </span>
            </div>
        </div>

        <!-- 2. Right Panel: Architectural Visual & Trust Showcase (50% Desktop, Hidden on Mobile) -->
        <div class="hidden lg:block lg:w-1/2 relative bg-primary overflow-hidden min-h-screen">
            <!-- Architectural Background Photo -->
            <div class="absolute inset-0">
                <img src="{{ asset('assets/hero_villa.png') }}" alt="Architecture Showcase"
                    class="w-full h-full object-cover opacity-75 filter contrast-105 scale-105"
                    onerror="this.src='https://placehold.co/1200x1600/11161B/FAF9F6?text=SJA+Architectural+Mastery'">
            </div>

            <!-- Multi-stop Dark Obsidian Gradient Mesh -->
            <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/60 to-primary/30"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Floating Trust Glass Card at Bottom -->
            <div class="absolute bottom-12 left-12 right-12 z-10">
                <div class="glass-card bg-primary/80 backdrop-blur-xl border border-white/15 p-8 rounded-[2rem] shadow-2xl text-white">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full bg-white/10 text-[10px] font-bold uppercase tracking-widest text-secondary border border-white/10">
                            Engineering Standard
                        </span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-[10px] font-bold uppercase tracking-widest text-emerald-400 border border-emerald-500/30">
                            ISO 9001:2015 &amp; LPJK
                        </span>
                    </div>

                    <h2 class="font-display text-2xl font-bold tracking-tight text-white mb-2">
                        Mastering Architectural Precision.
                    </h2>
                    
                    <p class="text-xs text-white/70 leading-relaxed mb-6 max-w-lg">
                        Centralized infrastructure management for PT Sistem Jaya Abadi. Delivering structural integrity, bespoke commercial developments, and general contracting across Indonesia.
                    </p>

                    <!-- Credibility Metrics Bar -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10 text-center">
                        <div>
                            <span class="font-display font-extrabold text-lg text-white block">15+</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Years Mastery</span>
                        </div>
                        <div class="border-x border-white/10">
                            <span class="font-display font-extrabold text-lg text-secondary block">500+</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Projects Built</span>
                        </div>
                        <div>
                            <span class="font-display font-extrabold text-lg text-emerald-400 block">100%</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">On-Time SLA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-password-icon');
            if (!passwordInput || !icon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>

</html>
