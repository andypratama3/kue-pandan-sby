@extends('layouts.argon')
@section('title', 'Admin Dashboard')
@section('page_title', 'Admin')

@section('content')

    @php
        use Illuminate\Support\Facades\DB;
        $user = Auth::user();
        $lastSession = DB::table('sessions')->where('user_id', Auth::id())->orderByDesc('last_activity')->first();
        $lastLogin = $lastSession
            ? \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->setTimezone('Asia/Jakarta')
                ->format('d M Y,
    H:i:s')
            : 'Tidak tersedia';

        // Mengatur zona waktu ke Asia/Jakarta (atau zona waktu server Anda)
        date_default_timezone_set('Asia/Jakarta');
        $hour = date('G'); // Mendapatkan jam dalam format 24-jam (0-23)
        $greeting = '';
        $icon = '';

        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
            $iconClass = 'fas fa-sun';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
            $iconClass = 'fas fa-city';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
            $iconClass = 'fas fa-cloud-sun';
        } else {
            $greeting = 'Selamat Malam';
            $iconClass = 'fas fa-moon';
        }
    @endphp

    <!-- Welcome Card Section -->
    <div class="p-6">
        <div class="p-6 bg-green-100 rounded-lg shadow dark:bg-slate-700 dark:shadow-dark-xl dark:border dark:border-slate-600">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Hai, {{ $greeting }} <span
                        class="text-violet-600 dark:text-violet-400">{{ $user->name ?? 'User' }}</span>! <i class="{{ $iconClass }} ml-2"></i>
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Senang melihat Anda kembali. Berikut ringkasan aktivitas hari ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards Section -->
    <div class="p-6">
        <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl"><i class="fas fa-wallet mr-2"></i> Resume Hari Ini</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div
                class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-800 dark:shadow-dark-xl dark:border dark:border-slate-700 rounded-2xl bg-clip-border">
                <div class="flex-auto p-4">
                    <div class="flex flex-row -mx-3">
                        <div class="flex-none w-2/3 max-w-full px-3">
                            <div>
                                <p
                                    class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">
                                    INCOME</p>
                                <h5 class="mb-2 font-bold dark:text-white">IDR
                                    {{ number_format($incomeToday, 0, ',', '.') }}</h5>
                                <p class="mb-0 dark:text-white dark:opacity-60">
                                    @if ($incomePercentageChange >= 0)
                                        <span
                                            class="text-sm font-bold leading-normal text-emerald-500">+{{ number_format($incomePercentageChange, 1) }}%</span>
                                    @else
                                        <span
                                            class="text-sm font-bold leading-normal text-red-400">{{ number_format($incomePercentageChange, 1) }}%</span>
                                    @endif
                                    since yesterday
                                </p>
                            </div>
                        </div>
                        <div class="px-3 text-right basis-1/3">
                            <div
                                class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-blue-500 to-violet-500">
                                <i class="fas fa-dollar-sign leading-none text-lg relative top-3.5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-800 dark:shadow-dark-xl dark:border dark:border-slate-700 rounded-2xl bg-clip-border">
                <div class="flex-auto p-4">
                    <div class="flex flex-row -mx-3">
                        <div class="flex-none w-2/3 max-w-full px-3">
                            <div>
                                <p
                                    class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">
                                    Total Sales</p>
                                <h5 class="mb-2 font-bold dark:text-white">{{ $totalSalesToday }}</h5>
                                <p class="mb-0 dark:text-white dark:opacity-60">
                                    @if ($salesPercentageChange >= 0)
                                        <span
                                            class="text-sm font-bold leading-normal text-emerald-500">+{{ number_format($salesPercentageChange, 1) }}%</span>
                                    @else
                                        <span
                                            class="text-sm font-bold leading-normal text-red-400">{{ number_format($salesPercentageChange, 1) }}%</span>
                                    @endif
                                    than last month
                                </p>
                            </div>
                        </div>
                        <div class="px-3 text-right basis-1/3">
                            <div
                                class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-orange-500 to-yellow-500">
                                <i class="fas fa-chart-line leading-none text-lg relative top-3.5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-800 dark:shadow-dark-xl dark:border dark:border-slate-700 rounded-2xl bg-clip-border">
                <div class="flex-auto p-4">
                    <div class="flex flex-row -mx-3">
                        <div class="flex-none w-2/3 max-w-full px-3">
                            <div>
                                <p
                                    class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">
                                    average sales</p>
                                <h5 class="mb-2 font-bold dark:text-white">{{ $avgSalesPerMonth }}</h5>
                                <p class="mb-0 dark:text-white dark:opacity-60">
                                    @if ($avgSalesPercentageChange >= 0)
                                        <span
                                            class="text-sm font-bold leading-normal text-emerald-500">+{{ number_format($salesPercentageChange, 1) }}%</span>
                                    @else
                                        <span
                                            class="text-sm font-bold leading-normal text-red-400">{{ number_format($salesPercentageChange, 1) }}%</span>
                                    @endif
                                    than last year
                                </p>
                            </div>
                        </div>
                        <div class="px-3 text-right basis-1/3">
                            <div
                                class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-orange-500 to-yellow-500">
                                <i class="fas fa-chart-line leading-none text-lg relative top-3.5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-800 dark:shadow-dark-xl dark:border dark:border-slate-700 rounded-2xl bg-clip-border">
                <div class="flex-auto p-4">
                    <div class="flex flex-row -mx-3">
                        <div class="flex-none w-2/3 max-w-full px-3">
                            <div>
                                <p
                                    class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">
                                    Customer {{ $user->region->name ?? 'Tidak ada region' }}</p>
                                <h5 class="mb-2 font-bold dark:text-white">
                                    {{ number_format($totalCustomersInRegion, 0, ',', '.') }}</h5>
                                <p class="mb-0 dark:text-white dark:opacity-60">
                                    @if ($customerPercentageChange >= 0)
                                        <span
                                            class="text-sm font-bold leading-normal text-emerald-500">+{{ number_format($customerPercentageChange, 1) }}%</span>
                                    @else
                                        <span
                                            class="text-sm font-bold leading-normal text-red-400">{{ number_format($customerPercentageChange, 1) }}%</span>
                                    @endif
                                    since last week
                                </p>
                            </div>
                        </div>
                        <div class="px-3 text-right basis-1/3">
                            <div
                                class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-red-600 to-orange-600">
                                <i class="fas fa-users leading-none text-lg relative top-3.5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-800 dark:shadow-dark-xl dark:border dark:border-slate-700 rounded-2xl bg-clip-border">
                <div class="flex-auto p-4">
                    <div class="flex flex-row -mx-3">
                        <div class="flex-none w-2/3 max-w-full px-3">
                            <div>
                                <p
                                    class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">
                                    New Customer</p>
                                <h5 class="mb-2 font-bold dark:text-white">
                                    +{{ number_format($newCustomersToday, 0, ',', '.') }}</h5>
                                <p class="mb-0 dark:text-white dark:opacity-60">
                                    @if ($newCustomerPercentageChange >= 0)
                                        <span
                                            class="text-sm font-bold leading-normal text-emerald-500">+{{ number_format($newCustomerPercentageChange, 1) }}%</span>
                                    @else
                                        <span
                                            class="text-sm font-bold leading-normal text-red-400">{{ number_format($newCustomerPercentageChange, 1) }}%</span>
                                    @endif
                                    since yesterday
                                </p>
                            </div>
                        </div>
                        <div class="px-3 text-right basis-1/3">
                            <div
                                class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-emerald-500 to-teal-400">
                                <i class="fas fa-user-plus leading-none text-lg relative top-3.5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="flex flex-col gap-6 p-6">

        <div
            class="relative overflow-hidden bg-white border border-gray-100 shadow-2xl rounded-3xl dark:bg-slate-800 dark:border-slate-700">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
                    <div class="flex flex-col">
                        <h6 class="flex items-center gap-2 text-lg font-bold text-gray-800 dark:text-white">
                            <i class="text-blue-500 fas fa-chart-line"></i>
                            <span>Grafik Penjualan</span>
                        </h6>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-calendar-alt mr-1"></i> {{ $dateRangeText }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <span class="w-3 h-3 mr-2 bg-blue-500 rounded-full"></span>
                            <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total </span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $totalOrdersInRange }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 mr-2 bg-green-500 rounded-full"></span>
                            <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Verifikasi </span>
                            <span
                                class="text-sm font-bold text-gray-800 dark:text-white">{{ $totalVerifiedInRange }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 mr-2 bg-orange-500 rounded-full"></span>
                            <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Retur </span>
                            <span
                                class="text-sm font-bold text-gray-800 dark:text-white">{{ $totalVerifiedWithReturnInRange }}</span>
                        </div>
                        <div class="relative">
                            <button id="chartFilterButton" type="button"
                                class="text-gray-500 js-dropdown-toggle hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none"
                                data-target-dropdown="chartFilterDropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="chartFilterDropdown"
                                class="absolute right-0 z-20 hidden w-40 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-slate-700 js-dropdown-menu dark:border-slate-600">
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'filter' => 'last_7_days',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-150 {{ $filter === 'last_7_days' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">7
                                    Hari</a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'filter' => 'daily',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold transition-colors duration-150 {{ $filter === 'daily' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Harian</a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'filter' => 'weekly',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold transition-colors duration-150 {{ $filter === 'weekly' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Mingguan</a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'filter' => 'monthly',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-b-lg transition-colors duration-150 {{ $filter === 'monthly' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Bulanan</a>
                                <a href="{{ route('admin.dashboard', ['region' => Auth::user()->region->slug, 'filter' => 'last_month']) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-b-lg transition-colors duration-150 {{ $filter === 'last_month' ? 'bg-blue-100 text-blue-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600' }}">Bulan
                                    Lalu</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-auto p-4 pt-0">
                    <div>
                        <canvas id="adminOrdersChart" class="h-[250px] lg:h-[300px]"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- visitor chart --}}
        <div
            class="relative overflow-hidden bg-white border border-gray-100 shadow-2xl rounded-3xl dark:bg-slate-800 dark:border-slate-700">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
                    <div class="flex flex-col">
                        <h6 class="flex items-center gap-2 text-lg font-bold text-gray-800 dark:text-white">
                            <i class="text-purple-500 fas fa-users"></i>
                            <span>Grafik Kunjungan</span>
                        </h6>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $visitDateRangeText }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <span class="w-3 h-3 mr-2 bg-purple-500 rounded-full"></span>
                            <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">
                                Total
                            </span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ $totalVisitsInRange }}
                            </span>
                        </div>

                        {{-- Filter --}}
                        <div class="relative">
                            <button id="visitChartFilterButton" type="button"
                                class="text-gray-500 js-dropdown-toggle hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none"
                                data-target-dropdown="visitChartFilterDropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div id="visitChartFilterDropdown"
                                class="absolute right-0 z-20 hidden w-40 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-slate-700 js-dropdown-menu dark:border-slate-600">
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'visit_filter' => 'last_7_days',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-t-lg
                            {{ $visitFilter === 'last_7_days' ? 'bg-purple-100 text-purple-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-slate-600' }}">
                                    7 Hari
                                </a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'visit_filter' => 'daily',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold
                            {{ $visitFilter === 'daily' ? 'bg-purple-100 text-purple-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-slate-600' }}">
                                    Harian
                                </a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'visit_filter' => 'weekly',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold
                            {{ $visitFilter === 'weekly' ? 'bg-purple-100 text-purple-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-slate-600' }}">
                                    Mingguan
                                </a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'visit_filter' => 'monthly',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-b-lg
                            {{ $visitFilter === 'monthly' ? 'bg-purple-100 text-purple-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-slate-600' }}">
                                    Bulanan
                                </a>
                                <a href="{{ route(
                                    'admin.dashboard',
                                    array_merge(request()->query(), [
                                        'region' => Auth::user()->region->slug,
                                        'visit_filter' => 'last_month',
                                    ]),
                                ) }}"
                                    class="block px-4 py-2 text-sm font-semibold rounded-b-lg
                            {{ $visitFilter === 'last_month' ? 'bg-purple-100 text-purple-700 dark:bg-slate-600 dark:text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-slate-600' }}">
                                    Bulan Lalu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-auto p-4 pt-0">
                    <canvas id="visitChart" class="h-[250px] lg:h-[300px]"></canvas>
                </div>
            </div>
        </div>
        {{-- visitor chart --}}

        <div
            class="relative flex flex-col overflow-hidden bg-white border border-gray-100 shadow-2xl rounded-3xl dark:bg-slate-800 dark:border-slate-700">
            <div class="p-6">
                {{-- Header Kartu --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div class="flex flex-col">
                        <h6 class="flex items-center gap-2 text-lg font-bold text-gray-800 dark:text-white">
                            <i class="text-blue-500 fas fa-shipping-fast"></i>
                            <span>Monitoring Kurir</span>
                        </h6>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Daftar kurir aktif di region {{ Auth::user()->region->name }}
                        </p>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('admin.couriers.index') }}"
                            class="px-4 py-2 text-xs font-bold text-white uppercase bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                {{-- Konten Tabel --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3 w-[5%] text-center">No.</th>
                                <th scope="col" class="px-4 py-3 w-[40%]">Nama Kurir</th>
                                <th scope="col" class="px-4 py-3 w-[40%]">Email</th>
                                <th scope="col" class="px-4 py-3 w-[15%] text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($couriers as $kurir)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-center text-gray-900 dark:text-white">
                                        {{ ($couriers->currentPage() - 1) * $couriers->perPage() + $loop->iteration }}
                                    </td>
                                    <th scope="row"
                                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $kurir->name }}
                                    </th>
                                    <td class="px-4 py-3">{{ $kurir->email }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b dark:border-gray-700">
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada data kurir di region ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Navigasi Paginasi --}}
                <nav class="flex justify-end w-full pt-4 mt-auto" aria-label="Table navigation">
                    {{ $couriers->links() }}
                </nav>
            </div>
        </div>

    </div>

@endsection

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // visitor chart
        const visitCtx = document.getElementById('visitChart').getContext('2d');

        new Chart(visitCtx, {
            type: 'line',
            data: {
                labels: @json($visitChartLabels),
                datasets: [{
                    label: 'Kunjungan',
                    data: @json($visitChartData),
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                }]
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
                            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                        }
                    }
                }
            }
        });
        // visitor chart


        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('adminOrdersChart');
            if (ctx && window.Chart) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                                label: 'Total Pesanan',
                                data: @json($chartDataTotal),
                                borderColor: '#3b82f6', // Biru
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Diverifikasi',
                                data: @json($chartDataVerified),
                                borderColor: '#22c55e', // Hijau
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Retur',
                                data: @json($chartDataVerifiedWithReturn),
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
        });
    </script>
@endpush
