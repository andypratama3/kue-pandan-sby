@extends('layouts.argon')
@section('title', 'History Pesanan')
@section('page_title', 'History')

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
            <span class="font-medium">Sukses!</span> {{ session('success') }}
        </div>
    @endif

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
                        <button onclick="toggleDropdown()" type="button"
                            class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:from-blue-600 dark:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 focus:outline-none dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>

                        <div id="dropdown"
                            class="hidden absolute right-0 z-50 w-56 mt-2 bg-white rounded-lg shadow-lg dark:bg-slate-800 dark:border dark:border-slate-700 p-3 space-y-3">
                            <div class="relative">
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Bulan</label>
                                <select name="month"
                                    class="w-full px-3 py-2 text-sm border rounded-lg appearance-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}"
                                            @if ($selectedMonth == $num) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Kurir</label>
                                <select name="courier"
                                    class="w-full px-3 py-2 text-sm border rounded-lg appearance-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="">
                                        Semua Kurir
                                    </option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{ $courier['id'] }}"
                                            @if ($selectedCourier == $courier['id']) selected @endif>
                                            {{ $courier['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Tahun</label>
                                <select name="year"
                                    class="w-full px-3 py-2 text-sm border rounded-lg appearance-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            @if ($selectedYear == $year) selected @endif>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-xl hover:from-green-700 hover:to-green-800 focus:ring-4 focus:ring-green-300 dark:from-green-600 dark:to-green-700 dark:hover:from-green-700 dark:hover:to-green-800 focus:outline-none dark:focus:ring-green-800 shadow-lg shadow-green-500/30 transition-all">
                        <i class="fas fa-eye mr-2"></i>Lihat
                    </button>
                    <a href="{{ route('admin.historys.export.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                        target="_blank"
                        class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl hover:from-orange-600 hover:to-orange-700 focus:ring-4 focus:ring-orange-300 dark:from-orange-500 dark:to-orange-600 dark:hover:from-orange-600 dark:hover:to-orange-700 focus:outline-none dark:focus:ring-orange-800 shadow-lg shadow-orange-500/30 transition-all">
                        <i class="fas fa-file-pdf mr-2"></i> Export PDF
                    </a>
                </form>
            </div>

            {{-- Search --}}
            <div class="mb-6">
                <form class="flex items-center" onsubmit="return false;">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                            class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                            placeholder="Cari invoice atau customer...">
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-hidden border border-gray-200 rounded-xl dark:border-slate-700">
                <div class="overflow-x-auto min-h-[580px]">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-slate-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4 text-center">No.</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Tanggal</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-results-container" class="divide-y divide-gray-200 dark:divide-slate-700">
                            @include('dashboard.admin.historys._table_rows', ['histories' => $histories])
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginasi --}}
            <nav class="flex justify-between items-center mt-6" aria-label="Table navigation">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $histories->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $histories->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900 dark:text-white">{{ $histories->total() }}</span> data
                </p>
                {{ $histories->withQueryString()->links() }}
            </nav>
        </div>
    </div>
@endsection

@push('flowbite-modals')
    {{-- Menggunakan file modal yang sudah di-styling --}}
    @include('dashboard.admin.historys.show-modal')

    @foreach ($orders as $historys)
        @include('dashboard.admin.historys.delete', ['historys' => $historys])
    @endforeach
@endpush

@push('page-scripts')
    <script>
        function toggleDropdown() {
            document.getElementById("dropdown").classList.toggle("hidden");
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('showOrderModal');
            if (!modalElement) return;

            const loader = document.getElementById('showOrderModalLoader');
            const content = document.getElementById('showOrderModalContent');
            const contentGrid = document.getElementById('showOrderModalGrid');
            const errorContainer = document.getElementById('showOrderModalError');
            const zoomWrapper = document.getElementById('showOrderModalZoomWrapper-admin');
            const zoomImg = document.getElementById('showOrderModalZoomImg-admin');

            if (!loader || !content || !contentGrid || !errorContainer || !zoomWrapper || !zoomImg) {
                console.error(
                    "Elemen modal penting (Admin) tidak ditemukan! Pastikan semua ID di 'show-modal.blade.php' sudah benar."
                );
                return;
            }

            const showError = (title, message) => {
                errorContainer.querySelector('[data-role="error-title"]').textContent = title;
                errorContainer.querySelector('[data-role="error-message"]').textContent = message;
                contentGrid.classList.add('hidden');
                errorContainer.classList.remove('hidden');
            };

            const showContent = () => {
                contentGrid.classList.remove('hidden');
                errorContainer.classList.add('hidden');
            };

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

                const url = `{{ url('admin/historys') }}/${orderId}/details`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    // First, hide the loader and prepare the content area
                    hideLoader();
                    // Then, show the main content grid
                    showContent();
                    // Finally, populate it with data
                    populateModal(data);

                } catch (error) {
                    console.error('Gagal mengambil detail pesanan (Admin):', error);
                    // Hide loader and show the error message
                    hideLoader();
                    showError('Gagal memuat detail pesanan.', 'Silakan tutup modal dan coba lagi.');
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

            zoomWrapper.addEventListener('click', () => {
                zoomWrapper.classList.add('hidden');
                zoomWrapper.classList.remove('flex');
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
                        createdAt: document.getElementById('showOrderModalCreatedAt'),
                        paidAt: document.getElementById('showOrderModalPaidAt'),
                        courier: document.getElementById('showOrderModalCourier'),
                        notesContainer: document.getElementById('showOrderModalNotesContainer'),
                        productDetails: document.getElementById('showOrderModalProductDetails'),

                        totalAmount: document.getElementById('showOrderModalTotalAmount-admin'),
                        paymentProof: document.getElementById('showOrderModalPaymentProof-admin'),
                        returnProof: document.getElementById('showOrderModalReturnProof-admin'),
                        totalReturned: document.getElementById('showOrderModalTotalReturned-admin'),
                        singleTotalContainer: document.getElementById('singleTotalContainer-admin'),
                        returnedTotalContainer: document.getElementById('returnedTotalContainer-admin'),
                        initialTotalAmount: document.getElementById('initialTotalAmount-admin'),
                        latestTotalAmount: document.getElementById('latestTotalAmount-admin'),
                        returnTotalValueContainer: document.getElementById('returnTotalValueContainer-admin'),
                        returnProofContainer: document.getElementById('returnProofContainer-admin'),
                    };
                    const orderItemTemplate = document.getElementById('orderItemTemplate-admin');

                    if (!orderItemTemplate) {
                        showError('Kesalahan Tampilan', 'Template untuk produk tidak ditemukan.');
                        return;
                    }

                    elements.productDetails.innerHTML = '';
                    elements.paymentProof.innerHTML = '';
                    elements.returnProof.innerHTML = '';

                    // Mengisi data utama
                    elements.invoiceNumber.textContent = data.invoice_number || '-';
                    elements.customerName.textContent = data.customer_name || '-';
                    elements.customerPhone.textContent = data.customer_phone || '-';
                    elements.customerAddress.textContent = data.customer_address || '-';
                    elements.courier.textContent = data.courier_name || 'Belum ditugaskan';
                    if (data.customer_company && data.customer_company !== 'N/A') {
                        elements.customerCompany.textContent = data.customer_company;
                        elements.customerCompany.classList.remove('hidden');
                    } else {
                        elements.customerCompany.classList.add('hidden');
                    }
                    elements.paymentMethod.textContent =
                        `${data.payment_method ? data.payment_method.charAt(0).toUpperCase() + data.payment_method.slice(1) : '-'}`;
                    elements.createdAt.textContent = data.created_at || '-';
                    elements.paidAt.textContent = data.paid_at || '-';
                    elements.notesContainer.textContent = data.note || '"Tidak ada catatan."';

                    // Mengisi produk
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

                            const nameVariantEl = clone.querySelector('[data-role="name-variant"]');
                            nameVariantEl.textContent = item.name || 'Produk Tidak Dikenal';
                            if (item.variant) {
                                nameVariantEl.textContent += ` (${item.variant})`;
                            }

                            const returnInfo = clone.querySelector('[data-role="return-info"]');
                            const normalInfo = clone.querySelector('[data-role="normal-info"]');

                            if (returnedQty > 0) {
                                normalInfo.remove();
                                returnInfo.querySelector('[data-role="initial-qty"]').textContent =
                                    initialQty;
                                returnInfo.querySelector('[data-role="returned-qty"]').textContent =
                                    returnedQty;
                                returnInfo.querySelector('[data-role="remaining-qty"]').textContent =
                                    remainingQty;
                            } else {
                                returnInfo.remove();
                                normalInfo.querySelector('[data-role="quantity"]').textContent = initialQty;
                            }

                            const priceLineEl = clone.querySelector('[data-role="price-line"]');
                            priceLineEl.innerHTML =
                                `${formatRupiah(price)} &rarr; ${formatRupiah(finalSubtotal)}`;

                            elements.productDetails.appendChild(clone);
                        });
                    } else {
                        elements.productDetails.innerHTML =
                            '<p class="text-center text-gray-500">Tidak ada produk.</p>';
                    }

                    // Mengelola total dan retur
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
                            elements.returnProof.innerHTML =
                                `<img src="${data.return_details.return_proof_url}" alt="Bukti Retur" class="w-full rounded border cursor-pointer hover:border-red-500" data-zoomable="true">`;
                        } else {
                            elements.returnProof.innerHTML =
                                '<p class="text-sm text-center text-gray-500">Tidak ada bukti retur</p>';
                        }
                    } else {
                        elements.singleTotalContainer.classList.remove('hidden');
                        elements.returnedTotalContainer.classList.add('hidden');
                        elements.returnTotalValueContainer.classList.add('hidden');
                        elements.returnProofContainer.classList.add('hidden');
                        elements.totalAmount.textContent = `${formatRupiah(data.total_amount || 0)}`;
                    }

                    // Mengelola bukti pembayaran
                    if (data.payment_proof_url) {
                        elements.paymentProof.innerHTML =
                            `<img src="${data.payment_proof_url}" alt="Bukti Pembayaran" class="w-full rounded border cursor-pointer hover:border-blue-500" data-zoomable="true">`;
                    } else {
                        elements.paymentProof.innerHTML =
                            '<p class="text-sm text-center text-gray-500">Tidak ada bukti pembayaran</p>';
                    }
                } catch (e) {
                    console.error("Gagal memproses konten modal:", e);
                    showError('Terjadi kesalahan', 'Gagal memproses data. Coba lagi.');
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initializeLiveSearch === 'function') {
                initializeLiveSearch({
                    searchInputId: 'live-search-input',
                    desktopContainerId: 'history-results-container'
                });
            }
        });
    </script>
@endpush
