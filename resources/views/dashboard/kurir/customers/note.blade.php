@props(['customer'])
<x-modal-custom id="note-customer-modal-{{ $customer->id }}" title="Catatan untuk {{ $customer->name }}" size="xl">
    <form action="{{ route('kurir.customers.updateNote', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label for="kurir-note-update-{{ $customer->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                <textarea id="kurir-note-update-{{ $customer->id }}" name="note" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-brand focus:border-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    placeholder="Tambahkan catatan untuk customer ini...">{{ old('note', $customer->note) }}</textarea>
            </div>
        </div>
        <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
            <button type="submit"
                class="text-white inline-flex items-center bg-brand hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep">
                <i class="fas fa-save me-1"></i>
                Simpan Catatan
            </button>
            <button type="button"
                class="js-close-modal-btn py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Batal
            </button>
        </div>
    </form>
</x-modal-custom>
