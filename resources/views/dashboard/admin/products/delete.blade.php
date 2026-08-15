@props(['product'])

<x-modal-custom id="delete-product-modal-{{ $product->id }}" title="Konfirmasi Hapus" size="md">
    <div class="p-4 text-center md:p-5">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Anda yakin ingin menghapus produk <br><span class="font-bold">{{ $product->name }}</span>?</h3>
        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">Ya, saya yakin</button>
        </form>
        {{-- ========================= PERBAIKAN TOMBOL BATAL ========================= --}}
        <button type="button" class="js-close-modal-btn py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            Tidak, batal
        </button>
    </div>
</x-modal-custom>