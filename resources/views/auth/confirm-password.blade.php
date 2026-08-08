<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => 'Confirm Security Password - PT Sistem Jaya Abadi',
        'seoTitle' => 'Confirm Security Password - PT Sistem Jaya Abadi',
        'seoDescription' => 'High-privilege security confirmation checkpoint for the PT Sistem Jaya Abadi management portal.',
        'seoUrl' => route('password.confirm'),
    ])
</head>

<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden min-h-screen bg-background flex flex-col justify-center">

    <div class="relative min-h-screen w-full flex bg-[#FAF9F6] text-primary antialiased">

        <!-- 1. Left Panel: Confirm Password Form (50% Desktop, 100% Mobile) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10 min-h-screen bg-[#FAF9F6]">
            <!-- Top Bar: Brand Logo & Dashboard Return Link -->
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center p-2 shadow-ambient border border-black/5 group-hover:scale-105 transition-transform duration-300">
                        <img alt="SJA Logo" class="w-full h-full object-contain" src="{{ asset('assets/logo.png') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=11161B&color=FAF9F6'"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-primary text-base tracking-tight">PT Sistem Jaya Abadi</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] font-bold text-muted">Security Checkpoint</span>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Main Form Area -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                <!-- Header Typography -->
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-secondary/10 border border-secondary/20 text-[10px] font-bold text-secondary uppercase tracking-widest mb-3 backdrop-blur-md">
                        <span class="material-symbols-outlined text-xs">shield</span>
                        <span>High-Privilege Area</span>
                    </div>
                    <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-primary tracking-tight leading-tight mb-2">
                        Security <span class="text-gradient">Confirmation</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-muted leading-relaxed">
                        This is a secure area of the management system. Please confirm your password before continuing with administrative operations.
                    </p>
                </div>

                <!-- Global Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-secondary/10 border border-secondary/20 flex items-start gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-secondary text-lg mt-0.5 shrink-0">error</span>
                        <div class="text-xs font-semibold text-secondary leading-relaxed">
                            <p>The provided password did not match our records.</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                    @csrf

                    <!-- Password Input with Toggle -->
                    <div>
                        <label class="block text-[10px] font-bold text-muted uppercase tracking-[0.2em] mb-2" for="password">
                            Current Password
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-muted text-lg pointer-events-none">lock</span>
                            <input class="w-full bg-white border {{ $errors->has('password') ? 'border-secondary focus:ring-secondary focus:border-secondary' : 'border-black/10 focus:ring-secondary focus:border-secondary' }} text-primary text-xs sm:text-sm font-medium rounded-xl py-3.5 pl-11 pr-11 focus:outline-none focus:ring-2 focus:ring-secondary/20 shadow-sm transition-all placeholder:text-muted/60"
                                   id="password" name="password" placeholder="••••••••" type="password" required autocomplete="current-password" autofocus />
                            <button type="button" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-primary transition-colors focus:outline-none p-1 rounded-lg">
                                <span id="toggle-password-icon" class="material-symbols-outlined text-lg leading-none">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 flex flex-col gap-3">
                        <button class="w-full bg-secondary hover:bg-secondary-hover text-white py-4 px-6 rounded-xl font-bold text-xs uppercase tracking-wider shadow-glow active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group cursor-pointer"
                                type="submit">
                            <span>Verify &amp; Proceed to Action</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                        </button>

                        <a href="{{ route('dashboard') }}" class="w-full py-3 text-center text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-colors flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">close</span>
                            <span>Cancel and Return to Dashboard</span>
                        </a>
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
                    onerror="this.src='https://placehold.co/1200x1600/11161B/FAF9F6?text=SJA+Security+Vault'">
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
                            Access Control
                        </span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-[10px] font-bold uppercase tracking-widest text-emerald-400 border border-emerald-500/30">
                            Session Elevation
                        </span>
                    </div>

                    <h2 class="font-display text-2xl font-bold tracking-tight text-white mb-2">
                        Institutional Privilege Guard.
                    </h2>
                    
                    <p class="text-xs text-white/70 leading-relaxed mb-6 max-w-lg">
                        Sensitive modifications to financial indices, project gallery archives, and system configurations require on-demand cryptographic re-verification.
                    </p>

                    <!-- Credibility Metrics Bar -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10 text-center">
                        <div>
                            <span class="font-display font-extrabold text-lg text-white block">Active</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Privilege Lock</span>
                        </div>
                        <div class="border-x border-white/10">
                            <span class="font-display font-extrabold text-lg text-secondary block">Zero</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Bypass Permitted</span>
                        </div>
                        <div>
                            <span class="font-display font-extrabold text-lg text-emerald-400 block">100%</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Audited Logs</span>
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
