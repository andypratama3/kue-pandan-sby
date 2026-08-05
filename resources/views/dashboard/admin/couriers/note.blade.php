@props(['courier'])

<x-modal-custom id="note-courier-modal-{{ $courier->id }}" title="Catatan untuk {{ $courier->name }}" size="xl">
    <form action="{{ route('admin.couriers.updateNote', $courier) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label for="note-{{ $courier->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                <textarea id="note-{{ $courier->id }}" name="note" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500"
                    placeholder="Tambahkan catatan untuk kurir ini...">{{ $courier->note }}</textarea>
            </div>
        </div>
        <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
            <button type="submit"
                class="text-white inline-flex items-center bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600">
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