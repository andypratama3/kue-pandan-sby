<x-modal-custom id="verifyOrderModal" title="Verifikasi Rincian Pesanan" size="4xl">
    {{-- Loader saat data dimuat --}}
    <div id="verifyModalLoader" class="p-8 text-center">
        <svg class="w-8 h-8 mx-auto text-brand-deep dark:text-brand-light animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">Memuat Rincian Pesanan...</p>
    </div>

    {{-- Konten utama modal, awalnya disembunyikan --}}
    <div id="verifyModalContent" class="hidden max-h-[75vh] overflow-y-auto">
        <div id="verifyModalGrid" class="grid grid-cols-1 gap-6 p-2 lg:grid-cols-3">
            {{-- KOLOM KIRI --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- KARTU DETAIL PESANAN --}}
                <div
                    class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-5 text-xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Invoice</label>
                            <p id="verifyModalInvoiceNumber"
                                class="font-mono text-lg font-semibold text-gray-800 dark:text-gray-200"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</label>
                            <p id="verifyModalCustomerName"
                                class="text-lg font-semibold text-gray-800 dark:text-gray-200"></p>
                            <p id="verifyModalCompanyName" class="hidden text-sm text-gray-600 dark:text-gray-300"></p>
                            <p id="verifyModalCustomerPhone" class="text-sm text-gray-600 dark:text-gray-300"></p>
                            <p id="verifyModalCustomerAddress" class="text-sm text-gray-600 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Detail
                                Pembayaran</label>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Metode: <span
                                    id="verifyModalPaymentMethod" class="font-semibold"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Tgl. Pesan: <span
                                    id="verifyModalOrderCreatedAt" class="font-semibold"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Tgl. Lunas: <span
                                    id="verifyModalOrderPaidAt" class="font-semibold"></span></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kurir Bertugas</label>
                            <p id="verifyModalCourierName"
                                class="font-semibold text-gray-800 text-base dark:text-gray-200"></p>
                        </div>
                    </div>
                    {{-- Bagian Catatan Pesanan --}}
                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan dari Kurir</label>
                        <div
                            class="text-center p-3 mt-1 text-sm text-gray-700 bg-yellow-100 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:text-white dark:border-yellow-800/50 min-h-[50px]">
                            <p id="verifyModalOrderNote">"Tidak ada catatan."</p>
                        </div>
                    </div>
                </div>

                {{-- KARTU PRODUK DIPESAN & DIRETUR --}}
                <div
                    class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white"><i class="fas fa-box mr-2"></i> Produk Dipesan</h3>
                    <div id="verifyModalProductDetails" class="space-y-3"></div>

                    {{-- Produk Retur (jika ada) --}}
                    <div id="returnedProductsSection"
                        class="hidden pt-4 mt-4 border-t border-red-300 dark:border-red-700">
                        <h4 class="mb-2 text-lg font-semibold text-red-800 dark:text-red-400"><i class="fas fa-undo mr-2"></i> Produk yang Diretur
                        </h4>
                        <div id="verifyModalReturnedProducts" class="space-y-2">
                            {{-- Daftar produk retur akan diisi oleh JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="space-y-6 lg:col-span-1">
                {{-- KARTU RINCIAN TOTAL --}}
                <div
                    class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tagihan</label>
                    <p id="verifyModalTotalAmount" class="text-2xl font-extrabold text-brand-deep dark:text-brand tabular-nums">
                    </p>
                </div>

                {{-- KARTU BUKTI PEMBAYARAN --}}
                {{-- <div
                    class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h4 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">✅ Bukti Pembayaran</h4>
                    <div id="verifyModalPaymentProof">

                    </div>
                </div> --}}
                <div
                    class="p-5 bg-white border border-gray-200 shadow-md dark:bg-gray-800 rounded-xl dark:border-gray-700">
                    <h4 id="verifyModalProofTitle" class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Bukti
                        Unggahan</h4>

                    <div id="verifyModalProofContainer">
                        <div id="verifyModalProofImageContainer" class="hidden text-center">
                            <img id="verifyModalProofImage" src="" alt="Bukti Unggahan"
                                class="object-cover w-full h-auto mx-auto border-2 border-gray-200 rounded-lg shadow-md cursor-pointer dark:border-gray-600 max-h-48">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Klik gambar untuk memperbesar.</p>
                        </div>
                        <div id="verifyModalNoProof"
                            class="hidden p-4 text-center bg-gray-100 rounded-lg dark:bg-gray-700">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Tidak ada bukti yang
                                diunggah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Slot untuk footer dengan tombol aksi --}}
    <x-slot name="footer">
        <div
            class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b md:p-5 dark:border-slate-700">
            <button id="btnOpenRejectModal" type="button"
                class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                Tolak
            </button>
            <button id="btnVerifyOrder" type="button"
                class="px-5 py-2.5 text-sm font-medium text-center text-white bg-brand-deep rounded-lg hover:bg-brand focus:ring-4 focus:outline-none focus:ring-brand-light dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-light">
                Verifikasi Pesanan
            </button>
        </div>
    </x-slot>
</x-modal-custom>

{{-- Wrapper untuk zoom gambar, ditempatkan di luar modal utama --}}
<div id="verifyModalZoomWrapper"
    class="fixed inset-0 z-[9999] items-center justify-center hidden bg-black bg-opacity-80">

    {{-- TOMBOL CLOSE BARU --}}
    <button id="verifyModalZoomCloseBtn"
        class="absolute text-5xl text-white transition-transform duration-200 ease-in-out top-5 right-8 hover:text-gray-300 hover:scale-110">&times;</button>

    <img id="verifyModalZoomImg" src="" alt="Bukti Pembayaran"
        class="max-w-[90%] max-h-[90%] border-4 border-white rounded shadow-lg">
</div>
