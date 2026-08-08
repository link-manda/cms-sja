<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<title>{{ $pageTitle ?? setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor') }}</title>

@include('partials.public-seo', [
    'title' => $seoTitle ?? ($pageTitle ?? null),
    'description' => $seoDescription ?? null,
    'url' => $seoUrl ?? null,
    'image' => $seoImage ?? null,
    'type' => $seoType ?? 'website',
])

<link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}" />

@php
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'PT Sistem Jaya Abadi',
        'url' => url('/'),
        'logo' => asset('assets/logo.png'),
        'description' => setting('site_description', 'Professional contractors for premium, on-time construction and engineering developments.'),
        'email' => setting('contact_email', ''),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => setting('company_address', ''),
            'addressCountry' => 'ID',
        ],
    ];
    if (!empty(setting('contact_whatsapp'))) {
        $organizationSchema['contactPoint'] = [
            '@type' => 'ContactPoint',
            'telephone' => setting('contact_whatsapp'),
            'contactType' => 'customer service',
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

<!-- Google Fonts Preconnect & High-End Typography Pairing -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

<!-- Tailwind CSS with Architectural Editorial Luxury Design Tokens -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    background: "#FAF9F6", // Architectural Warm Limestone Cream
                    surface: "#FFFFFF",
                    "surface-alt": "#F4F2ED",
                    "surface-dark": "#11161B", // Deep Obsidian Charcoal
                    primary: "#11161B",
                    "primary-light": "#242E38",
                    secondary: "#D9531E", // Architectural Terracotta / Refined Copper
                    "secondary-hover": "#B84314",
                    "accent-emerald": "#0D5C3A",
                    "accent-sage": "#5A7364",
                    muted: "#5C6B7A",
                    "muted-light": "#8E9EAB",
                    "hairline": "rgba(17, 22, 27, 0.08)",
                    "hairline-dark": "rgba(255, 255, 255, 0.12)",
                    "glass-bg": "rgba(255, 255, 255, 0.82)",
                    "glass-border": "rgba(255, 255, 255, 0.6)",
                },
                fontFamily: {
                    sans: ["'Plus Jakarta Sans'", "system-ui", "-apple-system", "sans-serif"],
                    display: ["'Syne'", "'Plus Jakarta Sans'", "sans-serif"],
                    mono: ["'JetBrains Mono'", "monospace"],
                },
                boxShadow: {
                    glass: "0 12px 36px 0 rgba(17, 22, 27, 0.06)",
                    glow: "0 0 24px rgba(217, 83, 30, 0.25)",
                    ambient: "0 20px 48px -12px rgba(17, 22, 27, 0.06), 0 0 1px 1px rgba(17, 22, 27, 0.04)",
                    "ambient-lg": "0 32px 64px -16px rgba(17, 22, 27, 0.08), 0 0 1px 1px rgba(17, 22, 27, 0.05)",
                    "double-bezel": "0 0 0 1px rgba(17, 22, 27, 0.06), 0 8px 24px -4px rgba(17, 22, 27, 0.06)",
                    "inner-highlight": "inset 0 1px 1px rgba(255, 255, 255, 0.8)",
                },
                transitionTimingFunction: {
                    'haptic': 'cubic-bezier(0.16, 1, 0.3, 1)',
                    'spring': 'cubic-bezier(0.32, 0.72, 0, 1)',
                },
                animation: {
                    'float-slow': 'floatSlow 7s cubic-bezier(0.45, 0, 0.55, 1) infinite',
                    'reveal-up': 'revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                },
                keyframes: {
                    floatSlow: {
                        '0%, 100%': { transform: 'translateY(0)' },
                        '50%': { transform: 'translateY(-12px)' },
                    },
                    revealUp: {
                        '0%': { opacity: '0', transform: 'translateY(28px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    }
                }
            }
        }
    }
</script>

<style>
    /* Architectural Base Foundations */
    :root {
        --color-bg: #FAF9F6;
        --color-primary: #11161B;
        --color-secondary: #D9531E;
        font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--color-bg);
        color: var(--color-primary);
        letter-spacing: -0.012em;
    }

    /* Ambient Spatial Glow Orbs */
    .ambient-mesh-1 {
        position: fixed;
        top: -8%;
        left: -6%;
        width: 48vw;
        height: 48vw;
        background: radial-gradient(circle, rgba(217, 83, 30, 0.045) 0%, rgba(250, 249, 246, 0) 70%);
        z-index: -1;
        pointer-events: none;
    }

    .ambient-mesh-2 {
        position: fixed;
        bottom: -10%;
        right: -8%;
        width: 55vw;
        height: 55vw;
        background: radial-gradient(circle, rgba(13, 92, 58, 0.038) 0%, rgba(250, 249, 246, 0) 70%);
        z-index: -1;
        pointer-events: none;
    }

    /* Glassmorphic Double-Bezel Panels */
    .glass-pill {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 30px -5px rgba(17, 22, 27, 0.05), 0 0 0 1px rgba(17, 22, 27, 0.04);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 16px 40px -10px rgba(17, 22, 27, 0.04), 0 0 0 1px rgba(17, 22, 27, 0.035);
    }

    /* Custom Micro-Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #FAF9F6;
    }
    ::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 9999px;
        border: 2px solid #FAF9F6;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>

@include('partials.public-animations')
