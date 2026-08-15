<!-- MODERN EMERALD SIDENAV SYSTEM -->
<aside id="sidebar"
    class="fixed inset-y-0 z-50 flex flex-col w-64 p-0 my-4 overflow-y-hidden antialiased transition-all duration-300 -translate-x-full bg-white/95 backdrop-blur-2xl border border-slate-200/80 shadow-xl group dark:shadow-none dark:bg-slate-800/60 dark:backdrop-blur-2xl dark:border-slate-700/70 ease-nav-brand lg:ml-6 rounded-3xl lg:left-0 lg:translate-x-0"
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
        <div class="p-4 border-b border-slate-100 dark:border-slate-800/80">
            <a id="sidebar-logo-link"
                class="flex items-center gap-3 px-2 py-1.5 text-base rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all dark:text-white"
                href="{{ $dashboardUrl }}">
                <div class="relative flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep shadow-md shadow-brand/20 shrink-0">
                    <img id="sidebar-logo-img" src="{{ asset('assets/homepage/logo.png') }}"
                        class="w-8 h-8 object-contain" alt="main_logo" />
                </div>
                <div class="flex flex-col sidenav-text">
                    <span id="sidebar-logo-text"
                        class="text-sm font-extrabold tracking-tight text-slate-800 dark:text-white leading-tight">
                        Kue Pandan Asli
                    </span>
                    <span class="text-[9px] font-bold tracking-wider text-brand-deep dark:text-brand uppercase mt-0.5">
                        Admin Portal
                    </span>
                </div>
            </a>
        </div>

        {{-- Scrollable Navigation Area --}}
        <div class="flex-grow w-full px-3 py-3 overflow-x-hidden overflow-y-auto space-y-3">
            <ul class="flex flex-col space-y-1 pl-0 mb-0 list-none">

                {{-- Dashboard --}}
                <li class="w-full">
                    <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.dashboard') || request()->routeIs('kurir.dashboard')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                        href="{{ $dashboardUrl }}" data-tooltip="Dashboard">
                        <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.dashboard') || request()->routeIs('kurir.dashboard')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                            <i class="fas fa-th-large text-xs"></i>
                        </div>
                        <span class="sidenav-text">Dashboard Utama</span>
                    </a>
                </li>

                @role('admin|owner')
                    {{-- Management Section --}}
                    <li class="w-full pt-2" x-data="{ openManagement: true }">
                        <button @click="openManagement = !openManagement"
                            class="flex items-center justify-between w-full px-3.5 py-1.5 text-[10px] font-extrabold tracking-widest text-slate-400 dark:text-slate-500 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Manajemen Master</span>
                            <i class="text-[9px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openManagement }"></i>
                        </button>
                        <ul x-show="openManagement" x-transition class="mt-1 space-y-1 list-none">
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.customers.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.customers.index') }}" data-tooltip="Manajemen Customer">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.customers.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-users text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Data Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.products.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.products.index') }}" data-tooltip="Manajemen Produk">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.products.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-box text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Katalog Produk</span>
                                </a>
                            </li>
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.couriers.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.couriers.index') }}" data-tooltip="Manajemen Kurir">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.couriers.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-shipping-fast text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Tim Kurir</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Order & Transaksi Section --}}
                    <li class="w-full pt-2" x-data="{ openOrder: true }">
                        <button @click="openOrder = !openOrder"
                            class="flex items-center justify-between w-full px-3.5 py-1.5 text-[10px] font-extrabold tracking-widest text-slate-400 dark:text-slate-500 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Pesanan & Transaksi</span>
                            <i class="text-[9px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openOrder }"></i>
                        </button>
                        <ul x-show="openOrder" x-transition class="mt-1 space-y-1 list-none">
                            <li>
                                <a class="relative group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.orders.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.orders.index') }}" data-tooltip="Pesanan">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.orders.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-shopping-bag text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Verifikasi Pesanan</span>
                                    @if (isset($newOrdersCount) && $newOrdersCount > 0)
                                        <span id="order-badge"
                                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-extrabold text-white bg-rose-500 rounded-full shadow-md">
                                            {{ $newOrdersCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.historys.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.historys.index') }}" data-tooltip="History Pesanan">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.historys.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Riwayat Transaksi</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Analisis & Performa --}}
                    <li class="w-full pt-2" x-data="{ openPerforma: true }">
                        <button @click="openPerforma = !openPerforma"
                            class="flex items-center justify-between w-full px-3.5 py-1.5 text-[10px] font-extrabold tracking-widest text-slate-400 dark:text-slate-500 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Laporan & Performa</span>
                            <i class="text-[9px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': openPerforma }"></i>
                        </button>
                        <ul x-show="openPerforma" x-transition class="mt-1 space-y-1 list-none">
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.peforma-customer.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.peforma-customer.index') }}" data-tooltip="Performa Customer">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.peforma-customer.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-chart-line text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Performa Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.peforma-kurir.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('admin.peforma-kurir.index') }}" data-tooltip="Performa Kurir">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.peforma-kurir.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
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
                            class="flex items-center justify-between w-full px-3.5 py-1.5 text-[10px] font-extrabold tracking-widest text-slate-400 dark:text-slate-500 uppercase transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                            <span>Menu Operasional</span>
                            <i class="text-[9px] transition-transform duration-300 fas fa-chevron-down"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 space-y-1 list-none">
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('kurir.customers.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('kurir.customers.index') }}" data-tooltip="Data Customer">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('kurir.customers.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-address-book text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Data Customer</span>
                                </a>
                            </li>
                            <li>
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('kurir.products.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('kurir.products.index') }}" data-tooltip="Katalog Produk">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('kurir.products.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-box text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">Katalog Produk</span>
                                </a>
                            </li>
                            <li>
                                <a class="relative group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('kurir.pesanan.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('kurir.pesanan.index') }}" data-tooltip="Order Tracking">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('kurir.pesanan.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
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
                                <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('kurir.historys.*')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                                    href="{{ route('kurir.historys.index') }}" data-tooltip="Order History">
                                    <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('kurir.historys.*')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                                        <i class="fas fa-history text-xs"></i>
                                    </div>
                                    <span class="sidenav-text">History Pengiriman</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                {{-- Account & Logout --}}
                <li class="w-full pt-2">
                    <a class="group py-2.5 px-3.5 text-xs font-bold ease-nav-brand flex items-center whitespace-nowrap rounded-2xl transition-all duration-200 @if (request()->routeIs('admin.profile') || request()->routeIs('kurir.profile')) bg-gradient-to-r from-brand-deep to-brand text-white shadow-md shadow-brand-deep/20 @else text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white hover:translate-x-1 @endif"
                        href="{{ (Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner')) ? route('admin.profile') : route('kurir.profile') }}" data-tooltip="Profil Saya">
                        <div class="mr-3 flex items-center justify-center w-8 h-8 rounded-xl shrink-0 @if (request()->routeIs('admin.profile') || request()->routeIs('kurir.profile')) bg-white/20 text-white @else bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-brand/15 group-hover:text-brand-deep dark:group-hover:text-brand @endif transition-colors">
                            <i class="fas fa-user-cog text-xs"></i>
                        </div>
                        <span class="sidenav-text">Pengaturan Akun</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Bottom CTA Box --}}
        <div class="p-3.5 m-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-extrabold text-brand-deep bg-brand/10 dark:bg-brand-deep/60 dark:text-brand-light rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span> Cabang Active
                </span>
            </div>
            <p class="text-xs font-bold text-slate-800 dark:text-white mt-1">
                {{ \App\Support\RegionContext::name() ?? 'Cabang Utama' }}
            </p>
        </div>

        {{-- Sidenav Toggler Footer --}}
        <div class="flex items-center justify-between p-3 border-t border-slate-100 dark:border-slate-800">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center w-full py-2.5 px-3 text-xs font-extrabold text-rose-600 bg-rose-50/80 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 rounded-2xl border border-rose-200/40 dark:border-rose-900/40 transition-all active:scale-95">
                    <i class="fas fa-sign-out-alt mr-2 text-rose-500"></i>
                    <span>Keluar Aplikasi</span>
                </button>
            </form>
        </div>
    @endauth
</aside>
