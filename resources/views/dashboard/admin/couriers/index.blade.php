@extends('layouts.argon')
@section('title', 'Manajemen Kurir')
@section('page_title', 'Tim Kurir')

@section('content')
    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-3xl shadow-lg space-y-6 min-h-[700px]">
            
            <!-- Header Section -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-2">
                    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20">
                            <i class="fas fa-truck text-lg"></i>
                        </div>
                        <span>Tim & Personel Kurir</span>
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Kelola akun pengantar, performa pengiriman, dan wilayah tugas kurir di Cabang {{ \App\Support\RegionContext::name() ?? 'Cabang Aktif' }}.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <!-- Live Search Input -->
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-sm text-slate-400"></i>
                        </div>
                        <input type="text" id="courier-search-input" name="search"
                            class="block w-full p-3 pl-11 text-sm text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white transition-all shadow-sm hover:shadow-md"
                            placeholder="Cari nama atau email kurir...">
                    </div>

                    <!-- Add Button -->
                    <button type="button" data-target-modal="create-courier-modal"
                        class="js-open-modal-btn w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-brand to-brand-deep hover:from-brand-deep hover:to-brand-deep rounded-2xl shadow-xl shadow-brand-deep/30 transition-all duration-300 hover:scale-105 active:scale-95">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Kurir Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-800 shadow-sm">
                <div class="overflow-x-auto min-h-[500px]">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-extrabold text-slate-400 uppercase bg-slate-50/90 dark:bg-slate-800/90">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-center w-14">No.</th>
                                <th scope="col" class="px-6 py-4">Nama Kurir</th>
                                <th scope="col" class="px-6 py-4 text-center">Email</th>
                                <th scope="col" class="px-6 py-4 text-center">Region</th>
                                <th scope="col" class="px-6 py-4 text-center">Note</th>
                                <th scope="col" class="px-6 py-4 text-center">Tanggal Bergabung</th>
                                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="courier-results-container" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @include('dashboard.admin.couriers._table_rows', ['couriers' => $couriers])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4" aria-label="Table navigation">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-white">{{ $couriers->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800 dark:text-white">{{ $couriers->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800 dark:text-white">{{ $couriers->total() }}</span> kurir
                </p>
                {{ $couriers->links() }}
            </nav>
        </div>
    </div>
@endsection

@push('flowbite-modals')
    @include('dashboard.admin.couriers.create')
    <div id="courier-modals-container">
        @foreach ($couriers as $courier)
            @include('dashboard.admin.couriers.show', ['courier' => $courier])
            @include('dashboard.admin.couriers.edit', ['courier' => $courier])
            @include('dashboard.admin.couriers.note', ['courier' => $courier])
            @include('dashboard.admin.couriers.delete', ['courier' => $courier])
            @include('dashboard.admin.couriers.performance', ['courier' => $courier])
        @endforeach
    </div>
@endpush

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initializeLiveSearch === 'function') {
                initializeLiveSearch({
                    searchInputId: 'courier-search-input',
                    desktopContainerId: 'courier-results-container'
                });
            }

            let performanceCharts = {};

            async function loadPerformanceChart(courierId, filter = 'last_7_days') {
                const loader = document.getElementById(`performance-loader-${courierId}`);
                const content = document.getElementById(`performance-content-${courierId}`);
                const canvas = document.getElementById(`performanceChart-${courierId}`);

                if (loader) loader.style.display = 'block';
                if (content) content.style.display = 'none';

                try {
                    const response = await fetch(
                        `/admin/couriers/${courierId}/performance-data?filter=${filter}`);
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memuat data.');
                    }

                    if (performanceCharts[courierId]) {
                        performanceCharts[courierId].destroy();
                    }

                    const dateRangeElem = document.getElementById(`date-range-${courierId}`);
                    const totalOrdersElem = document.getElementById(`total-orders-${courierId}`);
                    const totalCompletedElem = document.getElementById(`total-completed-${courierId}`);
                    const totalReturnedElem = document.getElementById(`total-returned-${courierId}`);

                    if (dateRangeElem) dateRangeElem.textContent = data.dateRangeText;
                    if (totalOrdersElem) totalOrdersElem.textContent = data.totalOrdersInRange;
                    if (totalCompletedElem) totalCompletedElem.textContent = data.totalCompletedOrdersInRange;
                    if (totalReturnedElem) totalReturnedElem.textContent = data.totalReturnedOrdersInRange;

                    if (canvas && window.Chart) {
                        performanceCharts[courierId] = new Chart(canvas, {
                            type: 'line',
                            data: {
                                labels: data.chartLabels,
                                datasets: [{
                                    label: 'Total Pesanan',
                                    data: data.chartData,
                                    borderColor: '#64748b',
                                    backgroundColor: 'rgba(100, 116, 139, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                }, {
                                    label: 'Selesai',
                                    data: data.chartDataCompleted,
                                    borderColor: '#6f8f5f',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                }, {
                                    label: 'Return',
                                    data: data.chartDataReturned,
                                    borderColor: '#f43f5e',
                                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { color: '#64748b', precision: 0 } },
                                    x: { ticks: { color: '#64748b' } }
                                }
                            }
                        });
                    }

                } catch (error) {
                    console.error('Error loading performance data:', error);
                    if (loader) loader.innerHTML = `<p class="text-rose-500">${error.message}</p>`;
                } finally {
                    if (loader) loader.style.display = 'none';
                    if (content) content.style.display = 'block';
                }
            }

            document.body.addEventListener('click', function(event) {
                const openBtn = event.target.closest('.js-open-performance-modal');
                if (openBtn) {
                    event.preventDefault();
                    const courierId = openBtn.dataset.courierId;
                    const modalId = openBtn.dataset.targetModal;

                    if (window.openModal) {
                        window.openModal(modalId);
                    }

                    loadPerformanceChart(courierId, 'last_7_days');
                }
            });

            document.body.addEventListener('click', function(event) {
                const filterBtn = event.target.closest('.js-performance-filter');
                if (filterBtn) {
                    event.preventDefault();
                    const courierId = filterBtn.dataset.courierId;
                    const filter = filterBtn.dataset.filter;

                    const allButtonsInGroup = filterBtn.closest('.flex').querySelectorAll('.js-performance-filter');
                    allButtonsInGroup.forEach(btn => {
                        btn.classList.remove('bg-brand-deep', 'text-white', 'dark:bg-brand-deep', 'dark:text-white');
                        btn.classList.add('bg-slate-100', 'text-slate-700', 'dark:bg-slate-700', 'dark:text-slate-200');
                    });

                    filterBtn.classList.remove('bg-slate-100', 'text-slate-700', 'dark:bg-slate-700', 'dark:text-slate-200');
                    filterBtn.classList.add('bg-brand-deep', 'text-white', 'dark:bg-brand-deep', 'dark:text-white');

                    loadPerformanceChart(courierId, filter);
                }
            });
        });
    </script>
@endpush
