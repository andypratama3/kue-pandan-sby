@props(['categories'])

<x-modal-custom id="create-product-modal" title="Tambah Produk Baru" size="3xl">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="p-4 md:p-5">
            {{-- Area utama yang bisa di-scroll --}}
            <div class="overflow-y-auto max-h-[70vh] p-1">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Nama Produk --}}
                    <div class="col-span-2">
                        <label for="create-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Produk</label>
                        <input type="text" name="name" id="create-name" value="{{ old('name') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            placeholder="Nama Produk (e.g., Kue Ijo)" required>
                    </div>
                    {{-- Kategori --}}
                    <div class="col-span-2 sm:col-span-1">
                        <label for="create-category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                        <select id="create-category" name="category_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Tag --}}
                    <div class="col-span-2 sm:col-span-1">
                        <label for="create-tag" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag (Opsional)</label>
                        <input type="text" name="tag" id="create-tag" value="{{ old('tag') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            placeholder="Tag (e.g., Ala Carte)">
                    </div>
                    {{-- Deskripsi --}}
                    <div class="col-span-2">
                        <label for="create-description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi</label>
                        <textarea id="create-description" name="description" rows="5"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 max-h-48"
                            placeholder="Deskripsi singkat produk..." required>{{ old('description') }}</textarea>
                    </div>
                    {{-- Gambar --}}
                    <div class="col-span-2">
                        <label for="create-image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gambar Produk</label>
                        <input type="file" name="image" id="create-image"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            required>
                    </div>
                    {{-- Varian Produk --}}
                    <div class="col-span-2">
                        <h4 class="mb-2 font-semibold">Varian Produk</h4>
                        <div id="create-variants-container" class="p-2 space-y-2 overflow-y-auto bg-gray-100 border border-gray-300 rounded-lg max-h-40 dark:bg-gray-700 dark:border-gray-600">
                            @if (old('variants'))
                                @foreach (old('variants') as $index => $variant)
                                    <div class="flex items-center space-x-2 variant-item">
                                        <input type="text" name="variants[{{ $index }}][name]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Nama Varian" value="{{ $variant['name'] }}" required>
                                        <input type="number" name="variants[{{ $index }}][price]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Harga" value="{{ $variant['price'] }}" required>
                                        <button type="button" class="px-3 py-2 text-white bg-red-600 rounded-lg remove-variant-btn">&times;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="add-create-variant-btn" class="w-full mt-2 text-white bg-brand hover:bg-brand-deep font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah Varian</button>
                    </div>
                </div>
            </div>
            {{-- Footer Modal --}}
            <div class="flex items-center justify-end pt-4 mt-4 space-x-2 border-t border-gray-200 dark:border-gray-600">
                <button type="submit" class="text-white bg-brand hover:bg-brand-deep font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Produk</button>
                <button type="button" class="js-close-modal-btn py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</button>
            </div>
        </div>
    </form>
</x-modal-custom>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi ini hanya akan berjalan jika menemukan modal 'create-product-modal'
        const setupCreateVariantLogic = () => {
            const modal = document.getElementById('create-product-modal');
            if (!modal) return;

            const addCreateBtn = document.getElementById('add-create-variant-btn');
            const createContainer = document.getElementById('create-variants-container');

            if (!addCreateBtn || !createContainer) return;

            const addCreateVariant = () => {
                const index = Date.now(); // Gunakan timestamp untuk index unik
                const newVariantHtml = `
                    <div class="flex items-center mb-2 space-x-2 variant-item">
                        <input type="text" name="variants[${index}][name]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Nama Varian (e.g., Isi 3)" required>
                        <input type="number" name="variants[${index}][price]" class="w-full p-2 text-sm border-gray-300 rounded-lg" placeholder="Harga (e.g., 9000)" required>
                        <button type="button" class="px-3 py-2 text-white bg-red-600 rounded-lg remove-variant-btn" title="Hapus Varian">&times;</button>
                    </div>
                `;
                createContainer.insertAdjacentHTML('beforeend', newVariantHtml);
            };

            addCreateBtn.addEventListener('click', addCreateVariant);

            createContainer.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-variant-btn')) {
                    e.target.closest('.variant-item').remove();
                }
            });

            // Otomatis tambahkan satu baris varian jika belum ada (termasuk saat form gagal validasi dan tidak ada old('variants'))
            if (createContainer.children.length === 0) {
                 addCreateVariant();
            }
        };

        // Menggunakan setTimeout untuk memastikan elemen modal sudah ada di DOM
        setTimeout(setupCreateVariantLogic, 100);
    });
</script>