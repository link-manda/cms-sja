<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.public-head', [
        'pageTitle' => 'Building Price Calculator - PT Sistem Jaya Abadi',
        'seoTitle' => 'Building Price Calculator - PT Sistem Jaya Abadi',
        'seoDescription' => 'Estimate your building cost with PT Sistem Jaya Abadi. Select a building type to view price ranges, 2D & 3D designs, and construction process visuals.',
        'seoUrl' => route('public.calculator.index'),
    ])
</head>

<body class="text-primary antialiased selection:bg-secondary selection:text-white relative overflow-x-hidden flex flex-col min-h-screen bg-background">

    <!-- Ambient Background Meshes -->
    <div class="ambient-mesh-1"></div>
    <div class="ambient-mesh-2"></div>

    <!-- Floating Island Navbar -->
    @include('partials.public-navbar')

    <!-- 1. Calculator Hero Section -->
    <section class="pt-36 sm:pt-44 md:pt-48 pb-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto z-10 relative w-full">
        <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16 animate-reveal-up">
            <!-- Trust & Transparency Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-black/5 shadow-ambient text-xs font-semibold text-primary uppercase tracking-widest mb-4 backdrop-blur-md">
                <span class="material-symbols-outlined text-secondary text-base">calculate</span>
                <span>Transparent Cost &amp; Blueprint Estimator</span>
            </div>

            <!-- Syne Headline -->
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary tracking-tight leading-[1.1] mb-6">
                Building Price Calculator &amp; <span class="text-gradient">Visual Estimator</span>
            </h1>

            <p class="text-muted text-base sm:text-lg leading-relaxed">
                Simulate construction investments in real-time, inspect 2D &amp; 3D architectural renders, and review milestone workflows with total transparency.
            </p>
        </div>

        @if ($options->isEmpty())
            <!-- Empty State -->
            <div class="py-20 text-center glass-card rounded-[2.5rem] border border-black/5 p-8 max-w-xl mx-auto shadow-ambient">
                <div class="w-16 h-16 bg-black/5 text-muted rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">calculate</span>
                </div>
                <h3 class="font-display text-xl font-bold text-primary mb-2">No Calculator Models Available</h3>
                <p class="text-muted text-sm max-w-sm mx-auto mb-6">
                    Our engineering team is currently updating building pricing indices. Please consult directly for a customized quote.
                </p>
                <a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20request%20a%20building%20cost%20estimation."
                    target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-full bg-secondary hover:bg-secondary-hover text-white px-6 py-3 text-xs font-bold uppercase tracking-wider shadow-sm transition-all">
                    <span>Inquire Direct Estimate</span>
                    <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>
        @else
            <!-- 2. Interactive Selector Card -->
            <div class="glass-card rounded-[2rem] p-6 sm:p-8 mb-10 shadow-ambient border border-black/5 animate-reveal-up" style="animation-delay: 80ms;">
                <label for="option-select" class="block text-[10px] font-bold text-muted uppercase tracking-[0.2em] mb-2.5">
                    Select Building Model &amp; Specification
                </label>
                <div class="relative">
                    <select id="option-select"
                        class="w-full bg-white/90 backdrop-blur-md border border-black/10 text-primary text-sm sm:text-base font-bold rounded-2xl focus:ring-secondary focus:border-secondary p-4 pr-10 appearance-none shadow-sm cursor-pointer transition-all">
                        <option value="">-- Choose a building type to calculate --</option>
                        @foreach ($options as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }} — {{ $option['price_range'] }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-4 top-4 pointer-events-none text-muted text-2xl">expand_more</span>
                </div>
            </div>

            <!-- 3. Dynamic Calculation Workspace (Hidden until user selects) -->
            <div id="calc-result" class="hidden space-y-10">
                <!-- Live Price Range Banner -->
                <div class="rounded-[2.5rem] bg-gradient-to-br from-primary via-[#161D24] to-primary p-8 sm:p-12 text-white shadow-2xl border border-white/10 relative overflow-hidden text-center">
                    <!-- Ambient Glow Blur -->
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-secondary/20 blur-3xl rounded-full pointer-events-none"></div>
                    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-emerald-500/15 blur-3xl rounded-full pointer-events-none"></div>

                    <div class="relative z-10">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 text-secondary border border-white/10 text-[10px] font-bold uppercase tracking-widest mb-3 backdrop-blur-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            <span>Verified Cost Range Estimation</span>
                        </span>

                        <p id="result-price" class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-secondary tracking-tight my-2">
                        </p>

                        <h2 id="result-name" class="font-display text-lg sm:text-xl font-bold text-white/90 tracking-tight">
                        </h2>
                    </div>
                </div>

                <!-- Visual Galleries -->
                <!-- 2D Design Plan -->
                <div id="gallery-2d" class="hidden glass-card rounded-[2rem] p-8 border border-black/5 shadow-ambient">
                    <div class="flex items-center gap-2.5 mb-6 border-b border-black/5 pb-4">
                        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-xl">architecture</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-secondary uppercase tracking-widest">Layout &amp; Structure</span>
                            <h3 class="font-display text-xl font-bold text-primary">2D Architectural Blueprints</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-gallery="2d"></div>
                </div>

                <!-- 3D Visualization -->
                <div id="gallery-3d" class="hidden glass-card rounded-[2rem] p-8 border border-black/5 shadow-ambient">
                    <div class="flex items-center gap-2.5 mb-6 border-b border-black/5 pb-4">
                        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-xl">view_in_ar</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-secondary uppercase tracking-widest">Realistic Perspectives</span>
                            <h3 class="font-display text-xl font-bold text-primary">3D Architectural Renderings</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-gallery="3d"></div>
                </div>

                <!-- Construction Process Milestones -->
                <div id="gallery-proses" class="hidden glass-card rounded-[2rem] p-8 border border-black/5 shadow-ambient">
                    <div class="flex items-center gap-2.5 mb-6 border-b border-black/5 pb-4">
                        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-xl">construction</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-secondary uppercase tracking-widest">Execution Methodology</span>
                            <h3 class="font-display text-xl font-bold text-primary">Construction Workflow &amp; Milestones</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-gallery="proses"></div>
                </div>

                <!-- Technical Description & Scope -->
                <div class="glass-card rounded-[2rem] p-8 sm:p-10 border border-black/5 shadow-ambient">
                    <span class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-2 block">Material &amp; Standard Scope</span>
                    <h3 class="font-display text-2xl font-extrabold text-primary mb-4 pb-3 border-b border-black/5 tracking-tight">
                        Technical Scope &amp; Deliverables
                    </h3>
                    <p id="result-description" class="text-muted leading-relaxed whitespace-pre-line text-sm sm:text-base"></p>
                </div>

                <!-- Consultation CTA Banner with Selected Model Info -->
                <div class="p-8 sm:p-10 rounded-[2rem] bg-primary text-white shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-6 border border-white/10">
                    <div>
                        <span class="text-[10px] font-bold text-secondary uppercase tracking-widest block mb-1">Direct Engineering Dialogue</span>
                        <h4 class="font-display text-xl sm:text-2xl font-bold">Ready to Start Building This Model?</h4>
                        <p class="text-xs sm:text-sm text-white/70 mt-1 max-w-md">
                            Connect with our estimation engineers on WhatsApp to discuss site conditions, exact land area, and customize this specification.
                        </p>
                    </div>

                    <a id="calc-whatsapp-btn"
                        href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}?text=Hello%20PT%20Sistem%20Jaya%20Abadi,%20I%20would%20like%20to%20consult%20about%20a%20construction%20project."
                        target="_blank" rel="noopener noreferrer"
                        class="rounded-full bg-secondary hover:bg-secondary-hover text-white pl-7 pr-3 py-3.5 flex items-center gap-3 font-bold text-xs sm:text-sm uppercase tracking-wider shadow-glow hover:scale-105 active:scale-[0.98] transition-all duration-300 shrink-0 group">
                        <span>Consult This Model</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-300">
                            <span class="material-symbols-outlined text-base">call</span>
                        </span>
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Master Architectural Slate Footer -->
    @include('partials.public-footer')

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
                const waBtn = document.getElementById('calc-whatsapp-btn');
                const baseWaPhone = "{{ format_wa_number(setting('contact_whatsapp', '628123456789')) }}";

                const renderGallery = (type, urls) => {
                    const wrapper = document.getElementById('gallery-' + type);
                    const grid = wrapper.querySelector('[data-gallery]');
                    grid.innerHTML = '';
                    if (!urls || !urls.length) {
                        wrapper.classList.add('hidden');
                        return;
                    }
                    wrapper.classList.remove('hidden');
                    urls.forEach(url => {
                        const div = document.createElement('div');
                        div.className = 'rounded-2xl overflow-hidden border border-black/5 shadow-ambient bg-white aspect-[4/3] group';
                        const img = document.createElement('img');
                        img.src = url;
                        img.loading = 'lazy';
                        img.className = 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-haptic';
                        img.onerror = () => { img.src = 'https://placehold.co/800x600/11161B/FAF9F6?text=Blueprint+Preview'; };
                        div.appendChild(img);
                        grid.appendChild(div);
                    });
                };

                select.addEventListener('change', function () {
                    const option = byId[this.value];
                    if (!option) {
                        result.classList.add('hidden');
                        return;
                    }
                    priceEl.textContent = option.price_range;
                    nameEl.textContent = option.name;
                    descEl.textContent = option.description;
                    renderGallery('2d', option.images['2d']);
                    renderGallery('3d', option.images['3d']);
                    renderGallery('proses', option.images['proses']);

                    if (waBtn) {
                        const customMsg = "Hello PT Sistem Jaya Abadi, I would like to consult about " + option.name + " with estimated budget range " + option.price_range;
                        waBtn.href = "https://wa.me/" + baseWaPhone + "?text=" + encodeURIComponent(customMsg);
                    }

                    result.classList.remove('hidden');
                });
            });
        </script>
    @endif

</body>

</html>
