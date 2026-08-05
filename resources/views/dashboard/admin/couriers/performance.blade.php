<x-modal-custom id="performance-modal-{{ $courier->id }}" title="Performa Pesanan - {{ $courier->name }}" size="4xl">
    {{-- Loader ditampilkan saat data dimuat --}}
    <div id="performance-loader-{{ $courier->id }}" class="py-10 text-center">
        <svg class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- SVG path --}}
        </svg>
        <p class="mt-2 text-sm text-gray-500">Memuat data performa...</p>
    </div>

    {{-- Konten utama modal, awalnya disembunyikan --}}
    <div id="performance-content-{{ $courier->id }}" class="hidden">
        {{-- Header Chart: Judul, Total, dan Filter --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
            <div>
                <p id="date-range-{{ $courier->id }}" class="text-sm text-gray-500 dark:text-gray-400"></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    <span class="w-3 h-3 mr-2 bg-blue-500 rounded-full"></span>
                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total</span>
                    <span id="total-orders-{{ $courier->id }}" class="text-sm font-bold text-gray-800 dark:text-white"></span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 mr-2 bg-green-500 rounded-full"></span>
                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Selesai</span>
                    <span id="total-completed-{{ $courier->id }}" class="text-sm font-bold text-gray-800 dark:text-white"></span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 mr-2 bg-red-500 rounded-full"></span>
                    <span class="mr-1 text-sm font-medium text-gray-600 dark:text-gray-300">Return</span>
                    <span id="total-returned-{{ $courier->id }}" class="text-sm font-bold text-gray-800 dark:text-white"></span>
                </div>
                <div class="relative">
                    <button id="chartFilterButton-{{ $courier->id }}" type="button" class="text-gray-500 js-dropdown-toggle hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none" data-target-dropdown="chartFilterDropdown-{{ $courier->id }}">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div id="chartFilterDropdown-{{ $courier->id }}" class="absolute right-0 z-20 hidden w-40 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-slate-700 js-dropdown-menu dark:border-slate-600">
                        <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 rounded-t-lg js-performance-filter dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600" data-courier-id="{{ $courier->id }}" data-filter="last_7_days">7 Hari</a>
                        <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 js-performance-filter dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600" data-courier-id="{{ $courier->id }}" data-filter="daily">Harian</a>
                        <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 js-performance-filter dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600" data-courier-id="{{ $courier->id }}" data-filter="weekly">Mingguan</a>
                        <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 rounded-b-lg js-performance-filter dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-600" data-courier-id="{{ $courier->id }}" data-filter="monthly">Bulanan</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Canvas untuk merender grafik --}}
        <div class="flex-auto pt-0">
            <canvas id="performanceChart-{{ $courier->id }}" class="h-[300px]"></canvas>
        </div>
    </div>
</x-modal-custom>
