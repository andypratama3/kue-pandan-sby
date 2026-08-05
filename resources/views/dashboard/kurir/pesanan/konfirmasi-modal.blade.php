{{-- Menggunakan komponen modal-custom yang sudah ada --}}
<x-modal-custom id="konfirmasiModal" title="Konfirmasi Detail Pesanan" toggle="konfirmasiModal" size="4xl">
    {{-- 
        Struktur diubah menjadi grid untuk tampilan yang lebih rapi di layar besar.
        Kolom kiri untuk info pelanggan & pembayaran, kolom kanan untuk daftar produk.
    --}}
    <div id="detail-pesanan-modal" class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        {{-- KOLOM KIRI --}}
        <div class="space-y-6 lg:col-span-3">
            {{-- Data Pelanggan --}}
            <div class="p-4 border rounded-lg bg-slate-50 dark:bg-slate-800 dark:border-slate-700">
                <h4 class="flex items-center pb-2 mb-3 text-lg font-semibold text-gray-800 border-b dark:text-white dark:border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Data Pelanggan
                </h4>
                <div class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-2">
                    {{-- Nama --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
                        <p id="modal-customer-name" class="font-semibold text-gray-900 dark:text-white">-</p>
                    </div>
                    {{-- No. HP --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">No. HP</p>
                        <p id="modal-customer-phone" class="font-semibold text-gray-900 dark:text-white">-</p>
                    </div>
                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Alamat</p>
                        <p id="modal-customer-address" class="font-semibold text-gray-900 dark:text-white">-</p>
                    </div>
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="p-4 border rounded-lg bg-slate-50 dark:bg-slate-800 dark:border-slate-700">
                <h4 class="flex items-center pb-2 mb-3 text-lg font-semibold text-gray-800 border-b dark:text-white dark:border-slate-700">
                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    Detail Pembayaran
                </h4>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                    <p id="modal-payment-method" class="font-semibold text-gray-900 dark:text-white">-</p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="p-4 border rounded-lg lg:col-span-2 bg-slate-50 dark:bg-slate-800 dark:border-slate-700">
            <h4 class="flex items-center pb-2 mb-3 text-lg font-semibold text-gray-800 border-b dark:text-white dark:border-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v2"/><path d="M21 16V8l-8 5-8-5v8l8 5 8-5Z"/><path d="m7.5 10.5 9 6"/><path d="M12 22V12"/></svg>
                Rincian Produk
            </h4>
            <div id="modal-product-list" class="pr-2 space-y-4 overflow-y-auto max-h-80">
                {{-- Daftar produk akan diisi oleh JavaScript --}}                               
            </div>
        </div>

        {{-- BAGIAN BAWAH (Total & Tombol) --}}
        <div class="pt-2 mt-2 lg:col-span-5  border-t dark:border-slate-700">
            {{-- Total Keseluruhan --}}
            <div class="flex items-baseline justify-end mb-2 text-right">
                <span class="text-lg font-medium text-gray-800 dark:text-white">Total :</span>
                <span id="modal-total-amount" class="ml-4 text-2xl font-bold text-brand-deep dark:text-brand">-</span>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end space-x-3 border-t dark:border-slate-700">
                <button type="button" class="mt-4 px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg js-close-modal-btn hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600">
                    Batal
                </button>
                <button type="button" id="submit-order-button" onclick="submitOrder()" class=" mt-4 inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-deep focus:outline-none focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-deep">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M20 6 9 17l-5-5"/></svg>
                    Konfirmasi Pesanan
                </button>
            </div>
        </div>
    </div>
</x-modal-custom>
