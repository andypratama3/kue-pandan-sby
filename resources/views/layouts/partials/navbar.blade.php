@php
    // Navbar: sticky (bukan fixed) agar selalu selebar kolom konten tanpa
    // shrink-to-fit; top-0 saat scroll tanpa jump karena in-flow di main.
@endphp
<nav id="navbar-main"
    class="sticky top-0 z-40 flex items-center justify-between mx-3 transition-all duration-300 ease-in-out bg-white/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700 backdrop-blur-xl shadow-md rounded-2xl lg:mx-4"
    navbar-main navbar-scroll="true">
    
    <!-- Subtle gradient line at the bottom of header -->
    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-brand/20 dark:via-brand/40 to-transparent pointer-events-none"></div>
    
    <div class="relative flex items-center justify-between w-full h-16 sm:h-20 px-4 sm:px-6 md:px-8">

        {{-- Left side: Mobile toggle + Breadcrumb & Page Title --}}
        <div class="flex items-center gap-3 sm:gap-4 flex-grow min-w-0 mr-4 h-full">
            {{-- Mobile Hamburger Toggle Button --}}
            <button type="button"
                class="flex items-center justify-center p-2.5 text-slate-600 dark:text-slate-300 transition-all duration-200 lg:hidden hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl active:scale-95 group shrink-0"
                id="mobile-toggle" sidenav-trigger aria-label="Toggle sidebar">
                <i class="text-xl fas fa-bars group-hover:text-brand-deep dark:group-hover:text-brand transition-colors"></i>
            </button>
            @push('page-scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var hamburger = document.getElementById('mobile-toggle');
                        var sidebar = document.getElementById('sidebar');

                        function updateHamburger() {
                            if (!hamburger || !sidebar) return;
                            if (window.innerWidth < 1024) {
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

            {{-- Breadcrumb Navigation & Page Title --}}
            <div class="flex flex-col justify-center flex-grow min-w-0 h-full">
                <ol class="flex items-center space-x-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <li>
                        @php
                            $user = Auth::user();
                            $homeUrl = url('/dashboard');
                            if ($user) {
                                if ($user->hasRole('owner')) {
                                    $homeUrl = route('admin.dashboard', ['region' => \App\Support\RegionContext::slug()]);
                                } elseif ($user->region) {
                                    if ($user->hasRole('admin')) {
                                        $homeUrl = route('admin.dashboard', ['region' => $user->region->slug]);
                                    } elseif ($user->hasRole('kurir')) {
                                        $homeUrl = route('kurir.dashboard', ['region' => $user->region->slug]);
                                    }
                                }
                            }
                        @endphp
                        <a class="hover:text-brand-deep dark:hover:text-brand transition-colors flex items-center gap-1.5" href="{{ $homeUrl }}">
                            <i class="fas fa-home text-brand text-[11px]"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-600 text-[10px]">/</li>
                    <li class="text-slate-800 dark:text-slate-200 font-bold truncate capitalize" aria-current="page">
                        @yield('page_title', 'Dashboard')
                    </li>
                </ol>
                <h1 class="mb-0 text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight capitalize truncate mt-0.5">
                    @yield('page_title', 'Dashboard')
                </h1>
            </div>
        </div>

        {{-- Right side cluster: Branch Switcher, Darkmode Toggle, Profile Avatar --}}
        <div class="flex items-center gap-2.5 sm:gap-3.5 shrink-0 h-full">
            
            {{-- Branch/Cabang Switcher (khusus Owner) --}}
            @php
                $isOwner = Auth::user() && Auth::user()->hasRole('owner');
                $currentRegion = \App\Support\RegionContext::region();
            @endphp
            @if ($isOwner)
            <div class="relative hidden sm:block">
                <button id="branch-switcher-btn" type="button"
                    class="flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl border border-slate-200/80 dark:border-slate-700 focus:outline-none transition-all shadow-sm">
                    <i class="fas fa-store mr-2 text-brand-deep dark:text-brand text-xs"></i>
                    <span id="current-branch-name">{{ $currentRegion->name ?? 'Pilih Cabang' }}</span>
                    <i class="fas fa-chevron-down ml-2 text-[9px] opacity-70 transition-transform duration-200"></i>
                </button>

                <div id="branch-dropdown"
                    class="absolute right-0 z-50 hidden w-64 mt-2 bg-white rounded-2xl shadow-xl shadow-slate-900/10 dark:bg-slate-800 dark:border dark:border-slate-700 overflow-hidden border border-slate-100">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-mint/60 dark:bg-slate-700/50">
                        <p class="text-xs font-bold text-brand-deep dark:text-brand uppercase tracking-wider">Ganti Cabang</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Owner — pantau semua cabang</p>
                    </div>
                    <div class="py-1 max-h-64 overflow-y-auto">
                        @foreach (App\Models\Region::active()->orderBy('id')->get() as $region)
                            <form method="POST" action="{{ route('admin.switch-region', ['region' => $region->slug]) }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center justify-between w-full px-4 py-3 text-xs font-semibold {{ $currentRegion && $region->id === $currentRegion->id ? 'bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60' }} transition-colors">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-store text-brand-deep dark:text-brand"></i>
                                        {{ $region->name }}
                                    </div>
                                    @if ($currentRegion && $region->id === $currentRegion->id)
                                        <i class="fas fa-check text-brand-deep dark:text-brand"></i>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Toggle Lightmode / Darkmode with Enhanced Design --}}
            <label id="theme-toggle-label-navbar" for="theme-toggle-checkbox-navbar"
                class="relative z-40 inline-flex items-center cursor-pointer group shrink-0" title="Mode gelap / terang">
                <input type="checkbox" value="" id="theme-toggle-checkbox-navbar" class="sr-only peer">
                <div class="w-12 h-[26px] bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-brand-deep dark:peer-checked:bg-brand transition-colors duration-300 border border-slate-300 dark:border-slate-600 shadow-inner"></div>
                <div
                    class="absolute top-[2px] left-[2px] w-[22px] h-[22px] bg-white rounded-full flex items-center justify-center shadow-md transition-transform duration-300 ease-out peer-checked:translate-x-[22px] group-hover:scale-105">
                    <div class="relative w-3.5 h-3.5">
                        <svg class="absolute inset-0 w-full h-full text-amber-500 transition-all duration-300 rotate-0 scale-100 dark:rotate-90 dark:scale-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <circle cx="12" cy="12" r="4.5"></circle>
                            <path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"></path>
                        </svg>
                        <svg class="absolute inset-0 w-full h-full text-brand-deep dark:text-brand transition-all duration-300 rotate-90 scale-0 dark:rotate-0 dark:scale-100" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.9 2.6a8.4 8.4 0 1 0 8.5 8.5 6.6 6.6 0 0 1-8.5-8.5Z"></path>
                        </svg>
                    </div>
                </div>
            </label>

            {{-- Profile section with Avatar & Dropdown --}}
            <ul class="flex flex-row items-center justify-end h-full pl-0 mb-0 list-none shrink-0">
                <li class="relative flex items-center h-full group">
                    <div class="relative w-10 h-10 sm:w-11 sm:h-11 overflow-hidden border-2 border-brand/40 dark:border-brand/60 rounded-full cursor-pointer hover:scale-105 transition-all shadow-sm hover:shadow-md hover:border-brand">
                        @php
                            $avatarSrc = '/assets/icon/admin.png';
                            if (Auth::user() && Auth::user()->hasRole('kurir')) {
                                $avatarSrc = '/assets/icon/kurir.png';
                            }
                        @endphp
                        <img src="{{ asset($avatarSrc) }}" alt="User Avatar" class="object-cover w-full h-full" />
                        <div class="absolute inset-0 bg-brand/20 opacity-0 group-hover:opacity-100 transition-opacity rounded-full"></div>
                    </div>
                    <div
                        class="absolute right-0 top-full z-[99999] w-80 mt-2 transition-all duration-200 ease-out origin-top-right scale-95 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto bg-white rounded-2xl shadow-2xl dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-5 text-sm text-slate-700 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-800/80 dark:to-slate-900/80 border-b border-slate-100 dark:border-slate-700">
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
                                } elseif ($user && $user->hasRole('owner')) {
                                    $ownerRegion = \App\Support\RegionContext::region();
                                    if ($ownerRegion && in_array(strtolower($ownerRegion->name), ['denpasar', 'bali', 'makassar'])) {
                                        $timezone = 'Asia/Makassar';
                                        $tzAbbr = 'WITA';
                                    }
                                }
                                $lastLogin =
                                    \Carbon\Carbon::now($timezone)->isoFormat('D MMMM YYYY, HH:mm') . ' ' . $tzAbbr;
                            @endphp
                            <div class="mb-4">
                                <span class="block text-base font-bold text-slate-900 dark:text-white">{{ $user->name ?? 'User' }}</span>
                                <span class="inline-block px-2.5 py-1 mt-1.5 text-[11px] font-semibold text-brand-deep bg-mint rounded-full dark:bg-brand-deep dark:text-brand">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ \App\Support\RegionContext::name() ?? 'Semua Region' }}
                                </span>
                            </div>
                            <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                                <p class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-brand/10 flex items-center justify-center">
                                        <i class="fas fa-envelope text-brand-deep dark:text-brand text-[10px]"></i>
                                    </span>
                                    <span>{{ $user->email ?? 'N/A' }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-brand/10 flex items-center justify-center">
                                        <i class="fas fa-clock text-brand text-[10px]"></i>
                                    </span>
                                    <span>Active: {{ $lastLogin }}</span>
                                </p>
                            </div>
                        </div>
                        @php
                            $profileUrl = route('profile.show');
                            if (Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner'))) {
                                $profileUrl = url('/admin/profile');
                            } elseif (Auth::user() && Auth::user()->hasRole('kurir')) {
                                $profileUrl = url('/kurir/profile');
                            }
                        @endphp
                        <a href="{{ $profileUrl }}" class="flex items-center px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/60 transition-colors group/item">
                            <span class="w-8 h-8 rounded-lg bg-brand/10 flex items-center justify-center mr-3 group-hover/item:bg-brand/20 transition-colors">
                                <i class="fas fa-user-circle text-brand-deep dark:text-brand text-sm"></i>
                            </span>
                            <span>Profil Saya</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-6 py-3.5 text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 transition-colors border-t border-slate-100 dark:border-slate-700 group/btn">
                                <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-950/30 flex items-center justify-center mr-3 group-hover/btn:bg-red-200 dark:group-hover/btn:bg-red-900/50 transition-colors">
                                    <i class="fas fa-sign-out-alt text-red-500 dark:text-red-400 text-sm"></i>
                                </span>
                                <span>Keluar Aplikasi</span>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
