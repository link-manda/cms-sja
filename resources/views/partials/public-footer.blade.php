<!-- Master Architectural Footer -->
<footer class="bg-surface-dark text-white relative z-10 overflow-hidden border-t border-white/10 mt-auto">
    <!-- Subtle Background Ambient Mesh -->
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-accent-emerald/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12 mb-16">
            <!-- Brand Column (Span 2 on desktop) -->
            <div class="lg:col-span-2 space-y-6">
                <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center p-2 shadow-sm border border-white/10 group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('assets/logo.png') }}" alt="PT Sistem Jaya Abadi" class="w-full h-full object-contain"
                            onerror="this.src='https://ui-avatars.com/api/?name=SJA&background=11161B&color=FAF9F6'">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-white text-xl tracking-tight">PT Sistem Jaya Abadi</span>
                        <span class="text-[10px] uppercase tracking-[0.25em] font-medium text-muted-light">General Contractor & Engineering</span>
                    </div>
                </a>
                <p class="text-muted-light text-sm leading-relaxed max-w-sm">
                    {{ setting('site_description', 'Delivering exceptional structural engineering, premium architectural developments, and general contracting across Indonesia with precision, safety, and unwavering integrity.') }}
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[11px] font-mono text-muted-light">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        ISO & LPJK Certified Standards
                    </span>
                </div>
            </div>

            <!-- Quick Navigation Links -->
            <div class="space-y-4">
                <h4 class="font-display text-sm font-bold uppercase tracking-wider text-white">Navigation</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ url('/') }}#home" class="text-muted-light hover:text-white transition-colors duration-200">Home</a></li>
                    <li><a href="{{ url('/') }}#about" class="text-muted-light hover:text-white transition-colors duration-200">About Us</a></li>
                    <li><a href="{{ url('/') }}#services" class="text-muted-light hover:text-white transition-colors duration-200">Services</a></li>
                    <li><a href="{{ route('public.projects.index') }}" class="text-muted-light hover:text-white transition-colors duration-200">Projects Portfolio</a></li>
                    <li><a href="{{ route('public.calculator.index') }}" class="text-muted-light hover:text-white transition-colors duration-200">Price Calculator</a></li>
                </ul>
            </div>

            <!-- Core Capabilities -->
            <div class="space-y-4">
                <h4 class="font-display text-sm font-bold uppercase tracking-wider text-white">Capabilities</h4>
                <ul class="space-y-2.5 text-sm text-muted-light">
                    <li>Commercial Buildings</li>
                    <li>Residential & Luxury Villas</li>
                    <li>Structural Engineering</li>
                    <li>Interior Fit-Out & Renovations</li>
                    <li>Project Management Consulting</li>
                </ul>
            </div>

            <!-- Direct Contact & Headquarters -->
            <div class="space-y-4">
                <h4 class="font-display text-sm font-bold uppercase tracking-wider text-white">Direct Inquiries</h4>
                <div class="space-y-3 text-sm text-muted-light">
                    @if(setting('company_address'))
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-lg shrink-0 mt-0.5">location_on</span>
                            <span class="text-xs leading-relaxed">{{ setting('company_address') }}</span>
                        </div>
                    @endif
                    @if(setting('contact_email'))
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-lg shrink-0">mail</span>
                            <a href="mailto:{{ setting('contact_email') }}" class="text-xs hover:text-white transition-colors">{{ setting('contact_email') }}</a>
                        </div>
                    @endif
                    @if(setting('contact_whatsapp'))
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-lg shrink-0">chat</span>
                            <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="text-xs hover:text-white transition-colors font-mono">
                                +{{ format_wa_number(setting('contact_whatsapp')) }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Copyright & Back-to-Top -->
        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-muted-light">
            <p>&copy; {{ date('Y') }} PT Sistem Jaya Abadi. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <span class="font-mono text-[11px] text-muted-light">Engineered with Precision</span>
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="p-2 rounded-full bg-white/5 hover:bg-white/10 text-white transition flex items-center justify-center"
                    aria-label="Scroll to top">
                    <span class="material-symbols-outlined text-base">arrow_upward</span>
                </button>
            </div>
        </div>
    </div>
</footer>
