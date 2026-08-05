@extends('layouts.argon')
@section('title', 'Dashboard Kurir')
@section('page_title', 'Dashboard')

@section('content')
    <?php

    use App\Models\OrderReturn;
    use App\Models\OrderReturnProduct;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;
    use Illuminate\Support\Str;

    $today = Carbon::today();
    $loggedInCourierRegionId = Auth::user()->region_id;
    $loggedInCourierId = Auth::id();

    $statusLabelMap = [
        'baru' => 'Baru',
        'dikemas' => 'Dikemas',
        'diambil' => 'Diambil',
        'diantar' => 'Diantar',
        'diterima_pembeli' => 'Diterima',
        'selesai' => 'Selesai',
        'menunggu_retur' => 'Menunggu Retur',
        'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
        'diverifikasi_admin' => 'Valid',
        'dikembalikan' => 'Retur',
        'dibatalkan' => 'Dibatalkan',
    ];

    $labelStatus = function ($status) use ($statusLabelMap) {
        return $statusLabelMap[$status] ?? ucwords(str_replace('_', ' ', $status));
    };

    // --- Statistik Harian (Diperbarui dengan filter kurir) ---
    $totalOrdersToday = App\Models\Order::where('created_by_user_id', $loggedInCourierId)->whereDate('created_at', $today)->count();
    $totalCustomersInRegion = App\Models\Customer::where('added_by_user_id', $loggedInCourierId)->count();
    $completedOrdersToday = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
        ->whereDate('updated_at', $today)
        ->whereIn('status', ['selesai', 'diverifikasi_admin'])
        ->count();
    $receivedByBuyerToday = App\Models\Order::where('created_by_user_id', $loggedInCourierId)->whereDate('received_by_buyer_at', $today)->where('status', 'diterima_pembeli')->count();

    // Menghitung retur berdasarkan pesanan yang dibuat oleh kurir
    $totalReturnedOrdersToday = OrderReturn::whereHas('order', function ($query) use ($loggedInCourierId) {
        $query->where('created_by_user_id', $loggedInCourierId);
    })
        ->whereDate('created_at', $today)
        ->count();

    // --- Pesanan Terbaru (Diperbarui untuk mengecualikan status 'diverifikasi_admin') ---
    $latestOrders = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
        ->where('status', '!=', 'diverifikasi_admin')
        ->with(['customer', 'items'])
        ->latest()
        ->take(3)
        ->get();

    // --- Logika Grafik Diperbarui dengan filter kurir ---
    $filter = Request::input('filter', 'last_7_days');
    $chartLabels = [];
    $chartData = [];
    $chartDataCompleted = [];
    $chartDataReturned = [];
    $chartTitle = 'Chart Pesanan';
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month;
    $dateRangeText = '';

    // Label untuk dropdown
    $filterLabels = [
        'last_7_days' => '7 Hari Terakhir',
        'daily' => 'Harian (Bulan Ini)',
        'weekly' => 'Mingguan (Bulan Ini)',
        'monthly' => 'Bulanan (Tahun Ini)',
    ];
    $currentFilterLabel = $filterLabels[$filter] ?? 'Pilih Filter';

    switch ($filter) {
        case 'daily':
            $daysInMonth = Carbon::now()->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($currentYear, $currentMonth, $day);
                $chartLabels[] = $date->format('d');

                $chartData[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)->whereDate('created_at', $date)->count();
                $chartDataCompleted[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
                    ->whereDate('updated_at', $date)
                    ->whereIn('status', ['selesai', 'diverifikasi_admin'])
                    ->count();
                $chartDataReturned[] = App\Models\OrderReturn::whereHas('order', function ($query) use ($loggedInCourierId) {
                    $query->where('created_by_user_id', $loggedInCourierId);
                })
                    ->whereDate('created_at', $date)
                    ->count();
            }
            $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
            break;

        case 'weekly':
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $weekNumber = 1;
            while ($startDate->lte($endDate)) {
                $weekEndDate = $startDate->copy()->endOfWeek(Carbon::SATURDAY);
                if ($weekEndDate->gt($endDate)) {
                    $weekEndDate = $endDate;
                }
                $chartLabels[] = 'Minggu Ke-' . $weekNumber;

                $chartData[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
                    ->whereBetween('created_at', [$startDate, $weekEndDate])
                    ->count();
                $chartDataCompleted[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
                    ->whereBetween('updated_at', [$startDate, $weekEndDate])
                    ->whereIn('status', ['selesai', 'diverifikasi_admin'])
                    ->count();
                $chartDataReturned[] = App\Models\OrderReturn::whereHas('order', function ($query) use ($loggedInCourierId) {
                    $query->where('created_by_user_id', $loggedInCourierId);
                })
                    ->whereBetween('created_at', [$startDate, $weekEndDate])
                    ->count();

                $startDate = $weekEndDate->copy()->addDay();
                $weekNumber++;
            }
            $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
            break;

        case 'monthly':
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::createFromDate($currentYear, $month, 1);
                $chartLabels[] = $date->isoFormat('MMM');

                $chartData[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->count();
                $chartDataCompleted[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
                    ->whereYear('updated_at', $currentYear)
                    ->whereMonth('updated_at', $month)
                    ->whereIn('status', ['selesai', 'diverifikasi_admin'])
                    ->count();
                $chartDataReturned[] = App\Models\OrderReturn::whereHas('order', function ($query) use ($loggedInCourierId) {
                    $query->where('created_by_user_id', $loggedInCourierId);
                })
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $month)
                    ->count();
            }
            $dateRangeText = $currentYear;
            break;

        case 'last_7_days':
        default:
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays(6);
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->format('d');

                $chartData[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)->whereDate('created_at', $date)->count();
                $chartDataCompleted[] = App\Models\Order::where('created_by_user_id', $loggedInCourierId)
                    ->whereDate('updated_at', $date)
                    ->whereIn('status', ['selesai', 'diverifikasi_admin'])
                    ->count();
                $chartDataReturned[] = App\Models\OrderReturn::whereHas('order', function ($query) use ($loggedInCourierId) {
                    $query->where('created_by_user_id', $loggedInCourierId);
                })
                    ->whereDate('created_at', $date)
                    ->count();
            }
            $dateRangeText = $startDate->isoFormat('D MMM') . ' - ' . $endDate->isoFormat('D MMM');
            break;
    }

    // Hitung total berdasarkan rentang yang dipilih
    $totalOrdersInRange = array_sum($chartData);
    $totalCompletedOrdersInRange = array_sum($chartDataCompleted);
    $totalReturnedOrdersInRange = array_sum($chartDataReturned);

    // BARU: Menghitung nilai maksimum untuk skala Y chart untuk mencegah pergeseran
    $maxChartValue = 0;
    if (!empty($chartData)) {
        $maxChartValue = max($maxChartValue, max($chartData));
    }
    if (!empty($chartDataCompleted)) {
        $maxChartValue = max($maxChartValue, max($chartDataCompleted));
    }
    if (!empty($chartDataReturned)) {
        $maxChartValue = max($maxChartValue, max($chartDataReturned));
    }

    // Tambahkan buffer dan atur nilai minimum untuk sumbu Y
    $suggestedMax = $maxChartValue > 0 ? ceil($maxChartValue * 1.2) : 5;

    ?>
    <div class="w-full max-w full">
        <div class="flex flex-wrap gap-6 -mx-3">
            <div class="w-full max-w-full px-3 space-y-4 lg:flex-nome">
                <div class="mb-6">
                    <div
                        class="relative flex flex-col flex-none max-w-full gap-4 p-6 px-6 py-6 overflow-hidden border shadow-2xl rounded-3xl xl:gap-0 bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800 border-amber-100 dark:border-slate-600">
                        <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                            <div
                                class="w-full h-full transform rotate-45 translate-x-8 -translate-y-8 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500">
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-3 shadow-lg bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-baseline gap-2">
                                        <div class="text-2xl font-bold text-transparent bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text dark:from-white dark:to-gray-300"
                                            id="ucapan">
                                        </div>
                                        <div id="greeting-emoji" class="text-2xl">
                                        </div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Your Achievement Today
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 grid grid-cols-2 gap-4 md:grid-cols-4">
                            <div
                                class="p-3 border shadow-lg bg-white/60 dark:bg-slate-700/60 backdrop-blur-sm rounded-2xl border-white/20 dark:border-slate-600/20">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full dark:bg-blue-900/50">
                                            <i class="text-lg text-blue-600 fas fa-shopping-basket dark:text-blue-400"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">Total Hari Ini</p>
                                        {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                        <p class="text-xl font-bold text-gray-800 truncate dark:text-white"
                                            id="total-orders-today-count">
                                            {{ $totalOrdersToday }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-3 border shadow-lg bg-white/60 dark:bg-slate-700/60 backdrop-blur-sm rounded-2xl border-white/20 dark:border-slate-600/20">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-full dark:bg-orange-900/50">
                                            <i class="text-lg text-orange-600 fas fa-users dark:text-orange-400"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">Customer</p>
                                        {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                        <p class="text-xl font-bold text-gray-800 truncate dark:text-white"
                                            id="total-customers-count">
                                            {{ $totalCustomersInRegion }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-3 border shadow-lg bg-white/60 dark:bg-slate-700/60 backdrop-blur-sm rounded-2xl border-white/20 dark:border-slate-600/20">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full dark:bg-green-900/50">
                                            <i class="text-lg text-green-600 fas fa-check-circle dark:text-green-400"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">Selesai</p>
                                        {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                        <p class="text-xl font-bold text-gray-800 truncate dark:text-white"
                                            id="completed-orders-count">
                                            {{ $completedOrdersToday }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-3 border shadow-lg bg-white/60 dark:bg-slate-700/60 backdrop-blur-sm rounded-2xl border-white/20 dark:border-slate-600/20">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full dark:bg-red-900/50">
                                            <i class="text-lg text-red-600 fas fa-undo dark:text-red-400"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">Return</p>
                                        {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                        <p class="text-xl font-bold text-gray-800 truncate dark:text-white"
                                            id="total-returned-today-count">
                                            {{ $totalReturnedOrdersToday }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="flex flex-wrap -mx-3">
                        <a href="{{ route('kurir.pesanan.create') }}" class="w-1/2 px-3">
                            <div
                                class="relative flex items-center justify-center p-4 overflow-hidden transition-all duration-300 ease-out transform border border-green-100 shadow-xl group md:justify-start bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl hover:shadow-2xl hover:scale-105 dark:border-slate-600">
                                <div
                                    class="absolute inset-0 transition-opacity duration-300 opacity-0 bg-gradient-to-br from-green-400/10 to-emerald-500/10 group-hover:opacity-100">
                                </div>
                                <div class="relative z-10 flex items-center">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 transition-shadow duration-300 shadow-lg md:mr-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl group-hover:shadow-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M12 3v2a2 2 0 01-2 2h4a2 2 0 01-2-2V3zm-2 9h4m-4 4h4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 hidden min-w-0 md:block">
                                        <div
                                            class="mb-1 text-lg font-bold text-gray-800 truncate transition-colors duration-300 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                            Pesanan</div>
                                        <div class="text-xs font-medium text-gray-600 truncate dark:text-gray-400">
                                            Buat pesanan baru</div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <button type="button" id="add-customer" class="w-1/2 px-3 mb-2 js-open-modal-btn"
                            data-target-modal="create-customer-modal">
                            <div
                                class="relative flex items-center justify-center p-4 overflow-hidden transition-all duration-300 ease-out transform border border-blue-100 shadow-xl group md:justify-start bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl hover:shadow-2xl hover:scale-105 dark:border-slate-600">
                                <div
                                    class="absolute inset-0 transition-opacity duration-300 opacity-0 bg-gradient-to-br from-blue-400/10 to-indigo-500/10 group-hover:opacity-100">
                                </div>
                                <div class="relative z-10 flex items-center">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 transition-shadow duration-300 shadow-lg md:mr-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl group-hover:shadow-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v6m3-3h-6M9 16a4 4 0 100-8 4 4 0 000 8zm0 0c-2.21 0-4 1.79-4 4v1h8v-1c0-2.21-1.79-4-4-4z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 hidden min-w-0 text-left md:block">
                                        <div
                                            class="mb-1 text-lg font-bold text-gray-800 truncate transition-colors duration-300 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                            Customer</div>
                                        <div class="text-xs font-medium text-gray-600 truncate dark:text-gray-400">
                                            Tambah customer baru</div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mt-0 mb-2 lg:mb-0 lg:flex-none">
                <div
                    class="relative flex flex-col min-w-0 overflow-hidden break-words border border-gray-100 shadow-2xl bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 rounded-3xl bg-clip-border dark:border-slate-700">
                    <div class="p-6 pb-4 mb-0 rounded-t-4">
                        <div class="flex-1">
                            @if (Auth::user()->note)
                                <div class="flex items-center mb-4">
                                    <div class="p-2 mr-3 rounded-lg bg-gradient-to-br from-yellow-400 to-orange-500">
                                        <i class="text-sm text-white fas fa-sticky-note"></i>
                                    </div>
                                    <h6 class="mb-0 text-lg font-bold text-gray-800 dark:text-white">Catatan dari Admin
                                    </h6>
                                </div>
                                <div
                                    class="p-4 border-l-4 border-yellow-400 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-slate-700 dark:to-slate-600 rounded-2xl">
                                    <p class="font-medium leading-relaxed text-gray-700 dark:text-gray-300">
                                        "{{ Auth::user()->note }}"
                                    </p>
                                </div>
                            @else
                                <div class="flex items-center mb-4">
                                    <div class="p-2 mr-3 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500">
                                        <i class="text-sm text-white fas fa-check-circle"></i>
                                    </div>
                                    <h6 class="mb-0 text-lg font-bold text-gray-800 dark:text-white">Tidak Ada Catatan Baru
                                    </h6>
                                </div>
                                <div
                                    class="p-4 border-l-4 border-green-400 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-slate-700 dark:to-slate-600 rounded-2xl">
                                    <p class="leading-relaxed text-gray-700 dark:text-gray-300">
                                        Saat ini tidak ada catatan baru dari admin.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mt-0 lg:flex-none">
                <div
                    class="relative overflow-hidden bg-white border border-gray-100 shadow-2xl rounded-3xl dark:bg-slate-800 dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
                            <div class="flex flex-col">
                                <h6 class="flex items-center gap-2 text-lg font-bold text-gray-800 dark:text-white">
                                    <svg class="w-6 h-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                    <span>{{ $chartTitle }}</span>
                                </h6>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-calendar-alt mr-1"></i> {{ $dateRangeText }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 mr-2 bg-blue-500 rounded-full"></span>
                                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total </span>
                                    {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                    <span class="text-sm font-bold text-gray-800 dark:text-white"
                                        id="chart-total-orders">{{ $totalOrdersInRange }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-3 h-3 mr-2 bg-green-500 rounded-full"></span>
                                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Selesai </span>
                                    {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                    <span class="text-sm font-bold text-gray-800 dark:text-white"
                                        id="chart-total-completed">{{ $totalCompletedOrdersInRange }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-3 h-3 mr-2 bg-red-500 rounded-full"></span>
                                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Return </span>
                                    {{-- DITAMBAHKAN: ID untuk pembaruan JS --}}
                                    <span class="text-sm font-bold text-gray-800 dark:text-white"
                                        id="chart-total-returned">{{ $totalReturnedOrdersInRange }}</span>
                                </div>
                                <div class="relative">
                                    <button id="chartFilterButton" type="button"
                                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                            </path>
                                        </svg>
                                    </button>
                                    <div id="chartFilterDropdown"
                                        class="absolute right-0 z-20 hidden w-40 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-slate-700 dark:border-slate-600">
                                        <a href="{{ url()->current() }}?filter=last_7_days"
                                            class="block px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-150 {{ $filter === 'last_7_days' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">7
                                            Hari</a>
                                        <a href="{{ url()->current() }}?filter=daily"
                                            class="block px-4 py-2 text-sm font-semibold transition-colors duration-150 {{ $filter === 'daily' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Harian</a>
                                        <a href="{{ url()->current() }}?filter=weekly"
                                            class="block px-4 py-2 text-sm font-semibold transition-colors duration-150 {{ $filter === 'weekly' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Mingguan</a>
                                        <a href="{{ url()->current() }}?filter=monthly"
                                            class="block px-4 py-2 text-sm font-semibold rounded-b-lg transition-colors duration-150 {{ $filter === 'monthly' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Bulanan</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto p-4 pt-0">
                        <div class="overflow-x-auto">
                            <div class="relative min-w-[700px] h-[250px] lg:h-[300px]">
                                <canvas id="ordersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap mt-3 -mx-3">
            <div class="w-full max-w-full px-3 mt-0 lg:flex-none">
                <div class="flex flex-wrap mt-6 -mx-3">
                    <div class="w-full max-w-full px-3 mt-0 lg:flex-none">
                        <div
                            class="relative flex flex-col min-w-0 mb-4 overflow-hidden break-words border border-gray-100 shadow-2xl bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 rounded-3xl bg-clip-border dark:border-slate-700">
                            <div class="p-6 pb-0 mb-0 rounded-t-3xl">
                                <div class="flex items-center justify-between">
                                    <h5 class="mb-1 text-xl font-bold text-gray-800 dark:text-white">🎯 Latest Orders
                                    </h5>
                                    <a href="{{ route('kurir.pesanan.index') }}" title="Lihat Pesanan">
                                        <div
                                            class="p-2 transition-all duration-300 rounded-lg bg-gradient-to-br from-purple-400 to-pink-500 hover:from-purple-500 hover:to-pink-600">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="flex-auto px-0 pt-0 pb-2">
                                <div class="hidden p-0 overflow-x-auto md:block">
                                    <table class="items-center w-full mb-0 align-top border-collapse text-slate-500">
                                        <thead class="align-bottom">
                                            <tr>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold tracking-wider text-center text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    #</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Invoice</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Customer</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Alamat</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Total Pesanan</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold tracking-wider text-center text-gray-700 uppercase align-middle border-b border-gray-200 dark:text-gray-300 bg-gray-50 dark:bg-slate-700 dark:border-slate-600">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($latestOrders as $loop => $order)
                                                @php
                                                    // Menghitung total awal dan total terbaru berdasarkan data retur
                                                    $initialTotal = 0;
                                                    foreach ($order->items as $item) {
                                                        $initialTotal += ($item->quantity ?? 0) * ($item->price ?? 0);
                                                    }

                                                    $latestTotal = $order->total_amount;
                                                    $showReturnedView = false;

                                                    if (
                                                        $order->status === 'menunggu_retur' ||
                                                        $order->status === 'menunggu_verifikasi_admin'
                                                    ) {
                                                        $showReturnedView = true;
                                                        $orderReturn = OrderReturn::where(
                                                            'order_id',
                                                            $order->id,
                                                        )->first();
                                                        if ($orderReturn) {
                                                            $returnProducts = OrderReturnProduct::where(
                                                                'order_return_id',
                                                                $orderReturn->id,
                                                            )->get();
                                                            $totalReturnValue = 0;
                                                            foreach ($returnProducts as $returnItem) {
                                                                $totalReturnValue +=
                                                                    ($returnItem->quantity ?? 0) *
                                                                    ($returnItem->price ?? 0);
                                                            }
                                                            $latestTotal = $initialTotal - $totalReturnValue;
                                                        } else {
                                                            $latestTotal = $initialTotal;
                                                        }
                                                    } else {
                                                        $calculatedLatestTotal = 0;
                                                        $hasReturnedItems = false;
                                                        foreach ($order->items as $item) {
                                                            $initialQty = $item->quantity ?? 0;
                                                            $returnedQty = $item->returned_quantity ?? 0;
                                                            $price = $item->price ?? 0;
                                                            $calculatedLatestTotal +=
                                                                ($initialQty - $returnedQty) * $price;
                                                            if ($returnedQty > 0) {
                                                                $hasReturnedItems = true;
                                                            }
                                                        }
                                                        if (
                                                            $hasReturnedItems &&
                                                            $calculatedLatestTotal < $initialTotal
                                                        ) {
                                                            $latestTotal = $calculatedLatestTotal;
                                                            $showReturnedView = true;
                                                        }
                                                    }
                                                @endphp

                                                @php
                                                    $shortInvoice = '#' . substr($order->invoice_number, -3);
                                                @endphp
                                                <tr data-order-id="{{ $order->id }}"
                                                    class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-slate-700">
                                                    <td
                                                        class="p-4 text-center align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <span
                                                            class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $loop->iteration }}</span>
                                                    </td>
                                                    <td
                                                        class="p-4 align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <span
                                                            class="text-sm font-semibold text-gray-800 dark:text-white">{{ $shortInvoice }}</span>
                                                        @if ($order->rejection_note)
                                                            <div class="flex items-center gap-1 mt-1 text-xs text-red-600 dark:text-red-400"
                                                                title="{{ $order->rejection_note }}">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                <span>Verifikasi Ditolak</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="p-4 align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <div class="flex px-2 py-1">
                                                            <div class="flex flex-col justify-center">
                                                                <h6
                                                                    class="mb-0 text-sm font-semibold leading-normal text-gray-800 dark:text-white">
                                                                    {{ $order->customer->name ?? 'Pelanggan Dihapus' }}
                                                                </h6>
                                                                <p
                                                                    class="mb-0 text-xs leading-tight text-gray-500 dark:text-gray-400">
                                                                    {{ $order->phone ?? ($order->customer->phone ?? 'N/A') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="p-4 align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <p
                                                            class="mb-0 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ Str::limit($order->address ?? 'Alamat tidak ada', 25) }}
                                                        </p>
                                                    </td>
                                                    <td
                                                        class="p-4 align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <button type="button"
                                                            class="w-full text-left transition-colors js-open-modal-btn hover:opacity-80"
                                                            data-target-modal="orderDetailsModal"
                                                            onclick="fetchOrderDetails({{ $order->id }})">
                                                            @if ($showReturnedView)
                                                                <div>
                                                                    <p
                                                                        class="text-sm font-bold text-gray-400 line-through dark:text-gray-500">
                                                                        Rp {{ number_format($initialTotal, 0, ',', '.') }}
                                                                    </p>
                                                                    <p
                                                                        class="text-lg font-extrabold text-green-600 dark:text-green-500">
                                                                        Rp {{ number_format($latestTotal, 0, ',', '.') }}
                                                                    </p>
                                                                </div>
                                                            @else
                                                                <p
                                                                    class="text-lg font-extrabold text-blue-600 dark:text-blue-400">
                                                                    Rp
                                                                    {{ number_format($order->total_amount, 0, ',', '.') }}
                                                                </p>
                                                            @endif
                                                        </button>
                                                    </td>
                                                    <td
                                                        class="p-4 text-sm leading-normal text-center align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        @php
                                                            $status = $order->status ?? 'dikemas';
                                                            $statusText =
                                                                $statusLabelMap[$status] ??
                                                                ucfirst(str_replace('_', ' ', $status));
                                                            $statusClass = '';
                                                            switch ($status) {
                                                                case 'diambil':
                                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                                    break;
                                                                case 'diantar':
                                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                                    break;
                                                                case 'diterima_pembeli':
                                                                    $statusClass = 'bg-purple-100 text-purple-800';
                                                                    break;
                                                                case 'selesai':
                                                                case 'diverifikasi_admin':
                                                                    $statusClass = 'bg-green-100 text-green-800';
                                                                    break;
                                                                case 'menunggu_retur':
                                                                    $statusClass = 'bg-red-100 text-red-800';
                                                                    break;
                                                                case 'menunggu_verifikasi_admin':
                                                                    $statusClass =
                                                                        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                                    break;
                                                            }
                                                        @endphp
                                                        <span
                                                            class="status-badge px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                                                    </td>
                                                    <td
                                                        class="p-4 text-center align-middle bg-transparent border-b dark:border-slate-600 whitespace-nowrap">
                                                        <button type="button"
                                                            class="p-2 text-gray-500 transition-colors rounded-full js-open-modal-btn hover:bg-gray-100 dark:hover:bg-slate-600"
                                                            title="Ubah Status" data-target-modal="statusStepperModal"
                                                            onclick="openStatusStepperModal({{ $order->id }})">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7"
                                                        class="p-6 text-center text-gray-500 dark:text-gray-400">
                                                        Belum ada pesanan yang dibuat.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="px-2 py-2 space-y-3 md:hidden">
                                    @forelse($latestOrders as $loop => $order)
                                        @php
                                            // Menghitung total awal dan total terbaru berdasarkan data retur untuk tampilan mobile
                                            $initialTotal = 0;
                                            foreach ($order->items as $item) {
                                                $initialTotal += ($item->quantity ?? 0) * ($item->price ?? 0);
                                            }

                                            $latestTotal = $order->total_amount;
                                            $showReturnedView = false;

                                            if (
                                                $order->status === 'menunggu_retur' ||
                                                $order->status === 'menunggu_verifikasi_admin'
                                            ) {
                                                $showReturnedView = true;
                                                $orderReturn = OrderReturn::where('order_id', $order->id)->first();
                                                if ($orderReturn) {
                                                    $returnProducts = OrderReturnProduct::where(
                                                        'order_return_id',
                                                        $orderReturn->id,
                                                    )->get();
                                                    $totalReturnValue = 0;
                                                    foreach ($returnProducts as $returnItem) {
                                                        $totalReturnValue +=
                                                            ($returnItem->quantity ?? 0) * ($returnItem->price ?? 0);
                                                    }
                                                    $latestTotal = $initialTotal - $totalReturnValue;
                                                } else {
                                                    $latestTotal = $initialTotal;
                                                }
                                            } else {
                                                $calculatedLatestTotal = 0;
                                                $hasReturnedItems = false;
                                                foreach ($order->items as $item) {
                                                    $initialQty = $item->quantity ?? 0;
                                                    $returnedQty = $item->returned_quantity ?? 0;
                                                    $price = $item->price ?? 0;
                                                    $calculatedLatestTotal += ($initialQty - $returnedQty) * $price;
                                                    if ($returnedQty > 0) {
                                                        $hasReturnedItems = true;
                                                    }
                                                }
                                                if ($hasReturnedItems && $calculatedLatestTotal < $initialTotal) {
                                                    $latestTotal = $calculatedLatestTotal;
                                                    $showReturnedView = true;
                                                }
                                            }
                                        @endphp
                                        @php
                                            $customerPhone = $order->phone ?? ($order->customer->phone ?? '');
                                            $formattedPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                                            $shortInvoice = '#' . substr($order->invoice_number, -3);
                                            $customerName = $order->customer->name ?? 'Pelanggan Dihapus';
                                            $displayName = Str::words($customerName, 2, '');
                                        @endphp
                                        <div data-order-id="{{ $order->id }}"
                                            class="p-3 border shadow-lg bg-white/60 dark:bg-slate-700/60 backdrop-blur-sm rounded-2xl border-white/20 dark:border-slate-600/20">
                                            <div class="flex items-start justify-between w-full space-x-3">
                                                <div class="flex-shrink-0 pt-1">
                                                    <span
                                                        class="flex items-center justify-center w-6 h-6 text-xs font-bold text-gray-600 bg-gray-200 rounded-full dark:bg-slate-600 dark:text-gray-300">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-baseline space-x-2">
                                                        <h6 class="text-base font-bold text-gray-800 dark:text-white">
                                                            {{ $displayName ?? 'Pelanggan Dihapus' }}
                                                        </h6>
                                                    </div>
                                                    @if ($order->rejection_note)
                                                        <div class="flex items-center gap-1 mt-0.5 text-xs text-red-600 dark:text-red-400"
                                                            title="{{ $order->rejection_note }}">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            <span>Verifikasi Ditolak</span>
                                                        </div>
                                                    @endif
                                                    <button type="button"
                                                        class="block w-full mt-2 text-sm text-left text-gray-500 transition-colors js-open-modal-btn dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                                                        data-target-modal="orderDetailsModal"
                                                        onclick="fetchOrderDetails({{ $order->id }})">
                                                        <span>{{ $shortInvoice ?? 'N/A' }}</span>
                                                        <span class="mx-1">|</span>
                                                        @if ($showReturnedView)
                                                            <span class="inline-flex items-center gap-1">
                                                                <del class="mr-1 text-xs text-gray-500">Rp
                                                                    {{ number_format($initialTotal, 0, ',', '.') }}</del>
                                                                <span
                                                                    class="font-bold text-green-600 dark:text-green-500">Rp
                                                                    {{ number_format($latestTotal, 0, ',', '.') }}</span>
                                                            </span>
                                                        @else
                                                            <span class="font-bold text-blue-600 dark:text-blue-400">Rp
                                                                {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                                        @endif
                                                    </button>
                                                </div>

                                                <div class="flex flex-col items-end flex-shrink-0 space-y-2">
                                                    @php
                                                        $status = $order->status ?? 'dikemas';
                                                        $statusText =
                                                            $statusLabelMap[$status] ??
                                                            ucfirst(str_replace('_', ' ', $status));
                                                        $statusClass = '';
                                                        switch ($status) {
                                                            case 'diambil':
                                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                                break;
                                                            case 'diantar':
                                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                                break;
                                                            case 'diterima_pembeli':
                                                                $statusClass = 'bg-purple-100 text-purple-800';
                                                                break;
                                                            case 'selesai':
                                                            case 'diverifikasi_admin':
                                                                $statusClass = 'bg-green-100 text-green-800';
                                                                break;
                                                            case 'menunggu_retur':
                                                                $statusClass = 'bg-red-100 text-red-800';
                                                                break;
                                                            case 'menunggu_verifikasi_admin':
                                                                $statusClass =
                                                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span
                                                        class="status-badge px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                    <div class="flex items-center space-x-3">
                                                        @if ($formattedPhone)
                                                            <a href="https://wa.me/{{ $formattedPhone }}" target="_blank"
                                                                class="text-green-500 transition-colors hover:text-green-600">
                                                                <i class="text-2xl fab fa-whatsapp"></i>
                                                            </a>
                                                        @endif
                                                        <button type="button"
                                                            class="text-gray-400 transition-colors js-open-modal-btn hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                                                            title="Ubah Status" data-target-modal="statusStepperModal"
                                                            onclick="openStatusStepperModal({{ $order->id }})">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada pesanan yang dibuat.
                                        </div>
                                    @endforelse
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    @push('flowbite-modals')
        @if (isset($customerCategories))
            @include('dashboard.kurir.customers.create', ['customerCategories' => $customerCategories])
        @else
            {{-- Fallback jika $customerCategories tidak ada, untuk mencegah error --}}
            @include('dashboard.kurir.customers.create', ['customerCategories' => []])
        @endif
        @include('dashboard.kurir.pesanan.rincian-modal')
        @include('dashboard.kurir.pesanan.status-modal')
        @include('dashboard.kurir.pesanan.return-modal')
    @endpush

    <script>
        // DITAMBAHKAN: Variabel global untuk menyimpan instance chart dan status order
        let ordersChartInstance = null;
        let currentOrderForUpdate = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown filter chart (fix: only one logic, robust)
            const chartFilterButton = document.getElementById('chartFilterButton');
            const chartFilterDropdown = document.getElementById('chartFilterDropdown');
            if (chartFilterButton && chartFilterDropdown) {
                chartFilterButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    chartFilterDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!chartFilterDropdown.classList.contains('hidden')) {
                        if (!chartFilterButton.contains(e.target) && !chartFilterDropdown.contains(e
                                .target)) {
                            chartFilterDropdown.classList.add('hidden');
                        }
                    }
                });
            }

            // --- Enhanced Orders Chart ---
            var ctx = document.getElementById('ordersChart');
            if (ctx && window.Chart) {
                // DIUBAH: Menyimpan instance chart ke variabel global
                ordersChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                                label: 'Total Pesanan',
                                data: @json($chartData),
                                borderColor: '#3b82f6', // Biru
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Selesai',
                                data: @json($chartDataCompleted),
                                borderColor: '#22c55e', // Hijau
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Return',
                                data: @json($chartDataReturned),
                                borderColor: '#f97316', // Oranye
                                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                fill: true,
                                tension: 0.4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' :
                                        '#6b7280',
                                    stepSize: 1
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' :
                                        '#6b7280'
                                }
                            }
                        }
                    }
                });
            }

            // Time-based greeting
            const courierName = @json(Str::words(Auth::user()->name, 2, ''));

            function updateGreeting() {
                const hour = new Date().getHours();
                let greetingText = '';
                let iconClass = '';

                if (hour >= 4 && hour < 11) {
                    greetingText = `Selamat Pagi, ${courierName}`;
                    iconClass = 'fa-sun';
                } else if (hour >= 11 && hour < 15) {
                    greetingText = `Selamat Siang, ${courierName}`;
                    iconClass = 'fa-sun';
                } else if (hour >= 15 && hour < 18) {
                    greetingText = `Selamat Sore, ${courierName}`;
                    iconClass = 'fa-cloud-sun';
                } else {
                    greetingText = `Selamat Malam, ${courierName}`;
                    iconClass = 'fa-moon';
                }

                document.getElementById("ucapan").textContent = greetingText;
                const greetingIcon = document.getElementById("greeting-icon");
                if (greetingIcon) {
                    greetingIcon.className = `fas ${iconClass} ml-2`;
                }
            }
            updateGreeting();

        });
    </script>


    <script>
        // Ganti dengan URL aplikasi Anda yang sebenarnya di production
        const APP_URL = "{{ url('/') }}";
        const STATUS_LABEL_MAP = @json($statusLabelMap);

        // --- Helper ---
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function dispatchToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    type: type,
                    message: message
                }
            }));
        }

        // [!code block:start]
        /**
         * Membuka modal image viewer untuk menampilkan gambar dalam ukuran penuh.
         * @param {string} src - URL sumber gambar yang akan ditampilkan.
         */
        function openImageViewer(src) {
            const imageViewer = document.getElementById('imageViewerModal');
            const fullSizeImage = document.getElementById('fullSizeImage');
            if (imageViewer && fullSizeImage) {
                fullSizeImage.src = src;
                imageViewer.classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // Mencegah scroll di belakang modal
            }
        }

        /**
         * Menutup modal image viewer.
         */
        function closeImageViewer() {
            const imageViewer = document.getElementById('imageViewerModal');
            if (imageViewer) {
                imageViewer.classList.add('hidden');
                document.body.classList.remove('overflow-hidden'); // Mengembalikan kemampuan scroll
            }
        }
        // [!code block:end]

        // DITAMBAHKAN: Fungsi baru untuk memperbarui statistik di dashboard secara client-side
        function updateDashboardCounters(oldStatus, newStatus) {
            if (oldStatus === newStatus) return; // Tidak ada perubahan, tidak perlu update

            const wasCompleted = ['selesai', 'diverifikasi_admin'].includes(oldStatus);
            const isNowCompleted = ['selesai', 'diverifikasi_admin'].includes(newStatus);

            const wasReturned = oldStatus === 'menunggu_retur';
            const isNowReturned = newStatus === 'menunggu_retur';

            // 1. Update Kartu "Selesai"
            if (!wasCompleted && isNowCompleted) {
                const countEl = document.getElementById('completed-orders-count');
                if (countEl) {
                    countEl.textContent = parseInt(countEl.textContent, 10) + 1;
                }
            }

            // 2. Update Kartu "Return"
            if (!wasReturned && isNowReturned) {
                const countEl = document.getElementById('total-returned-today-count');
                if (countEl) {
                    countEl.textContent = parseInt(countEl.textContent, 10) + 1;
                }
            }


            // 3. Update Grafik & Totalnya
            if (ordersChartInstance) {
                // Update Total Selesai di legenda grafik
                if (!wasCompleted && isNowCompleted) {
                    const chartTotalEl = document.getElementById('chart-total-completed');
                    if (chartTotalEl) {
                        chartTotalEl.textContent = parseInt(chartTotalEl.textContent, 10) + 1;
                    }
                    // Update data 'selesai' di chart (asumsi data hari ini adalah data terakhir)
                    const completedData = ordersChartInstance.data.datasets[1].data;
                    if (completedData.length > 0) {
                        completedData[completedData.length - 1]++;
                    }
                }

                // Update Total Return di legenda grafik
                if (!wasReturned && isNowReturned) {
                    const chartTotalEl = document.getElementById('chart-total-returned');
                    if (chartTotalEl) {
                        chartTotalEl.textContent = parseInt(chartTotalEl.textContent, 10) + 1;
                    }
                    const returnedData = ordersChartInstance.data.datasets[2].data;
                    if (returnedData.length > 0) {
                        returnedData[returnedData.length - 1]++;
                    }
                }

                ordersChartInstance.update('none'); // Update chart tanpa animasi agar tidak berkedip
            }
        }

        // tambah jumlah return di setiap produk
        document.addEventListener('DOMContentLoaded', () => {
            const returnModal = document.getElementById('returnProductModal');

            if (returnModal) {
                returnModal.addEventListener('click', function(event) {
                    const button = event.target.closest(
                        'button'); // Cari elemen tombol yang paling dekat diklik
                    if (!button) return; // Jika yang diklik bukan tombol, abaikan

                    // Cari baris atau kartu produk terdekat dari tombol yang diklik
                    const productContainer = event.target.closest('[data-return-key]');
                    if (!productContainer) return;

                    // Cari elemen span yang menampilkan angka di dalam container produk itu
                    const quantitySpan = productContainer.querySelector('.quantity-input');
                    if (!quantitySpan) return;

                    let currentValue = parseInt(quantitySpan.textContent, 10);
                    const maxValue = parseInt(quantitySpan.dataset.max, 10);

                    // --- Logika untuk Tombol Tambah (+) ---
                    if (button.classList.contains('quantity-plus')) {
                        if (currentValue < maxValue) {
                            quantitySpan.textContent = currentValue + 1;
                        }
                    }

                    // --- Logika untuk Tombol Kurang (-) ---
                    if (button.classList.contains('quantity-minus')) {
                        if (currentValue > 0) {
                            quantitySpan.textContent = currentValue - 1;
                        }
                    }

                    // --- Logika untuk Tombol Hapus (Ikon Sampah) ---
                    if (button.classList.contains('remove-product')) {
                        // Setel kuantitas kembali ke 0
                        quantitySpan.textContent = 0;
                    }
                });
            }
        });

        // --- Logika Modal Rincian ---
        async function fetchOrderDetails(orderId) {
            openModal('orderDetailsModal');
            const modalLoader = document.getElementById('modalLoader');
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.add('hidden');
            modalLoader.classList.remove('hidden');
            modalLoader.innerHTML =
                `<svg class="w-8 h-8 mx-auto text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">Memuat Detail Pesanan...</p>`;
            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/details`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal mengambil data.');
                populateOrderDetailsModal(data);
            } catch (error) {
                modalLoader.innerHTML =
                    `<div class="text-center"><p class="font-bold text-red-600">Gagal Memuat Data</p><p class="mt-2 text-sm text-gray-500">${error.message}</p></div>`;
            }
        }


        function populateOrderDetailsModal(order) {
            // Populate data umum
            document.getElementById('modalInvoiceNumber').textContent = order.invoice_number || 'N/A';
            document.getElementById('customerName').textContent = order.customer.name || 'N/A';
            document.getElementById('customerPhone').textContent = order.customer.phone || 'N/A';
            document.getElementById('customerAddress').textContent = order.customer.address || 'N/A';
            const companyNameEl = document.getElementById('customerCompanyName');
            if (order.customer.company_name && order.customer.company_name !== 'N/A') {
                companyNameEl.textContent = order.customer.company_name;
                companyNameEl.classList.remove('hidden');
            } else {
                companyNameEl.textContent = '';
                companyNameEl.classList.add('hidden');
            }
            document.getElementById('paymentMethod').textContent = order.payment_method || 'N/A';
            document.getElementById('orderCreatedAt').textContent = order.created_at || 'Tidak Tersedia';
            document.getElementById('orderPaidAt').textContent = order.paid_at ? (order.paid_at + (order.paid_at_label ||
                '')) : 'Belum Lunas';
            // Populate Order Notes
            document.getElementById('orderNotesContainer').textContent = order.note || '"Tidak ada catatan."';

            // Logika untuk menampilkan catatan penolakan
            const rejectionContainer = document.getElementById('rejectionNoteContainer');
            const rejectionText = document.getElementById('rejectionNoteText');
            if (order.rejection_note) {
                rejectionText.textContent = order.rejection_note;
                rejectionContainer.classList.remove('hidden');
            } else {
                rejectionContainer.classList.add('hidden');
            }

            const statusSection = document.getElementById('modalOrderStatusSection');
            const statusBadge = document.getElementById('modalOrderStatusBadge');
            const statusIcon = document.getElementById('modalOrderStatusIcon');
            if (statusBadge) {
                // 1. Ambil teks status dari map yang sudah ada
                const statusText = STATUS_LABEL_MAP[order.status] || (order.status.charAt(0).toUpperCase() + order.status
                    .slice(1).replace(/_/g, ' '));
                statusBadge.textContent = statusText;

                // 2. Tentukan kelas warna berdasarkan status
                let badgeColorClasses = 'bg-gray-100 text-gray-800'; // Default
                switch (order.status) {
                    case 'diambil':
                        badgeColorClasses = 'bg-blue-100 text-blue-800';
                        break;
                    case 'diantar':
                        badgeColorClasses = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'diterima_pembeli':
                        badgeColorClasses = 'bg-purple-100 text-purple-800';
                        break;
                    case 'menunggu_retur':
                        badgeColorClasses = 'bg-red-100 text-red-800';
                        break;
                    case 'menunggu_verifikasi_admin':
                        badgeColorClasses = 'bg-orange-100 text-orange-800';
                        break;
                    case 'selesai':
                        badgeColorClasses = 'bg-green-100 text-green-800';
                        break;
                }
                // 3. Gabungkan kelas dasar dengan kelas warna baru
                const baseClasses = 'flex-shrink-0 px-3 py-1 text-sm font-semibold rounded-full whitespace-nowrap';
                statusBadge.className = `${baseClasses} ${badgeColorClasses}`;
                // icon success
                if (order.status === 'selesai' && statusIcon) {
                    statusIcon.innerHTML =
                        `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="#10b981" stroke-width="1.5" fill="#d1fae5"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" stroke="#10b981" stroke-width="2"/></svg>`;
                    statusIcon.classList.remove('hidden');
                } else if (statusIcon) {
                    statusIcon.innerHTML = ''; // Kosongkan ikon jika status bukan 'selesai'
                    statusIcon.classList.add('hidden');
                }
            }

            // Logika Perhitungan Total Tagihan untuk handle retur
            let calculatedInitialTotal = 0;
            let calculatedLatestTotal = 0;
            let isReturned = false;
            if (order.products && order.products.length > 0) {
                order.products.forEach(p => {
                    const initialQuantity = p.quantity || 0;
                    const returnedQuantity = p.returned_quantity || 0;
                    const price = p.price || 0;
                    calculatedInitialTotal += initialQuantity * price;
                    const latestQuantity = initialQuantity - returnedQuantity;
                    calculatedLatestTotal += latestQuantity * price;
                    if (returnedQuantity > 0) isReturned = true;
                });
            }

            const singleTotalContainer = document.getElementById('singleTotalAmountContainer');
            const returnedTotalContainer = document.getElementById('returnedTotalAmountContainer');
            if (isReturned && calculatedInitialTotal !== calculatedLatestTotal) {
                document.getElementById('modalInitialTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(calculatedInitialTotal)}`;
                document.getElementById('modalLatestTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(calculatedLatestTotal)}`;
                singleTotalContainer.classList.add('hidden');
                returnedTotalContainer.classList.remove('hidden');
            } else {
                document.getElementById('modalTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(order.total_amount || 0)}`;
                singleTotalContainer.classList.remove('hidden');
                returnedTotalContainer.classList.add('hidden');
            }

            // Populate Product List
            const productDetailsDiv = document.getElementById('productDetails');
            productDetailsDiv.innerHTML = '';
            productDetailsDiv.className = 'flex flex-col space-y-2';
            if (order.products && order.products.length > 0) {
                order.products.forEach(product => {
                    const productItem = document.createElement('div');
                    // Main container for each product card
                    productItem.className =
                        'p-3 border rounded-lg dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 flex items-start space-x-4';

                    const initialQty = product.quantity || 0;
                    const returnedQty = product.returned_quantity || 0;
                    const remainingQty = initialQty - returnedQty;
                    const price = product.price || 0;
                    const newSubtotal = remainingQty * price;

                    // SVG icon similar to the one in the image
                    const iconHTML = `
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mt-1 bg-gray-200 rounded-lg dark:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            `;

                    let quantityLine =
                        `<p class="text-sm text-gray-600 dark:text-gray-300">Jumlah: ${initialQty}</p>`;
                    if (returnedQty > 0) {
                        quantityLine = `
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Awal: <span class="font-medium text-gray-800 dark:text-gray-200">${initialQty}</span> |
                        Retur: <span class="font-medium text-red-500">${returnedQty}</span> |
                        Sisa: <span class="font-medium text-green-600">${remainingQty}</span>
                    </p>
                `;
                    }

                    const priceLine = `
                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                    Rp ${new Intl.NumberFormat('id-ID').format(price)} &rarr; Rp ${new Intl.NumberFormat('id-ID').format(newSubtotal)}
                </p>
            `;

                    const detailsHTML = `
                <div class="flex-grow">
                    <p class="font-bold text-gray-900 dark:text-white">${product.name} ${product.variant_name ? `(${product.variant_name})` : ''}</p>
                    ${quantityLine}
                    ${priceLine}
                </div>
            `;

                    productItem.innerHTML = iconHTML + detailsHTML;
                    productDetailsDiv.appendChild(productItem);
                });
            } else {
                productDetailsDiv.innerHTML =
                    '<p class="text-center text-gray-500 dark:text-gray-400">Tidak ada produk dalam pesanan ini.</p>';
            }

            // --- LOGIKA BUKTI UNGGAHAN YANG DIPERBAIKI ---
            const paymentUploadForm = document.getElementById('paymentUploadForm');
            const paymentProofUploaded = document.getElementById('paymentProofUploaded');
            const paymentUploadBlocker = document.getElementById('paymentUploadBlocker');
            const proofImage = document.getElementById('proofImage');
            const proofUploadedTitle = document.getElementById('proofUploadedTitle');
            const compressLink = document.getElementById('compress-link');
            const paymentProofTitle = document.getElementById('paymentProofTitle');

            // Sembunyikan semua elemen terkait bukti unggahan terlebih dahulu
            paymentUploadForm.classList.add('hidden');
            paymentProofUploaded.classList.add('hidden');
            paymentUploadBlocker.classList.add('hidden');
            compressLink.classList.add('hidden');

            // Helper untuk mendapatkan URL gambar yang benar
            const getImageUrl = (path) => {
                if (!path) return '';
                if (path.startsWith('http')) return path;
                if (path.startsWith('storage/')) return `${APP_URL}/${path}`;
                return `${APP_URL}/storage/${path.replace(/^public\//, '')}`;
            };

            // Logika 1: Jika ini adalah pesanan dengan retur, TAMPILKAN BUKTI RETUR
            // (Asumsi backend konsisten mengirim 'return_details')
            if (order.return_details && order.return_details.return_proof) {
                proofUploadedTitle.textContent = 'Bukti Retur';
                proofImage.src = getImageUrl(order.return_details.return_proof);
                paymentProofUploaded.classList.remove('hidden');

                // Logika 2: Jika BUKAN retur, tapi punya bukti bayar, TAMPILKAN BUKTI BAYAR
            } else if (order.payment_proof) {
                proofUploadedTitle.textContent = 'Bukti Pembayaran';
                proofImage.src = getImageUrl(order.payment_proof);
                paymentProofUploaded.classList.remove('hidden');

                // Logika 3: Jika status memungkinkan untuk upload (belum ada bukti)
            } else if (order.status === 'diterima_pembeli' || order.status === 'menunggu_retur') {
                const isReturn = order.status === 'menunggu_retur';
                paymentProofTitle.textContent = isReturn ? 'Unggah Bukti Retur' : 'Unggah Bukti Pembayaran';
                document.getElementById('uploadButtonText').textContent = isReturn ? 'Unggah Bukti Retur' :
                    'Unggah Bukti Pembayaran';
                paymentUploadForm.onsubmit = (e) => {
                    e.preventDefault();
                    handleProofUpload(order.id, order.status);
                };
                paymentUploadForm.classList.remove('hidden');
                compressLink.classList.remove('hidden');

                // Logika 4: Jika status tidak memungkinkan upload
            } else {
                paymentUploadBlocker.classList.remove('hidden');
            }

            // --- Logika untuk Menampilkan Waktu Retur ---
            const returnTimestampContainer = document.getElementById('returnTimestampContainer');
            const returnCreatedAtEl = document.getElementById('returnCreatedAt');

            // Cek apakah ada data retur dan timestamp-nya
            if (order.order_return && order.order_return.created_at) {
                // Tentukan label zona waktu berdasarkan data dari server
                const timezoneLabel = order.timezone === 'Asia/Makassar' ? 'WITA' : 'WIB';
                returnCreatedAtEl.textContent = `${order.order_return.created_at} ${timezoneLabel}`;
                returnTimestampContainer.classList.remove('hidden');
            } else {
                returnTimestampContainer.classList.add('hidden');
            }
            // Tombol Retur
            const returnRequestButtonContainer = document.getElementById('returnRequestButtonContainer');
            if (order.status === 'diterima_pembeli') {
                returnRequestButtonContainer.classList.remove('hidden');
                document.getElementById('requestReturnButton').onclick = () => openReturnProductModal(order);
            } else {
                returnRequestButtonContainer.classList.add('hidden');
            }

            document.getElementById('modalLoader').classList.add('hidden');
            document.getElementById('modalContent').classList.remove('hidden');
        }

        async function handleProofUpload(orderId, status) {
            const form = document.getElementById('paymentUploadForm');
            const submitButton = form.querySelector('button[type="submit"]');
            let url, nextStatus;

            if (status === 'diterima_pembeli') {
                url = `/kurir/pesanan/${orderId}/upload-proof`;
                nextStatus = 'selesai';
            } else if (status === 'menunggu_retur') {
                url = `/kurir/pesanan/${orderId}/upload-return-proof`;
                nextStatus = 'menunggu_verifikasi_admin';
            } else return;

            submitButton.disabled = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
                dispatchToast(result.message, 'success');

                // DIUBAH: Panggil fungsi update counter
                updateDashboardCounters(status, nextStatus);

                fetchOrderDetails(orderId);
                updateTableRowStatus(orderId, nextStatus);
            } catch (error) {
                dispatchToast(error.message, 'error');
            } finally {
                submitButton.disabled = false;
            }
        }

        // --- Logika Modal Status & Stepper ---
        async function openStatusStepperModal(orderId) {
            openModal('statusStepperModal');
            const modalLoader = document.getElementById('statusStepperModalLoader');
            const modalContent = document.getElementById('statusStepperModalContent');
            modalContent.classList.add('hidden');
            modalLoader.classList.remove('hidden');
            modalLoader.innerHTML =
                `<svg class="w-8 h-8 mx-auto text-blue-600 animate-spin" ...></svg><p class="mt-4 ...">Memuat Status...</p>`;
            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/details`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message);
                populateStatusStepperModal(data);
            } catch (error) {
                modalLoader.innerHTML =
                    `<div class="text-center"><p class="font-bold text-red-600">Gagal Memuat</p><p class="mt-2 text-sm">${error.message}</p></div>`;
            }
        }

        function populateStatusStepperModal(order) {
            // DIUBAH: Menyimpan status order saat ini untuk perbandingan nanti
            currentOrderForUpdate = order;

            const statusMap = {
                'baru': {
                    label: 'Baru',
                    nextStatus: 'diambil',
                    buttonText: 'Ubah Status ke Diambil'
                },
                'dikemas': {
                    label: 'Dikemas',
                    nextStatus: 'diambil',
                    buttonText: 'Ubah Status ke Diambil'
                },
                'diambil': {
                    label: 'Diambil',
                    nextStatus: 'diantar',
                    buttonText: 'Ubah Status ke Diantar'
                },
                'diantar': {
                    label: 'Diantar',
                    nextStatus: 'diterima_pembeli',
                    buttonText: 'Ubah Status ke Diterima Pembeli'
                },
                'diterima_pembeli': {
                    label: 'Diterima Pembeli',
                    nextStatus: null,
                    buttonText: 'Menunggu Bukti Pembayaran'
                },
                'menunggu_retur': {
                    label: 'Menunggu Retur',
                    nextStatus: null,
                    buttonText: 'Menunggu Proses Retur'
                },
                'menunggu_verifikasi_admin': {
                    label: 'Menunggu Verifikasi Admin',
                    nextStatus: null,
                    buttonText: 'Menunggu Verifikasi Admin'
                },
                'selesai': {
                    label: 'Selesai (Lunas)',
                    nextStatus: null,
                    buttonText: 'Pesanan Selesai'
                },
                'diverifikasi_admin': {
                    label: 'Telah Diverifikasi Admin',
                    nextStatus: null,
                    buttonText: 'Telah Diverifikasi Admin'
                }
            };

            document.getElementById('modalStatusInvoiceNumber').textContent = order.invoice_number || 'N/A';
            document.getElementById('modalStatusCustomerName').textContent = order.customer.name ||
                'Pelanggan Tidak Dikenal';

            updateStepperUI(order);

            const updateButton = document.getElementById('updateStatusButton');
            const updateButtonText = document.getElementById('updateStatusButtonText');
            const currentStatus = order.status || 'baru';
            const currentStatusInfo = statusMap[currentStatus];

            // Menyimpan timezone ke tombol untuk digunakan nanti
            updateButton.setAttribute('data-order-timezone', order.timezone || 'Asia/Jakarta');

            if (currentStatusInfo) {
                updateButtonText.textContent = currentStatusInfo.buttonText;

                if (!currentStatusInfo.nextStatus || ['selesai', 'diverifikasi_admin', 'menunggu_verifikasi_admin',
                        'menunggu_retur'
                    ].includes(currentStatus)) {
                    updateButton.disabled = true;
                    updateButton.classList.add('opacity-50', 'cursor-not-allowed');
                    if (currentStatus === 'diverifikasi_admin') {
                        updateButton.classList.remove('bg-blue-700', 'hover:bg-blue-800');
                        updateButton.classList.add('bg-teal-600', 'hover:bg-teal-700');
                    }
                } else {
                    updateButton.disabled = false;
                    updateButton.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-teal-600', 'hover:bg-teal-700');
                    updateButton.classList.add('bg-blue-700', 'hover:bg-blue-800');
                    updateButton.setAttribute('data-next-status', currentStatusInfo.nextStatus);
                }
            } else {
                updateButtonText.textContent = `Status Tidak Dikenal: ${currentStatus}`;
                updateButton.disabled = true;
                updateButton.classList.add('opacity-50', 'cursor-not-allowed');
            }

            updateButton.setAttribute('data-order-id', order.id);
            document.getElementById('statusStepperModalLoader').classList.add('hidden');
            document.getElementById('statusStepperModalContent').classList.remove('hidden');
        }

        // Fungsi pembantu untuk memperbarui UI Stepper secara spesifik
        function updateStepperUI(order) {
            const steps = ['diambil', 'diantar', 'diterima_pembeli'];
            const timestamps = {
                diambil: order.picked_up_at,
                diantar: order.delivered_at,
                diterima_pembeli: order.received_by_buyer_at
            };
            const icons = {
                diambil: 'fa-box',
                diantar: 'fa-truck-moving',
                diterima_pembeli: 'fa-home'
            };
            const timeSpans = {
                diambil: 'pickedUpAt',
                diantar: 'deliveredAt',
                diterima_pembeli: 'receivedByBuyerAt'
            };

            // Menentukan label zona waktu berdasarkan data dari server
            const timezone = order.timezone || 'Asia/Jakarta';
            const tzAbbr = timezone === 'Asia/Makassar' ? 'WITA' : 'WIB';

            // Loop tunggal untuk mengatur setiap langkah
            steps.forEach(step => {
                const iconEl = document.getElementById(`step-${step}-icon`);
                const timeSpanEl = document.getElementById(timeSpans[step]);
                const mobileLineEl = document.getElementById(`line-${step}-mobile`);
                const desktopLineEl = document.getElementById(step === 'diambil' ? 'line-diantar' : `line-${step}`);

                // Reset warna
                iconEl.classList.remove('bg-green-600', 'text-green-600', 'border-green-600');
                if (mobileLineEl) mobileLineEl.classList.remove('bg-green-600');
                if (desktopLineEl) desktopLineEl.classList.remove('bg-green-600');

                // Cek apakah langkah sudah selesai
                if (timestamps[step]) {
                    iconEl.innerHTML =
                    '<i class="text-white dark:text-green-600 fas fa-check-circle"></i>'; // [!code ++]
                    iconEl.classList.add('bg-green-600', 'border-green-600');
                    timeSpanEl.textContent = `${timestamps[step]}  ${tzAbbr}`;
                    if (mobileLineEl) mobileLineEl.classList.add('bg-green-600');
                    if (desktopLineEl) desktopLineEl.classList.add('bg-green-600');
                } else {
                    iconEl.innerHTML = `<i class="fas ${icons[step]} text-green-600"></i>`;
                    timeSpanEl.textContent = tzAbbr; // Tampilkan hanya label jika belum ada waktu
                }
            });
        }

        async function handleStatusUpdate() {
            const updateButton = document.getElementById('updateStatusButton');
            const orderId = updateButton.getAttribute('data-order-id');
            const newStatus = updateButton.getAttribute('data-next-status');
            // [!code --]
            // Mengambil timezone dari atribut data yang disimpan sebelumnya
            const timezone = updateButton.getAttribute('data-order-timezone')

            if (!orderId || !newStatus) {
                dispatchToast('Error: Status atau Order ID tidak ditemukan.', 'error');
                return;
            }

            const buttonText = document.getElementById('updateStatusButtonText');
            const buttonSpinner = document.getElementById('updateStatusButtonSpinner');

            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');
            updateButton.disabled = true;
            updateButton.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        new_status: newStatus
                    })
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui status.');

                dispatchToast(result.message, 'success');

                // [!code block:start]
                // --- LOGIKA WAKTU DENGAN ZONA WAKTU DINAMIS ---
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    timeZone: timezone, // Menggunakan timezone dari server
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
                const localizedTimestamp = formatter.format(now);
                // [!code block:end]

                const timeSpanId = {
                    'diambil': 'pickedUpAt',
                    'diantar': 'deliveredAt',
                    'diterima_pembeli': 'receivedByBuyerAt'
                } [newStatus];

                if (timeSpanId) {
                    // [!code ++]
                    // Menampilkan waktu yang sudah dilokalisasi
                    document.getElementById(timeSpanId).textContent = localizedTimestamp;
                }

                // Muat ulang konten modal untuk mendapatkan data server terbaru
                openStatusStepperModal(orderId);

                // Perbarui status pada baris tabel di halaman utama
                updateTableRowStatus(orderId, result.order.status);

            } catch (error) {
                console.error('Error updating order status:', error);
                dispatchToast(`Gagal: ${error.message}`, 'error');
            } finally {
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        }

        function updateTableRowStatus(orderId, newStatus) {
            const rows = document.querySelectorAll(`[data-order-id="${orderId}"]`);
            if (!rows.length) return;

            const statusText = STATUS_LABEL_MAP[newStatus] || (newStatus.charAt(0).toUpperCase() + newStatus.slice(1)
                .replace(/_/g, ' '));

            let newClasses = 'bg-gray-100 text-gray-800';
            switch (newStatus) {
                case 'diambil':
                    newClasses = 'bg-blue-100 text-blue-800';
                    break;
                case 'diantar':
                    newClasses = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'diterima_pembeli':
                    newClasses = 'bg-purple-100 text-purple-800';
                    break;
                case 'menunggu_retur':
                    newClasses = 'bg-red-100 text-red-800';
                    break;
                case 'menunggu_verifikasi_admin':
                    newClasses = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                    break;
                case 'selesai':
                case 'diverifikasi_admin':
                    newClasses = 'bg-green-100 text-green-800';
                    break;
            }

            const colorClassesToRemove = [
                'bg-blue-100', 'text-blue-800', 'bg-yellow-100', 'text-yellow-800',
                'bg-purple-100', 'text-purple-800', 'bg-green-100', 'text-green-800',
                'bg-red-100', 'text-red-800', 'bg-orange-100', 'text-orange-800',
                'dark:bg-orange-900', 'dark:text-orange-300', 'bg-gray-100', 'text-gray-800'
            ];

            rows.forEach(row => {
                const statusSpan = row.querySelector('.status-badge');
                if (statusSpan) {
                    statusSpan.textContent = statusText;
                    statusSpan.classList.remove(...colorClassesToRemove);
                    newClasses.split(' ').forEach(cls => {
                        if (cls) statusSpan.classList.add(cls);
                    });
                }
            });
        }

        function openReturnProductModal(order) {
            const returnModalLoader = document.getElementById('returnModalLoader');
            const returnModalContent = document.getElementById('returnModalContent');
            const returnOrderIdInput = document.getElementById('returnOrderId');
            const desktopContainer = document.getElementById('return-product-list-desktop');
            const mobileContainer = document.getElementById('return-product-list-mobile');

            returnModalContent.classList.add('hidden');
            returnModalLoader.classList.remove('hidden');
            returnOrderIdInput.value = order.id;
            desktopContainer.innerHTML = '';
            mobileContainer.innerHTML = '';

            if (!order.products || order.products.length === 0) {
                const noProductHTML =
                    '<p class="py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada produk dalam pesanan ini untuk diretur.</p>';
                desktopContainer.innerHTML = `<tr><td colspan="4">${noProductHTML}</td></tr>`;
                mobileContainer.innerHTML = noProductHTML;
                returnModalLoader.classList.add('hidden');
                returnModalContent.classList.remove('hidden');
                return;
            }

            order.products.forEach((product, index) => {
                const productId = product.product_id || product.id;
                const variantId = product.variant_id !== undefined && product.variant_id !== null ? product
                    .variant_id : 0;
                const returnKey = `${productId}-${variantId}`;
                const productImage = product.image_url || 'https://placehold.co/64x64/E2E8F0/64748B?text=No+Img';

                const desktopRowHTML =
                    `<tr data-return-key="${returnKey}"><td class="px-4 py-4 whitespace-nowrap"><div class="text-sm text-gray-900 dark:text-white">${index + 1}</div></td><td class="px-2 py-4"><div class="flex items-center"><div class="flex-shrink-0 w-16 h-16"><img class="object-cover w-16 h-16 rounded-md" src="${productImage}" alt="${product.name}"></div><div class="ml-4"><div class="text-sm font-medium text-gray-900 dark:text-white">${product.name}</div>${product.variant_name ? `<div class="text-xs text-gray-400 dark:text-gray-500">${product.variant_name}</div>` : ''}<div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Awal: ${product.quantity}</div></div></div></td><td class="py-4 whitespace-nowrap"><div class="flex items-center justify-center gap-2"><button type="button" class="px-2 text-black transition rounded quantity-minus hover:bg-gray-300 dark:text-white dark:hover:bg-gray-700 hover:scale-110 active:scale-90">–</button><span data-name="return_qty[${returnKey}]" data-max="${product.quantity}" class="px-2 text-black bg-gray-200 rounded quantity-input dark:text-white dark:bg-gray-700">0</span><button type="button" class="px-2 text-black transition rounded quantity-plus hover:bg-gray-300 dark:text-white dark:hover:bg-gray-700 hover:scale-110 active:scale-90">+</button></div></td><td class="px-4 py-4 text-sm font-medium text-center whitespace-nowrap"><button type="button" class="text-red-600 remove-product hover:text-red-900 dark:hover:text-red-500 hover:scale-110 active:scale-90" title="Setel kuantitas ke 0">🗑</button></td></tr>`;
                const mobileCardHTML =
                    `<div class="flex items-start gap-4 px-4 py-2 mx-0 border-b border-gray-200 dark:border-gray-700" data-return-key="${returnKey}"><div class="flex-shrink-0 w-24 h-24"><img class="object-cover w-24 h-24 rounded-md" src="${productImage}" alt="${product.name}"></div><div class="flex flex-col flex-1"><div class="flex items-center justify-between mb-1"><p class="font-bold text-black dark:text-white">${product.name}</p><button type="button" class="text-red-600 remove-product text-md hover:text-red-900 dark:hover:text-red-500 hover:scale-110 active:scale-90" title="Setel kuantitas ke 0">🗑</button></div>${product.variant_name ? `<p class="mb-1 text-xs text-gray-500 dark:text-gray-400">${product.variant_name}</p>` : ''}<p class="text-sm text-gray-600 dark:text-gray-300">Jumlah Awal: ${product.quantity}</p><div class="flex items-center justify-between mt-3"><div class="flex items-center gap-2"><button type="button" class="px-2 text-black rounded quantity-minus dark:text-white hover:scale-110 active:scale-90">–</button><span data-name="return_qty[${returnKey}]" data-max="${product.quantity}" class="px-2 text-black bg-gray-200 rounded quantity-input dark:text-white dark:bg-gray-700">0</span><button type="button" class="px-2 text-black rounded quantity-plus dark:text-white hover:scale-110 active:scale-90">+</button></div></div></div></div>`;

                desktopContainer.insertAdjacentHTML('beforeend', desktopRowHTML);
                mobileContainer.insertAdjacentHTML('beforeend', mobileCardHTML);
            });

            returnModalLoader.classList.add('hidden');
            returnModalContent.classList.remove('hidden');

            document.getElementById('returnProductForm').onsubmit = (e) => {
                e.preventDefault();
                handleReturnRequestSubmit(order.id);
            };
        }

        async function handleReturnRequestSubmit(orderId) {
            const form = document.getElementById('returnProductForm');
            const submitButton = document.getElementById('submitReturnRequestButton');
            const buttonText = document.getElementById('submitReturnRequestButtonText');
            const buttonSpinner = document.getElementById('submitReturnRequestButtonSpinner');

            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');

            const returnQuantities = {};
            let hasValidReturn = false;

            form.querySelectorAll('.quantity-input').forEach(spanElement => {
                const key = spanElement.dataset.name.match(/\[(.*?)\]/)[1];
                const quantity = parseInt(spanElement.textContent, 10);
                if (!isNaN(quantity) && quantity > 0) {
                    returnQuantities[key] = quantity;
                    hasValidReturn = true;
                }
            });

            if (!hasValidReturn) {
                dispatchToast('Anda harus memasukkan jumlah minimal 1 untuk satu produk.', 'error');
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/request-return`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        return_quantities: returnQuantities
                    })
                });

                const result = await response.json();
                if (!response.ok) {
                    const errorMsg = result.errors ? Object.values(result.errors).flat().join(' ') : result.message;
                    throw new Error(errorMsg || 'Gagal mengajukan pengembalian.');
                }

                dispatchToast(result.message, 'success');

                const returnModal = document.getElementById('returnProductModal');
                if (typeof closeModal === 'function') {
                    closeModal(returnModal);
                }

                // DIUBAH: Panggil fungsi update counter
                const oldStatus = 'diterima_pembeli'; // Asumsi retur hanya bisa dari status ini
                updateDashboardCounters(oldStatus, result.order.status);

                updateTableRowStatus(orderId, result.order.status);
                fetchOrderDetails(orderId);

            } catch (error) {
                dispatchToast(`Gagal: ${error.message}`, 'error');
            } finally {
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        }


        // --- Event Delegation ---
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(event) {
                const openStatusBtn = event.target.closest('.js-open-status-modal');
                if (openStatusBtn) {
                    const orderId = openStatusBtn.getAttribute('data-order-id');
                    openStatusStepperModal(orderId);
                    return;
                }

                const openDetailsBtn = event.target.closest('.js-open-details-modal');
                if (openDetailsBtn) {
                    const orderId = openDetailsBtn.getAttribute('data-order-id');
                    fetchOrderDetails(orderId);
                    return;
                }
            });

            const updateButton = document.getElementById('updateStatusButton');
            if (updateButton) {
                updateButton.addEventListener('click', handleStatusUpdate);
            }
        });
    </script>

    <script src="/assets/argon/js/plugins/chartjs.min.js"></script>
    <script src="/assets/argon/js/plugins/perfect-scrollbar.min.js" async></script>
    <script src="/assets-argon-dashboard-tailwind.js?v=1.0.1" async></script>

@endsection
