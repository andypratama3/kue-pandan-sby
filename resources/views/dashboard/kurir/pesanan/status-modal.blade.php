<x-modal-custom id="statusStepperModal" title="Ubah Status Pesanan" size="xl">
    {{-- Modal body --}}
    <div class="p-6 pt-0 space-y-4">
        <div id="statusStepperModalLoader" class="text-center">
            {{-- Loader akan diinjeksi oleh JavaScript --}}
        </div>

        <div id="statusStepperModalContent" class="hidden">
            {{-- Menampilkan Nomor Invoice dan Nama Pelanggan di atas stepper --}}
            <div class="mb-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400"><span id="modalStatusInvoiceNumber" class="font-semibold text-gray-800 dark:text-white"></span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400"><span id="modalStatusCustomerName" class="font-semibold text-gray-800 dark:text-white"></span></p>
            </div>

            <div class="md:flex md:items-center md:justify-between md:text-center md:mb-6">

                {{-- Step Diambil --}}
                <div class="relative flex items-start pb-8 md:flex-1 md:flex-col md:items-center md:pb-0">
                    {{-- Garis Vertikal (Hanya Mobile) --}}
                    <div class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600 md:hidden" id="line-diambil-mobile"></div>
                    {{-- Ikon --}}
                    <div class="relative z-10 flex items-center justify-center w-10 h-10 text-gray-500 transition-all duration-300 bg-white border-2 border-gray-300 rounded-full dark:border-gray-600 dark:text-gray-400 dark:bg-gray-700" id="step-diambil-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    {{-- Detail Teks --}}
                    <div class="ml-4 md:ml-0">
                        <p class="mt-0 text-sm font-medium text-gray-700 md:mt-2 dark:text-white">Diambil</p>
                        <p id="pickedUpAt" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>

                {{-- Garis Horizontal (Hanya Desktop) --}}
                <div id="line-diantar" class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-600 transition-all duration-300 hidden md:block"></div>

                {{-- Step Diantar --}}
                <div class="relative flex items-start pb-8 md:flex-1 md:flex-col md:items-center md:pb-0">
                    {{-- Garis Vertikal (Hanya Mobile) --}}
                    <div class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600 md:hidden" id="line-diantar-mobile"></div>
                    {{-- Ikon --}}
                    <div class="relative z-10 flex items-center justify-center w-10 h-10 text-gray-500 transition-all duration-300 bg-white border-2 border-gray-300 rounded-full dark:border-gray-600 dark:text-gray-400 dark:bg-gray-700" id="step-diantar-icon">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    {{-- Detail Teks --}}
                    <div class="ml-4 md:ml-0">
                        <p class="mt-0 text-sm font-medium text-gray-700 md:mt-2 dark:text-white">Diantar</p>
                        <p id="deliveredAt" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>

                {{-- Garis Horizontal (Hanya Desktop) --}}
                <div id="line-diterima_pembeli" class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-600 transition-all duration-300 hidden md:block"></div>

                {{-- Step Diterima Pembeli --}}
                <div class="relative flex items-start md:flex-1 md:flex-col md:items-center">
                    {{-- Ikon --}}
                    <div class="relative z-10 flex items-center justify-center w-10 h-10 text-gray-500 transition-all duration-300 bg-white border-2 border-gray-300 rounded-full dark:border-gray-600 dark:text-gray-400 dark:bg-gray-700" id="step-diterima_pembeli-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    {{-- Detail Teks --}}
                    <div class="ml-4 md:ml-0">
                        <p class="mt-0 text-sm font-medium text-gray-700 md:mt-2 dark:text-white">Diterima Pembeli</p>
                        <p id="receivedByBuyerAt" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>
            </div>

            <p class="pt-4 text-center text-gray-600 dark:text-gray-400" id="currentOrderStatusText"></p>

            <button id="updateStatusButton" type="button"
                class="w-full px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep transition-colors duration-200"
                data-order-id="" data-next-status="">
                <span id="updateStatusButtonText">Memuat...</span>
                <svg id="updateStatusButtonSpinner" class="hidden w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Path untuk spinner --}}
                </svg>
            </button>
        </div>
    </div>
</x-modal-custom>
