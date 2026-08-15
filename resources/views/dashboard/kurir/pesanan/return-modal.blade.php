{{-- Modal for Product Return Request --}}
<x-modal-custom id="returnProductModal" title="Pilih Produk untuk Pengembalian" size="xl">
    {{-- Loader --}}
    <div id="returnModalLoader" class="text-center">
        <svg class="w-8 h-8 mx-auto text-blue-600 dark:text-blue-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">Memuat Produk...</p>
    </div>

    {{-- Modal Content --}}
    <div id="returnModalContent" class="hidden">
        <form id="returnProductForm" class="flex flex-col h-full">
            <input type="hidden" id="returnOrderId" name="order_id">

            {{-- Product list container --}}
            <div class="max-h-[60vh] overflow-y-auto pr-2 sm:pr-4">
                {{-- Desktop View (Table) --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="w-[5%] px-4 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase dark:text-white">
                                    No
                                </th>
                                <th scope="col"
                                    class="w-[40%] px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase dark:text-white">
                                    <i class="fas fa-box mr-1"></i> Produk
                                </th>
                                <th scope="col"
                                    class="w-[35%] px-6 py-3 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-white">
                                    Jumlah Diretur
                                </th>
                                <th scope="col"
                                    class="w-[10%] px-6 py-3 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="return-product-list-desktop"
                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            {{-- Desktop rows injected here by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View (Cards) --}}
                <div id="return-product-list-mobile" class="space-y-3 md:hidden">
                    {{-- Mobile cards injected here by JS --}}
                </div>

            </div>
            {{-- return reason text area --}}
            <div class="pt-4">
                <label for="return_reason" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    📝 Alasan Pengembalian
                </label>

                <textarea id="return_reason" name="return_reason" rows="4" required
                    placeholder="Tuliskan alasan pengembalian produk (contoh: produk rusak, tidak sesuai pesanan, dll)"
                    class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl resize-none
               focus:ring-2 focus:ring-brand focus:border-brand
               dark:bg-gray-800 dark:border-gray-600 dark:text-white
               dark:focus:ring-brand dark:focus:border-brand"></textarea>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    * Alasan pengembalian wajib diisi
                </p>
            </div>
            {{-- return reason text area --}}

            {{-- Action Buttons --}}
            <div class="flex justify-end pt-4 space-x-3 border-t border-gray-200 dark:border-gray-600">
                {{-- <button type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    data-modal-hide="returnProductModal">
                    Batal
                </button> --}}
                <button type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg js-close-modal-btn hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Batal
                </button>
                <button type="button"
                    onclick="handleReturnRequestSubmit(document.getElementById('returnOrderId').value)"
                    id="submitReturnRequestButton"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light disabled:opacity-50 dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep">
                    <span id="submitReturnRequestButtonText">Kirim Pengajuan</span>
                    <svg id="submitReturnRequestButtonSpinner" class="hidden w-4 h-4 ml-2 text-white animate-spin"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 28.0001 72.5987 9.68022 50 9.68022C27.4013 9.68022 9.08144 28.0001 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0492C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-modal-custom>
