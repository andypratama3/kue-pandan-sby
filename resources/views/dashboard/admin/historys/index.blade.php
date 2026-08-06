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
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-lg space-y-6 min-h-[700px]">
            
            <!-- Header & Filter Form -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-2">
                    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20">
                            <i class="fas fa-file-invoice text-lg"></i>
                        </div>
                        <span>Riwayat Transaksi & Laporan PDF</span>
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Arsip pesanan selesai dan cetak rekapitulasi laporan bulanan.
                    </p>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative" x-data="{ openFilterDropdown: false }">
                        <button @click="openFilterDropdown = !openFilterDropdown" type="button"
                            class="inline-flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 rounded-2xl transition-colors shadow-sm hover:shadow-md">
                            <i class="fas fa-filter text-brand"></i>
                            <span>Filter Periode</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
                        </button>

                        <div x-show="openFilterDropdown" @click.away="openFilterDropdown = false" x-transition
                            class="absolute right-0 z-50 w-72 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 p-5 space-y-4">
                            <div>
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Bulan</label>
                                <select name="month"
                                    class="w-full px-4 py-2.5 text-sm border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}" @if ($selectedMonth == $num) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Kurir</label>
                                <select name="courier"
                                    class="w-full px-4 py-2.5 text-sm border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="">Semua Kurir</option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{ $courier['id'] }}" @if ($selectedCourier == $courier['id']) selected @endif>
                                            {{ $courier['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Tahun</label>
                                <select name="year"
                                    class="w-full px-4 py-2.5 text-sm border rounded-xl focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-brand hover:bg-brand-deep rounded-2xl shadow-lg transition-all hover:shadow-xl">
                        <i class="fas fa-eye"></i> Tampilkan
                    </button>
                    <a href="{{ route('admin.historys.export.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-2xl shadow-lg transition-all hover:shadow-xl">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </form>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center">
                <form class="flex items-center w-full sm:w-auto" onsubmit="return false;">
                    <div class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-sm text-slate-400"></i>
                        </div>
                        <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                            class="block w-full p-3 pl-11 text-sm text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white transition-all shadow-sm hover:shadow-md"
                            placeholder="Cari invoice atau nama customer...">
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 shadow-sm">
                <div class="overflow-x-auto min-h-[500px]">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-extrabold text-slate-400 uppercase bg-slate-50/90 dark:bg-slate-800/90">
                            <tr>
                                <th class="px-6 py-4 text-center w-14">No.</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Tanggal</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-results-container" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @include('dashboard.admin.historys._table_rows', ['orders' => $orders])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4" aria-label="Table navigation">
                <p class="text-sm text-slate-500 dark:text-slate-400">
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

            const formatRupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

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

            const populateModal = (data) => {
                const set = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value;
                };

                set('showOrderModalInvoiceNumber', data.invoice_number || '-');
                set('showOrderModalCustomerName', data.customer_name || '-');
                set('showOrderModalCustomerCompany', data.customer_company || '');
                set('showOrderModalCustomerPhone', data.customer_phone || '-');
                set('showOrderModalCustomerAddress', data.customer_address || '-');
                set('showOrderModalPaymentMethod', data.payment_method || '-');
                set('showOrderModalCreatedAt', data.created_at || '-');
                set('showOrderModalPaidAt', data.paid_at || '-');
                set('showOrderModalCourier', data.courier_name || '-');
                set('showOrderModalNotesContainer', data.note || '"Tidak ada catatan."');

                // --- Produk dipesan (template orderItemTemplate-admin) ---
                const productsContainer = document.getElementById('showOrderModalProductDetails');
                const orderItemTemplate = document.getElementById('orderItemTemplate-admin');
                if (productsContainer) productsContainer.innerHTML = '';

                if (Array.isArray(data.items) && productsContainer && orderItemTemplate) {
                    const returnedMap = new Map();
                    const returnedProducts = data.return_details?.returned_products || [];
                    returnedProducts.forEach(rp => {
                        returnedMap.set(`${rp.name}-${rp.variant || ''}`, rp);
                    });

                    data.items.forEach(item => {
                        if (!item) return;
                        const clone = orderItemTemplate.content.cloneNode(true);
                        const key = `${item.name}-${item.variant || ''}`;
                        const returnedItem = returnedMap.get(key);
                        const initialQty = item.quantity || 0;
                        const returnedQty = returnedItem ? (returnedItem.quantity || 0) : 0;
                        const remainingQty = initialQty - returnedQty;

                        clone.querySelector('[data-role="name-variant"]').textContent =
                            item.name + (item.variant ? ` (${item.variant})` : '');

                        const returnInfo = clone.querySelector('[data-role="return-info"]');
                        const normalInfo = clone.querySelector('[data-role="normal-info"]');
                        if (returnedQty > 0) {
                            if (normalInfo) normalInfo.remove();
                            const q1 = clone.querySelector('[data-role="initial-qty"]');
                            const q2 = clone.querySelector('[data-role="returned-qty"]');
                            const q3 = clone.querySelector('[data-role="remaining-qty"]');
                            if (q1) q1.textContent = initialQty;
                            if (q2) q2.textContent = returnedQty;
                            if (q3) q3.textContent = remainingQty;
                        } else {
                            if (returnInfo) returnInfo.remove();
                            const q = clone.querySelector('[data-role="quantity"]');
                            if (q) q.textContent = initialQty;
                        }

                        const priceLine = clone.querySelector('[data-role="price-line"]');
                        if (priceLine) {
                            priceLine.textContent = returnedQty > 0
                                ? `${formatRupiah(item.price)} × ${remainingQty} = ${formatRupiah(remainingQty * (item.price || 0))}`
                                : `${formatRupiah(item.price)} × ${initialQty} = ${formatRupiah((item.price || 0) * initialQty)}`;
                        }

                        productsContainer.appendChild(clone);
                    });
                }

                // --- Total (single vs dengan retur) ---
                const singleContainer = document.getElementById('singleTotalContainer-admin');
                const returnedContainer = document.getElementById('returnedTotalContainer-admin');
                const returnValueContainer = document.getElementById('returnTotalValueContainer-admin');

                if (data.return_details) {
                    if (singleContainer) singleContainer.classList.add('hidden');
                    if (returnedContainer) {
                        returnedContainer.classList.remove('hidden');
                        const initial = returnedContainer.querySelector('#initialTotalAmount-admin');
                        const latest = returnedContainer.querySelector('#latestTotalAmount-admin');
                        if (initial) initial.textContent = formatRupiah(data.total_amount);
                        if (latest) latest.textContent = formatRupiah((data.total_amount || 0) - (data.return_details.total_amount_returned || 0));
                    }
                    if (returnValueContainer) {
                        returnValueContainer.classList.remove('hidden');
                        const totalReturned = returnValueContainer.querySelector('#showOrderModalTotalReturned-admin');
                        if (totalReturned) totalReturned.textContent = formatRupiah(data.return_details.total_amount_returned);
                    }
                } else {
                    if (singleContainer) singleContainer.classList.remove('hidden');
                    const totalAmount = document.getElementById('showOrderModalTotalAmount-admin');
                    if (totalAmount) totalAmount.textContent = formatRupiah(data.total_amount);
                    if (returnedContainer) returnedContainer.classList.add('hidden');
                    if (returnValueContainer) returnValueContainer.classList.add('hidden');
                }

                // --- Bukti pembayaran ---
                const paymentProofContainer = document.getElementById('showOrderModalPaymentProof-admin');
                if (paymentProofContainer) {
                    if (data.payment_proof_url) {
                        paymentProofContainer.innerHTML = `<img src="${data.payment_proof_url}" alt="Bukti Pembayaran" data-zoomable class="w-full h-40 object-cover rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer">`;
                    } else {
                        paymentProofContainer.innerHTML = '<p class="text-xs text-slate-400">Belum ada bukti pembayaran.</p>';
                    }
                }

                // --- Bukti retur (kondisional) ---
                const returnProofContainer = document.getElementById('returnProofContainer-admin');
                const returnProofSlot = document.getElementById('showOrderModalReturnProof-admin');
                if (returnProofContainer && returnProofSlot) {
                    if (data.return_details?.return_proof_url) {
                        returnProofSlot.innerHTML = `<img src="${data.return_details.return_proof_url}" alt="Bukti Retur" data-zoomable class="w-full h-40 object-cover rounded-xl cursor-pointer">`;
                        returnProofContainer.classList.remove('hidden');
                    } else {
                        returnProofSlot.innerHTML = '';
                        returnProofContainer.classList.add('hidden');
                    }
                }
            };

            const openModal = async (orderId) => {
                modalElement.classList.remove('hidden');
                modalElement.classList.add('flex');
                showLoader();

                const url = `{{ url('admin/historys') }}/${orderId}/details`;
                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    hideLoader();

                    if (data.error) {
                        showError('Gagal Memuat Detail', data.error);
                        return;
                    }
                    populateModal(data);
                    showContent();

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

            // Zoom bukti (admin)
            if (zoomWrapper && zoomImg) {
                const closeZoom = () => {
                    zoomWrapper.classList.add('hidden');
                    zoomWrapper.classList.remove('flex');
                };
                zoomWrapper.addEventListener('click', function(e) {
                    if (e.target === zoomWrapper || e.target.tagName === 'BUTTON') closeZoom();
                });
                document.addEventListener('click', function(e) {
                    if (e.target.dataset && e.target.dataset.zoomable) {
                        zoomImg.src = e.target.src;
                        zoomWrapper.classList.remove('hidden');
                        zoomWrapper.classList.add('flex');
                    }
                });
            }

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
