<style>
    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal-on-scroll.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {

        .reveal-on-scroll,
        .animate-reveal-up,
        .animate-float {
            animation: none !important;
            transition: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
    }
</style>
<noscript>
    <style>
        .reveal-on-scroll {
            opacity: 1 !important;
            transform: none !important;
        }
    </style>
</noscript>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.revealDelay || 0;
                    entry.target.style.transitionDelay = `${delay}ms`;
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12
        });

        document.querySelectorAll('.reveal-on-scroll').forEach((el) => revealObserver.observe(el));

        const nav = document.getElementById('navbar');
        if (nav) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    nav.classList.add('py-2');
                    nav.classList.remove('mt-4');
                } else {
                    nav.classList.remove('py-2');
                    nav.classList.add('mt-4');
                }
            });
        }
    });
</script>
