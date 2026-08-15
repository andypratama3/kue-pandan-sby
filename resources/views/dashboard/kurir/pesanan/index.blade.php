@extends('layouts.argon')
@section('title', 'Daftar Pesanan Saya')
@section('page_title', 'Pesanan Kurir')

@php
    $allStatuses = [
        'semua' => 'Semua',
        'baru' => 'Baru',
        'diambil' => 'Diambil',
        'diantar' => 'Diantar',
        'diterima_pembeli' => 'Diterima Pembeli',
        'menunggu_retur' => 'Menunggu Retur',
        'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
        'selesai' => 'Selesai',
    ];
@endphp

@section('content')
    <div class="flex-auto p-3 pt-0 -mx-3">
        <div class="p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700 min-h-[715px]">
            {{-- Header: Title dan User Info --}}
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>Daftar Pesanan Saya
                    </h2>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-user mr-1"></i>{{ Auth::user()->name ?? 'Pengguna' }}</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ optional(Auth::user()->region)->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="px-3 py-1 font-semibold text-white bg-brand rounded-full dark:bg-brand">
                        Total: {{ $orders->total() }}
                    </span>
                </div>
            </div>

            {{-- Search dan Tombol Tambah --}}
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="live-search-input" name="search" value="{{ request('search') }}"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                        placeholder="Cari invoice atau nama customer...">
                </div>

                <div class="flex items-center gap-3">
                    {{-- Filter Status Pesanan (Tabs) --}}
                    <div
                        class="hidden xl:flex items-center p-1 space-x-1 text-sm text-gray-600 bg-gray-100 rounded-xl dark:bg-slate-700 dark:text-gray-300">
                        @foreach ($allStatuses as $key => $label)
                            <a href="{{ route('kurir.pesanan.index', array_merge(request()->except('page'), ['status' => $key == 'semua' ? null : $key])) }}"
                                class="flex-shrink-0 px-4 py-2 font-medium rounded-lg whitespace-nowrap transition-all
                                  @if ((empty($activeStatus) && $key == 'semua') || $activeStatus == $key) bg-white text-blue-600 shadow-sm dark:bg-slate-600 dark:text-white
                                  @else
                                      hover:bg-white/60 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-slate-600/50 dark:hover:text-white @endif
                           ">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ route('kurir.pesanan.create') }}"
                        class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-brand to-brand-deep rounded-xl hover:from-brand-deep hover:to-brand focus:ring-4 focus:ring-brand-light dark:from-brand dark:to-brand-deep dark:hover:from-brand-deep dark:hover:to-brand focus:outline-none dark:focus:ring-brand-deep shadow-lg shadow-brand/30 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pesanan
                    </a>
                </div>
            </div>

            {{-- Mobile Filter Status --}}
            <div
                class="flex items-center w-full p-2 space-x-2 overflow-x-auto text-sm text-gray-600 bg-gray-100 rounded-xl mb-6 xl:hidden dark:bg-slate-700 dark:text-gray-300">
                @foreach ($allStatuses as $key => $label)
                    <a href="{{ route('kurir.pesanan.index', array_merge(request()->except('page'), ['status' => $key == 'semua' ? null : $key])) }}"
                        class="flex-shrink-0 px-4 py-2 font-medium rounded-lg whitespace-nowrap transition-all
                          @if ((empty($activeStatus) && $key == 'semua') || $activeStatus == $key) bg-white text-blue-600 shadow-sm dark:bg-slate-600 dark:text-white
                          @else
                              hover:bg-white/60 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-slate-600/50 dark:hover:text-white @endif
                   ">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Blok Notifikasi Error atau Info --}}
            @if (isset($error))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-md dark:bg-red-900/20 dark:text-red-400 dark:border-red-600" role="alert">
                    <p class="font-bold">Error:</p>
                    <p>{{ $error }}</p>
                </div>
            @endif

            @if ($orders->isEmpty() && !request('search'))
                <div class="p-12 text-center bg-gray-50 rounded-xl dark:bg-slate-700">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada pesanan aktif untuk Anda saat ini.</p>
                </div>
            @else
                {{-- Tampilan Desktop (Tabel) --}}
                <div class="hidden overflow-hidden border border-gray-200 rounded-xl md:block dark:border-slate-700">
                    <div class="overflow-x-auto min-h-[580px]">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-slate-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-4 text-center">No.</th>
                                    <th class="px-6 py-4">Nomor Invoice</th>
                                    <th class="px-6 py-4">Nama Pelanggan</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Total</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="order-results-container" class="divide-y divide-gray-200 dark:divide-slate-700">
                                @include('dashboard.kurir.pesanan._table_rows', ['orders' => $orders])
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tampilan Mobile (Card View) --}}
                <div class="md:hidden space-y-4" id="mobile-order-results-container">
                    @include('dashboard.kurir.pesanan._card_view', ['orders' => $orders])
                </div>

                {{-- Paginasi --}}
                <nav class="flex justify-between items-center mt-6" aria-label="Table navigation">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900 dark:text-white">{{ $orders->total() }}</span> data
                    </p>
                    {{ $orders->withQueryString()->links() }}
                </nav>
            @endif
        </div>
    </div>
@endsection

@push('flowbite-modals')
    @include('dashboard.kurir.pesanan.rincian-modal')
    @include('dashboard.kurir.pesanan.status-modal')
    @include('dashboard.kurir.pesanan.return-modal')
@endpush

@push('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <script>
        // Ganti dengan URL aplikasi Anda yang sebenarnya di production
        const APP_URL = "{{ url('/') }}";
        const STATUS_LABEL_MAP = @json($statusLabelMap ?? []);

        // --- Helper ---
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function dispatchToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    type: type,
                    message: message
                }
            }));
        }

        // tambah jumlah return di setiap produk
        document.addEventListener('DOMContentLoaded', () => {
            const returnModal = document.getElementById('returnProductModal');

            if (returnModal) {
                returnModal.addEventListener('click', function(event) {
                    const button = event.target.closest(
                        'button'); // Cari elemen tombol yang paling dekat diklik
                    if (!button) return; // Jika yang diklik bukan tombol, abaikan

                    // Cari baris atau kartu produk terdekat dari tombol yang diklik
                    const productContainer = event.target.closest('[data-return-key]');
                    if (!productContainer) return;

                    // Cari elemen span yang menampilkan angka di dalam container produk itu
                    const quantitySpan = productContainer.querySelector('.quantity-input');
                    if (!quantitySpan) return;

                    let currentValue = parseInt(quantitySpan.textContent, 10);
                    const maxValue = parseInt(quantitySpan.dataset.max, 10);

                    // --- Logika untuk Tombol Tambah (+) ---
                    if (button.classList.contains('quantity-plus')) {
                        if (currentValue < maxValue) {
                            quantitySpan.textContent = currentValue + 1;
                        }
                    }

                    // --- Logika untuk Tombol Kurang (-) ---
                    if (button.classList.contains('quantity-minus')) {
                        if (currentValue > 0) {
                            quantitySpan.textContent = currentValue - 1;
                        }
                    }

                    // --- Logika untuk Tombol Hapus (Ikon Sampah) ---
                    if (button.classList.contains('remove-product')) {
                        // Setel kuantitas kembali ke 0
                        quantitySpan.textContent = 0;
                    }
                });
            }

            document.body.addEventListener('click', function(event) {
                const openStatusBtn = event.target.closest('.js-open-status-modal');

                if (openStatusBtn) {
                    const orderId = openStatusBtn.getAttribute('data-order-id');
                    openStatusStepperModal(orderId);
                    return;
                }

                const openDetailsBtn = event.target.closest('.js-open-details-modal');
                if (openDetailsBtn) {
                    document.getElementById('editReturnProductButton').innerHTML = ""
                    const orderId = openDetailsBtn.getAttribute('data-order-id');
                    fetchOrderDetails(orderId);
                    return;
                }

            });

            const updateButton = document.getElementById('updateStatusButton');
            if (updateButton) {
                updateButton.addEventListener('click', handleStatusUpdate);
            }

// Inisialisasi Live Search
            initializeLiveSearch({
                searchInputId: 'live-search-input',
                desktopContainerId: 'order-results-container',
                mobileContainerId: 'mobile-order-results-container',
            });

        });
    </script>
    <script>

        /**
         * Menutup modal image viewer.
         */

        // --- Logika Modal Rincian ---
        async function fetchOrderDetails(orderId) {
            openModal('orderDetailsModal');
            const modalLoader = document.getElementById('modalLoader');
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.add('hidden');
            modalLoader.classList.remove('hidden');
            modalLoader.innerHTML =
                `<svg class="w-8 h-8 mx-auto text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">Memuat Detail Pesanan...</p>`;
            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/details`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal mengambil data.');
                populateOrderDetailsModal(data);
            } catch (error) {
                modalLoader.innerHTML =
                    `<div class="text-center"><p class="font-bold text-red-600 dark:text-red-400">Gagal Memuat Data</p><p class="mt-2 text-sm text-gray-500 dark:text-gray-400">${error.message}</p></div>`;
            }
        }


        function populateOrderDetailsModal(order) {
            // Populate data umum
            document.getElementById('modalInvoiceNumber').textContent = order.invoice_number || 'N/A';
            document.getElementById('customerName').textContent = order.customer.name || 'N/A';
            document.getElementById('customerPhone').textContent = order.customer.phone || 'N/A';
            document.getElementById('customerAddress').textContent = order.customer.address || 'N/A';
            const companyNameEl = document.getElementById('customerCompanyName');
            if (order.customer.company_name && order.customer.company_name !== 'N/A') {
                companyNameEl.textContent = order.customer.company_name;
                companyNameEl.classList.remove('hidden');
            } else {
                companyNameEl.textContent = '';
                companyNameEl.classList.add('hidden');
            }
            document.getElementById('paymentMethod').textContent = order.payment_method || 'N/A';
            document.getElementById('orderCreatedAt').textContent = order.created_at || 'Tidak Tersedia';
            document.getElementById('orderPaidAt').textContent = order.paid_at ? (order.paid_at + (order.paid_at_label ||
                '')) : 'Belum Lunas';
            // Populate Order Notes
            document.getElementById('orderNotesContainer').textContent = order.note || '"Tidak ada catatan."';

            // Logika untuk menampilkan catatan penolakan
            const rejectionContainer = document.getElementById('rejectionNoteContainer');
            const rejectionText = document.getElementById('rejectionNoteText');
            if (order.rejection_note) {
                rejectionText.textContent = order.rejection_note;
                rejectionContainer.classList.remove('hidden');
            } else {
                rejectionContainer.classList.add('hidden');
            }

            const statusSection = document.getElementById('modalOrderStatusSection');
            const statusBadge = document.getElementById('modalOrderStatusBadge');
            const statusIcon = document.getElementById('modalOrderStatusIcon');
            if (statusBadge) {
                // 1. Ambil teks status dari map yang sudah ada
                const statusText = STATUS_LABEL_MAP[order.status] || (order.status.charAt(0).toUpperCase() + order.status
                    .slice(1).replace(/_/g, ' '));
                statusBadge.textContent = statusText;

                // 2. Tentukan kelas warna berdasarkan status
                let badgeColorClasses = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; // Default
                switch (order.status) {
                    case 'diambil':
                        badgeColorClasses = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
                        break;
                    case 'diantar':
                        badgeColorClasses = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                        break;
                    case 'diterima_pembeli':
                        badgeColorClasses = 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
                        break;
                    case 'menunggu_retur':
                        // Tombol edit hanya muncul bila retur masih menunggu konfirmasi
                        // (belum unggah bukti) dan masih dalam jendela 5 hari.
                        if (order.order_return && order.order_return.status === 'menunggu_konfirmasi' && order.order_return
                            .created_at_raw) {
                            const createdAt = new Date(order.order_return.created_at_raw);
                            const diffDays = (Date.now() - createdAt.getTime()) / (1000 * 60 * 60 * 24);

                            if (diffDays <= 5) {
                                const buttonReturn = document.createElement('a');
                                buttonReturn.id = 'editReturn';
                                buttonReturn.textContent = 'edit';
                                buttonReturn.href = `/kurir/pesanan/${order.id}/request-return/edit`;
                                document
                                    .getElementById('editReturnProductButton')
                                    .appendChild(buttonReturn);
                            }
                        }
                        badgeColorClasses = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                        break;
                    case 'menunggu_verifikasi_admin':
                        badgeColorClasses = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                        break;
                    case 'selesai':
                        badgeColorClasses = 'bg-brand-light text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light';
                        break;
                }
                // 3. Gabungkan kelas dasar dengan kelas warna baru
                const baseClasses = 'flex-shrink-0 px-3 py-1 text-sm font-semibold rounded-full whitespace-nowrap';
                statusBadge.className = `${baseClasses} ${badgeColorClasses}`;
                // icon success
                if (order.status === 'selesai' && statusIcon) {
                    statusIcon.innerHTML =
                        `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="#6f8f5f" stroke-width="1.5" fill="#eef3ec"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" stroke="#6f8f5f" stroke-width="2"/></svg>`;
                    statusIcon.classList.remove('hidden');
                } else if (statusIcon) {
                    statusIcon.innerHTML = ''; // Kosongkan ikon jika status bukan 'selesai'
                    statusIcon.classList.add('hidden');
                }
            }

            // Logika Perhitungan Total Tagihan untuk handle retur
            let calculatedInitialTotal = 0;
            let calculatedLatestTotal = 0;
            let isReturned = false;
            if (order.products && order.products.length > 0) {
                order.products.forEach(p => {
                    const initialQuantity = p.quantity || 0;
                    const returnedQuantity = p.returned_quantity || 0;
                    const price = p.price || 0;
                    calculatedInitialTotal += initialQuantity * price;
                    const latestQuantity = initialQuantity - returnedQuantity;
                    calculatedLatestTotal += latestQuantity * price;
                    if (returnedQuantity > 0) isReturned = true;
                });
            }

            const singleTotalContainer = document.getElementById('singleTotalAmountContainer');
            const returnedTotalContainer = document.getElementById('returnedTotalAmountContainer');
            if (isReturned && calculatedInitialTotal !== calculatedLatestTotal) {
                document.getElementById('modalInitialTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(calculatedInitialTotal)}`;
                document.getElementById('modalLatestTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(calculatedLatestTotal)}`;
                singleTotalContainer.classList.add('hidden');
                returnedTotalContainer.classList.remove('hidden');
            } else {
                document.getElementById('modalTotalAmount').textContent =
                    `Rp ${new Intl.NumberFormat('id-ID').format(order.total_amount || 0)}`;
                singleTotalContainer.classList.remove('hidden');
                returnedTotalContainer.classList.add('hidden');
            }

            // Populate Product List
            const productDetailsDiv = document.getElementById('productDetails');
            productDetailsDiv.innerHTML = '';
            productDetailsDiv.className = 'flex flex-col space-y-2';
            if (order.products && order.products.length > 0) {
                order.products.forEach(product => {
                    const productItem = document.createElement('div');
                    // Main container for each product card
                    productItem.className =
                        'p-3 border rounded-lg dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 flex items-start space-x-4';

                    const initialQty = product.quantity || 0;
                    const returnedQty = product.returned_quantity || 0;
                    const remainingQty = initialQty - returnedQty;
                    const price = product.price || 0;
                    const newSubtotal = remainingQty * price;

                    // SVG icon similar to the one in the image
                    const iconHTML = `
            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mt-1 bg-gray-200 rounded-lg dark:bg-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        `;

                    let quantityLine =
                        `<p class="text-sm text-gray-600 dark:text-gray-300">Jumlah: ${initialQty}</p>`;
                    if (returnedQty > 0) {
                        quantityLine = `
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Awal: <span class="font-medium text-gray-800 dark:text-gray-200">${initialQty}</span> |
                    Retur: <span class="font-medium text-red-500 dark:text-red-400">${returnedQty}</span> |
                    Sisa: <span class="font-medium text-brand-deep dark:text-brand-light">${remainingQty}</span>
                </p>
            `;
                    }

                    const priceLine = `
            <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                Rp ${new Intl.NumberFormat('id-ID').format(price)} &rarr; Rp ${new Intl.NumberFormat('id-ID').format(newSubtotal)}
            </p>
        `;

                    const detailsHTML = `
            <div class="flex-grow">
                <p class="font-bold text-gray-900 dark:text-white">${escapeHtml(product.name)} ${product.variant_name ? `(${escapeHtml(product.variant_name)})` : ''}</p>
                ${quantityLine}
                ${priceLine}
            </div>
        `;

                    productItem.innerHTML = iconHTML + detailsHTML;
                    productDetailsDiv.appendChild(productItem);
                });
            } else {
                productDetailsDiv.innerHTML =
                    '<p class="text-center text-gray-500 dark:text-gray-400">Tidak ada produk dalam pesanan ini.</p>';
            }

            // --- LOGIKA BUKTI UNGGAHAN YANG DIPERBAIKI ---
            const paymentUploadForm = document.getElementById('paymentUploadForm');
            const paymentProofUploaded = document.getElementById('paymentProofUploaded');
            const paymentUploadBlocker = document.getElementById('paymentUploadBlocker');
            const proofImage = document.getElementById('proofImage');
            const proofUploadedTitle = document.getElementById('proofUploadedTitle');
            const compressLink = document.getElementById('compress-link');
            const paymentProofTitle = document.getElementById('paymentProofTitle');

            // Sembunyikan semua elemen terkait bukti unggahan terlebih dahulu
            paymentUploadForm.classList.add('hidden');
            paymentProofUploaded.classList.add('hidden');
            paymentUploadBlocker.classList.add('hidden');
            compressLink.classList.add('hidden');

            // Helper untuk mendapatkan URL gambar yang benar
            const getImageUrl = (path) => {
                if (!path) return '';
                if (path.startsWith('http')) return path;
                if (path.startsWith('storage/')) return `${APP_URL}/${path}`;
                return `${APP_URL}/storage/${path.replace(/^public\//, '')}`;
            };

            // Logika 1: Jika ini adalah pesanan dengan retur, TAMPILKAN BUKTI RETUR
            // (Backend mengirim data retur di key 'order_return')
            if (order.order_return && order.order_return.return_proof) {
                proofUploadedTitle.textContent = 'Bukti Retur';
                proofImage.src = getImageUrl(order.order_return.return_proof);
                paymentProofUploaded.classList.remove('hidden');

                // Logika 2: Jika BUKAN retur, tapi punya bukti bayar, TAMPILKAN BUKTI BAYAR
            } else if (order.payment_proof) {
                proofUploadedTitle.textContent = 'Bukti Pembayaran';
                console.log(getImageUrl(order.payment_proof))
                proofImage.src = getImageUrl(order.payment_proof);
                paymentProofUploaded.classList.remove('hidden');

                // Logika 3: Jika status memungkinkan untuk upload (belum ada bukti)
            } else if (order.status === 'diterima_pembeli' || order.status === 'menunggu_retur') {
                const isReturn = order.status === 'menunggu_retur';
                paymentProofTitle.textContent = isReturn ? 'Unggah Bukti Retur' : 'Unggah Bukti Pembayaran';
                document.getElementById('uploadButtonText').textContent = isReturn ? 'Unggah Bukti Retur' :
                    'Unggah Bukti Pembayaran';
                paymentUploadForm.onsubmit = (e) => {
                    e.preventDefault();
                    handleProofUpload(order.id, order.status);
                };
                paymentUploadForm.classList.remove('hidden');
                compressLink.classList.remove('hidden');

                // Logika 4: Jika status tidak memungkinkan upload
            } else {
                paymentUploadBlocker.classList.remove('hidden');
            }

            // --- Logika untuk Menampilkan Waktu Retur ---
            const returnTimestampContainer = document.getElementById('returnTimestampContainer');
            const returnCreatedAtEl = document.getElementById('returnCreatedAt');

            // Cek apakah ada data retur dan timestamp-nya
            if (order.order_return && order.order_return.created_at) {
                // Tentukan label zona waktu berdasarkan data dari server
                const timezoneLabel = order.timezone === 'Asia/Makassar' ? 'WITA' : 'WIB';
                returnCreatedAtEl.textContent = `${order.order_return.created_at} ${timezoneLabel}`;
                returnTimestampContainer.classList.remove('hidden');
            } else {
                returnTimestampContainer.classList.add('hidden');
            }
            // Tombol Retur
            const returnRequestButtonContainer = document.getElementById('returnRequestButtonContainer');
            if (order.status === 'diterima_pembeli') {
                returnRequestButtonContainer.classList.remove('hidden');
                document.getElementById('requestReturnButton').onclick = () => openReturnProductModal(order);
            } else {
                returnRequestButtonContainer.classList.add('hidden');
            }

            document.getElementById('modalLoader').classList.add('hidden');
            document.getElementById('modalContent').classList.remove('hidden');
        }

        async function handleProofUpload(orderId, status) {
            const form = document.getElementById('paymentUploadForm');
            const submitButton = form.querySelector('button[type="submit"]');
            let url, nextStatus;

            if (status === 'diterima_pembeli') {
                url = `/kurir/pesanan/${orderId}/upload-proof`;
                nextStatus = 'selesai';
            } else if (status === 'menunggu_retur') {
                url = `/kurir/pesanan/${orderId}/upload-return-proof`;
                nextStatus = 'menunggu_verifikasi_admin';
            } else return;

            submitButton.disabled = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
                dispatchToast(result.message, 'success');
                fetchOrderDetails(orderId);
                updateTableRowStatus(orderId, nextStatus);
            } catch (error) {
                dispatchToast(error.message, 'error');
            } finally {
                submitButton.disabled = false;
            }
        }

        // --- Logika Modal Status & Stepper ---
        async function openStatusStepperModal(orderId) {
            openModal('statusStepperModal');
            const modalLoader = document.getElementById('statusStepperModalLoader');
            const modalContent = document.getElementById('statusStepperModalContent');
            modalContent.classList.add('hidden');
            modalLoader.classList.remove('hidden');
            modalLoader.innerHTML =
                `<div class="flex flex-col items-center justify-center py-8"><svg class="w-8 h-8 text-brand animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><p class="mt-4 text-sm text-gray-600 dark:text-gray-300">Memuat Status...</p></div>`;
            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/details`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message);
                populateStatusStepperModal(data);
            } catch (error) {
                modalLoader.innerHTML =
                    `<div class="text-center"><p class="font-bold text-red-600 dark:text-red-400">Gagal Memuat</p><p class="mt-2 text-sm">${error.message}</p></div>`;
            }
        }

        function populateStatusStepperModal(order) {
            // 1. Peta status yang sudah dilengkapi semua kemungkinan
            const statusMap = {
                'baru': {
                    label: 'Baru',
                    nextStatus: 'diambil',
                    buttonText: 'Ubah Status ke Diambil'
                },
                'dikemas': {
                    label: 'Dikemas',
                    nextStatus: 'diambil',
                    buttonText: 'Ubah Status ke Diambil'
                },
                'diambil': {
                    label: 'Diambil',
                    nextStatus: 'diantar',
                    buttonText: 'Ubah Status ke Diantar'
                },
                'diantar': {
                    label: 'Diantar',
                    nextStatus: 'diterima_pembeli',
                    buttonText: 'Ubah Status ke Diterima Pembeli'
                },
                'diterima_pembeli': {
                    label: 'Diterima Pembeli',
                    nextStatus: null,
                    buttonText: 'Menunggu Bukti Pembayaran'
                },
                'menunggu_retur': {
                    label: 'Menunggu Retur',
                    nextStatus: null,
                    buttonText: 'Menunggu Proses Retur'
                },
                'menunggu_verifikasi_admin': {
                    label: 'Menunggu Verifikasi Admin',
                    nextStatus: null,
                    buttonText: 'Menunggu Verifikasi Admin'
                },
                'selesai': {
                    label: 'Selesai (Lunas)',
                    nextStatus: null,
                    buttonText: 'Pesanan Selesai'
                },
                'diverifikasi_admin': {
                    label: 'Telah Diverifikasi Admin',
                    nextStatus: null,
                    buttonText: 'Telah Diverifikasi Admin'
                }
            };

            // 2. Mengisi info dasar modal
            document.getElementById('modalStatusInvoiceNumber').textContent = order.invoice_number || 'N/A';
            document.getElementById('modalStatusCustomerName').textContent = order.customer.name || 'N/A';

            // 3. Memperbarui UI Stepper dengan memanggil fungsi terpisah
            updateStepperUI(order);

            // 4. Mengatur tombol aksi utama
            const updateButton = document.getElementById('updateStatusButton');
            const updateButtonText = document.getElementById('updateStatusButtonText');
            const currentStatus = order.status || 'baru';
            const currentStatusInfo = statusMap[currentStatus];

            // Menyimpan timezone ke tombol untuk digunakan nanti
            updateButton.setAttribute('data-order-timezone', order.timezone || 'Asia/Jakarta');

            // 5. Pengecekan pengaman untuk menghindari error
            if (currentStatusInfo) {
                updateButtonText.textContent = currentStatusInfo.buttonText;

                // Menonaktifkan tombol jika status sudah final
                if (!currentStatusInfo.nextStatus || ['selesai', 'diverifikasi_admin', 'menunggu_verifikasi_admin',
                        'menunggu_retur'
                    ].includes(currentStatus)) {
                    updateButton.disabled = true;
                    updateButton.classList.add('opacity-50', 'cursor-not-allowed');
                    if (currentStatus === 'diverifikasi_admin') {
                        updateButton.classList.remove('bg-brand', 'hover:bg-brand-deep');
                        updateButton.classList.add('bg-brand-deep', 'hover:bg-brand-deep');
                    }
                } else {
                    updateButton.disabled = false;
                    updateButton.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-brand-deep', 'hover:bg-brand-deep');
                    updateButton.classList.add('bg-brand', 'hover:bg-brand-deep');
                    updateButton.setAttribute('data-next-status', currentStatusInfo.nextStatus);
                }
            } else {
                // Fallback jika status tidak dikenal
                updateButtonText.textContent = `Status Tidak Dikenal: ${currentStatus}`;
                updateButton.disabled = true;
                updateButton.classList.add('opacity-50', 'cursor-not-allowed');
            }

            updateButton.setAttribute('data-order-id', order.id);

            // 6. Menampilkan konten modal
            document.getElementById('statusStepperModalLoader').classList.add('hidden');
            document.getElementById('statusStepperModalContent').classList.remove('hidden');
        }

        // Fungsi pembantu untuk memperbarui UI Stepper secara spesifik
        function updateStepperUI(order) {
            const steps = ['diambil', 'diantar', 'diterima_pembeli'];
            const timestamps = {
                diambil: order.picked_up_at,
                diantar: order.delivered_at,
                diterima_pembeli: order.received_by_buyer_at
            };
            const icons = {
                diambil: 'fa-box',
                diantar: 'fa-truck-moving',
                diterima_pembeli: 'fa-home'
            };
            const timeSpans = {
                diambil: 'pickedUpAt',
                diantar: 'deliveredAt',
                diterima_pembeli: 'receivedByBuyerAt'
            };

            // Menentukan label zona waktu berdasarkan data dari server
            const timezone = order.timezone || 'Asia/Jakarta';
            const tzAbbr = timezone === 'Asia/Makassar' ? 'WITA' : 'WIB';

            // Loop tunggal untuk mengatur setiap langkah
            steps.forEach(step => {
                const iconEl = document.getElementById(`step-${step}-icon`);
                const timeSpanEl = document.getElementById(timeSpans[step]);
                const mobileLineEl = document.getElementById(`line-${step}-mobile`);
                const desktopLineEl = document.getElementById(step === 'diambil' ? 'line-diantar' : `line-${step}`);

                // Reset warna
                iconEl.classList.remove('bg-brand-deep', 'text-brand-deep', 'border-brand-deep');
                if (mobileLineEl) mobileLineEl.classList.remove('bg-brand-deep');
                if (desktopLineEl) desktopLineEl.classList.remove('bg-brand-deep');

                // Cek apakah langkah sudah selesai
                if (timestamps[step]) {
                    iconEl.innerHTML = '<i class="text-white fas fa-check-circle"></i>';
                    iconEl.classList.add('bg-brand-deep', 'border-brand-deep');
                    timeSpanEl.textContent = `${timestamps[step]}  ${tzAbbr}`;
                    if (mobileLineEl) mobileLineEl.classList.add('bg-brand-deep');
                    if (desktopLineEl) desktopLineEl.classList.add('bg-brand-deep');
                } else {
                    iconEl.innerHTML = `<i class="fas ${icons[step]} text-brand-deep dark:text-brand-light"></i>`;
                    timeSpanEl.textContent = tzAbbr; // Tampilkan hanya label jika belum ada waktu
                }
            });
        }

        async function handleStatusUpdate() {
            const updateButton = document.getElementById('updateStatusButton');
            const orderId = updateButton.getAttribute('data-order-id');
            const newStatus = updateButton.getAttribute('data-next-status');
            // Mengambil timezone dari atribut data yang disimpan sebelumnya
            const timezone = updateButton.getAttribute('data-order-timezone')

            if (!orderId || !newStatus) {
                dispatchToast('Error: Status atau Order ID tidak ditemukan.', 'error');
                return;
            }

            const buttonText = document.getElementById('updateStatusButtonText');
            const buttonSpinner = document.getElementById('updateStatusButtonSpinner');

            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');
            updateButton.disabled = true;
            updateButton.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        new_status: newStatus
                    })
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui status.');

                dispatchToast(result.message, 'success');

                // --- LOGIKA WAKTU DENGAN ZONA WAKTU DINAMIS ---
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    timeZone: timezone, // Menggunakan timezone dari server
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
                const localizedTimestamp = formatter.format(now);

                const timeSpanId = {
                    'diambil': 'pickedUpAt',
                    'diantar': 'deliveredAt',
                    'diterima_pembeli': 'receivedByBuyerAt'
                } [newStatus];

                if (timeSpanId) {
                    // Menampilkan waktu yang sudah dilokalisasi
                    document.getElementById(timeSpanId).textContent = localizedTimestamp;
                }

                // Muat ulang konten modal untuk mendapatkan data server terbaru
                openStatusStepperModal(orderId);

                // Perbarui status pada baris tabel di halaman utama
                updateTableRowStatus(orderId, result.order.status);

            } catch (error) {
                console.error('Error updating order status:', error);
                dispatchToast(`Gagal: ${error.message}`, 'error');
            } finally {
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        }

        function updateTableRowStatus(orderId, newStatus) {
            const rows = document.querySelectorAll(`[data-order-id="${orderId}"]`);
            const statusText = STATUS_LABEL_MAP[newStatus] || (newStatus.charAt(0).toUpperCase() + newStatus.slice(1)
                .replace(/_/g, ' '));

            // Definisikan kelas warna dinamis
            let newClasses = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            let newColorBarClass = 'bg-gray-400';
            // ... (switch case untuk newClasses dan newColorBarClass tetap sama)
            switch (newStatus) {
                case 'diambil':
                    newClasses = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
                    newColorBarClass = 'bg-blue-500';
                    break;
                case 'diantar':
                    newClasses = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                    newColorBarClass = 'bg-yellow-500';
                    break;
                case 'diterima_pembeli':
                    newClasses = 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
                    newColorBarClass = 'bg-purple-500';
                    break;
                case 'menunggu_retur':
                    newClasses = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                    newColorBarClass = 'bg-red-500';
                    break;
                case 'menunggu_verifikasi_admin':
                    newClasses = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
                    newColorBarClass = 'bg-orange-500';
                    break;
                case 'selesai':
                    newClasses = 'bg-brand-light text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light';
                    newColorBarClass = 'bg-brand';
                    break;
            }


            // Definisikan semua kelas dasar yang statis

            rows.forEach(row => {
                const statusSpan = row.querySelector('.status-badge');
                if (statusSpan) {
                    statusSpan.textContent = statusText;
                    // Gabungkan kelas dasar dengan kelas warna yang baru
                }
                const colorBar = row.querySelector('.absolute.top-0.left-0');
                if (colorBar) {
                    colorBar.className = colorBar.className.replace(/bg-\w+-\d+/g, '') + ` ${newColorBarClass}`;
                }
            });
        }

        function openReturnProductModal(order) {
            const returnModalLoader = document.getElementById('returnModalLoader');
            const returnModalContent = document.getElementById('returnModalContent');
            const desktopContainer = document.getElementById('return-product-list-desktop');
            const mobileContainer = document.getElementById('return-product-list-mobile');
            const returnOrderIdInput = document.getElementById('returnOrderId');

            returnModalContent.classList.add('hidden');
            returnModalLoader.classList.remove('hidden');
            returnOrderIdInput.value = order.id;
            desktopContainer.innerHTML = '';
            mobileContainer.innerHTML = '';

            if (!order.products || order.products.length === 0) {
                const noProductHTML =
                    '<p class="py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada produk untuk diretur.</p>';
                desktopContainer.innerHTML = `<tr><td colspan="4">${noProductHTML}</td></tr>`;
                mobileContainer.innerHTML = noProductHTML;
            } else {
                order.products.forEach((product, index) => {
                    const productId = product.product_id || product.id;
                    const variantId = product.variant_id ?? 0;
                    const returnKey = `${productId}-${variantId}`;
                    const placeholderImg = 'https://placehold.co/64x64/E2E8F0/64748B?text=No+Img';
                    let productImage = placeholderImg; // Set placeholder sebagai default

                    if (product.image_url) {
                        if (product.image_url.startsWith('http')) {
                            // KASUS 1: URL sudah benar (misal: https://.../storage/...)
                            productImage = product.image_url;
                        } else if (product.image_url.startsWith('/storage/')) {
                            // KASUS 2: URL adalah path relatif yang benar
                            productImage = product.image_url;
                        } else if (product.image_url.startsWith('products/')) {
                            // KASUS 3: URL adalah path rusak ('products/...')
                            // Kita perbaiki manual
                            productImage = `/storage/${product.image_url}`;
                        } else {
                            // Fallback jika ada format lain
                            productImage = product.image_url;
                        }
                    }

                    // DIUBAH: Template menggunakan <input type="number">
                    const desktopRowHTML = `
                        <tr data-return-key="${returnKey}">
                            <td class="px-4 py-4 whitespace-nowrap"><div class="text-sm text-gray-900 dark:text-white">${index + 1}</div></td>
                            <td class="px-2 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-16 h-16"><img class="object-cover w-16 h-16 rounded-md" src="${productImage}" alt="${escapeHtml(product.name)}"></div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(product.name)}</div>
                                        ${product.variant_name ? `<div class="text-xs text-gray-400 dark:text-gray-500">${escapeHtml(product.variant_name)}</div>` : ''}
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Awal: ${product.quantity}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="px-2 text-black transition rounded quantity-minus hover:bg-gray-300 dark:text-white dark:hover:bg-gray-700 hover:scale-110 active:scale-90">–</button>
                                    <input type="number"
                                           data-name="return_qty[${returnKey}]"
                                           data-max="${product.quantity}"
                                           value="0"
                                           min="0"
                                           max="${product.quantity}"
                                           class="quantity-input w-12 px-1 py-0 text-center text-black bg-transparent rounded dark:text-white dark:bg-transparent
                                           [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0">
                                    <button type="button" class="px-2 text-black transition rounded quantity-plus hover:bg-gray-300 dark:text-white dark:hover:bg-gray-700 hover:scale-110 active:scale-90">+</button>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-center whitespace-nowrap">
                                <button type="button" class="text-red-600 remove-product hover:text-red-900 dark:hover:text-red-500" title="Setel kuantitas ke 0">🗑</button>
                            </td>
                        </tr>`;

                    const mobileCardHTML = `
                        <div class="flex items-start gap-4 p-2 mx-0 border-b border-gray-200 dark:border-gray-700" data-return-key="${returnKey}">
                            <div class="flex-shrink-0 w-24 h-24"><img class="object-cover w-24 h-24 rounded-md" src="${productImage}" alt="${escapeHtml(product.name)}"></div>
                            <div class="flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-bold text-black dark:text-white">${escapeHtml(product.name)}</p>
                                    <button type="button" class="text-xl text-red-600 dark:text-red-400 remove-product hover:text-red-900 dark:hover:text-red-300" title="Setel kuantitas ke 0">🗑</button>
                                </div>
                                ${product.variant_name ? `<p class="mb-1 text-xs text-gray-500 dark:text-gray-400">${escapeHtml(product.variant_name)}</p>` : ''}
                                <p class="text-sm text-gray-600 dark:text-gray-300">Jumlah Awal: ${product.quantity}</p>
                                    <div class="flex items-center justify-start gap-2 mt-3">
                                        <button type="button" class="px-2 text-black rounded quantity-minus dark:text-white hover:scale-110 active:scale-90">–</button>
                                        <input type="number"
                                           data-name="return_qty[${returnKey}]"
                                           data-max="${product.quantity}"
                                           value="0"
                                           min="0"
                                           max="${product.quantity}"
                                           class="quantity-input w-12 px-1 py-0 text-center text-black bg-transparent rounded dark:text-white dark:bg-transparent
                                           [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0">
                                        <button type="button" class="px-2 text-black rounded quantity-plus dark:text-white hover:scale-110 active:scale-90">+</button>
                                    </div>
                            </div>
                        </div>`;

                    desktopContainer.insertAdjacentHTML('beforeend', desktopRowHTML);
                    mobileContainer.insertAdjacentHTML('beforeend', mobileCardHTML);
                });
            }

            // BARU: Event listener dinamis untuk tombol +/- dan hapus
            // Ini akan menangani semua baris dan kartu produk yang baru dibuat
            document.querySelectorAll('#returnModalContent [data-return-key]').forEach(container => {
                const qtyInput = container.querySelector('.quantity-input');
                const maxVal = parseInt(qtyInput.max, 10);

                container.querySelector('.quantity-minus').addEventListener('click', () => {
                    let currentVal = parseInt(qtyInput.value, 10);
                    if (currentVal > 0) qtyInput.value = currentVal - 1;
                });

                container.querySelector('.quantity-plus').addEventListener('click', () => {
                    let currentVal = parseInt(qtyInput.value, 10);
                    if (currentVal < maxVal) qtyInput.value = currentVal + 1;
                });

                container.querySelector('.remove-product').addEventListener('click', () => {
                    qtyInput.value = 0;
                });

                qtyInput.addEventListener('change', () => { // Validasi jika user mengetik langsung
                    let currentVal = parseInt(qtyInput.value, 10);
                    if (isNaN(currentVal) || currentVal < 0) qtyInput.value = 0;
                    if (currentVal > maxVal) qtyInput.value = maxVal;
                });
            });

            returnModalLoader.classList.add('hidden');
            returnModalContent.classList.remove('hidden');

            // Menghubungkan form submit dengan fungsi handler
            document.getElementById('returnProductForm').onsubmit = (e) => {
                e.preventDefault();
                handleReturnRequestSubmit(order.id);
            };
        }

        async function handleReturnRequestSubmit(orderId) {
            const form = document.getElementById('returnProductForm');
            const submitButton = document.getElementById('submitReturnRequestButton');
            const buttonText = document.getElementById('submitReturnRequestButtonText');
            const buttonSpinner = document.getElementById('submitReturnRequestButtonSpinner');

            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');

            const returnQuantities = {};
            const reason = document.getElementById('return_reason').value;

            let hasValidReturn = false;

            // Menyesuaikan: Mengambil data dari <input> dan atribut data-name
            form.querySelectorAll('.quantity-input').forEach(inputElement => {
                const key = inputElement.dataset.name.match(/\[(.*?)\]/)[1];
                const quantity = parseInt(inputElement.value, 10); // Mengambil nilai dari input
                if (!isNaN(quantity) && quantity > 0) {
                    returnQuantities[key] = quantity;
                    hasValidReturn = true;
                }
            });

            if (!hasValidReturn) {
                dispatchToast('Anda harus memasukkan jumlah minimal 1 untuk satu produk.', 'error');
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
                return;
            }


            try {
                const response = await fetch(`/kurir/pesanan/${orderId}/request-return`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        return_quantities: returnQuantities,
                        reason: reason
                    })
                });

                const result = await response.json();
                if (!response.ok) {
                    const errorMsg = result.errors ? Object.values(result.errors).flat().join(' ') : result.message;
                    throw new Error(errorMsg || 'Gagal mengajukan pengembalian.');
                }

                dispatchToast(result.message, 'success');
                closeModal(document.getElementById('returnProductModal'));
                updateTableRowStatus(orderId, result.order.status);
                fetchOrderDetails(orderId);
            } catch (error) {
                dispatchToast(`Gagal: ${error.message}`, 'error');
            } finally {
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        }
    </script>
    <script>
        const previewImage = document.getElementById('previewImage');
        const paymentProfFile = document.getElementById('payment_proof_file')

        function resetPaymentProof() {
            if (previewImage) {
                previewImage.src = "";
                previewImage.style.display = 'none';
            }

            if (paymentProfFile) {
                paymentProfFile.value = "";
            }
        }
        // preview image


        paymentProfFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                previewImage.src = ""
                return
            }

            const reader = new FileReader();
            reader.onload = function(event) {

                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        document.querySelector('.js-close-modal-btn').addEventListener('click', () => {
            resetPaymentProof()
        });

        document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                resetPaymentProof()
            }
        });
    </script>
@endpush
