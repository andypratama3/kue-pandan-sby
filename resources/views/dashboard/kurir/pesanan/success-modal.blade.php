{{-- Modal Sukses menggunakan x-modal-custom agar konsisten dengan custom-modal.js --}}
<x-modal-custom id="successModal" title="Sukses!" size="sm">
    <div class="flex flex-col items-center justify-center">
        <div class="mb-4 text-5xl text-brand">✔</div>
        <p id="success-message" class="mb-2 text-gray-700 dark:text-gray-200 text-lg font-semibold">Pesanan berhasil disimpan.</p>
        <p id="success-invoice" class="p-2 mb-4 text-sm font-semibold text-gray-800 bg-gray-100 rounded dark:bg-gray-800 dark:text-gray-200"></p>
    </div>
    <div class="flex justify-end mt-4">
        <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-brand rounded-lg js-close-modal-btn hover:bg-brand-deep focus:outline-none focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-deep">
            Tutup
        </button>
    </div>
</x-modal-custom>
