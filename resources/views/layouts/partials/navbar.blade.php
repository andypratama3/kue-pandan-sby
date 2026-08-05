{{-- FIXED: Fixed navbar with proper alignment between breadcrumb and profile --}}
<nav id="navbar-main"
    class="fixed top-0 left-0 right-0 z-40 flex items-center justify-between px-0 py-0 transition-all duration-300 ease-in lg:flex-nowrap lg:justify-start bg-gradient-to-r from-blue-600 to-blue-700 dark:from-slate-800 dark:to-slate-900 dark:shadow-none"
    navbar-main navbar-scroll="true">
    <div class="flex items-center justify-between w-full h-20 px-6">

        {{-- Left side: Mobile toggle + Breadcrumb --}}
        <div class="flex items-center flex-grow h-full">
            {{-- Mobile Hamburger Toggle Button --}}
            <a href="javascript:;"
                class="flex items-center justify-center p-2.5 text-white transition-all ease-nav-brand xl:hidden hover:bg-white/10 rounded-lg"
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
                            // Hanya jalankan di layar < 1280px (xl)
                            if (window.innerWidth < 1280) {
                                var isSidebarOpen = !sidebar.classList.contains('-translate-x-full');
                                if (isSidebarOpen) {
                                    hamburger.classList.add('hidden');
                                } else {
                                    hamburger.classList.remove('hidden');
                                }
                            } else {
                                // Di desktop, pastikan hamburger selalu hidden
                                hamburger.classList.add('hidden');
                            }
                        }
                        // Pantau perubahan class sidebar dan resize
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
                            
                            // Close dropdown when clicking outside
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
                <ol class="flex flex-wrap bg-transparent rounded-lg">
                    <li class="text-sm leading-normal">
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
                        <a class="text-white opacity-50" href="{{ $homeUrl }}">Dashboard</a>
                    </li>
                    <li class="text-sm pl-2 capitalize leading-normal text-white before:float-left before:pr-2 before:text-white before:content-['/']"
                        aria-current="page">
                        @yield('page_title', 'Dashboard')
                    </li>
                </ol>
                <h6 class="mb-0 font-bold text-white capitalize">@yield('page_title', 'Dashboard')</h6>
            </div>
        </div>

        {{-- Branch/Cabang Switcher --}}
        <div class="relative hidden mr-4 xl:block">
            <button id="branch-switcher-btn" type="button"
                class="flex items-center px-4 py-2.5 text-sm font-medium text-white bg-white/20 rounded-xl hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all shadow-lg">
                <i class="fas fa-map-marker-alt mr-2"></i>
                <span id="current-branch-name">{{ Auth::user()->region->name ?? 'Pilih Cabang' }}</span>
                <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
            </button>
            
            <div id="branch-dropdown"
                class="absolute right-0 z-50 hidden w-56 mt-2 bg-white rounded-xl shadow-2xl dark:bg-slate-800 dark:border dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pilih Cabang</p>
                </div>
                <div class="py-2 max-h-64 overflow-y-auto">
                    @php
                        $allRegions = App\Models\Region::all();
                        $currentRegionSlug = Auth::user()->region->slug ?? null;
                    @endphp
                    @foreach($allRegions as $region)
                        <a href="{{ route('admin.dashboard', ['region' => $region->slug]) }}"
                            class="block px-4 py-2.5 text-sm {{ $region->slug === $currentRegionSlug ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }} transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-building mr-3 text-gray-400"></i>
                                {{ $region->name }}
                                @if($region->slug === $currentRegionSlug)
                                    <i class="fas fa-check ml-auto text-blue-500"></i>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Toggle Lightmode - Darkmode --}}
        <!-- Toggle Lightmode/Darkmode: hanya tampil di desktop/tab -->
        <label id="theme-toggle-label-navbar" for="theme-toggle-checkbox-navbar"
            class="relative z-40 inline-flex items-center hidden cursor-pointer xl:inline-flex">
            <input type="checkbox" value="" id="theme-toggle-checkbox-navbar" class="sr-only peer">
            <div class="h-6 bg-gray-200 rounded-full w-11 peer dark:bg-gray-700 peer-checked:bg-blue-600"></div>
            <div
                class="absolute top-0.5 left-[2px] bg-white border-gray-300 border rounded-full h-5 w-5 transition-all peer-checked:translate-x-full flex items-center justify-center">
                <i class="text-sm text-yellow-500 peer-checked:hidden fas fa-sun"></i>
                <i class="text-sm text-blue-400 hidden peer-checked:block fas fa-moon"></i>
            </div>
        </label>

        {{-- Right side: Profile section - visible on all devices with proper margin --}}
        <div class="flex items-center justify-end h-full pr-4">
            <ul class="flex flex-row items-center justify-end h-full pl-0 mb-0 list-none">
                <!-- Avatar with dropdown settings -->
                <li class="relative flex items-center h-full px-3 group">
                    <div class="relative w-10 h-10 overflow-hidden border-2 border-white rounded-full cursor-pointer shadow-lg hover:shadow-xl transition-shadow">
                        @php
                            $avatarSrc = '/assets/icon/admin.png';
                            if (Auth::user() && Auth::user()->hasRole('kurir')) {
                                $avatarSrc = '/assets/icon/kurir.png';
                            }
                        @endphp
                        <img src="{{ asset($avatarSrc) }}" alt="User Avatar" class="object-cover w-full h-full" />
                    </div>
                    <div
                        class="absolute right-0 z-[99999] w-72 mt-3 transition duration-200 ease-out origin-top-right scale-95 bg-white rounded-xl shadow-2xl opacity-0 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto dark:bg-slate-800 dark:border dark:border-slate-700">
                        <div class="px-5 py-4 text-sm text-gray-700 border-b border-gray-200 dark:border-slate-700">
                            @php
                                use Illuminate\Support\Facades\DB;
                                $user = Auth::user();
                                // PERBAIKAN: Mengambil waktu saat ini sesuai timezone region pengguna
                                $timezone = 'Asia/Jakarta'; // Default WIB
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
                                // [!code block:end]
                            @endphp
                            <div class="mb-3">
                                <span class="block text-base font-semibold text-gray-900 dark:text-white">{{ $user->name ?? 'User' }}</span>
                                <span class="block mt-2 text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>Region:
                                    {{ $user->region->name ?? 'Tidak ada region' }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-envelope mr-1 text-blue-500"></i>Email:
                                    {{ $user->email ?? 'Tidak ada email' }}</span>
                            </div>
                            <div class="pt-3 border-t border-gray-100 dark:border-slate-600">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white"><i class="fas fa-clock mr-1 text-blue-500"></i>Last Activity:</span>
                                <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $lastLogin }}</span>
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
                        <a href="{{ $profileUrl }}" class="block px-5 py-3 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-slate-700 transition-colors"><i class="fas fa-user mr-2 text-blue-500"></i>
                            My Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full px-5 py-3 text-sm text-left text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-slate-700 transition-colors"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i>
                                Logout</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
