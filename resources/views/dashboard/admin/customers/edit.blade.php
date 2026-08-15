@props(['customer', 'customerCategories'])

<x-modal-custom id="edit-customer-modal-{{ $customer->id }}" title="Edit Customer" size="3xl">
    <form class="p-4 md:p-5" action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-2">
            {{-- Nama Perusahaan --}}
            <div>
                <label for="edit-company_name-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Perusahaan <span
                        class="text-red-500">*</span></label>
                <input type="text" name="company_name" id="edit-company_name-{{ $customer->id }}"
                    value="{{ old('company_name', $customer->company_name) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>
            </div>
            {{-- Nama Customer --}}
            <div>
                <label for="edit-name-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" id="edit-name-{{ $customer->id }}"
                    value="{{ old('name', $customer->name) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>
            </div>
            {{-- Kategori Customer --}}
            <div>
                <label for="edit-customer_category_id-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori <span
                        class="text-red-500">*</span></label>
                <select name="customer_category_id" id="edit-customer_category_id-{{ $customer->id }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($customerCategories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('customer_category_id', $customer->customer_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Ditambahkan Oleh (Kurir) --}}
            <div>
                <label for="edit-added_by_user_id-{{ $customer->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ditambahkan Oleh (Kurir) <span
                        class="text-red-500">*</span></label>
                <select name="added_by_user_id" id="edit-added_by_user_id-{{ $customer->id }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400" required>
                    {{-- Opsi default bisa jadi Admin yang sedang login atau null jika tidak ada --}}
                    <option value="{{ Auth::user()->hasRole('admin') ? Auth::id() : '' }}">Pilih Penanggung Jawab</option>
                    @foreach ($couriers as $courier)
                        <option value="{{ $courier->id }}" {{ old('added_by_user_id', $customer->added_by_user_id) == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Alamat --}}
            <div class="sm:col-span-2">
                <label for="edit-address-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat <span
                        class="text-red-500">*</span></label>
                <textarea name="address" id="edit-address-{{ $customer->id }}" rows="2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>{{ old('address', $customer->address) }}</textarea>
            </div>
            {{-- Patokan Tempat --}}
            <div class="sm:col-span-2">
                <label for="edit-landmark-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Patokan Tempat
                    (Opsional)</label>
                <input type="text" name="landmark" id="edit-landmark-{{ $customer->id }}"
                    value="{{ old('landmark', $customer->landmark) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
            </div>
            {{-- Nomor HP --}}
            <div>
                <label for="kurir-edit-phone-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. HP <span
                        class="text-red-500">*</span></label>
                <div class="flex">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">+62</span>
                    <input type="tel" name="phone" id="kurir-edit-phone-{{ $customer->id }}"
                        value="{{ old('phone', substr($customer->phone, 2)) }}"
                        class="rounded-none rounded-r-lg bg-gray-50 border text-gray-900 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                        required inputmode="numeric" pattern="[0-9]*"
                        oninput="this.value = this.value.replace(/\D/g, '')">
                </div>
            </div>
            {{-- Jenis Pembayaran --}}
            <div>
                <label for="edit-payment_type-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Pembayaran <span
                        class="text-red-500">*</span></label>
                <input type="text" name="payment_type" id="edit-payment_type-{{ $customer->id }}"
                    value="{{ old('payment_type', $customer->payment_type) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>
            </div>
            {{-- Jam Buka --}}
            <div class="sm:col-span-2">
                <label for="edit-opening_hours-{{ $customer->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jam Buka <span
                        class="text-red-500">*</span></label>
                <input type="text" name="opening_hours" id="edit-opening_hours-{{ $customer->id }}"
                    value="{{ old('opening_hours', $customer->opening_hours) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                    required>
            </div>
        </div>
        <button type="submit"
            class="text-white inline-flex items-center bg-brand hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep">
            <i class="fas fa-save me-1"></i>
            Simpan Perubahan
        </button>
    </form>
</x-modal-custom>
