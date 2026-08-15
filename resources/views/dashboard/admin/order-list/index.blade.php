@extends('layouts.argon')
@section('title', 'Manajemen Pesanan')
@section('page_title', 'Pesanan Masuk')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-xs font-semibold text-brand-deep rounded-2xl bg-mint dark:bg-brand-deep/40 dark:text-brand-light border border-brand-light dark:border-brand-deep flex items-center gap-2 shadow-sm" role="alert">
            <i class="fas fa-check-circle text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-3xl shadow-lg min-h-[700px] space-y-6">
            
            <!-- Header: Title & Meta -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-2">
                    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand to-brand-deep text-white flex items-center justify-center shadow-lg shadow-brand/20">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <span>Daftar Pesanan Masuk</span>
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Verifikasi pembayaran & kelola status pesanan dari reseller/kurir.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-full bg-mint text-brand-deep dark:bg-brand-deep/50 dark:text-brand-light border border-brand-light dark:border-brand-deep text-sm font-bold flex items-center gap-2 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand animate-pulse"></span>
                        <span>Total Pesanan: {{ number_format($orders->total()) }}</span>
                    </div>
                </div>
            </div>

            <!-- Search Bar & Filters -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <form class="flex items-center w-full sm:w-auto" method="GET">
                    <div class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-sm text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full p-3 pl-11 text-sm text-slate-900 border border-slate-200 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-brand focus:border-brand dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white transition-all shadow-sm hover:shadow-md"
                            placeholder="Cari nomor invoice atau nama customer...">
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-800 shadow-sm">
                <div class="overflow-x-auto min-h-[500px]">
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
                        <thead class="text-xs font-extrabold text-slate-400 uppercase bg-slate-50/90 dark:bg-slate-800/90">
                            <tr>
                                <th class="px-6 py-4 text-center w-14">No.</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Kurir</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Catatan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="order-result-container" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @include('dashboard.admin.order-list._table_rows', ['orders' => $orders])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4" aria-label="Table navigation">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-white">{{ $orders->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800 dark:text-white">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800 dark:text-white">{{ $orders->total() }}</span> pesanan
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

        function showVerifyModalZoom(imgSrc) {
            if (!zoomImg || !zoomWrapper) return;
            zoomImg.src = imgSrc;
            zoomWrapper.classList.remove('hidden');
            zoomWrapper.classList.add('flex');
        }

        function hideVerifyModalZoom() {
            if (!zoomWrapper) return;
            zoomWrapper.classList.add('hidden');
            zoomWrapper.classList.remove('flex');
        }

        if (zoomCloseBtn) zoomCloseBtn.addEventListener('click', hideVerifyModalZoom);
        if (zoomWrapper) {
            zoomWrapper.addEventListener('click', function(event) {
                if (event.target === zoomWrapper) {
                    hideVerifyModalZoom();
                }
            });
        }

        let currentOrderId = null;
        const baseOrdersUrl = "{{ url('admin/orders') }}";

        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        }

        function setModalText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        }

        function formatRupiah(value) {
            return `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
        }

        async function openVerifyModal(orderId) {
            const loader = document.getElementById('verifyModalLoader');
            const content = document.getElementById('verifyModalContent');
            if (loader) loader.classList.remove('hidden');
            if (content) content.classList.add('hidden');

            try {
                const response = await fetch(`${baseOrdersUrl}/${orderId}/details`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('Gagal memuat detail pesanan.');
                const data = await response.json();

                setModalText('verifyModalInvoiceNumber', data.invoice_number || 'N/A');
                setModalText('verifyModalCustomerName', data.customer?.name || 'N/A');
                setModalText('verifyModalCompanyName', data.customer?.company_name || '');
                setModalText('verifyModalCustomerPhone', data.customer?.phone || 'N/A');
                setModalText('verifyModalCustomerAddress', data.customer?.address || 'N/A');
                setModalText('verifyModalPaymentMethod', data.payment_method || '-');
                setModalText('verifyModalOrderCreatedAt', data.created_at || '-');
                setModalText('verifyModalOrderPaidAt', data.paid_at || '-');
                setModalText('verifyModalCourierName', data.kurir_name || '-');
                setModalText('verifyModalOrderNote', data.note || 'Tidak ada catatan.');
                setModalText('verifyModalTotalAmount', formatRupiah(data.total_amount));

                const companyName = document.getElementById('verifyModalCompanyName');
                if (companyName) companyName.classList.toggle('hidden', !data.customer?.company_name);

                const productContainer = document.getElementById('verifyModalProductDetails');
                if (productContainer) {
                    productContainer.innerHTML = (data.items || []).map(item => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">${escapeHtml(item.name || '-')}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(item.variant_name || 'Tanpa Varian')} &times; ${item.quantity}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">${formatRupiah(item.price)}</p>
                        </div>
                    `).join('') || '<p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada produk.</p>';
                }

                const returnedSection = document.getElementById('returnedProductsSection');
                const returnedContainer = document.getElementById('verifyModalReturnedProducts');
                if (data.return_details && data.return_details.returned_products?.length) {
                    if (returnedContainer) {
                        returnedContainer.innerHTML = data.return_details.returned_products.map(p => `
                            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div>
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">${escapeHtml(p.name || '-')}</p>
                                    <p class="text-xs text-red-600 dark:text-red-400">${escapeHtml(p.variant_name || '')} &times; ${p.quantity}</p>
                                </div>
                                <p class="text-sm font-bold text-red-800 dark:text-red-300">${formatRupiah(p.price)}</p>
                            </div>
                        `).join('');
                    }
                    if (returnedSection) returnedSection.classList.remove('hidden');
                } else {
                    if (returnedSection) returnedSection.classList.add('hidden');
                }

                const proofImg = document.getElementById('verifyModalProofImage');
                const proofImgContainer = document.getElementById('verifyModalProofImageContainer');
                const noProof = document.getElementById('verifyModalNoProof');
                if (data.payment_proof) {
                    if (proofImg) proofImg.src = data.payment_proof;
                    if (proofImgContainer) proofImgContainer.classList.remove('hidden');
                    if (noProof) noProof.classList.add('hidden');
                } else {
                    if (proofImgContainer) proofImgContainer.classList.add('hidden');
                    if (noProof) noProof.classList.remove('hidden');
                }

                if (loader) loader.classList.add('hidden');
                if (content) content.classList.remove('hidden');
            } catch (error) {
                console.error('Error loading order details:', error);
                if (loader) {
                    loader.innerHTML = '<p class="text-center text-red-500 dark:text-red-400">Gagal memuat detail pesanan.</p>';
                }
            }
        }

        async function verifyOrder(orderId) {
            try {
                const response = await fetch(`${baseOrdersUrl}/${orderId}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) throw new Error('Gagal memverifikasi pesanan.');
                const data = await response.json();
                dispatchToast(data.message || 'Pesanan berhasil diverifikasi.');
                window.location.reload();
            } catch (error) {
                console.error('Verify error:', error);
                alert('Gagal memverifikasi pesanan.');
            }
        }

        function submitRejectionForm() {
            if (!currentOrderId) return;
            const noteInput = document.getElementById('rejection_note');
            const note = (noteInput && noteInput.value.trim()) || '';
            if (note.length < 10) {
                alert('Alasan penolakan minimal 10 karakter.');
                return;
            }
            const form = document.getElementById('rejectionForm');
            form.action = `${baseOrdersUrl}/${currentOrderId}/reject`;
            form.submit();
        }

        async function deleteOrder(orderId) {
            try {
                const response = await fetch(`${baseOrdersUrl}/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) throw new Error('Gagal menghapus pesanan.');
                const data = await response.json();
                dispatchToast(data.message || 'Pesanan berhasil dihapus.');
                window.location.reload();
            } catch (error) {
                console.error('Delete error:', error);
                alert('Gagal menghapus pesanan.');
            }
        }

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
                        document.getElementById('fullOrderNote').textContent = noteContent || 'Tidak ada catatan.';
                    }
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
                        closeModal(document.getElementById('verifyOrderModal'));
                        openModal('rejectionNoteModal');
                    }
                    return;
                }

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
    </script>
@endpush
