<!DOCTYPE html>
<html lang="en" @yield('html_attribute')>
<head>
    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')
</head>
<body>
    <!-- Preloader Overlay -->
    <div id="preloader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-zinc-950 transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4">
            <!-- Spinner Animasi Minimalis -->
            <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-medium text-default-500 animate-pulse">Memuat sistem...</p>
        </div>
    </div>

    <script>
        (function () {
            function hidePreloader() {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => {
                        if (preloader.parentNode) {
                            preloader.parentNode.removeChild(preloader);
                        }
                    }, 300);
                }
            }
            window.addEventListener('load', hidePreloader);
            setTimeout(hidePreloader, 1200);
        })();
    </script>

    @yield('content')

    @include('layouts.partials/customizer')

    <!-- Navigation Loading Overlay -->
    <div id="nav-loader" class="fixed inset-0 z-[9998] flex items-center justify-center bg-white/80 dark:bg-zinc-950/80 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-medium text-default-600 dark:text-default-400">Memuat halaman...</p>
        </div>
    </div>

    <script>
        (function() {
            const loader = document.getElementById('nav-loader');

            function showLoader() {
                if (loader) {
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                    loader.classList.add('opacity-100');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Attach to all internal links (not external or #)
                document.querySelectorAll('a[href]').forEach(function(link) {
                    const href = link.getAttribute('href');

                    // Skip: external links, hash links, javascript, logout forms, modal triggers
                    if (!href || href.startsWith('#') || href.startsWith('javascript') ||
                        href.startsWith('http') || link.hasAttribute('data-hs-overlay') ||
                        link.classList.contains('menu-link') === false && link.closest('.app-menu') === null && link.closest('.app-header') === null) {
                        return;
                    }

                    link.addEventListener('click', function(e) {
                        // Skip if modifier key pressed (new tab/window)
                        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                        showLoader();
                    });
                });

                // Also trigger on form submissions (search, filter, etc.)
                document.querySelectorAll('form[method="GET"], form[method="get"]').forEach(function(form) {
                    form.addEventListener('submit', showLoader);
                });
            });

            // Safety: hide loader after 5 seconds in case page doesn't load
            setTimeout(function() {
                if (loader) {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    loader.classList.remove('opacity-100');
                }
            }, 5000);
        })();
    </script>
</body>
</html>