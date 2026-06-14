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
    <div id="nav-loader" style="display:none; position:fixed; inset:0; z-index:9998; align-items:center; justify-content:center; background:rgba(255,255,255,0.85); backdrop-filter:blur(4px);">
        <div style="display:flex; flex-direction:column; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border:4px solid #141B23; border-top-color:transparent; border-radius:50%; animation:navSpin 0.8s linear infinite;"></div>
            <p style="font-size:14px; font-weight:500; color:#525252;">Memuat halaman...</p>
        </div>
    </div>
    <style>
        @keyframes navSpin { to { transform: rotate(360deg); } }
    </style>

    <script>
        (function() {
            var loader = document.getElementById('nav-loader');
            var hideTimer = null;

            function showNavLoader() {
                if (!loader) return;
                loader.style.display = 'flex';
                // Auto-hide after 8s as safety net
                clearTimeout(hideTimer);
                hideTimer = setTimeout(function() {
                    loader.style.display = 'none';
                }, 8000);
            }

            document.addEventListener('click', function(e) {
                // Find closest <a> from click target
                var link = e.target.closest('a[href]');
                if (!link) return;

                var href = link.getAttribute('href');

                // Skip: no href, hash, javascript, external, modifier keys
                if (!href || href === '#' || href.startsWith('javascript') || href.startsWith('http')) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                // Skip: modal triggers, dropdown toggles, tab buttons
                if (link.hasAttribute('data-hs-overlay') || link.classList.contains('hs-dropdown-toggle')) return;

                // Only trigger for sidebar menu and topbar navigation links
                var inSidebar = link.closest('#app-menu');
                var inTopbar = link.closest('.app-header');
                if (!inSidebar && !inTopbar) return;

                showNavLoader();
            });
        })();
    </script>
</body>
</html>