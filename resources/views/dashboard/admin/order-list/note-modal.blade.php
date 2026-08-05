{{-- resources/views/dashboard/admin/order-list/note-modal.blade.php --}}
<x-modal-custom id="viewNoteModal" title="Catatan Pesanan dari Kurir" size="lg">
    {{-- Konten utama modal --}}
    <div class="p-4 space-y-4 md:p-5">
        <p id="fullOrderNote" class="text-base leading-relaxed text-gray-700 whitespace-pre-wrap dark:text-gray-300">
            {{-- Isi catatan akan diinjeksi oleh JavaScript --}}
        </p>
    </div>

    {{-- Slot untuk footer dengan tombol tutup --}}
    <x-slot name="footer">
        <div
            class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
            <button type="button"
                class="px-5 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg js-close-modal-btn hover:bg-gray-100 focus:z-10 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Tutup
            </button>
        </div>
    </x-slot>
</x-modal-custom>
