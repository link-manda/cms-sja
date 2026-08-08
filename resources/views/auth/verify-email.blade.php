<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => 'Verify Email Address - PT Sistem Jaya Abadi',
        'seoTitle' => 'Verify Email Address - PT Sistem Jaya Abadi',
        'seoDescription' => 'Institutional email verification for the PT Sistem Jaya Abadi management portal.',
        'seoUrl' => route('verification.notice'),
    ])
</head>

<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden min-h-screen bg-background flex flex-col justify-center">

    <div class="relative min-h-screen w-full flex bg-[#FAF9F6] text-primary antialiased">

        <!-- 1. Left Panel: Verify Email Notice & Actions (50% Desktop, 100% Mobile) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10 min-h-screen bg-[#FAF9F6]">
            <!-- Top Bar: Brand Logo & Return Link -->
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center p-2 shadow-ambient border border-black/5 group-hover:scale-105 transition-transform duration-300">
                        <img alt="SJA Logo" class="w-full h-full object-contain" src="{{ asset('assets/logo.png') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=11161B&color=FAF9F6'"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-primary text-base tracking-tight">PT Sistem Jaya Abadi</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] font-bold text-muted">Identity Verification</span>
                    </div>
                </a>

                <a href="/" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Back to Site</span>
                </a>
            </div>

            <!-- Main Form Area -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                <!-- Header Typography -->
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-700 uppercase tracking-widest mb-3 backdrop-blur-md">
                        <span class="material-symbols-outlined text-xs">mail_lock</span>
                        <span>Inbox Confirmation</span>
                    </div>
                    <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-primary tracking-tight leading-tight mb-2">
                        Verify Email <span class="text-gradient">Address</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-muted leading-relaxed">
                        A secure activation link has been dispatched to your institutional email. Please click the link inside to verify your identity and unlock full administrative permissions.
                    </p>
                </div>

                <!-- Session Flash Message (Verification Sent) -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-emerald-600 text-lg mt-0.5 shrink-0">mark_email_read</span>
                        <p class="text-xs font-semibold text-emerald-700 leading-relaxed">
                            A fresh verification link has been transmitted to the email address associated with your profile.
                        </p>
                    </div>
                @endif

                <!-- Actions Area -->
                <div class="space-y-4 pt-2">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button class="w-full bg-secondary hover:bg-secondary-hover text-white py-4 px-6 rounded-xl font-bold text-xs uppercase tracking-wider shadow-glow active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group cursor-pointer"
                                type="submit">
                            <span>Resend Verification Link</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform duration-200">send</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full py-3 text-center text-xs font-bold uppercase tracking-wider text-muted hover:text-primary transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                                type="submit">
                            <span class="material-symbols-outlined text-sm">logout</span>
                            <span>Sign Out of This Session</span>
                        </button>
                    </form>
                </div>
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
                <img src="{{ asset('assets/office_sesetan.png') }}" alt="Architecture Showcase"
                    class="w-full h-full object-cover opacity-75 filter contrast-105 scale-105"
                    onerror="this.src='https://placehold.co/1200x1600/11161B/FAF9F6?text=SJA+Institutional+Governance'">
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
                            Governance Standard
                        </span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-[10px] font-bold uppercase tracking-widest text-emerald-400 border border-emerald-500/30">
                            Verified Deliverability
                        </span>
                    </div>

                    <h2 class="font-display text-2xl font-bold tracking-tight text-white mb-2">
                        Institutional Email Governance.
                    </h2>
                    
                    <p class="text-xs text-white/70 leading-relaxed mb-6 max-w-lg">
                        All administrator accounts are bound to verified corporate mailboxes with SPF, DKIM, and DMARC enforcement to guarantee communication authenticity.
                    </p>

                    <!-- Credibility Metrics Bar -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10 text-center">
                        <div>
                            <span class="font-display font-extrabold text-lg text-white block">DMARC</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Strict Policy</span>
                        </div>
                        <div class="border-x border-white/10">
                            <span class="font-display font-extrabold text-lg text-secondary block">TLS 1.3</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Transport Layer</span>
                        </div>
                        <div>
                            <span class="font-display font-extrabold text-lg text-emerald-400 block">100%</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-white/60">Audit Trail</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
