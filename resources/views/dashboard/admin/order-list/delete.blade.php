<x-modal-custom id="deleteConfirmModal" title="Konfirmasi Hapus Pesanan" size="md">
    <div class="p-4 text-center md:p-5">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
            Apakah Anda yakin ingin menghapus pesanan
            <br>
            <span id="deleteInvoiceNumber" class="font-bold"></span>?
            <br>
            <span class="text-sm">Aksi ini tidak dapat dibatalkan.</span>
        </h3>
    </div>
    <x-slot name="footer">
        <div
            class="flex items-center justify-center p-4 space-x-3 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg js-close-modal-btn hover:bg-gray-300">Batal</button>
            {{-- Tombol ini akan diberi event listener oleh JavaScript --}}
            <button id="btnConfirmDelete" type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                Ya, Hapus
            </button>
        </div>
    </x-slot>
</x-modal-custom>
