{{-- resources/views/dashboard/admin/couriers/modals/create.blade.php --}}

<x-modal-custom id="create-courier-modal" title="Tambah Kurir Baru" toggle="create-courier-modal" size="2xl">
    <form name="createCourierForm" method="POST" action="{{ route('admin.couriers.store') }}">
        @csrf
        <div class="p-4 md:p-5">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                        Kurir</label>
                    <input type="text" name="name" id="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Ketik nama kurir" required="">
                </div>
                <div class="col-span-2">
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" name="email" id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="contoh@email.com" required="">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="password"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required="">
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password">
                            <i class="text-gray-400 fas fa-eye"></i>
                        </button>
                    </div>
                     <p class="mt-1 text-sm text-gray-400">Password Minimal 8 Digit.</p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="password_confirmation"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi
                        Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required="">
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password">
                            <i class="text-gray-400 fas fa-eye"></i>
                        </button>
                    </div>
                    <p id="create_password_error" class="hidden mt-1 text-sm text-red-600">Password tidak cocok.</p>
                </div>
            </div>
            <button type="submit"
                class="text-white inline-flex items-center bg-brand hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep">
                <i class="fas fa-save me-1"></i>
                Simpan Kurir
            </button>
        </div>
    </form>
</x-modal-custom>
