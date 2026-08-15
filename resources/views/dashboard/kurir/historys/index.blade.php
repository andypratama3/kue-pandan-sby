@extends('layouts.argon')
@section('title', 'History Pesanan')
@section('page_title', 'History')

@section('content')
    <div class="flex-auto p-3 pt-0 -mx-3">
        <div class="p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700 min-h-[715px]">
            {{-- Header: Title dan Filter --}}
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-history mr-2 text-blue-500"></i>History Pesanan
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat riwayat pesanan yang telah selesai</p>
                </div>
                <form method="GET" class="flex flex-row flex-wrap items-center gap-3">
                    <div class="relative">
                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Bulan</label>
                        <select name="month"
                            class="px-3 py-2 text-sm border rounded-lg appearance-none focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            @foreach($months as $num => $name)
                            <option value="{{ $num }}" @if($selectedMonth == $num) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Tahun</label>
                        <select name="year"
                            class="px-3 py-2 text-sm border rounded-lg appearance-none focus:ring-2 focus:ring-brand dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            @foreach($years as $year)
                            <option value="{{ $year }}" @if($selectedYear == $year) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-brand to-brand-deep rounded-xl hover:from-brand-deep hover:to-brand focus:ring-4 focus:ring-brand-light dark:from-brand dark:to-brand-deep dark:hover:from-brand-deep dark:hover:to-brand focus:outline-none dark:focus:ring-brand-deep shadow-lg shadow-brand/30 transition-all mt-5">
                        <i class="fas fa-eye mr-2"></i>Lihat
                    </button>
                </form>
            </div>

            {{-- Search --}}
            <div class="mb-6">
                <form class="flex items-center" onsubmit="return false;">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="live-search-input" name="search"
                            class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                            placeholder="Cari invoice atau customer...">
                    </div>
                </form>
            </div>

            {{-- CARD VIEW UNTUK MOBILE (md:hidden) --}}
            <div id="history-results-container-mobile" class="space-y-4 md:hidden">
                @include('dashboard.kurir.historys._card_view', ['orders' => $orders])
            </div>

            {{-- TABLE VIEW UNTUK DESKTOP (hidden md:block) --}}
            <div class="hidden overflow-hidden border border-gray-200 rounded-xl md:block dark:border-slate-700">
                <div class="overflow-x-auto min-h-[580px]">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-slate-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4 text-center">No.</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody id="history-results-container-desktop" class="divide-y divide-gray-200 dark:divide-slate-700">
                            @include('dashboard.kurir.historys._table_rows', ['orders' => $orders])
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginasi --}}
            <nav class="flex justify-between items-center mt-6" aria-label="Table navigation">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->total() }}</span> data
                </p>
                {{ $orders->withQueryString()->links() }}
            </nav>
        </div>
    </div>
@endsection

@push('flowbite-modals')
@include('dashboard.kurir.historys.show-modal')
@endpush

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('showOrderModal');
        if (!modalElement) return;

        const loader = document.getElementById('showOrderModalLoader');
        const content = document.getElementById('showOrderModalContent');
        const zoomWrapper = document.getElementById('showOrderModalZoomWrapper');
        const zoomImg = document.getElementById('showOrderModalZoomImg');
        const zoomCloseBtn = document.getElementById('showOrderModalZoomCloseBtn');

        if (!loader || !content) {
            console.error("Modal loader or content element not found!");
            return;
        }

        const showLoader = () => {
            loader.classList.remove('hidden');
            content.classList.add('hidden');
        };
        const hideLoader = () => {
            loader.classList.add('hidden');
            content.classList.remove('hidden');
        };

        const openModal = async (orderId) => {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            showLoader();

            const url = `{{ url('kurir/historys') }}/${orderId}/details`;
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                populateModal(data);
            } catch (error) {
                console.error('Error fetching order details:', error);
                content.innerHTML =
                    `<div class="py-10 text-center"><p class="font-semibold text-red-600 dark:text-red-400">Gagal memuat detail pesanan.</p><p class="text-sm text-gray-500 dark:text-gray-400">Silakan coba lagi.</p></div>`;
            } finally {
                hideLoader();
            }
        };

        const closeModal = () => {
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
        };

        document.body.addEventListener('click', function(event) {
            const openBtn = event.target.closest(
                '.js-open-modal-btn[data-target-modal="showOrderModal"]');
            if (openBtn) {
                const orderId = openBtn.dataset.orderId;
                openModal(orderId);
                return;
            }

            const closeBtn = event.target.closest('.js-close-modal-btn');
            if (closeBtn || event.target === modalElement) {
                closeModal();
                return;
            }

            if (event.target.tagName === 'IMG' && event.target.dataset.zoomable) {
                zoomImg.src = event.target.src;
                zoomWrapper.classList.remove('hidden');
                zoomWrapper.classList.add('flex');
            }
        });

            // --- TAMBAHKAN LOGIKA BARU UNTUK MENUTUP ZOOM ---

            // 1. Tutup saat tombol 'X' ditekan
            zoomCloseBtn.addEventListener('click', () => {
                zoomWrapper.classList.add('hidden');
                zoomWrapper.classList.remove('flex');
            });

            // 2. Tutup saat area luar gambar (overlay) ditekan
            zoomWrapper.addEventListener('click', (event) => {
                // Hanya tutup jika yang diklik adalah wrapper-nya, bukan gambar di dalamnya
                if (event.target === zoomWrapper) {
                    zoomWrapper.classList.add('hidden');
                    zoomWrapper.classList.remove('flex');
                }
            });

        function populateModal(data) {
            try {
                const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);

                const elements = {
                    invoiceNumber: document.getElementById('showOrderModalInvoiceNumber'),
                    customerName: document.getElementById('showOrderModalCustomerName'),
                    customerPhone: document.getElementById('showOrderModalCustomerPhone'),
                    customerCompany: document.getElementById('showOrderModalCustomerCompany'),
                    customerAddress: document.getElementById('showOrderModalCustomerAddress'),
                    paymentMethod: document.getElementById('showOrderModalPaymentMethod'),
                    totalAmount: document.getElementById('showOrderModalTotalAmount'),
                    createdAt: document.getElementById('showOrderModalCreatedAt'),
                    paidAt: document.getElementById('showOrderModalPaidAt'),
                    notesContainer: document.getElementById('showOrderModalNotesContainer'),
                    productDetails: document.getElementById('showOrderModalProductDetails'),
                    paymentProof: document.getElementById('showOrderModalPaymentProof'),
                    returnProof: document.getElementById('showOrderModalReturnProof'),
                    totalReturned: document.getElementById('showOrderModalTotalReturned'),
                    singleTotalContainer: document.getElementById('singleTotalContainer'),
                    returnedTotalContainer: document.getElementById('returnedTotalContainer'),
                    initialTotalAmount: document.getElementById('initialTotalAmount'),
                    latestTotalAmount: document.getElementById('latestTotalAmount'),
                    returnTotalValueContainer: document.getElementById('returnTotalValueContainer'),
                    returnProofContainer: document.getElementById('returnProofContainer'),
                };
                const orderItemTemplate = document.getElementById('orderItemTemplate');

                if (!orderItemTemplate) {
                    console.error('Template #orderItemTemplate not found!');
                    content.innerHTML = `<p class="py-10 text-center text-red-500 dark:text-red-400">Error: Template produk tidak ditemukan.</p>`;
                    return;
                }

                elements.productDetails.innerHTML = '';
                elements.paymentProof.innerHTML = '';
                elements.returnProof.innerHTML = '';

                elements.invoiceNumber.textContent = data.invoice_number || '-';
                elements.customerName.textContent = data.customer_name || '-';
                elements.customerPhone.textContent = data.customer_phone || '-';
                elements.customerAddress.textContent = data.customer_address || '-';
                if (data.customer_company && data.customer_company !== 'N/A') {
                    elements.customerCompany.textContent = data.customer_company;
                    elements.customerCompany.classList.remove('hidden');
                } else {
                    elements.customerCompany.textContent = '';
                    elements.customerCompany.classList.add('hidden');
                }
                elements.paymentMethod.textContent =
                    `${data.payment_method ? data.payment_method.charAt(0).toUpperCase() + data.payment_method.slice(1) : '-'}`;
                elements.createdAt.textContent = data.created_at || '-';
                elements.paidAt.textContent = data.paid_at || '-';
                elements.notesContainer.textContent = data.note || '"Tidak ada catatan."';

                const returnedItemsMap = new Map();
                if (data.return_details && Array.isArray(data.return_details.returned_products)) {
                    data.return_details.returned_products.forEach(item => {
                        const key = `${item.name}-${item.variant || ''}`;
                        returnedItemsMap.set(key, item);
                    });
                }

                if (Array.isArray(data.items) && data.items.length > 0) {
                    data.items.forEach(item => {
                        if (!item) return;

                        const clone = orderItemTemplate.content.cloneNode(true);
                        const key = `${item.name}-${item.variant || ''}`;
                        const returnedItem = returnedItemsMap.get(key);

                        const initialQty = item.quantity || 0;
                        const price = item.price || 0;
                        const returnedQty = returnedItem ? (returnedItem.quantity || 0) : 0;
                        const remainingQty = initialQty - returnedQty;
                        const finalSubtotal = remainingQty * price;

                        clone.querySelector('[data-role="name"]').textContent = item.name || 'Produk tidak valid';

                        const variantEl = clone.querySelector('[data-role="variant"]');
                        if (item.variant) {
                            variantEl.textContent = item.variant;
                        } else {
                            variantEl.remove();
                        }

                        const returnInfo = clone.querySelector('[data-role="return-info"]');
                        const normalInfo = clone.querySelector('[data-role="normal-info"]');

                        if (returnedQty > 0) {
                            normalInfo.remove();
                            returnInfo.querySelector('[data-role="initial-qty"]').textContent = initialQty;
                            returnInfo.querySelector('[data-role="returned-qty"]').textContent = returnedQty;
                            returnInfo.querySelector('[data-role="remaining-qty"]').textContent = remainingQty;
                        } else {
                            returnInfo.remove();
                            normalInfo.querySelector('[data-role="quantity"]').textContent = initialQty;
                        }

                        clone.querySelector('[data-role="price"]').textContent = formatRupiah(price);
                        clone.querySelector('[data-role="subtotal"]').textContent = formatRupiah(finalSubtotal);

                        elements.productDetails.appendChild(clone);
                    });
                }

                if (data.return_details) {
                    elements.singleTotalContainer.classList.add('hidden');
                    elements.returnedTotalContainer.classList.remove('hidden');
                    elements.returnTotalValueContainer.classList.remove('hidden');
                    elements.returnProofContainer.classList.remove('hidden');

                    const totalAmount = data.total_amount || 0;
                    const totalAmountReturned = data.return_details.total_amount_returned || 0;
                    const finalTotal = totalAmount - totalAmountReturned;

                    elements.initialTotalAmount.textContent = formatRupiah(totalAmount);
                    elements.latestTotalAmount.textContent = formatRupiah(finalTotal);

                    if (elements.totalReturned) {
                        elements.totalReturned.textContent = formatRupiah(totalAmountReturned);
                    }
                    if (data.return_details.return_proof_url) {
                        elements.returnProof.innerHTML = '';
                        const returnImg = document.createElement('img');
                        returnImg.src = data.return_details.return_proof_url;
                        returnImg.alt = 'Bukti Retur';
                        returnImg.dataset.zoomable = 'true';
                        returnImg.className = 'w-full border rounded cursor-pointer hover:border-red-500';
                        elements.returnProof.appendChild(returnImg);
                    } else {
                        elements.returnProof.innerHTML = '<p class="text-sm text-center text-gray-500 dark:text-gray-400">Tidak ada bukti retur</p>';
                    }
                } else {
                    elements.singleTotalContainer.classList.remove('hidden');
                    elements.returnedTotalContainer.classList.add('hidden');
                    elements.returnTotalValueContainer.classList.add('hidden');
                    elements.returnProofContainer.classList.add('hidden');
                    elements.totalAmount.textContent = `${formatRupiah(data.total_amount || 0)}`;
                }

                if (data.payment_proof_url) {
                    elements.paymentProof.innerHTML = '';
                    const paymentImg = document.createElement('img');
                    paymentImg.src = data.payment_proof_url;
                    paymentImg.alt = 'Bukti Pembayaran';
                    paymentImg.dataset.zoomable = 'true';
                    paymentImg.className = 'w-full border rounded cursor-pointer hover:border-blue-500';
                    elements.paymentProof.appendChild(paymentImg);
                } else {
                    elements.paymentProof.innerHTML =
                        '<p class="text-sm text-center text-gray-500 dark:text-gray-400">Tidak ada bukti pembayaran</p>';
                }
            } catch (e) {
                console.error("Error populating modal content:", e);
                content.innerHTML = `<div class="py-10 text-center"><p class="font-semibold text-red-600 dark:text-red-400">Terjadi kesalahan saat memproses data.</p><p class="text-sm text-gray-500 dark:text-gray-400">Silakan coba lagi.</p></div>`;
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeLiveSearch === 'function') {
            initializeLiveSearch({
                searchInputId: 'live-search-input',
                desktopContainerId: 'history-results-container-desktop',
                mobileContainerId: 'history-results-container-mobile'
            });
        }
    });
</script>
@endpush

