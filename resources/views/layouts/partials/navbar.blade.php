{{-- FIXED: Clean top navigation bar with proper alignment and dropdowns --}}
<nav id="navbar-main"
    class="fixed top-0 left-0 right-0 z-40 flex items-center justify-between px-0 py-0 transition-all duration-300 ease-in lg:flex-nowrap lg:justify-start bg-white/95 dark:bg-slate-900/95 border-b border-slate-200/70 dark:border-slate-800 backdrop-blur-xl"
    navbar-main navbar-scroll="true">
    <div class="flex items-center justify-between w-full h-16 sm:h-20 px-4 sm:px-6">

        {{-- Left side: Mobile toggle + Breadcrumb --}}
        <div class="flex items-center flex-grow h-full">
            {{-- Mobile Hamburger Toggle Button --}}
            <a href="javascript:;"
                class="flex items-center justify-center p-2.5 text-slate-600 dark:text-slate-300 transition-all ease-nav-brand xl:hidden hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl"
                id="mobile-toggle" sidenav-trigger>
                <i class="text-xl fas fa-bars"></i>
            </a>
            @push('page-scripts')
                <script>
                    // Hamburger menu auto hide saat sidebar terbuka (mobile & tablet)
                    document.addEventListener('DOMContentLoaded', function() {
                        var hamburger = document.getElementById('mobile-toggle');
                        var sidebar = document.getElementById('sidebar');

                        function updateHamburger() {
                            if (!hamburger || !sidebar) return;
                            if (window.innerWidth < 1280) {
                                var isSidebarOpen = !sidebar.classList.contains('-translate-x-full');
                                if (isSidebarOpen) {
                                    hamburger.classList.add('hidden');
                                } else {
                                    hamburger.classList.remove('hidden');
                                }
                            } else {
                                hamburger.classList.add('hidden');
                            }
                        }
                        const observer = new MutationObserver(updateHamburger);
                        observer.observe(sidebar, {
                            attributes: true,
                            attributeFilter: ['class']
                        });
                        window.addEventListener('resize', updateHamburger);
                        updateHamburger();

                        // Branch switcher dropdown toggle
                        const branchSwitcherBtn = document.getElementById('branch-switcher-btn');
                        const branchDropdown = document.getElementById('branch-dropdown');
                        
                        if (branchSwitcherBtn && branchDropdown) {
                            branchSwitcherBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                branchDropdown.classList.toggle('hidden');
                            });
                            
                            document.addEventListener('click', function(e) {
                                if (!branchSwitcherBtn.contains(e.target) && !branchDropdown.contains(e.target)) {
                                    branchDropdown.classList.add('hidden');
                                }
                            });
                        }
                    });
                </script>
            @endpush

            {{-- Breadcrumb Navigation --}}
            <div class="flex-col justify-center flex-grow hidden h-full ml-4 xl:flex xl:ml-0">
                <ol class="flex items-center space-x-2 bg-transparent rounded-lg text-xs sm:text-sm">
                    <li>
                        @php
                            $user = Auth::user();
                            $region = $user->region ?? null;
                            $homeUrl = url('/dashboard');
                            if ($user && $region) {
                                if ($user->hasRole('admin')) {
                                    $homeUrl = route('admin.dashboard', ['region' => $region]);
                                } elseif ($user->hasRole('kurir')) {
                                    $homeUrl = route('kurir.dashboard', ['region' => $region]);
                                }
                            }
                        @endphp
                        <a class="text-slate-500 hover:text-brand-deep dark:text-slate-400 dark:hover:text-white transition-colors flex items-center gap-1.5" href="{{ $homeUrl }}">
                            <i class="fas fa-home text-xs"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-600">/</li>
                    <li class="font-medium text-slate-900 dark:text-slate-100 capitalize" aria-current="page">
                        @yield('page_title', 'Dashboard')
                    </li>
                </ol>
                <h6 class="mb-0 text-lg font-bold text-slate-900 dark:text-white tracking-wide capitalize flex items-center gap-2">
                    @yield('page_title', 'Dashboard')
                </h6>
            </div>
        </div>

        {{-- Branch/Cabang Switcher --}}
        <div class="relative hidden sm:block mr-4">
            <button id="branch-switcher-btn" type="button"
                class="flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl border border-slate-200/60 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-brand/30 transition-all">
                <i class="fas fa-map-marker-alt mr-2 text-brand-deep dark:text-brand"></i>
                <span id="current-branch-name">{{ Auth::user()->region->name ?? 'Pilih Cabang' }}</span>
                <i class="fas fa-chevron-down ml-2 text-[10px] opacity-70 transition-transform duration-200"></i>
            </button>
            
            <div id="branch-dropdown"
                class="absolute right-0 z-50 hidden w-56 mt-2 bg-white rounded-2xl shadow-xl shadow-slate-900/5 dark:bg-slate-800 dark:border dark:border-slate-700 overflow-hidden border border-slate-100">
                <div class="p-3.5 border-b border-slate-100 dark:border-slate-700 bg-mint dark:bg-slate-700/50">
                    <p class="text-[11px] font-bold text-brand-deep dark:text-brand uppercase tracking-wider">Cabang Toko</p>
                </div>
                <div class="py-1 max-h-64 overflow-y-auto">
                    @php
                        $allRegions = App\Models\Region::all();
                        $currentRegionSlug = Auth::user()->region->slug ?? null;
                    @endphp
                    @foreach($allRegions as $region)
                        <a href="{{ route('admin.dashboard', ['region' => $region->slug]) }}"
                            class="flex items-center justify-between px-4 py-2.5 text-xs font-medium {{ $region->slug === $currentRegionSlug ? 'bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60' }} transition-colors">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-store text-brand-deep dark:text-brand"></i>
                                {{ $region->name }}
                            </div>
                            @if($region->slug === $currentRegionSlug)
                                <i class="fas fa-check text-brand-deep dark:text-brand"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Toggle Lightmode / Darkmode --}}
        <label id="theme-toggle-label-navbar" for="theme-toggle-checkbox-navbar"
            class="relative z-40 inline-flex items-center cursor-pointer mr-3">
            <input type="checkbox" value="" id="theme-toggle-checkbox-navbar" class="sr-only peer">
            <div class="h-6 bg-slate-200 dark:bg-slate-700 rounded-full w-11 peer dark:peer-checked:bg-slate-700 peer-checked:bg-brand transition-colors border border-slate-300 dark:border-slate-600"></div>
            <div
                class="absolute top-0.5 left-[2px] bg-white rounded-full h-5 w-5 transition-all peer-checked:translate-x-full flex items-center justify-center shadow-md">
                <i class="text-[10px] text-amber-500 peer-checked:hidden fas fa-sun"></i>
                <i class="text-[10px] text-brand-deep hidden peer-checked:block fas fa-moon"></i>
            </div>
        </label>

        {{-- Right side: Profile section --}}
        <div class="flex items-center justify-end h-full">
            <ul class="flex flex-row items-center justify-end h-full pl-0 mb-0 list-none">
                <!-- Avatar with dropdown settings -->
                <li class="relative flex items-center h-full group">
                    <div class="relative w-9 h-9 sm:w-10 sm:h-10 overflow-hidden border-2 border-slate-200 dark:border-slate-600 rounded-full cursor-pointer hover:scale-105 transition-all">
                        @php
                            $avatarSrc = '/assets/icon/admin.png';
                            if (Auth::user() && Auth::user()->hasRole('kurir')) {
                                $avatarSrc = '/assets/icon/kurir.png';
                            }
                        @endphp
                        <img src="{{ asset($avatarSrc) }}" alt="User Avatar" class="object-cover w-full h-full" />
                    </div>
                    <div
                        class="absolute right-0 top-full z-[99999] w-72 mt-2 transition-all duration-200 ease-out origin-top-right scale-95 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto bg-white rounded-2xl shadow-2xl dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 text-xs text-slate-700 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700">
                            @php
                                use Illuminate\Support\Facades\DB;
                                $user = Auth::user();
                                $timezone = 'Asia/Jakarta';
                                $tzAbbr = 'WIB';

                                if ($user && $user->region) {
                                    $regionName = strtolower($user->region->name);
                                    if (in_array($regionName, ['denpasar', 'bali', 'makassar'])) {
                                        $timezone = 'Asia/Makassar';
                                        $tzAbbr = 'WITA';
                                    }
                                }
                                $lastLogin =
                                    \Carbon\Carbon::now($timezone)->isoFormat('D MMMM YYYY, HH:mm') . ' ' . $tzAbbr;
                            @endphp
                            <div class="mb-3">
                                <span class="block text-sm font-bold text-slate-900 dark:text-white">{{ $user->name ?? 'User' }}</span>
                                <span class="inline-block px-2 py-0.5 mt-1 text-[10px] font-semibold text-brand-deep bg-mint rounded-full dark:bg-brand-deep dark:text-brand">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $user->region->name ?? 'Semua Region' }}
                                </span>
                            </div>
                            <div class="space-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                                <p><i class="fas fa-envelope mr-1.5 text-brand-deep dark:text-brand"></i>{{ $user->email ?? 'N/A' }}</p>
                                <p><i class="fas fa-clock mr-1.5 text-brand"></i>Active: {{ $lastLogin }}</p>
                            </div>
                        </div>
                        @php
                            $profileUrl = route('profile.show');
                            if (Auth::user() && Auth::user()->hasRole('admin')) {
                                $profileUrl = url('/admin/profile');
                            } elseif (Auth::user() && Auth::user()->hasRole('kurir')) {
                                $profileUrl = url('/kurir/profile');
                            }
                        @endphp
                        <a href="{{ $profileUrl }}" class="flex items-center px-5 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/60 transition-colors">
                            <i class="fas fa-user-circle mr-2.5 text-brand-deep dark:text-brand text-sm"></i>
                            Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-5 py-3 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 transition-colors border-t border-slate-100 dark:border-slate-700">
                                <i class="fas fa-sign-out-alt mr-2.5 text-red-500 text-sm"></i>
                                Keluar Aplikasi
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
