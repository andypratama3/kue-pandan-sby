@props(['product', 'categories'])

<x-modal-custom id="edit-product-modal-{{ $product->id }}" title="Edit Produk" size="3xl">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="p-4 md:p-5">
            {{-- Area utama yang bisa di-scroll --}}
            <div class.blade.php"overflow-y-auto max-h-[70vh] p-1">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Nama Produk, Kategori, Tag --}}
                    <div class="col-span-2"><label for="name-{{ $product->id }}" class="block mb-2 text-sm font-medium">Nama Produk</label><input type="text" name="name" value="{{ old('name', $product->name) }}" id="name-{{ $product->id }}" class="w-full p-2.5 text-sm bg-gray-50 rounded-lg border border-gray-300" required></div>
                    <div class="col-span-2 sm:col-span-1"><label for="category-{{ $product->id }}" class="block mb-2 text-sm font-medium">Kategori</label><select id="category-{{ $product->id }}" name="category_id" class="w-full p-2.5 text-sm bg-gray-50 rounded-lg border border-gray-300">@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                    <div class="col-span-2 sm:col-span-1"><label for="tag-{{ $product->id }}" class="block mb-2 text-sm font-medium">Tag (Opsional)</label><input type="text" name="tag" value="{{ old('tag', $product->tag) }}" id="tag-{{ $product->id }}" class="w-full p-2.5 text-sm bg-gray-50 rounded-lg border border-gray-300" placeholder="Ala Carte"></div>

                    {{-- Deskripsi --}}
                    <div class="col-span-2">
                        <label for="description-{{ $product->id }}" class="block mb-2 text-sm font-medium">Deskripsi</label>
                        <textarea id="description-{{ $product->id }}" name="description" rows="5" class="block p-2.5 w-full text-sm bg-gray-50 rounded-lg border border-gray-300 max-h-48" required>{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="col-span-2">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {{-- Kolom Gambar --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium">Gambar Produk</label>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ Storage::url($product->image_path) }}" class="object-cover w-20 h-20 rounded-lg" alt="{{ $product->name }}">
                                    {{-- <img src="{{ asset($product->image_path) }}" class="object-cover w-20 h-20 rounded-lg" alt="{{ $product->name }}"> --}}
                                    <input type="file" name="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti gambar.</p>
                            </div>
                            {{-- Kolom Status Aktif --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium">Status Produk</label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $product->is_active))>
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="text-sm font-medium ms-3">Produk Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- Varian Produk --}}
                    <div class="col-span-2">
                        <h4 class="mb-2 font-semibold">Varian Produk</h4>
                        <div id="edit-variants-container-{{ $product->id }}" class="p-2 space-y-2 overflow-y-auto bg-gray-100 border border-gray-300 rounded-lg max-h-40 dark:bg-gray-700 dark:border-gray-600">
                            @if(old('variants'))
                                @foreach (old('variants') as $index => $variant)
                                    <div class="flex items-center space-x-2 variant-item">
                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                                        <input type="text" name="variants[{{ $index }}][name]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Nama Varian" value="{{ $variant['name'] }}" required>
                                        <input type="number" name="variants[{{ $index }}][price]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Harga" value="{{ $variant['price'] }}" required>
                                        <button type="button" class="px-3 py-2 text-white bg-red-600 rounded-lg remove-variant-btn">&times;</button>
                                    </div>
                                @endforeach
                            @else
                                @foreach ($product->variants->where('is_active', true) as $index => $variant)
                                    <div class="flex items-center space-x-2 variant-item">
                                        <input type="hidden" name="variants[{{ $variant->id }}][id]" value="{{ $variant->id }}">
                                        <input type="text" name="variants[{{ $variant->id }}][name]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Nama Varian" value="{{ $variant->name }}" required>
                                        <input type="number" name="variants[{{ $variant->id }}][price]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Harga" value="{{ $variant->price }}" required>
                                        <button type="button" class="px-3 py-2 text-white bg-red-600 rounded-lg remove-variant-btn">&times;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="w-full mt-2 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 add-edit-variant-btn" data-product-id="{{ $product->id }}">Tambah Varian</button>
                    </div>
                </div>
            </div>
            {{-- Footer Modal --}}
            <div class="flex items-center justify-end pt-4 mt-4 space-x-2 border-t border-gray-200 dark:border-gray-600">
                <button type="submit" class="text-white bg-brand hover:bg-brand-deep font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Perubahan</button>
                <button type="button" class="js-close-modal-btn py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</button>
            </div>
        </div>
    </form>
</x-modal-custom>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi ini hanya akan berjalan jika menemukan modal 'edit-product-modal-...'
        const setupEditVariantLogic = (productId) => {
            const modal = document.getElementById(`edit-product-modal-${productId}`);
            if (!modal) return;

            const addBtn = modal.querySelector('.add-edit-variant-btn');
            const container = modal.querySelector(`#edit-variants-container-${productId}`);

            if (!addBtn || !container) return;

            addBtn.addEventListener('click', () => {
                const index = Date.now();
                const newVariantHtml = `
                    <div class="flex items-center mb-2 space-x-2 variant-item">
                        <input type="hidden" name="variants[${index}][id]" value="">
                        <input type="text" name="variants[${index}][name]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Nama Varian" required>
                        <input type="number" name="variants[${index}][price]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Harga" required>
                        <button type="button" class="px-3 py-2 text-white bg-red-600 rounded-lg remove-variant-btn" title="Hapus Varian">&times;</button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', newVariantHtml);
            });

            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-variant-btn')) {
                    e.target.closest('.variant-item').remove();
                }
            });
        };

        // Inisialisasi logika untuk setiap tombol edit yang ada di halaman
        document.querySelectorAll('.add-edit-variant-btn').forEach(button => {
            const productId = button.dataset.productId;
            if(productId) {
                 // Menggunakan setTimeout untuk memastikan modal sudah ada di DOM saat dipanggil
                setTimeout(() => setupEditVariantLogic(productId), 100);
            }
        });
    });
</script>
