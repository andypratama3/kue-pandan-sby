@extends('layouts.argon')
@section('title', 'Manajemen Pesanan')
@section('page_title', 'Pesanan')

{{-- Tambahkan CSRF Token untuk request AJAX --}}
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
            <span class="font-medium">Sukses!</span> {{ session('success') }}
        </div>
    @endif

    <div class="flex-auto p-3 pt-0 -mx-3">
        <div class="p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700 min-h-[715px]">
            {{-- Header: Title dan User Info --}}
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>Daftar Pesanan
                    </h2>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-user mr-1"></i>{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ Auth::user()->region->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="px-3 py-1 font-semibold text-white bg-blue-600 rounded-full dark:bg-blue-500">
                        Total: {{ $orders->total() }}
                    </span>
                </div>
            </div>

            {{-- Search --}}
            <div class="mb-6">
                <form class="flex items-center" method="GET">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white transition-all"
                            placeholder="Cari invoice atau customer...">
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-hidden border border-gray-200 rounded-xl dark:border-slate-700">
                <div class="overflow-x-auto min-h-[580px]">
                    @php
                        $statusLabelMap = [
                            'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
                            'menunggu_pembayaran' => 'Menunggu Pembayaran',
                            'sedang_diproses' => 'Sedang Diproses',
                            'sedang_dikirim' => 'Sedang Dikirim',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                            'dibatalkan' => 'Dibatalkan',
                        ];

                        $labelStatus = function ($status) use ($statusLabelMap) {
                            return $statusLabelMap[$status] ?? ucwords(str_replace('_', ' ', $status));
                        };
                    @endphp
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-slate-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4 text-center">No.</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Kurir</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Catatan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="order-result-container" class="divide-y divide-gray-200 dark:divide-slate-700">
                            @include('dashboard.admin.order-list._table_rows', ['orders' => $orders])
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
    @include('dashboard.admin.order-list.verify-modal')
    @include('dashboard.admin.order-list.delete')
    @include('dashboard.admin.order-list.note-modal')
    @include('dashboard.admin.order-list.rejection-modal')
@endpush

@push('page-scripts')
    <script>
        // --- FUNGSI PEMBANTU ---
        function dispatchToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    type: type,
                    message: message
                }
            }));
        }

        const zoomWrapper = document.getElementById('verifyModalZoomWrapper');
        const zoomImg = document.getElementById('verifyModalZoomImg');
        const zoomCloseBtn = document.getElementById('verifyModalZoomCloseBtn');

        // Fungsi untuk menampilkan gambar zoom
        function showVerifyModalZoom(imgSrc) {
            zoomImg.src = imgSrc;
            zoomWrapper.classList.remove('hidden');
            zoomWrapper.classList.add('flex');
        }

        // Fungsi untuk menyembunyikan gambar zoom
        function hideVerifyModalZoom() {
            zoomWrapper.classList.add('hidden');
            zoomWrapper.classList.remove('flex');
        }

        // Event listener untuk tombol close '×'
        zoomCloseBtn.addEventListener('click', hideVerifyModalZoom);

        // Event listener untuk klik di luar gambar (area overlay)
        zoomWrapper.addEventListener('click', function(event) {
            if (event.target === zoomWrapper) {
                hideVerifyModalZoom();
            }
        });

        // --- FUNGSI UTAMA MODAL ---
        let currentOrderId = null;

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(event) {
                const target = event.target.closest('button');
                if (!target) return;

                if (target.matches('.js-open-modal-btn')) {
                    const modalId = target.dataset.targetModal;
                    if (modalId === 'verifyOrderModal') {
                        currentOrderId = target.dataset.orderId;
                        openVerifyModal(currentOrderId);
                    }
                    if (modalId === 'viewNoteModal') {
                        const noteContent = target.dataset.note;
                        document.getElementById('fullOrderNote').textContent = noteContent ||
                            'Tidak ada catatan.';
                    }
                    // This will handle opening all modals including the ones above
                    openModal(modalId);
                    return;
                }

                if (target.matches('.js-open-delete-modal')) {
                    currentOrderId = target.dataset.orderId;
                    const invoiceNumber = target.dataset.invoiceNumber;
                    document.getElementById('deleteInvoiceNumber').textContent = invoiceNumber;
                    openModal('deleteConfirmModal');
                    return;
                }

                if (target.matches('#btnVerifyOrder')) {
                    if (currentOrderId) verifyOrder(currentOrderId);
                    return;
                }

                if (target.matches('#btnOpenRejectModal')) {
                    if (currentOrderId) {
                        // Close verify modal first, then open rejection modal
                        closeModal(document.getElementById('verifyOrderModal'));
                        openModal('rejectionNoteModal');
                    }
                    return;
                }

                // Event listener for the actual form submission button
                if (target.matches('#btnConfirmRejection')) {
                    submitRejectionForm();
                    return;
                }

                if (target.matches('#btnConfirmDelete')) {
                    if (currentOrderId) deleteOrder(currentOrderId);
                    return;
                }
            });
        });

        function submitRejectionForm() {
            const rejectionForm = document.getElementById('rejectionForm');
            const noteTextarea = document.getElementById('rejection_note');
            const noteValue = noteTextarea.value.trim();

            if (noteValue.length < 10) {
                dispatchToast('Alasan penolakan harus diisi minimal 10 karakter.', 'error');
                noteTextarea.focus();
                return;
            }

            if (!currentOrderId) {
                dispatchToast('Error: Order ID tidak ditemukan. Silakan coba lagi.', 'error');
                return;
            }

            rejectionForm.action = `/admin/orders/${currentOrderId}/reject`;
            rejectionForm.submit();
        }

        // [!code block:start]
        // --- FUNGSI openVerifyModal YANG TELAH DIPERBAIKI ---
        async function openVerifyModal(orderId) {
            const loader = document.getElementById('verifyModalLoader');
            const content = document.getElementById('verifyModalContent');
            const modalTitle = document.querySelector('#verifyOrderModal h3');

            // Elemen-elemen spesifik di modal verifikasi
            const returnedProductsSection = document.getElementById('returnedProductsSection');
            const returnedProductsList = document.getElementById('verifyModalReturnedProducts');
            const proofTitle = document.getElementById('verifyModalProofTitle');
            const proofImageContainer = document.getElementById('verifyModalProofImageContainer');
            const proofImage = document.getElementById('verifyModalProofImage');
            const noProofContainer = document.getElementById('verifyModalNoProof');

            // Reset state
            loader.classList.remove('hidden');
            content.classList.add('hidden');
            modalTitle.textContent = "Verifikasi Rincian Pesanan";
            returnedProductsSection.classList.add('hidden');
            proofImageContainer.classList.add('hidden');
            noProofContainer.classList.add('hidden');

            try {
                const response = await fetch(`/admin/orders/${orderId}/details`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memuat data.');

                // Populate data umum (Nama customer, invoice, dll)
                document.getElementById('verifyModalInvoiceNumber').textContent = data.invoice_number || '-';
                document.getElementById('verifyModalCustomerName').textContent = data.customer?.name || '-';
                const companyNameEl = document.getElementById('verifyModalCompanyName');
                if (data.customer?.company_name) {
                    companyNameEl.textContent = data.customer.company_name;
                    companyNameEl.classList.remove('hidden');
                } else {
                    companyNameEl.classList.add('hidden');
                }
                document.getElementById('verifyModalCustomerPhone').textContent = data.customer?.phone || '';
                document.getElementById('verifyModalCustomerAddress').textContent = data.customer?.address || '';
                document.getElementById('verifyModalPaymentMethod').textContent = data.payment_method || '-';
                document.getElementById('verifyModalOrderCreatedAt').textContent = data.created_at || '-';
                document.getElementById('verifyModalOrderPaidAt').textContent = data.paid_at ?
                    `${data.paid_at}${data.paid_at_label || ''}` : 'Belum Lunas';
                document.getElementById('verifyModalCourierName').textContent = data.kurir_name || '-';
                document.getElementById('verifyModalOrderNote').textContent = data.note ||
                    'Tidak ada catatan dari kurir.';

                // Populate produk yang dipesan
                const productDetailsDiv = document.getElementById('verifyModalProductDetails');
                productDetailsDiv.innerHTML = '';
                if (Array.isArray(data.items) && data.items.length > 0) {
                    data.items.forEach(item => {
                        const productItem = document.createElement('div');
                        productItem.className =
                            'p-2 border rounded-lg dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50';
                        productItem.innerHTML =
                            `<p class="font-semibold text-gray-900 dark:text-white">${item.name} ${item.variant_name ? `(${item.variant_name})` : ''}</p><p class="text-sm text-gray-700 dark:text-gray-300">Jumlah: ${item.quantity} x Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</p>`;
                        productDetailsDiv.appendChild(productItem);
                    });
                } else {
                    productDetailsDiv.innerHTML = '<p>Tidak ada produk.</p>';
                }

                // Logika utama untuk menampilkan Total dan Bukti (Pembayaran vs Retur)
                let proofUrl = null;

                if (data.return_details) {
                    // --- TAMPILAN JIKA ADA RETUR ---
                    modalTitle.textContent = "Verifikasi Pesanan dengan Retur";
                    proofTitle.textContent = "✅ Bukti Retur";
                    proofUrl = data.return_details.return_proof;

                    const originalTotal = data.total_amount || 0;
                    const returnedAmount = data.return_details.total_amount_returned || 0;
                    const newTotal = originalTotal - returnedAmount;

                    document.getElementById('verifyModalTotalAmount').innerHTML =
                        `<span class="block text-sm font-normal text-gray-500 line-through">Rp ${Number(originalTotal).toLocaleString('id-ID')}</span>` +
                        `<span class="block text-green-600 dark:text-green-500">Rp ${Number(newTotal).toLocaleString('id-ID')} (Setelah Retur)</span>`;

                    // Tampilkan detail produk retur
                    returnedProductsSection.classList.remove('hidden');
                    returnedProductsList.innerHTML = '';
                    if (Array.isArray(data.return_details.returned_products) && data.return_details.returned_products
                        .length > 0) {
                        data.return_details.returned_products.forEach(item => {
                            const returnedItem = document.createElement('div');
                            returnedItem.className = 'text-sm';
                            returnedItem.innerHTML =
                                `<p class="font-semibold text-gray-800 dark:text-gray-200">${item.name} ${item.variant_name ? `(${item.variant_name})` : ''}</p><p class="text-gray-600 dark:text-gray-400">Jumlah Diretur: ${item.quantity} x Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</p>`;
                            returnedProductsList.appendChild(returnedItem);
                        });
                    } else {
                        returnedProductsList.innerHTML = '<p>Tidak ada detail produk retur.</p>';
                    }

                } else {
                    // --- TAMPILAN NORMAL (TANPA RETUR) ---
                    modalTitle.textContent = "Verifikasi Rincian Pesanan";
                    proofTitle.textContent = "✅ Bukti Pembayaran";
                    proofUrl = data.payment_proof;
                    document.getElementById('verifyModalTotalAmount').innerHTML =
                        `Rp ${data.total_amount ? Number(data.total_amount).toLocaleString('id-ID') : '0'}`;
                }

                // Tampilkan gambar jika URL ada, jika tidak, tampilkan pesan "Tidak ada bukti"
                if (proofUrl) {
                    proofImage.src = proofUrl; // Langsung gunakan URL dari backend
                    proofImage.alt = data.return_details ? 'Bukti Retur' : 'Bukti Pembayaran';
                    proofImageContainer.classList.remove('hidden');
                    proofImage.onclick = () => showVerifyModalZoom(proofUrl);
                } else {
                    noProofContainer.classList.remove('hidden');
                }

                loader.classList.add('hidden');
                content.classList.remove('hidden');

            } catch (error) {
                console.error('Error openVerifyModal:', error);
                loader.innerHTML = `<div class='text-center text-red-600'>Error: ${error.message}</div>`;
                content.classList.add('hidden');
            }
        }
        // [!code block:end]

        // Fungsi untuk mengirim request verifikasi
        async function verifyOrder(orderId) {
            const modalElement = document.getElementById('verifyOrderModal');
            try {
                const response = await fetch(`/admin/orders/${orderId}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan');

                closeModal(modalElement);
                dispatchToast('Pesanan berhasil diverifikasi!', 'success');
                setTimeout(() => window.location.reload(), 1500);

            } catch (error) {
                dispatchToast(`Gagal verifikasi: ${error.message}`, 'error');
            }
        }

        // Fungsi untuk menghapus pesanan
        async function deleteOrder(orderId) {
            const modalElement = document.getElementById('deleteConfirmModal');
            try {
                const response = await fetch(`/admin/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan');

                closeModal(modalElement);
                dispatchToast('Pesanan berhasil dihapus!', 'success');
                setTimeout(() => window.location.reload(), 1500);

            } catch (error) {
                dispatchToast(`Gagal menghapus: ${error.message}`, 'error');
            }
        }
    </script>
@endpush
