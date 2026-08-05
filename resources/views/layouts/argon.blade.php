<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section('title', 'Dashboard')
    @include('layouts.headicon')
    {{-- Vite build assets --}}
    @vite(['resources/css/app.css', 'resources/css/argon-dashboard-tailwind.css', 'resources/js/app.js', 'resources/js/custom.js', 'resources/js/argon-dashboard-tailwind.js', 'resources/js/sidenav-burger.js', 'resources/js/navbar-scroll-fix.js', 'resources/js/dark-mode-toggle.js', 'resources/js/live-search.js', 'resources/js/custom-modal.js'])

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    {{-- <link href="resources/css/nucleo-icons.css" rel="stylesheet" />
    <link href="resources/css/nucleo-svg.css" rel="stylesheet" /> --}}
    <script src="https://unpkg.com/@popperjs/core@2"></script>

    {{-- ! Script untuk Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- dropdown search -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- IMPROVED DARKMODE SCRIPT - PREVENTS FOUC AND SYNCS WITH TOGGLE -->
    <script>
        // Reset and initialize theme properly
        (function() {
            // For debugging - clear localStorage if there are conflicts
            // Uncomment the next line if you want to reset theme to light mode
            // localStorage.removeItem('color-theme');

            const savedTheme = localStorage.getItem('color-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            let isDark;
            if (savedTheme) {
                isDark = savedTheme === 'dark';
            } else {
                // Default to light mode if no preference is saved
                isDark = false; // Changed from systemPrefersDark to false for default light mode
                localStorage.setItem('color-theme', 'light');
            }

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body
    class="m-0 overflow-x-hidden font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    {{-- KOMPONEN TOAST --}}
    <x-toast />

    <!-- PRELOADER -->
    <div id="preloader" class="fixed top-0 left-0 z-50 flex items-center justify-center w-full h-full bg-white dark:bg-slate-900">
        <div class="w-16 h-16 border-t-4 border-b-4 border-green-500 rounded-full animate-spin"></div>
    </div>

    {{-- Top color bar --}}
    <div class="fixed w-full bg-greenlight dark:hidden min-h-75"></div>

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden transition-opacity duration-200 bg-black bg-opacity-40">
    </div>

    @include('layouts.partials.sidenav')

    {{-- PERBAIKAN: Menambahkan id="main-content" --}}
    {{-- <main id="main-content"
        class="relative h-full max-h-screen transition-all duration-200 ease-in-out main-content rounded-xl"> --}}
    <main id="main-content"
        class="relative h-full max-h-screen transition-all transition-opacity duration-200 duration-500 ease-in-out opacity-0 main-content rounded-xl">
        @include('layouts.partials.navbar')
        @include('layouts.partials.content')
        @include('layouts.partials.footer')
    </main>

    {{-- @stack('modals') --}}
    {{-- Stack khusus untuk modal yang dikontrol oleh Livewire/Jetstream --}}
    @stack('livewire-modals')

    {{-- Stack untuk modal yang dikontrol oleh Flowbite atau modal kustom lainnya --}}
    @stack('flowbite-modals')

    <!-- Livewire Scripts -->
    @livewireScripts

    {{-- Script for Modal --}}
    {{-- <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script> --}}

    @stack('page-scripts')
    <script>
      window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        const mainContent = document.getElementById('main-content');
        const sidebar = document.getElementById('sidebar'); // Assuming your sidebar has id="sidebar"

        // Pastikan semua elemen ada sebelum melanjutkan
        if (preloader && mainContent && sidebar) {
          // 1. Tampilkan konten utama dan sidebar
          mainContent.classList.remove('opacity-0');
          sidebar.classList.remove('opacity-0'); // Hapus class ini dari sidebar juga

          // 2. Hilangkan preloader dengan efek fade-out
          preloader.style.opacity = '0';
          setTimeout(() => {
            preloader.style.display = 'none';
          }, 500); // Durasi harus cocok dengan transisi CSS
        }
      });
    </script>
</body>


</html>
