{{-- resources/views/dashboard/admin/couriers/modals/edit.blade.php --}}
@props(['courier'])

<x-modal-custom id="edit-courier-modal-{{ $courier->id }}" title="Edit Kurir" toggle="edit-courier-modal-{{ $courier->id }}"
    size="2xl">
    <form class="p-4 md:p-5" method="POST" action="{{ route('admin.couriers.update', $courier->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="col-span-2">
                <label for="name-{{ $courier->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kurir</label>
                <input type="text" name="name" id="name-{{ $courier->id }}"
                    value="{{ old('name', $courier->name) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand dark:focus:border-brand"
                    required="">
            </div>
            <div class="col-span-2">
                <label for="email-{{ $courier->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                <input type="email" name="email" id="email-{{ $courier->id }}"
                    value="{{ old('email', $courier->email) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand dark:focus:border-brand"
                    required="">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="password-{{ $courier->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Baru
                    (Opsional)</label>
                <div class="relative">
                    <input type="password" name="password" id="password-{{ $courier->id }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand dark:focus:border-brand">
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password">
                        <i class="text-gray-400 fas fa-eye"></i>
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-400">Password Minimal 8 Digit.</p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label for="password_confirmation-{{ $courier->id }}"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi
                    Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation-{{ $courier->id }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand dark:focus:border-brand">
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password">
                        <i class="text-gray-400 fas fa-eye"></i>
                    </button>
                </div>
                <p id="edit_password_error-{{ $courier->id }}" class="hidden mt-1 text-sm text-red-600">
                    Password tidak cocok.</p>
            </div>
        </div>
        <button type="submit"
            class="text-white inline-flex items-center bg-brand hover:bg-brand-deep focus:ring-4 focus:outline-none focus:ring-brand-light font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand dark:hover:bg-brand-deep dark:focus:ring-brand-deep">
            <i class="fas fa-save me-1"></i>
            Simpan Perubahan
        </button>
    </form>
</x-modal-custom>
