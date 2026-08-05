@extends('layouts.argon')
@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')

    @php
        use Illuminate\Support\Facades\DB;
        $user = Auth::user();
        $lastSession = DB::table('sessions')->where('user_id', Auth::id())->orderByDesc('last_activity')->first();
        $lastLogin = $lastSession
            ? \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->setTimezone('Asia/Jakarta')
                ->format('d M Y, H:i:s')
            : 'Tidak tersedia';

        date_default_timezone_set('Asia/Jakarta');
        $hour = date('G');
        $greeting = '';
        $iconClass = '';

        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
            $iconClass = 'fas fa-sun text-amber-400';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
            $iconClass = 'fas fa-sun text-orange-400';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
            $iconClass = 'fas fa-cloud-sun text-amber-500';
        } else {
            $greeting = 'Selamat Malam';
            $iconClass = 'fas fa-moon text-indigo-300';
        }
    @endphp

    <div class="space-y-6">

        <!-- 1. Hero Welcome Card with Quick Actions -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-deep via-brand-deep to-brand-deep p-6 sm:p-8 text-white shadow-xl shadow-brand-deep/10">
            <!-- Background Decorative Ornaments -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 bg-brand-light/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold tracking-wide text-brand-light border border-white/20">
                        <i class="{{ $iconClass }}"></i>
                        <span>{{ date('l, d F Y') }}</span>
                        <span class="opacity-60">&bull;</span>
                        <span>Region {{ $user->region->name ?? 'Cabang' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        {{ $greeting }}, <span class="underline decoration-brand-light underline-offset-4">{{ $user->name ?? 'Admin' }}</span>!
                    </h1>
                    <p class="text-xs sm:text-sm text-brand-light/90 max-w-xl">
                        Selamat datang di pusat kendali operasional toko kue. Berikut ringkasan performa penjualan dan aktivitas terkini hari ini.
                    </p>
                </div>

                <!-- Quick Shortcut Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-brand-deep hover:bg-mint text-xs font-bold transition-all shadow-md hover:scale-105 active:scale-95">
                        <i class="fas fa-shopping-bag text-brand-deep"></i>
                        <span>Verifikasi Pesanan</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-brand/30 hover:bg-brand/40 text-white backdrop-blur-md border border-white/20 text-xs font-bold transition-all hover:scale-105 active:scale-95">
                        <i class="fas fa-plus"></i>
                        <span>Katalog Produk</span>
                    </a>
                    <a href="{{ route('admin.historys.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-brand/30 hover:bg-brand/40 text-white backdrop-blur-md border border-white/20 text-xs font-bold transition-all hover:scale-105 active:scale-95">
                        <i class="fas fa-file-pdf"></i>
                        <span>Rekap Transaksi</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Modern 5-KPI Stats Section -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-chart-pie text-brand"></i>
                    <span>Resume Aktivitas Hari Ini</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5">
                <!-- Card 1: Income -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Income</span>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20">
                            <i class="fas fa-wallet text-sm"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">
                            Rp {{ number_format($incomeToday, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold">
                            @if ($incomePercentageChange >= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px]">
                                    <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($incomePercentageChange, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px]">
                                    <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($incomePercentageChange, 1) }}%
                                </span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Sales Today -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Penjualan</span>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <i class="fas fa-shopping-cart text-sm"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalSalesToday, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Order</span>
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold">
                            @if ($salesPercentageChange >= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px]">
                                    <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($salesPercentageChange, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px]">
                                    <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($salesPercentageChange, 1) }}%
                                </span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs bln lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Avg Sales -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Rata-Rata/Bln</span>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">
                            {{ number_format($avgSalesPerMonth, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold">
                            @if ($avgSalesPercentageChange >= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px]">
                                    <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($salesPercentageChange, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px]">
                                    <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($salesPercentageChange, 1) }}%
                                </span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs thn lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Customers in Region -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Customer</span>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">
                            {{ number_format($totalCustomersInRegion, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold">
                            @if ($customerPercentageChange >= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px]">
                                    <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($customerPercentageChange, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px]">
                                    <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($customerPercentageChange, 1) }}%
                                </span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs mgg lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Card 5: New Customers Today -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">New Customer</span>
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-cyan-600 text-white flex items-center justify-center shadow-lg shadow-brand/20">
                            <i class="fas fa-user-plus text-sm"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">
                            +{{ number_format($newCustomersToday, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold">
                            @if ($newCustomerPercentageChange >= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px]">
                                    <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($newCustomerPercentageChange, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px]">
                                    <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($newCustomerPercentageChange, 1) }}%
                                </span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs kemarin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dual Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart 1: Sales Chart -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span>Grafik Penjualan Pesanan</span>
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                            <i class="far text-[10px] fa-calendar-alt mr-1"></i>{{ $dateRangeText }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-3 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Total: {{ $totalOrdersInRange }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-brand-deep dark:text-brand-light">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand"></span> Verif: {{ $totalVerifiedInRange }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Retur: {{ $totalVerifiedWithReturnInRange }}
                            </span>
                        </div>

                        <!-- Dropdown filter -->
                        <div class="relative" x-data="{ openFilter: false }">
                            <button @click="openFilter = !openFilter" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openFilter" @click.away="openFilter = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'filter' => 'last_7_days'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'last_7_days' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">7 Hari Terakhir</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'filter' => 'daily'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'daily' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Harian</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'filter' => 'weekly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'weekly' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Mingguan</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'filter' => 'monthly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'monthly' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Bulanan</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-64 sm:h-72 w-full">
                    <canvas id="adminOrdersChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Visitors Chart -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span>Grafik Kunjungan Web</span>
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                            <i class="far text-[10px] fa-calendar-alt mr-1"></i>{{ $visitDateRangeText }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-600 dark:text-purple-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Total Kunjungan: {{ $totalVisitsInRange }}
                        </span>

                        <div class="relative" x-data="{ openVisitFilter: false }">
                            <button @click="openVisitFilter = !openVisitFilter" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openVisitFilter" @click.away="openVisitFilter = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'visit_filter' => 'last_7_days'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'last_7_days' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">7 Hari Terakhir</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'visit_filter' => 'daily'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'daily' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Harian</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'visit_filter' => 'weekly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'weekly' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Mingguan</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => Auth::user()->region->slug, 'visit_filter' => 'monthly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'monthly' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Bulanan</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-64 sm:h-72 w-full">
                    <canvas id="visitChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. Courier Activity Monitoring Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>
                        <span>Monitoring Tim Kurir</span>
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Daftar kurir aktif terdaftar di Cabang {{ Auth::user()->region->name ?? 'Region' }}
                    </p>
                </div>

                <a href="{{ route('admin.couriers.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-brand-deep dark:text-brand-light bg-mint hover:bg-brand-light dark:bg-brand-deep/50 rounded-xl transition-colors">
                    <span>Lihat Semua Kurir</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                    <thead class="text-[11px] font-extrabold text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-800/80 rounded-xl">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center rounded-l-xl w-12">No</th>
                            <th scope="col" class="px-4 py-3">Nama Kurir</th>
                            <th scope="col" class="px-4 py-3">Email Akun</th>
                            <th scope="col" class="px-4 py-3 text-center rounded-r-xl w-28">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($couriers as $kurir)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-slate-400">
                                    {{ ($couriers->currentPage() - 1) * $couriers->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-light dark:bg-brand-deep text-brand-deep dark:text-brand-light font-extrabold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($kurir->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $kurir->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">
                                    {{ $kurir->email }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-light text-brand-deep dark:bg-brand-deep dark:text-brand-light">
                                        <span class="w-1.5 h-1.5 mr-1 bg-brand rounded-full animate-pulse"></span>
                                        Aktif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada data kurir terdaftar di region ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($couriers->hasPages())
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    {{ $couriers->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Sales Chart
            const ordersCtx = document.getElementById('adminOrdersChart');
            if (ordersCtx && window.Chart) {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                const textColor = isDark ? '#94a3b8' : '#64748b';

                new Chart(ordersCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Total Pesanan',
                                data: @json($chartDataTotal),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointRadius: 3,
                            },
                            {
                                label: 'Diverifikasi',
                                data: @json($chartDataVerified),
                                borderColor: '#6f8f5f',
                                backgroundColor: 'rgba(111, 143, 95, 0.12)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointRadius: 3,
                            },
                            {
                                label: 'Retur',
                                data: @json($chartDataVerifiedWithReturn),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointRadius: 3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor },
                                ticks: { color: textColor, precision: 0 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor }
                            }
                        }
                    }
                });
            }

            // Chart 2: Visitor Chart
            const visitCtx = document.getElementById('visitChart');
            if (visitCtx && window.Chart) {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                const textColor = isDark ? '#94a3b8' : '#64748b';

                new Chart(visitCtx, {
                    type: 'line',
                    data: {
                        labels: @json($visitChartLabels),
                        datasets: [{
                            label: 'Kunjungan Web',
                            data: @json($visitChartData),
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.12)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor },
                                ticks: { color: textColor, precision: 0 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
