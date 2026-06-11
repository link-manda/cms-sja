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

    <div class="wrapper">

        @include('layouts.partials/sidenav')

        <div class="page-content">

            @include('layouts.partials/topbar')

            <main>

                @yield('content')

            </main>

            @include('layouts.partials/footer')
            
        </div>

    </div>

    @include('layouts.partials/customizer')
</body>

</html>
