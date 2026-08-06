<!-- MODERN EMERALD SIDENAV SYSTEM -->
<aside id="sidebar"
    class="fixed inset-y-0 z-50 flex flex-col w-64 p-0 my-4 overflow-y-hidden antialiased transition-all duration-300 -translate-x-full bg-white/98 backdrop-blur-xl border border-slate-200/70 shadow-2xl group dark:shadow-none dark:bg-slate-900/98 dark:border-slate-800 ease-nav-brand xl:ml-6 rounded-3xl xl:left-0 xl:translate-x-0"
    aria-expanded="false">

    @auth
        @php
            $user = Auth::user();
            $regionName = ucwords(strtolower(\App\Support\RegionContext::name() ?? ''));
            $dashboardUrl = url('/dashboard');

            if ($user->hasRole('owner')) {
                $regionSlug = \App\Support\RegionContext::slug();
            } elseif ($user->region) {
                $regionSlug = $user->region->slug;
                if ($user->hasRole('admin')) {
                    $dashboardUrl = route('admin.dashboard', ['region' => $regionSlug]);
                } elseif ($user->hasRole('kurir')) {
                    $dashboardUrl = route('kurir.dashboard', ['region' => $regionSlug]);
                }
            }
        @endphp

        {{-- Brand Logo Header --}}
        <div class="p-5 border-b border-slate-100 dark:border-slate-800">
            <a id="sidebar-logo-link"
                class="flex items-center gap-3 px-3 py-2.5 text-base rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all dark:text-white"
                href="{{ $dashboardUrl }}">
                <div class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gradient-to-br from-brand to-brand-deep shadow-lg shadow-brand/20">
                    <img id="sidebar-logo-img" src="{{ asset('assets/homepage/logo.png') }}"
                        class="w-8 h-8 object-contain" alt="main_logo" />
                </div>
                <div class="flex flex-col sidenav-text">
                    <span id="sidebar-logo-text"
                        class="text-base font-extrabold tracking-tight text-slate-800 dark:text-white leading-tight">
                        Kue Pandan Asli
                    </span>
                    <span class="text-[10px] font-semibold tracking-wider text-brand-deep dark:text-brand uppercase">
                        Admin Portal
                    </span>
                </div>
            </a>
        </div>

        {{-- Scrollable Navigation Area --}}
        <div class="flex-grow w-full px-3 py-4 overflow-x-hidden overflow-y-auto space-y-4">
            <ul class="flex flex-col space-y-1 pl-0 mb-0 list-none">

                {{-- Dashboard --}}
                <li class="w-full">
                    <a class="py-3 text-sm font-bold ease-nav-brand my-0.5 flex items-center whitespace-nowrap rounded-xl px-4 transition-all @if (request()->routeIs('admin.dashboard') || request()->routeIs('kurir.dashboard')) bg-brand-deep text-white font-semibold shadow-lg shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                        href="{{ $dashboardUrl }}" data-tooltip="Dashboard">
                        <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg @if (request()->routeIs('admin.dashboard') || request()->routeIs('kurir.dashboard')) bg-white/20 text-white @else bg-mint text-brand-deep @endif">
                            <i class="fas fa-th-large text-sm"></i>
                        </div>
                        <span class="duration-300 opacity-100 pointer-events-none ease sidenav-text">Dashboard Utama</span>
                    </a>
                </li>

                @role('admin|owner')
                    {{-- Management Section -->
                    <li class="w-full pt-3" x-data="{ openManagement: true }">
                        <button @click="openManagement = !openManagement"
                            class="flex items-center justify-between w-full px-4 py-2 text-xs font-bold tracking-wider text-slate-400 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Manajemen Master</span>
                            <i class="text-[10px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openManagement }"></i>
                        </button>
                        <ul x-show="openManagement" x-transition class="mt-2 space-y-1 list-none">
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.customers.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.customers.index') }}" data-tooltip="Manajemen Customer">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-users text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Data Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.products.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.products.index') }}" data-tooltip="Manajemen Produk">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-box text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Katalog Produk</span>
                                </a>
                            </li>
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.couriers.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.couriers.index') }}" data-tooltip="Manajemen Kurir">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-shipping-fast text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Tim Kurir</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Order & Transaksi Section -->
                    <li class="w-full pt-3" x-data="{ openOrder: true }">
                        <button @click="openOrder = !openOrder"
                            class="flex items-center justify-between w-full px-4 py-2 text-xs font-bold tracking-wider text-slate-400 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Pesanan & Transaksi</span>
                            <i class="text-[10px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openOrder }"></i>
                        </button>
                        <ul x-show="openOrder" x-transition class="mt-2 space-y-1 list-none">
                            <li>
                                <a class="relative py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.orders.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.orders.index') }}" data-tooltip="Pesanan">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-shopping-bag text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Verifikasi Pesanan</span>
                                    @if (isset($newOrdersCount) && $newOrdersCount > 0)
                                        <span id="order-badge"
                                            class="ml-auto inline-flex items-center justify-center px-2.5 py-0.5 text-[10px] font-extrabold text-white bg-rose-500 rounded-full shadow-md">
                                            {{ $newOrdersCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.historys.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.historys.index') }}" data-tooltip="History Pesanan">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Riwayat Transaksi</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Analisis & Performa -->
                    <li class="w-full pt-3" x-data="{ openPerforma: true }">
                        <button @click="openPerforma = !openPerforma"
                            class="flex items-center justify-between w-full px-4 py-2 text-xs font-bold tracking-wider text-slate-400 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Laporan & Performa</span>
                            <i class="text-[10px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openPerforma }"></i>
                        </button>
                        <ul x-show="openPerforma" x-transition class="mt-2 space-y-1 list-none">
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.peforma-customer.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.peforma-customer.index') }}" data-tooltip="Performa Customer">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-chart-line text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Performa Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.peforma-kurir.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('admin.peforma-kurir.index') }}" data-tooltip="Performa Kurir">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-chart-bar text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Performa Kurir</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                @role('kurir')
                    {{-- Kurir Menu --}}
                    <li class="w-full pt-2" x-data="{ open: true }">
                        <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-1.5 text-[11px] font-bold tracking-wider text-slate-400 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Menu Operasional</span>
                            <i class="text-[10px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 space-y-1 list-none">
                            <li>
                                <a class="py-2 text-xs ease-nav-brand flex items-center whitespace-nowrap px-3.5 rounded-xl transition-all @if (request()->routeIs('kurir.customers.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('kurir.customers.index') }}" data-tooltip="Data Customer">
                                    <div class="mr-3 flex items-center justify-center w-7 h-7 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-address-book text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Data Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="py-2 text-xs ease-nav-brand flex items-center whitespace-nowrap px-3.5 rounded-xl transition-all @if (request()->routeIs('kurir.products.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('kurir.products.index') }}" data-tooltip="Katalog Produk">
                                    <div class="mr-3 flex items-center justify-center w-7 h-7 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-box text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Katalog Produk</span>
                                </a>
                            </li>
                            <li>
                                <a class="relative py-2 text-xs ease-nav-brand flex items-center whitespace-nowrap px-3.5 rounded-xl transition-all @if (request()->routeIs('kurir.pesanan.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('kurir.pesanan.index') }}" data-tooltip="Order Tracking">
                                    <div class="mr-3 flex items-center justify-center w-7 h-7 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-truck text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Input & Tracking Order</span>
                                    @if (isset($rejectedOrdersCount) && $rejectedOrdersCount > 0)
                                        <span id="reject-badge"
                                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-extrabold text-white bg-rose-500 rounded-full shadow-sm">
                                            {{ $rejectedOrdersCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="py-2 text-xs ease-nav-brand flex items-center whitespace-nowrap px-3.5 rounded-xl transition-all @if (request()->routeIs('kurir.historys.*')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                                    href="{{ route('kurir.historys.index') }}" data-tooltip="Order History">
                                    <div class="mr-3 flex items-center justify-center w-7 h-7 rounded-lg bg-mint text-brand-deep dark:text-brand">
                                        <i class="fas fa-history text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">History Pengiriman</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                {{-- Account & Logout --}}
                <li class="w-full pt-3">
                    <a class="py-2.5 text-sm ease-nav-brand flex items-center whitespace-nowrap px-4 rounded-xl transition-all @if (request()->routeIs('admin.profile') || request()->routeIs('kurir.profile')) bg-mint text-brand-deep dark:bg-brand-deep/40 dark:text-brand font-bold @else text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 @endif"
                        href="{{ (Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner')) ? route('admin.profile') : route('kurir.profile') }}" data-tooltip="Profil Saya">
                        <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-lg bg-mint text-brand-deep dark:text-brand">
                            <i class="fas fa-user-cog text-xs"></i>
                        </div>
                        <span class="sidenav-text">Pengaturan Akun</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Bottom CTA Box --}}
        <div class="p-4 m-3 rounded-2xl bg-mint border border-brand-deep/10 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold text-brand-deep bg-white/80 rounded-md dark:bg-brand-deep dark:text-brand">
                    <i class="fas fa-shield-alt mr-1"></i> Cabang Active
                </span>
            </div>
            <p class="text-sm font-bold text-slate-800 dark:text-white">
                {{ \App\Support\RegionContext::name() ?? 'Cabang Utama' }}
            </p>
        </div>

        {{-- Sidenav Toggler Footer --}}
        <div class="flex items-center justify-between p-4 border-t border-slate-100 dark:border-slate-800">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center w-full py-2.5 px-3 text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-900/40 rounded-xl transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    @endauth
</aside>
