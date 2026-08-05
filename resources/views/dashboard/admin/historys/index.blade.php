@extends('layouts.argon')
@section('title', 'History Pesanan')
@section('page_title', 'Riwayat Transaksi')

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-xs font-semibold text-brand-deep rounded-2xl bg-mint dark:bg-brand-deep/40 dark:text-brand-light border border-brand-light dark:border-brand-deep flex items-center gap-2 shadow-sm" role="alert">
            <i class="fas fa-check-circle text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6 min-h-[700px]">
            
            <!-- Header & Filter Form -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-file-invoice text-base"></i>
                        </div>
                        <span>Riwayat Transaksi & Laporan PDF</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Arsip pesanan selesai dan cetak rekapitulasi laporan bulanan.
                    </p>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative" x-data="{ openFilterDropdown: false }">
                        <button @click="openFilterDropdown = !openFilterDropdown" type="button"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 rounded-2xl transition-colors">
                            <i class="fas fa-filter text-brand"></i>
                            <span>Filter Periode</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
                        </button>

                        <div x-show="openFilterDropdown" @click.away="openFilterDropdown = false" x-transition
                            class="absolute right-0 z-50 w-64 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 p-4 space-y-3">
                            <div>
                                <label class="block mb-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bulan</label>
                                <select name="month"
                                    class="w-full px-3 py-2 text-xs border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}" @if ($selectedMonth == $num) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Kurir</label>
                                <select name="courier"
                                    class="w-full px-3 py-2 text-xs border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="">Semua Kurir</option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{ $courier['id'] }}" @if ($selectedCourier == $courier['id']) selected @endif>
                                            {{ $courier['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Tahun</label>
                                <select name="year"
                                    class="w-full px-3 py-2 text-xs border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" @if ($selectedYear == $year) selected @endif>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-deep rounded-2xl shadow-md transition-all">
                        <i class="fas fa-eye"></i> Tampilkan
                    </button>
                    <a href="{{ route('admin.historys.export.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-2xl shadow-md transition-all">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </form>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center">
                <form class="flex items-center w-full sm:w-auto" onsubmit="return false;">
                    <div class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class="fas fa-search text-xs text-slate-400"></i>
                        </div>
                        <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                            class="block w-full p-2.5 pl-10 text-xs text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white transition-all"
                            placeholder="Cari invoice atau nama customer...">
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900">
                <div class="overflow-x-auto min-h-[500px]">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] font-extrabold text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-center w-12">No.</th>
                                <th class="px-5 py-3.5">Invoice</th>
                                <th class="px-5 py-3.5">Customer</th>
                                <th class="px-5 py-3.5 text-center">Total</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-center">Tanggal</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-results-container" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @include('dashboard.admin.historys._table_rows', ['orders' => $orders])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2" aria-label="Table navigation">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-white">{{ $orders->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800 dark:text-white">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800 dark:text-white">{{ $orders->total() }}</span> riwayat
                </p>
                {{ $orders->withQueryString()->links() }}
            </nav>
        </div>
    </div>
@endsection

@push('flowbite-modals')
    @include('dashboard.admin.historys.show-modal')

    @foreach ($orders as $historys)
        @include('dashboard.admin.historys.delete', ['historys' => $historys])
    @endforeach
@endpush

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('showOrderModal');
            if (!modalElement) return;

            const loader = document.getElementById('showOrderModalLoader');
            const content = document.getElementById('showOrderModalContent');
            const contentGrid = document.getElementById('showOrderModalGrid');
            const errorContainer = document.getElementById('showOrderModalError');
            const zoomWrapper = document.getElementById('showOrderModalZoomWrapper-admin');
            const zoomImg = document.getElementById('showOrderModalZoomImg-admin');

            const showError = (title, message) => {
                if (errorContainer) {
                    const t = errorContainer.querySelector('[data-role="error-title"]');
                    const m = errorContainer.querySelector('[data-role="error-message"]');
                    if (t) t.textContent = title;
                    if (m) m.textContent = message;
                    if (contentGrid) contentGrid.classList.add('hidden');
                    errorContainer.classList.remove('hidden');
                }
            };

            const showContent = () => {
                if (contentGrid) contentGrid.classList.remove('hidden');
                if (errorContainer) errorContainer.classList.add('hidden');
            };

            const showLoader = () => {
                if (loader) loader.classList.remove('hidden');
                if (content) content.classList.add('hidden');
            };
            const hideLoader = () => {
                if (loader) loader.classList.add('hidden');
                if (content) content.classList.remove('hidden');
            };

            const openModal = async (orderId) => {
                modalElement.classList.remove('hidden');
                modalElement.classList.add('flex');
                showLoader();

                const url = `{{ url('admin/historys') }}/${orderId}/details`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    hideLoader();

                    if (data.error) {
                        showError('Gagal Memuat Detail', data.error);
                        return;
                    }
                    showContent();

                    const invoiceElem = document.getElementById('showOrderModalInvoice');
                    const customerNameElem = document.getElementById('showOrderModalCustomerName');
                    const customerAddressElem = document.getElementById('showOrderModalCustomerAddress');
                    const customerPhoneElem = document.getElementById('showOrderModalCustomerPhone');
                    const courierNameElem = document.getElementById('showOrderModalCourierName');
                    const orderDateElem = document.getElementById('showOrderModalOrderDate');
                    const totalElem = document.getElementById('showOrderModalTotal');
                    const noteElem = document.getElementById('showOrderModalNote');
                    const statusBadgeContainer = document.getElementById('showOrderModalStatusBadge');
                    const proofImg = document.getElementById('showOrderModalProofImg');

                    if (invoiceElem) invoiceElem.textContent = data.invoice_number || 'N/A';
                    if (customerNameElem) customerNameElem.textContent = data.customer_name || 'N/A';
                    if (customerAddressElem) customerAddressElem.textContent = data.customer_address || 'N/A';
                    if (customerPhoneElem) customerPhoneElem.textContent = data.customer_phone || 'N/A';
                    if (courierNameElem) courierNameElem.textContent = data.courier_name || 'N/A';
                    if (orderDateElem) orderDateElem.textContent = data.created_at || 'N/A';
                    if (totalElem) totalElem.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.total_amount || 0)}`;
                    if (noteElem) noteElem.textContent = data.notes || 'Tidak ada catatan.';

                    if (statusBadgeContainer) {
                        statusBadgeContainer.innerHTML = `<span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-brand-light text-brand-deep dark:bg-brand-deep dark:text-brand-light">${data.status_label || 'Selesai'}</span>`;
                    }

                    if (proofImg) {
                        if (data.proof_image_url) {
                            proofImg.src = data.proof_image_url;
                            proofImg.classList.remove('hidden');
                        } else {
                            proofImg.classList.add('hidden');
                        }
                    }

                    const itemsContainer = document.getElementById('showOrderModalItemsContainer');
                    if (itemsContainer && data.items) {
                        itemsContainer.innerHTML = data.items.map(item => `
                            <tr class="border-b dark:border-slate-700">
                                <td class="py-2 px-3 font-semibold text-slate-800 dark:text-white">${item.product_name} (${item.variant_name})</td>
                                <td class="py-2 px-3 text-center">${item.quantity}</td>
                                <td class="py-2 px-3 text-right">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                                <td class="py-2 px-3 text-right font-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                            </tr>
                        `).join('');
                    }

                } catch (error) {
                    console.error('Fetch error:', error);
                    hideLoader();
                    showError('Kesalahan Jaringan', 'Gagal terhubung ke server.');
                }
            };

            const closeModal = () => {
                modalElement.classList.add('hidden');
                modalElement.classList.remove('flex');
            };

            document.body.addEventListener('click', function(event) {
                const target = event.target.closest('.js-open-detail-modal');
                if (target) {
                    event.preventDefault();
                    const orderId = target.dataset.orderId;
                    if (orderId) openModal(orderId);
                }

                const closeTarget = event.target.closest('[data-modal-hide="showOrderModal"]');
                if (closeTarget) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
