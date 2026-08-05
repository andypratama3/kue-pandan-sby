@extends('layouts.argon')
@section('title', 'Manajemen Customer')
@section('page_title', 'Master Customer')

@section('content')
    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6 min-h-[700px]">
            
            <!-- Header Section -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-users text-base"></i>
                        </div>
                        <span>Data Master Customer & Reseller</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Kelola informasi lokasi, kontak reseller, dan riwayat pesanan di Cabang {{ Auth::user()->region->name ?? 'N/A' }}.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <!-- Live Search Field -->
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class="fas fa-search text-xs text-slate-400"></i>
                        </div>
                        <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                            class="block w-full p-2.5 pl-10 text-xs text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white transition-all"
                            placeholder="Cari nama customer atau alamat...">
                    </div>

                    <!-- Add Button -->
                    <button type="button" data-target-modal="create-customer-modal"
                        class="js-open-modal-btn w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-brand to-brand-deep hover:from-brand-deep hover:to-brand-deep rounded-2xl shadow-lg shadow-brand-deep/25 transition-all hover:scale-105 active:scale-95">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Customer Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900">
                <div class="overflow-x-auto min-h-[500px]">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] font-extrabold text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 text-center w-12">No.</th>
                                <th scope="col" class="px-5 py-3.5">Nama Perusahaan</th>
                                <th scope="col" class="px-5 py-3.5">Nama Customer</th>
                                <th scope="col" class="px-5 py-3.5">Alamat</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Nomor Telepon</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Catatan</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customer-results-container" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @include('dashboard.admin.customers._table_rows', ['customers' => $customers])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2" aria-label="Table navigation">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-white">{{ $customers->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800 dark:text-white">{{ $customers->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800 dark:text-white">{{ $customers->total() }}</span> customer
                </p>
                {{ $customers->withQueryString()->links() }}
            </nav>
        </div>
    </div>

    <!-- Modals Container for Live Search -->
    <div id="customer-modals-container"></div>
@endsection

@push('flowbite-modals')
    @include('dashboard.admin.customers.create', [
        'customerCategories' => $customerCategories,
        'couriers' => $couriers,
    ])

    @foreach ($customers as $customer)
        @include('dashboard.admin.customers.show', ['customer' => $customer])
        @include('dashboard.admin.customers.edit', [
            'customer' => $customer,
            'customerCategories' => $customerCategories,
            'couriers' => $couriers,
        ])
        @include('dashboard.admin.customers.note', ['customer' => $customer])
        @include('dashboard.admin.customers.delete', ['customer' => $customer])
        @include('dashboard.admin.customers.rekap', ['customer' => $customer])
    @endforeach
@endpush

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initializeLiveSearch === 'function') {
                initializeLiveSearch({
                    searchInputId: 'live-search-input',
                    desktopContainerId: 'customer-results-container'
                });
            }

            const resultsContainer = document.getElementById('customer-results-container');
            if (resultsContainer) {
                resultsContainer.addEventListener('click', function(event) {
                    const flagButton = event.target.closest('.toggle-flag-btn');
                    if (flagButton) {
                        event.preventDefault();
                        toggleCustomerFlag(flagButton);
                    }
                });
            }
        });

        async function toggleCustomerFlag(button) {
            const url = button.dataset.url;
            const icon = button.querySelector('i');
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                if (data.success) {
                    if (data.is_flagged) {
                        icon.classList.remove('text-slate-400', 'hover:text-slate-600');
                        icon.classList.add('text-rose-500');
                        icon.setAttribute('title', 'Customer Bermasalah. Klik untuk menghapus tanda.');
                    } else {
                        icon.classList.remove('text-rose-500');
                        icon.classList.add('text-slate-400', 'hover:text-slate-600');
                        icon.setAttribute('title', 'Tandai sebagai customer bermasalah.');
                    }
                }
            } catch (error) {
                console.error('Flag toggle error:', error);
                alert('Gagal mengubah status customer.');
            }
        }
    </script>
@endpush
