<x-modal-custom id="create-customer-modal" title="Tambah Customer Baru" size="3xl">
    <form class="p-4 md:p-5" action="{{ route('admin.customers.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-2">
            {{-- Nama Perusahaan --}}
            <div>
                <label for="create-company_name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Toko <span
                        class="text-red-500">*</span></label>
                <input type="text" name="company_name" id="create-company_name" value="{{ old('company_name') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Contoh: Toko Kue Pandan Asli" required>
            </div>
            {{-- Nama Customer --}}
            <div>
                <label for="create-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                    Customer <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="create-name" value="{{ old('name') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan nama customer" required>
            </div>
            {{-- Kategori Customer --}}
            <div>
                <label for="create-customer_category_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori <span
                        class="text-red-500">*</span></label>
                <select name="customer_category_id" id="create-customer_category_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($customerCategories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('customer_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Ditambahkan Oleh (Kurir) --}}
            <div>
                <label for="create-added_by_user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ditambahkan Oleh (Kurir) <span class="text-red-500">*</span></label>
                <select name="added_by_user_id" id="create-added_by_user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                    <option value="{{ Auth::id() }}">Saya Sendiri (Admin)</option>
                    @foreach ($couriers as $courier)
                        <option value="{{ $courier->id }}" {{ old('added_by_user_id') == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Alamat --}}
            <div class="sm:col-span-2">
                <label for="create-address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat
                    <span class="text-red-500">*</span></label>
                <textarea name="address" id="create-address" rows="2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan alamat lengkap" required>{{ old('address') }}</textarea>
            </div>
            {{-- Patokan Tempat --}}
            <div class="sm:col-span-2">
                <label for="create-landmark"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Patokan Tempat
                    (Opsional)</label>
                <input type="text" name="landmark" id="create-landmark" value="{{ old('landmark') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Contoh: Sebelah Indomaret, depan gang">
            </div>
            {{-- Nomor HP --}}
            <div>
                <label for="kurir-create-phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No.
                    HP <span class="text-red-500">*</span></label>
                <div class="flex">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">+62</span>
                    <input type="tel" name="phone" id="kurir-create-phone" value="{{ old('phone') }}"
                        class="rounded-none rounded-r-lg bg-gray-50 border text-gray-900 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5"
                        placeholder="8123456789" required inputmode="numeric" pattern="[0-9]*"
                        oninput="this.value = this.value.replace(/\D/g, '')">
                </div>
            </div>
            {{-- Jenis Pembayaran --}}
            <div>
                <label for="create-payment_type"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Pembayaran <span
                        class="text-red-500">*</span></label>
                <input type="text" name="payment_type" id="create-payment_type" value="{{ old('payment_type') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Contoh: Harian, Mingguan" required>
            </div>
            {{-- Jam Buka --}}
            <div class="sm:col-span-2">
                <label for="create-opening_hours"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jam Buka <span
                        class="text-red-500">*</span></label>
                <input type="text" name="opening_hours" id="create-opening_hours" value="{{ old('opening_hours') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Contoh: 08:00 - 17:00" required>
            </div>
            {{-- Note --}}
            <div class="sm:col-span-2">
                <label for="create-note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Note
                    (Opsional)</label>
                <textarea name="note" id="create-note" rows="3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Catatan tambahan...">{{ old('note') }}</textarea>
            </div>
        </div>
        <button type="submit"
            class="text-white inline-flex items-center bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
            <i class="fas fa-save me-1"></i>
            Simpan Customer
        </button>
    </form>
</x-modal-custom>
