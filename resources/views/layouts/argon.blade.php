<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section('title', 'Admin Dashboard')
    @include('layouts.headicon')

    {{-- Vite build assets --}}
    @vite(['resources/css/app.css', 'resources/css/argon-dashboard-tailwind.css', 'resources/js/app.js', 'resources/js/custom.js', 'resources/js/sidenav-burger.js', 'resources/js/navbar-scroll-fix.js', 'resources/js/dark-mode-toggle.js', 'resources/js/live-search.js', 'resources/js/custom-modal.js'])

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    <!-- DARK MODE PREVENT FOUC -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('color-theme');
            let isDark = savedTheme ? savedTheme === 'dark' : false;
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body
    class="m-0 overflow-x-hidden font-sans text-base antialiased font-normal bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-200 min-h-screen flex flex-col">
    {{-- TOAST COMPONENT --}}
    <x-toast />

    <!-- PRELOADER -->
    <div id="preloader" class="fixed top-0 left-0 z-50 flex items-center justify-center w-full h-full bg-white/90 dark:bg-slate-950/90 backdrop-blur-md transition-opacity duration-300">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 border-4 border-brand/20 border-t-brand-deep rounded-full animate-spin"></div>
            <span class="text-xs font-bold text-brand-deep dark:text-brand-light tracking-wider uppercase">Memuat System...</span>
        </div>
    </div>

    {{-- Top ambient decorative bar --}}
    <div class="fixed top-0 left-0 w-full h-64 bg-gradient-to-r from-brand-deep/10 via-brand-deep/5 to-transparent pointer-events-none -z-10"></div>

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden transition-opacity duration-200 bg-slate-950/40 backdrop-blur-sm">
    </div>

    @include('layouts.partials.sidenav')

    <main id="main-content"
        class="relative flex-grow transition-all duration-300 ease-in-out main-content pt-20">
        @include('layouts.partials.navbar')
        @include('layouts.partials.content')
        @include('layouts.partials.footer')
    </main>

    @stack('livewire-modals')
    @stack('flowbite-modals')

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('page-scripts')
    <script>
      window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        const mainContent = document.getElementById('main-content');
        const sidebar = document.getElementById('sidebar');

        if (preloader && mainContent) {
          mainContent.classList.remove('opacity-0');
          if (sidebar) sidebar.classList.remove('opacity-0');

          preloader.style.opacity = '0';
          setTimeout(() => {
            preloader.style.display = 'none';
          }, 300);
        }
      });
    </script>
</body>

</html>
