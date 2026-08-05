import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS Files
                'resources/css/app.css',
                'resources/css/argon-dashboard-tailwind.css',

                // JavaScript Files (sesuai screenshot Anda)
                'resources/js/app.js',
                'resources/js/script_homepage.js',
                'resources/js/argon-dashboard-tailwind.js',
                'resources/js/bootstrap.js',
                'resources/js/carousel.js',
                'resources/js/charts.js',
                'resources/js/custom-modal.js',
                'resources/js/custom.js',
                'resources/js/dark-mode-toggle.js',
                'resources/js/dropdown.js',
                'resources/js/fixed-plugin.js',
                'resources/js/live-search.js',
                'resources/js/nav-pills.js',
                'resources/js/navbar-collapse.js', // Asumsi nama file ini
                'resources/js/navbar-scroll-fix.js',
                'resources/js/navbar-sticky.js',
                'resources/js/perfect-scrollbar.js',
                'resources/js/sidenav-burger.js',
                'resources/js/tooltips.js',
            ],
            refresh: true,
        }),
    ],
});

