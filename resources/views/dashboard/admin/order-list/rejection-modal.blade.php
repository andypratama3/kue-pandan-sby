<x-modal-custom id="rejectionNoteModal" title="Tulis Alasan Penolakan" size="lg">
    {{-- Form ini tidak lagi memerlukan method atau action di sini --}}
    <form id="rejectionForm" action="" method="POST">
        @csrf
        @method('POST')
        <div class="p-4 md:p-5">
            <label for="rejection_note" class="sr-only">Alasan Penolakan</label>
            <textarea id="rejection_note" name="rejection_note" rows="4"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-brand focus:border-brand dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="Tuliskan alasan mengapa verifikasi pesanan ini ditolak..."></textarea> {{-- Atribut required dihapus --}}
        </div>
        <x-slot name="footer">
            <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
                <button type="button" class="px-5 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg js-close-modal-btn hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Batal
                </button>
                {{-- PERUBAHAN UTAMA: Ganti type menjadi 'button' dan tambahkan onclick --}}
                <button type="button" onclick="submitRejectionForm()"
                    class="px-5 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    Tolak Verifikasi
                </button>
            </div>
        </x-slot>
    </form>
</x-modal-custom>
