<x-modal-custom id="showOrderModal" title="Rincian Pesanan" size="4xl">
    {{-- Loader saat data dimuat --}}
    <div id="showOrderModalLoader" class="p-8 text-center">
        <svg class="w-8 h-8 mx-auto text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">Memuat Detail Pesanan...</p>
    </div>

    {{-- Konten utama modal, awalnya disembunyikan --}}
    <div id="showOrderModalContent" class="hidden">
        {{-- Kontainer Error --}}
        <div id="showOrderModalError" class="hidden py-10 text-center">
            <p class="font-semibold text-red-600" data-role="error-title">Gagal Memuat Data</p>
            <p class="mt-1 text-sm text-gray-500" data-role="error-message">Silakan coba lagi.</p>
        </div>

        {{-- Kontainer Grid untuk konten utama --}}
        <div id="showOrderModalGrid" class="grid grid-cols-1 gap-6 p-1 lg:grid-cols-3">
            {{-- KOLOM KIRI --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- KARTU DETAIL PESANAN --}}
                <div class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-5 text-xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Invoice</label>
                            <p id="showOrderModalInvoiceNumber" class="font-mono text-lg font-semibold text-gray-800 dark:text-gray-200"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</label>
                            <p id="showOrderModalCustomerName" class="text-lg font-semibold text-gray-800 dark:text-gray-200"></p>
                            <p id="showOrderModalCustomerCompany" class="text-sm text-gray-600 dark:text-gray-300"></p>
                            <p id="showOrderModalCustomerPhone" class="text-sm text-gray-600 dark:text-gray-300"></p>
                            <p id="showOrderModalCustomerAddress" class="text-sm text-gray-600 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Metode Pembayaran</label>
                            <p id="showOrderModalPaymentMethod" class="font-semibold text-gray-800 uppercase text-md dark:text-gray-200"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Penting</label>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Tgl. Pesan: <span id="showOrderModalCreatedAt" class="font-semibold"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Tgl. Lunas: <span id="showOrderModalPaidAt" class="font-semibold"></span></p>
                        </div>
                         {{-- KURIR (KHUSUS ADMIN) --}}
                        <div class="sm:col-span-2">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kurir Bertugas</label>
                            <p id="showOrderModalCourier" class="font-semibold text-gray-800 text-md dark:text-gray-200"></p>
                        </div>
                    </div>
                    {{-- Bagian Catatan Pesanan --}}
                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Pesanan</label>
                        <div class="text-center p-3 mt-1 text-sm text-gray-700 bg-yellow-100 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:text-white dark:border-yellow-800/50">
                            <p id="showOrderModalNotesContainer">"Tidak ada catatan."</p>
                        </div>
                    </div>
                </div>

                {{-- KARTU PRODUK DIPESAN --}}
                <div class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">🛒 Produk Dipesan</h3>
                    <div id="showOrderModalProductDetails" class="space-y-3"></div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="space-y-6 lg:col-span-1">
                {{-- KARTU RINCIAN TOTAL --}}
                <div class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <div id="singleTotalContainer-admin">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tagihan</label>
                        <p id="showOrderModalTotalAmount-admin" class="text-2xl font-extrabold text-blue-600 dark:text-blue-500"></p>
                    </div>
                    <div id="returnedTotalContainer-admin" class="hidden">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tagihan</label>
                        <p id="initialTotalAmount-admin" class="text-sm font-bold text-gray-400 line-through dark:text-gray-500"></p>
                        <p id="latestTotalAmount-admin" class="text-2xl font-extrabold text-green-600 dark:text-green-500"></p>
                    </div>
                    <div id="returnTotalValueContainer-admin" class="hidden pt-4 mt-4 border-t border-gray-200 dark:border-gray-600">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Nilai Retur</label>
                        <p id="showOrderModalTotalReturned-admin" class="text-lg font-bold text-red-600 dark:text-red-500"></p>
                    </div>
                </div>

                {{-- KARTU BUKTI PEMBAYARAN --}}
                <div class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Bukti Pembayaran</h3>
                    <div id="showOrderModalPaymentProof-admin"></div>
                </div>

                {{-- KARTU BUKTI RETUR (Kondisional) --}}
                <div id="returnProofContainer-admin" class="hidden p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Bukti Retur</h3>
                    <div id="showOrderModalReturnProof-admin"></div>
                </div>
            </div>
        </div>
</div>
</x-modal-custom>

{{-- Wrapper untuk zoom gambar --}}
<div id="showOrderModalZoomWrapper-admin"
    class="fixed inset-0 z-[9999] items-center justify-center hidden bg-black bg-opacity-80">

    {{-- TOMBOL CLOSE BARU --}}
    <button class="absolute text-5xl text-white transition-transform duration-200 ease-in-out top-5 right-8 hover:text-gray-300 hover:scale-110">&times;</button>

    <img id="showOrderModalZoomImg-admin" src="" alt="Bukti Pembayaran"
        class="max-w-[90%] max-h-[90%] border-4 border-white rounded shadow-lg">
</div>

{{-- TEMPLATE UNTUK ITEM PRODUK --}}
<template id="orderItemTemplate-admin">
    <div class="flex items-start p-3 space-x-4 border rounded-lg bg-gray-50 dark:border-gray-200 dark:bg-gray-700/50 dark:border-gray-600">
        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mt-1 bg-gray-200 rounded-lg dark:bg-gray-600">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
        <div class="flex-grow">
            <p class="font-bold text-gray-900 dark:text-white" data-role="name-variant"></p>
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                <div data-role="return-info">
                    <span>Awal: <b class="font-medium text-gray-800 dark:text-gray-100" data-role="initial-qty"></b></span> |
                    <span class="font-medium text-red-600 dark:text-red-400">Retur: <b data-role="returned-qty"></b></span> |
                    <span class="font-medium text-green-600 dark:text-green-400">Sisa: <b data-role="remaining-qty"></b></span>
                </div>
                <div data-role="normal-info">
                    Jumlah: <b class="font-medium text-gray-800 dark:text-gray-100" data-role="quantity"></b>
                </div>
            </div>
            <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200" data-role="price-line"></p>
        </div>
    </div>
</template>
