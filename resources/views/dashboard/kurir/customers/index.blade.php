@extends('layouts.argon')
@section('title', 'Data Customer')
@section('page_title', 'Customer')

@section('content')
    <div class="relative min-h-[715px] bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        {{-- Header: Search dan Tombol Tambah --}}
        <div class="flex flex-col p-4 space-y-3 md:flex-row md:items-center md:justify-between md:space-y-0 md:space-x-4">
            <div class="w-full md:w-1/2">
                <form class="flex items-center" onsubmit="return false;">
                    <label for="live-search-input" class="sr-only">Cari</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                            class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-brand focus:border-brand dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Cari customer...">
                    </div>
                </form>
            </div>
            {{-- Tombol Tambah Data --}}
            <button type="button" data-target-modal="create-customer-modal"
                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-brand rounded-lg js-open-modal-btn md:w-auto hover:bg-brand-deep focus:ring-4 focus:ring-brand-light dark:bg-brand dark:hover:bg-brand-deep focus:outline-none dark:focus:ring-brand-deep">
                <i class="fas fa-plus me-2"></i>
                Tambah Data
            </button>
        </div>

        {{-- Desktop Table View --}}
        <div class="relative hidden overflow-x-auto min-h-[580px] md:block">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                {{-- ========================= PERUBAHAN HEADER TABEL ========================= --}}
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center">No.</th>
                        <th scope="col" class="px-4 py-3">Nama Toko</th>
                        <th scope="col" class="px-4 py-3">Nama Customer</th>
                        <th scope="col" class="px-4 py-3">Alamat</th>
                        <th scope="col" class="px-4 py-3 text-center">Nomor Telepon</th>
                        <th scope="col" class="px-4 py-3 text-center">Note</th>
                        <th scope="col" class="px-4 py-3 text-center"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                {{-- ========================================================================== --}}
                <tbody id="customer-results-container-desktop">
                    @include('dashboard.kurir.customers._table_rows', ['customers' => $customers])
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View (Tidak ada perubahan di sini) --}}
        <div id="customer-results-container-mobile" class="grid grid-cols-1 gap-4 p-4 md:hidden">
            @include('dashboard.kurir.customers._card_view', ['customers' => $customers])
        </div>

        <div class="flex justify-center w-full p-4 md:justify-end">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>
@endsection


@push('flowbite-modals')
    {{-- Modal create tetap di luar container agar selalu tersedia --}}
    @if(isset($customerCategories))
        @include('dashboard.kurir.customers.create', ['customerCategories' => $customerCategories])
    @else
        @include('dashboard.kurir.customers.create', ['customerCategories' => []])
    @endif
    {{-- Container untuk modal customer hasil search --}}
    <div id="customer-modals-container">
        @foreach ($customers as $customer)
            @include('dashboard.kurir.customers.show', ['customer' => $customer])
            @include('dashboard.kurir.customers.edit', ['customer' => $customer, 'customerCategories' => $customerCategories])
            @include('dashboard.kurir.customers.note', ['customer' => $customer])
            @include('dashboard.kurir.customers.delete', ['customer' => $customer])
        @endforeach
    </div>
@endpush

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi live search untuk kedua view
            initializeLiveSearch({
                searchInputId: 'live-search-input',
                desktopContainerId: 'customer-results-container-desktop',
                mobileContainerId: 'customer-results-container-mobile'
            });
        });
    </script>
@endpush
