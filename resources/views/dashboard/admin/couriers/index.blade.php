@extends('layouts.argon')
@section('title', 'Manajemen Kurir')
@section('page_title', 'Kurir')

@section('content')
    <div class="relative p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700 min-h-[715px]">
        {{-- Header: Title, Search dan Tombol Tambah --}}
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-truck mr-2 text-blue-500"></i>Manajemen Kurir
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data kurir untuk region {{ Auth::user()->region->name ?? 'N/A' }}</p>
            </div>
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:w-auto w-full">
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="courier-search-input" name="search"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                        placeholder="Cari berdasarkan nama atau email">
                </div>
                <button type="button" data-target-modal="create-courier-modal"
                    class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl js-open-modal-btn hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:from-blue-600 dark:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 focus:outline-none dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kurir
                </button>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-hidden border border-gray-200 rounded-xl dark:border-slate-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-slate-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center">No.</th>
                            <th scope="col" class="px-6 py-4">Nama Kurir</th>
                            <th scope="col" class="px-6 py-4 text-center">Email</th>
                            <th scope="col" class="px-6 py-4 text-center">Region</th>
                            <th scope="col" class="px-6 py-4 text-center">Note</th>
                            <th scope="col" class="px-6 py-4 text-center">Tanggal Bergabung</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="courier-results-container" class="divide-y divide-gray-200 dark:divide-slate-700">
                        @include('dashboard.admin.couriers._table_rows', ['couriers' => $couriers])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginasi --}}
        <nav class="flex justify-between items-center mt-6" aria-label="Table navigation">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $couriers->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $couriers->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900 dark:text-white">{{ $couriers->total() }}</span> data
            </p>
            {{ $couriers->links() }}
        </nav>
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
            // Objek untuk menyimpan instance chart agar bisa di-destroy
            let performanceCharts = {};

            // Fungsi utama untuk memuat dan merender chart
            async function loadPerformanceChart(courierId, filter = 'last_7_days') {
                const loader = document.getElementById(`performance-loader-${courierId}`);
                const content = document.getElementById(`performance-content-${courierId}`);
                const canvas = document.getElementById(`performanceChart-${courierId}`);

                // Tampilkan loader
                loader.style.display = 'block';
                content.style.display = 'none';

                try {
                    const response = await fetch(
                        `/admin/couriers/${courierId}/performance-data?filter=${filter}`);
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memuat data.');
                    }

                    // Hancurkan chart lama jika ada
                    if (performanceCharts[courierId]) {
                        performanceCharts[courierId].destroy();
                    }

                    // Update info teks
                    document.getElementById(`date-range-${courierId}`).textContent = data.dateRangeText;
                    document.getElementById(`total-orders-${courierId}`).textContent = data.totalOrdersInRange;
                    document.getElementById(`total-completed-${courierId}`).textContent = data
                        .totalCompletedOrdersInRange;
                    document.getElementById(`total-returned-${courierId}`).textContent = data
                        .totalReturnedOrdersInRange;

                    // Buat chart baru
                    performanceCharts[courierId] = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: data.chartLabels,
                            datasets: [{
                                label: 'Total Pesanan',
                                data: data.chartData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                            }, {
                                label: 'Selesai',
                                data: data.chartDataCompleted,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                            }, {
                                label: 'Return',
                                data: data.chartDataReturned,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.4,
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
                                        color: '#6b7280',
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6b7280'
                                    }
                                }
                            }
                        }
                    });

                } catch (error) {
                    console.error('Error loading performance data:', error);
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height); // Bersihkan canvas
                    loader.innerHTML = `<p class="text-red-500">${error.message}</p>`;
                } finally {
                    // Sembunyikan loader dan tampilkan konten
                    loader.style.display = 'none';
                    content.style.display = 'block';
                }
            }

            // Event listener untuk membuka modal dan memuat chart pertama kali
            document.body.addEventListener('click', function(event) {
                const openBtn = event.target.closest('.js-open-performance-modal');
                if (openBtn) {
                    event.preventDefault();
                    const courierId = openBtn.dataset.courierId;
                    const modalId = openBtn.dataset.targetModal;

                    // Buka modal (menggunakan fungsi global dari custom-modal.js)
                    if (window.openModal) {
                        window.openModal(modalId);
                    }

                    // Muat chart dengan filter default
                    loadPerformanceChart(courierId, 'last_7_days');
                }
            });

            // Event listener untuk tombol filter di dalam modal
            document.body.addEventListener('click', function(event) {
                const filterBtn = event.target.closest('.js-performance-filter');
                if (filterBtn) {
                    event.preventDefault();
                    const courierId = filterBtn.dataset.courierId;
                    const filter = filterBtn.dataset.filter;
                    loadPerformanceChart(courierId, filter);

                    // Tutup dropdown setelah filter dipilih
                    const dropdown = filterBtn.closest('.js-dropdown-menu');
                    if (dropdown) dropdown.classList.add('hidden');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeLiveSearch({
                searchInputId: 'courier-search-input',
                desktopContainerId: 'courier-results-container',
            });

            // --- Validasi untuk Form Tambah Kurir ---
            const createForm = document.forms['createCourierForm'];
            if (createForm) {
                createForm.addEventListener('submit', function(event) {
                    const password = createForm.elements['password'];
                    const passwordConfirmation = createForm.elements['password_confirmation'];
                    const errorElement = document.getElementById('create_password_error');

                    password.classList.remove('border-red-500');
                    passwordConfirmation.classList.remove('border-red-500');
                    errorElement.classList.add('hidden');

                    if (password.value !== passwordConfirmation.value) {
                        event.preventDefault();
                        errorElement.classList.remove('hidden');
                        password.classList.add('border-red-500');
                        passwordConfirmation.classList.add('border-red-500');
                    }
                });
            }

            // --- Validasi untuk Semua Form Edit Kurir ---
            document.querySelectorAll('[id^="edit-courier-modal-"]').forEach(modal => {
                const form = modal.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        const courierId = modal.id.split('-').pop();
                        const password = document.getElementById(`password-${courierId}`);
                        const passwordConfirmation = document.getElementById(
                            `password_confirmation-${courierId}`);
                        const errorElement = document.getElementById(
                            `edit_password_error-${courierId}`);

                        password.classList.remove('border-red-500');
                        passwordConfirmation.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');

                        if (password.value !== '' && password.value !== passwordConfirmation
                            .value) {
                            event.preventDefault();
                            if (errorElement) errorElement.classList.remove('hidden');
                            password.classList.add('border-red-500');
                            passwordConfirmation.classList.add('border-red-500');
                        }
                    });
                }
            });

            // --- Fungsi untuk Toggle Password Visibility ---
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
@endpush
