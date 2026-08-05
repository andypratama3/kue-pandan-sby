@extends('layouts.argon')
@section('title', 'Manajemen Customer')
@section('page_title', 'Customer')

@section('content')
    {{-- Container utama --}}
    <div class="relative p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700 min-h-[715px]">
        {{-- Header: Title, Search dan Tombol Tambah --}}
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-users mr-2 text-blue-500"></i>Manajemen Customer
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data customer untuk region {{ Auth::user()->region->name ?? 'N/A' }}</p>
            </div>
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:w-auto w-full">
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                        placeholder="Cari customer...">
                </div>
                <button type="button" data-target-modal="create-customer-modal"
                    class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl js-open-modal-btn hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:from-blue-600 dark:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 focus:outline-none dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
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
                            <th scope="col" class="px-6 py-4">Nama Perusahaan</th>
                            <th scope="col" class="px-6 py-4">Nama Customer</th>
                            <th scope="col" class="px-6 py-4">Alamat</th>
                            <th scope="col" class="px-6 py-4 text-center">Nomor Telepon</th>
                            <th scope="col" class="px-6 py-4 text-center">Note</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="customer-results-container" class="divide-y divide-gray-200 dark:divide-slate-700">
                        {{-- Konten tabel akan dimuat di sini oleh live search --}}
                        @include('dashboard.admin.customers._table_rows', ['customers' => $customers])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginasi --}}
        <nav class="flex justify-between items-center mt-6" aria-label="Table navigation">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->total() }}</span> data
            </p>
            {{ $customers->withQueryString()->links() }}
        </nav>
    </div>

    {{-- Container untuk modals hasil live search --}}
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
            initializeLiveSearch({
                searchInputId: 'live-search-input',
                desktopContainerId: 'customer-results-container'
            });

            const resultsContainer = document.getElementById('customer-results-container');
            resultsContainer.addEventListener('click', function(event) {
                const flagButton = event.target.closest('.toggle-flag-btn');
                if (flagButton) {
                    event.preventDefault();
                    toggleCustomerFlag(flagButton);
                }
            });
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
                    // DIUBAH: Logika untuk mengubah tampilan bendera secara dinamis
                    if (data.is_flagged) {
                        // Jika status menjadi DITANDAI
                        icon.classList.remove('text-gray-400', 'hover:text-gray-600');
                        icon.classList.add('text-red-500');
                        icon.setAttribute('title', 'Customer Bermasalah. Klik untuk menghapus tanda.');
                    } else {
                        // Jika status menjadi NORMAL (tanda dihilangkan)
                        icon.classList.remove('text-red-500');
                        icon.classList.add('text-gray-400', 'hover:text-gray-600');
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
