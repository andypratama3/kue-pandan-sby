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
            $iconClass = 'fas fa-moon text-brand-light';
        }
    @endphp

    <div class="space-y-6" x-data="dashboardApp()">
        
        <!-- Pending Orders Notification -->
        @php
            $pendingOrdersCount = \App\Models\Order::where('region_id', \App\Support\RegionContext::regionId())
                ->whereIn('status', ['menunggu_verifikasi_admin', 'selesai'])
                ->count();
        @endphp
        
        @if($pendingOrdersCount > 0)
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 dark:from-amber-950/20 dark:via-orange-950/20 dark:to-yellow-950/20 border border-amber-200 dark:border-amber-900/50 p-4 shadow-lg"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center animate-pulse">
                        <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                        Ada {{ $pendingOrdersCount }} pesanan menunggu verifikasi!
                    </h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                        Segera verifikasi pesanan untuk memastikan proses pengiriman berjalan lancar.
                    </p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all hover:scale-105 active:scale-95 flex-1 sm:flex-initial">
                        <i class="fas fa-check-circle"></i>
                        <span>Verifikasi Sekarang</span>
                    </a>
                    <button @click="show = false" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 p-2 hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Animated pulse indicator -->
            <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none">
                <div class="absolute inset-0 bg-amber-400/20 rounded-full animate-ping"></div>
            </div>
        </div>
        @endif
        
        <!-- Hidden data for Alpine.js -->
        <template x-if="false">
            <div>
                <span x-text="JSON.stringify({chartLabels: @json($chartLabels), chartDataTotal: @json($chartDataTotal), chartDataVerified: @json($chartDataVerified)})"></span>
            </div>
        </template>

        <!-- 1. Hero Welcome Card with Quick Actions -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-deep via-brand to-brand-deep p-6 sm:p-8 text-white shadow-2xl shadow-brand-deep/30 border border-white/10">
            <!-- Animated Background Decorative Ornaments -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 bg-brand-light/30 rounded-full blur-2xl pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute left-20 top-1/2 w-32 h-32 bg-brand/20 rounded-full blur-xl pointer-events-none animate-pulse" style="animation-delay: 0.5s;"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-xl text-xs font-bold tracking-wide text-brand-light border border-white/30 shadow-lg">
                        <i :class="'{{ $iconClass }}'" class="{{ $iconClass }}"></i>
                        <span>{{ date('l, d F Y') }}</span>
                        <span class="opacity-60">&bull;</span>
                        <span>Region {{ \App\Support\RegionContext::name() }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                        {{ $greeting }}, <span class="underline decoration-2 decoration-brand-light underline-offset-8">{{ $user->name ?? 'Admin' }}</span>!
                    </h1>
                    <p class="text-sm sm:text-base text-brand-light/95 max-w-xl leading-relaxed">
                        Selamat datang di pusat kendali operasional toko kue. Berikut ringkasan performa penjualan dan aktivitas terkini hari ini.
                    </p>
                </div>

                <!-- Quick Shortcut Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-white text-brand-deep hover:bg-mint hover:shadow-xl text-xs font-bold transition-all duration-300 shadow-lg hover:scale-105 active:scale-95 border border-white/20 group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        <i class="fas fa-shopping-bag text-brand-deep relative z-10"></i>
                        <span class="relative z-10">Verifikasi Pesanan</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-white/20 hover:bg-white/30 text-white backdrop-blur-xl border border-white/30 text-xs font-bold transition-all duration-300 shadow-lg hover:scale-105 active:scale-95 group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        <i class="fas fa-plus relative z-10"></i>
                        <span class="relative z-10">Katalog Produk</span>
                    </a>
                    <a href="{{ route('admin.historys.index') }}"
                        class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-white/20 hover:bg-white/30 text-white backdrop-blur-xl border border-white/30 text-xs font-bold transition-all duration-300 shadow-lg hover:scale-105 active:scale-95 group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        <i class="fas fa-file-pdf relative z-10"></i>
                        <span class="relative z-10">Rekap Transaksi</span>
                    </a>
                    
                    <!-- Refresh Button -->
                    <button @click="refreshDashboard" 
                            :disabled="isRefreshing"
                            class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-white/20 hover:bg-white/30 text-white backdrop-blur-xl border border-white/30 text-xs font-bold transition-all duration-300 shadow-lg hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group relative overflow-hidden">
                        <i class="fas fa-sync-alt relative z-10" :class="{'animate-spin': isRefreshing}"></i>
                        <span class="relative z-10" x-text="isRefreshing ? 'Memuat...' : 'Refresh Data'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Ringkasan Per Cabang (khusus Owner) -->
        @if ($user->hasRole('owner') && isset($branchSummary) && $branchSummary->isNotEmpty())
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-store text-sm"></i>
                        </div>
                        <span>Pantauan Semua Cabang</span>
                    </h2>
                    <span class="text-[11px] font-bold text-brand-deep dark:text-brand hover:underline cursor-pointer">
                        <i class="fas fa-sync-alt mr-1"></i> Live Monitoring
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-{{ min($branchSummary->count(), 4) }} gap-4">
                    @foreach ($branchSummary as $branch)
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all hover:shadow-lg group {{ $branch['region']->id === \App\Support\RegionContext::regionId() ? 'bg-mint/50 dark:bg-brand-deep/30 border-brand/30' : 'bg-slate-50/70 dark:bg-slate-800/40' }}"
                             x-data="{ isHovered: false }"
                             @mouseenter="isHovered = true"
                             @mouseleave="isHovered = false">
                            
                            <!-- Animated border on hover -->
                            <div class="absolute inset-0 rounded-2xl border-2 border-brand opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-brand-deep text-white flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-store text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $branch['region']->name }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">
                                            {{ $branch['region']->id === \App\Support\RegionContext::regionId() ? 'Cabang Aktif' : 'Cabang Lain' }}
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.switch-region', ['region' => $branch['region']->slug]) }}">
                                    @csrf
                                    <button type="submit"
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-brand-deep dark:text-brand hover:bg-mint hover:scale-105 transition-all duration-200 inline-flex items-center gap-1">
                                        <span>Buka</span>
                                        <i class="fas fa-arrow-right text-[9px]"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-center">
                                <div class="rounded-xl bg-white dark:bg-slate-800 p-2.5 transform group-hover:scale-105 transition-transform duration-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Income Hari Ini</p>
                                    <p class="text-sm font-extrabold text-brand-deep dark:text-brand">Rp {{ number_format($branch['income_today'], 0, ',', '.') }}</p>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-slate-800 p-2.5 transform group-hover:scale-105 transition-transform duration-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Order Hari Ini</p>
                                    <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $branch['orders_today'] }}</p>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-slate-800 p-2.5 transform group-hover:scale-105 transition-transform duration-200 relative">
                                    @if($branch['pending_verify'] > 0)
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full animate-ping"></span>
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full"></span>
                                    @endif
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Menunggu Verifikasi</p>
                                    <p class="text-sm font-extrabold {{ $branch['pending_verify'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-white' }}">{{ $branch['pending_verify'] }}</p>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-slate-800 p-2.5 transform group-hover:scale-105 transition-transform duration-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Customer / Kurir</p>
                                    <p class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $branch['customers'] }} / {{ $branch['couriers'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Modern 5-KPI Stats Section with Animations -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20">
                        <i class="fas fa-chart-pie text-sm"></i>
                    </div>
                    <span>Resume Aktivitas Hari Ini</span>
                </h2>
                
                <!-- Live indicator -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-light/30 dark:bg-brand-deep/30 text-xs font-semibold text-brand-deep dark:text-brand-light">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-deep"></span>
                    </span>
                    <span>Real-time</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5">
                <!-- Card 1: Income -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                     x-data="{ showTooltip: false }"
                     @mouseenter="showTooltip = true"
                     @mouseleave="showTooltip = false">
                    
                    <!-- Animated gradient background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-brand/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-brand/5 to-brand-deep/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Tooltip -->
                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap z-10">
                        Total pendapatan hari ini
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-slate-900 dark:bg-slate-700"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Income</span>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-wallet text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white transform group-hover:scale-105 transition-transform duration-300">
                                Rp {{ number_format($incomeToday, 0, ',', '.') }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs font-semibold">
                                @if ($incomePercentageChange >= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($incomePercentageChange, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($incomePercentageChange, 1) }}%
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs kemarin</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Sales Today -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                     x-data="{ showTooltip: false }"
                     @mouseenter="showTooltip = true"
                     @mouseleave="showTooltip = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-light/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-brand-light/5 to-brand/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap z-10">
                        Total penjualan bulan ini
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-slate-900 dark:bg-slate-700"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Penjualan</span>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-light to-brand text-white flex items-center justify-center shadow-lg shadow-brand/20 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-shopping-cart text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white transform group-hover:scale-105 transition-transform duration-300">
                                {{ number_format($totalSalesToday, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Order</span>
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs font-semibold">
                                @if ($salesPercentageChange >= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($salesPercentageChange, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($salesPercentageChange, 1) }}%
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs bln lalu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Avg Sales -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                     x-data="{ showTooltip: false }"
                     @mouseenter="showTooltip = true"
                     @mouseleave="showTooltip = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-brand/5 to-brand-deep/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap z-10">
                        Rata-rata penjualan per bulan
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-slate-900 dark:bg-slate-700"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Rata-Rata/Bln</span>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand-deep/20 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white transform group-hover:scale-105 transition-transform duration-300">
                                {{ number_format($avgSalesPerMonth, 0, ',', '.') }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs font-semibold">
                                @if ($avgSalesPercentageChange >= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($avgSalesPercentageChange, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($avgSalesPercentageChange, 1) }}%
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs bln lalu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Customers in Region -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                     x-data="{ showTooltip: false }"
                     @mouseenter="showTooltip = true"
                     @mouseleave="showTooltip = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-light/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-brand-light/5 to-brand-deep/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap z-10">
                        Total customer di region
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-slate-900 dark:bg-slate-700"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Customer</span>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-light to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand-deep/20 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white transform group-hover:scale-105 transition-transform duration-300">
                                {{ number_format($totalCustomersInRegion, 0, ',', '.') }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs font-semibold">
                                @if ($customerPercentageChange >= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($customerPercentageChange, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($customerPercentageChange, 1) }}%
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs mgg lalu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: New Customers Today -->
                <div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                     x-data="{ showTooltip: false }"
                     @mouseenter="showTooltip = true"
                     @mouseleave="showTooltip = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-brand/5 to-brand-deep/5 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap z-10">
                        Customer baru hari ini
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-slate-900 dark:bg-slate-700"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">New Customer</span>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand-deep/20 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white transform group-hover:scale-105 transition-transform duration-300">
                                +{{ number_format($newCustomersToday, 0, ',', '.') }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs font-semibold">
                                @if ($newCustomerPercentageChange >= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-light dark:bg-brand-deep/60 text-brand-deep dark:text-brand-light text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-up text-[9px] mr-1"></i>+{{ number_format($newCustomerPercentageChange, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-[11px] transform group-hover:scale-105 transition-transform">
                                        <i class="fas fa-arrow-down text-[9px] mr-1"></i>{{ number_format($newCustomerPercentageChange, 1) }}%
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">vs kemarin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Dual Charts Section with Enhanced Interactivity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart 1: Sales Chart -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300"
                 x-data="{ showExportMenu: false, chartLoading: false }">
                
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
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
                            <span class="inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span> Total: {{ $totalOrdersInRange }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-brand-deep dark:text-brand-light cursor-pointer hover:text-brand-deep dark:hover:text-brand transition-colors">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand"></span> Verif: {{ $totalVerifiedInRange }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 cursor-pointer hover:text-rose-700 dark:hover:text-rose-300 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Retur: {{ $totalVerifiedWithReturnInRange }}
                            </span>
                        </div>

                        <!-- Export Button -->
                        <div class="relative">
                            <button @click="showExportMenu = !showExportMenu" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <div x-show="showExportMenu" @click.away="showExportMenu = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <button @click="exportChart('sales', 'png')" class="w-full text-left px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2">
                                    <i class="fas fa-image text-brand-deep"></i>
                                    <span>Export PNG</span>
                                </button>
                                <button @click="exportChart('sales', 'pdf')" class="w-full text-left px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2">
                                    <i class="fas fa-file-pdf text-rose-600"></i>
                                    <span>Export PDF</span>
                                </button>
                                <button @click="exportData('sales', 'csv')" class="w-full text-left px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2">
                                    <i class="fas fa-file-csv text-brand"></i>
                                    <span>Export CSV</span>
                                </button>
                            </div>
                        </div>

                        <!-- Dropdown filter -->
                        <div class="relative" x-data="{ openFilter: false }">
                            <button @click="openFilter = !openFilter" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openFilter" @click.away="openFilter = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'filter' => 'last_7_days'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'last_7_days' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">7 Hari Terakhir</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'filter' => 'daily'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'daily' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Harian</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'filter' => 'weekly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'weekly' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Mingguan</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'filter' => 'monthly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $filter === 'monthly' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Bulanan</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading skeleton -->
                <div x-show="chartLoading" class="h-64 sm:h-72 w-full flex items-center justify-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 border-4 border-brand/20 border-t-brand-deep rounded-full animate-spin"></div>
                        <span class="text-xs font-bold text-brand-deep dark:text-brand">Memuat Grafik...</span>
                    </div>
                </div>

                <div x-show="!chartLoading" class="h-64 sm:h-72 w-full">
                    <canvas id="adminOrdersChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Visitors Chart -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300"
                 x-data="{ showExportMenu: false }">
                
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span>Grafik Kunjungan Web</span>
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                            <i class="far text-[10px] fa-calendar-alt mr-1"></i>{{ $visitDateRangeText }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-deep dark:text-brand-light">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand"></span> Total Kunjungan: {{ $totalVisitsInRange }}
                        </span>

                        <!-- Export Button -->
                        <div class="relative">
                            <button @click="showExportMenu = !showExportMenu" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <div x-show="showExportMenu" @click.away="showExportMenu = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <button @click="exportChart('visitors', 'png')" class="w-full text-left px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2">
                                    <i class="fas fa-image text-brand-deep"></i>
                                    <span>Export PNG</span>
                                </button>
                                <button @click="exportData('visitors', 'csv')" class="w-full text-left px-4 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 flex items-center gap-2">
                                    <i class="fas fa-file-csv text-brand"></i>
                                    <span>Export CSV</span>
                                </button>
                            </div>
                        </div>

                        <div class="relative" x-data="{ openVisitFilter: false }">
                            <button @click="openVisitFilter = !openVisitFilter" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openVisitFilter" @click.away="openVisitFilter = false" x-transition
                                class="absolute right-0 z-30 w-44 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1 text-xs">
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'visit_filter' => 'last_7_days'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'last_7_days' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">7 Hari Terakhir</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'visit_filter' => 'daily'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'daily' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Harian</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'visit_filter' => 'weekly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'weekly' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Mingguan</a>
                                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['region' => \App\Support\RegionContext::slug(), 'visit_filter' => 'monthly'])) }}"
                                    class="block px-4 py-2 font-medium {{ $visitFilter === 'monthly' ? 'bg-brand-light/60 text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60' }}">Bulanan</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-64 sm:h-72 w-full">
                    <canvas id="visitChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 5. Courier Activity Monitoring Card with Enhanced Features -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>
                        <span>Monitoring Tim Kurir</span>
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Daftar kurir aktif terdaftar di Cabang {{ \App\Support\RegionContext::name() }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-brand-light/30 dark:bg-brand-deep/30 text-xs font-semibold text-brand-deep dark:text-brand-light">
                        <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                        <span>{{ $couriers->total() }} Kurir Aktif</span>
                    </div>
                    
                    <a href="{{ route('admin.couriers.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-brand-deep dark:text-brand-light bg-mint hover:bg-brand-light dark:bg-brand-deep/50 rounded-xl transition-all hover:scale-105 active:scale-95">
                        <span>Lihat Semua</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Enhanced Table with Better UX -->
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
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60 transition-all duration-200 group cursor-pointer"
                                x-data="{ showActions: false }"
                                @mouseenter="showActions = true"
                                @mouseleave="showActions = false">
                                
                                <td class="px-4 py-3.5 text-center font-bold text-slate-400">
                                    {{ ($couriers->currentPage() - 1) * $couriers->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-light dark:bg-brand-deep text-brand-deep dark:text-brand-light font-extrabold flex items-center justify-center text-xs transform group-hover:scale-110 transition-transform duration-200">
                                            {{ strtoupper(substr($kurir->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="block">{{ $kurir->name }}</span>
                                            <span class="block text-[10px] text-slate-400 group-hover:text-slate-500 transition-colors">
                                                ID: #{{ str_pad($kurir->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-[10px] text-slate-400"></i>
                                        <span>{{ $kurir->email }}</span>
                                    </div>
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
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            <i class="fas fa-user-slash text-2xl text-slate-300 dark:text-slate-600"></i>
                                        </div>
                                        <p class="text-slate-400 font-medium">Belum ada data kurir terdaftar di region ini.</p>
                                        <a href="{{ route('admin.couriers.create') }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-brand-deep hover:bg-brand rounded-xl transition-colors">
                                            <i class="fas fa-plus"></i>
                                            <span>Tambah Kurir Baru</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($couriers->hasPages())
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-slate-400">
                        Menampilkan {{ $couriers->firstItem() }} - {{ $couriers->lastItem() }} dari {{ $couriers->total() }} kurir
                    </p>
                    {{ $couriers->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1" integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0" integrity="sha384-M00GJNq2IplZCB3+JOJEl2H0un45ODvqJSSnIc4DvG8gPn8RX5ToITFXcv3AvOx5" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script>
        // Alpine.js Dashboard Application
        function dashboardApp() {
            return {
                isRefreshing: false,
                lastUpdate: new Date(),
                
                init() {
                    // Auto-refresh setiap 5 menit
                    this.startAutoRefresh();
                    
                    // Update timestamp setiap menit
                    setInterval(() => {
                        this.lastUpdate = new Date();
                    }, 60000);
                    
                    // Initialize charts
                    this.initCharts();
                    
                    // Show welcome notification
                    this.showWelcomeNotification();
                },
                
                // Auto-refresh functionality
                startAutoRefresh() {
                    setInterval(() => {
                        if (!document.hidden && !this.isRefreshing) {
                            this.refreshDashboard();
                        }
                    }, 300000); // 5 minutes
                },
                
                // Manual refresh
                async refreshDashboard() {
                    if (this.isRefreshing) return;
                    
                    this.isRefreshing = true;
                    
                    try {
                        // Refresh dengan reload halaman (bisa diganti dengan AJAX)
                        window.location.reload();
                    } catch (error) {
                        console.error('Error refreshing dashboard:', error);
                        this.showNotification('Gagal memperbarui data', 'error');
                    } finally {
                        this.isRefreshing = false;
                    }
                },
                
                // Chart initialization
                initCharts() {
                    this.initOrdersChart();
                    this.initVisitChart();
                },
                
                // Orders Chart
                initOrdersChart() {
                    const ordersCtx = document.getElementById('adminOrdersChart');
                    if (!ordersCtx || !window.Chart) return;
                    
                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                    const textColor = isDark ? '#94a3b8' : '#64748b';
                    
                    // Destroy existing chart if exists
                    if (window.adminOrdersChartInstance) {
                        window.adminOrdersChartInstance.destroy();
                    }
                    
                    window.adminOrdersChartInstance = new Chart(ordersCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [
                                {
                                    label: 'Total Pesanan',
                                    data: @json($chartDataTotal),
                                    borderColor: '#64748b',
                                    backgroundColor: 'rgba(100, 116, 139, 0.12)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#64748b',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                },
                                {
                                    label: 'Diverifikasi',
                                    data: @json($chartDataVerified),
                                    borderColor: '#6f8f5f',
                                    backgroundColor: 'rgba(111, 143, 95, 0.12)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#6f8f5f',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                },
                                {
                                    label: 'Retur',
                                    data: @json($chartDataVerifiedWithReturn),
                                    borderColor: '#f43f5e',
                                    backgroundColor: 'rgba(244, 63, 94, 0.12)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#f43f5e',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: { 
                                    display: true,
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: {
                                            size: 11,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#1e293b' : '#fff',
                                    titleColor: isDark ? '#f1f5f9' : '#1e293b',
                                    bodyColor: isDark ? '#cbd5e1' : '#64748b',
                                    borderColor: isDark ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: true,
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + ' pesanan';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: gridColor },
                                    ticks: { 
                                        color: textColor, 
                                        precision: 0,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { 
                                        color: textColor,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                },
                
                // Visit Chart
                initVisitChart() {
                    const visitCtx = document.getElementById('visitChart');
                    if (!visitCtx || !window.Chart) return;
                    
                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                    const textColor = isDark ? '#94a3b8' : '#64748b';
                    
                    // Destroy existing chart if exists
                    if (window.visitChartInstance) {
                        window.visitChartInstance.destroy();
                    }
                    
                    window.visitChartInstance = new Chart(visitCtx, {
                        type: 'line',
                        data: {
                            labels: @json($visitChartLabels),
                            datasets: [{
                                label: 'Kunjungan Web',
                                data: @json($visitChartData),
                                borderColor: '#6f8f5f',
                                backgroundColor: 'rgba(111, 143, 95, 0.12)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#6f8f5f',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: { 
                                    display: true,
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: {
                                            size: 11,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#1e293b' : '#fff',
                                    titleColor: isDark ? '#f1f5f9' : '#1e293b',
                                    bodyColor: isDark ? '#cbd5e1' : '#64748b',
                                    borderColor: isDark ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Kunjungan: ' + context.parsed.y;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: gridColor },
                                    ticks: { 
                                        color: textColor, 
                                        precision: 0,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { 
                                        color: textColor,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                },
                
                // Export chart as PNG
                async exportChart(chartType, format) {
                    const canvasId = chartType === 'sales' ? 'adminOrdersChart' : 'visitChart';
                    const canvas = document.getElementById(canvasId);
                    
                    if (!canvas) {
                        this.showNotification('Grafik tidak ditemukan', 'error');
                        return;
                    }
                    
                    try {
                        const chart = chartType === 'sales' ? window.adminOrdersChartInstance : window.visitChartInstance;
                        
                        if (format === 'png') {
                            const link = document.createElement('a');
                            link.download = `grafik-${chartType}-${new Date().toISOString().split('T')[0]}.png`;
                            link.href = chart.toBase64Image();
                            link.click();
                            this.showNotification('Grafik berhasil di-export sebagai PNG', 'success');
                        } else if (format === 'pdf') {
                            const { jsPDF } = window.jspdf;
                            const pdf = new jsPDF('l', 'mm', 'a4');
                            const imgData = chart.toBase64Image();
                            
                            pdf.addImage(imgData, 'PNG', 10, 10, 277, 140);
                            pdf.save(`grafik-${chartType}-${new Date().toISOString().split('T')[0]}.pdf`);
                            this.showNotification('Grafik berhasil di-export sebagai PDF', 'success');
                        }
                    } catch (error) {
                        console.error('Error exporting chart:', error);
                        this.showNotification('Gagal meng-export grafik', 'error');
                    }
                },
                
                // Export data as CSV
                exportData(dataType, format) {
                    try {
                        let csvContent = '';
                        let filename = '';
                        
                        if (dataType === 'sales') {
                            const labels = @json($chartLabels);
                            const total = @json($chartDataTotal);
                            const verified = @json($chartDataVerified);
                            const returns = @json($chartDataVerifiedWithReturn);
                            
                            csvContent = 'Tanggal,Total Pesanan,Diverifikasi,Retur\n';
                            labels.forEach((label, index) => {
                                csvContent += `${label},${total[index]},${verified[index]},${returns[index]}\n`;
                            });
                            filename = `data-penjualan-${new Date().toISOString().split('T')[0]}.csv`;
                        } else if (dataType === 'visitors') {
                            const labels = @json($visitChartLabels);
                            const data = @json($visitChartData);
                            
                            csvContent = 'Tanggal,Kunjungan\n';
                            labels.forEach((label, index) => {
                                csvContent += `${label},${data[index]}\n`;
                            });
                            filename = `data-kunjungan-${new Date().toISOString().split('T')[0]}.csv`;
                        }
                        
                        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();
                        
                        this.showNotification('Data berhasil di-export sebagai CSV', 'success');
                    } catch (error) {
                        console.error('Error exporting data:', error);
                        this.showNotification('Gagal meng-export data', 'error');
                    }
                },
                
                // Show welcome notification
                showWelcomeNotification() {
                    const hour = new Date().getHours();
                    let greeting = '';
                    
                    if (hour >= 5 && hour < 11) {
                        greeting = 'Selamat Pagi';
                    } else if (hour >= 11 && hour < 15) {
                        greeting = 'Selamat Siang';
                    } else if (hour >= 15 && hour < 18) {
                        greeting = 'Selamat Sore';
                    } else {
                        greeting = 'Selamat Malam';
                    }
                    
                    // Could integrate with a notification system
                    console.log(`${greeting}, selamat bekerja!`);
                },
                
                // Show notification
                showNotification(message, type = 'info') {
                    // Create toast notification
                    const toast = document.createElement('div');
                    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-xl shadow-2xl text-white text-sm font-semibold z-50 transform translate-y-full transition-transform duration-300 ${
                        type === 'success' ? 'bg-brand-deep' :
                        type === 'error' ? 'bg-rose-500' :
                        'bg-slate-700'
                    }`;
                    toast.textContent = message;
                    
                    document.body.appendChild(toast);
                    
                    // Animate in
                    setTimeout(() => {
                        toast.classList.remove('translate-y-full');
                    }, 100);
                    
                    // Animate out
                    setTimeout(() => {
                        toast.classList.add('translate-y-full');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 3000);
                }
            }
        }
        
        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for dark mode changes to update charts
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        // Re-initialize charts with new colors
                        if (window.dashboardAppInstance) {
                            window.dashboardAppInstance.initCharts();
                        }
                    }
                });
            });
            
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>
@endpush
