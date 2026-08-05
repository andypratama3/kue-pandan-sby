@forelse ($customers as $customer)
    {{-- Card Container --}}
    {{-- DIUBAH: Padding p-5 menjadi p-4 agar lebih ringkas --}}
    <div class="flex flex-col justify-between w-full max-w-sm pt-2 px-4 bg-white border border-gray-200 rounded-xl shadow-md transition-shadow duration-300 hover:shadow-lg dark:bg-gray-800 dark:border-gray-700">
        
        {{-- 1. Konten Utama --}}
        <div class="flex-grow">
            {{-- Header Kartu: Nama & Tombol Detail --}}
            {{-- DIUBAH: Padding pb-3 mb-3 menjadi pb-2 mb-2 --}}
            <div class="flex items-start justify-between pb-1 mb-2 border-b dark:border-gray-700">
                <div class="flex-1 min-w-0">
                    {{-- Judul Utama (Nama Pelanggan) --}}
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="mr-2 text-blue-500 dark:text-blue-400">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                            <path d="M3 6h18" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
                        {{-- DIUBAH: Selalu menampilkan nama pelanggan --}}
                        <h4 class="text-lg font-bold text-gray-900 tracking-wider truncate dark:text-white"
                            title="{{ $customer->name }}">
                            {{ $customer->name }}
                        </h4>
                    </div>

                    {{-- Subjudul (Nama Toko jika ada) --}}
                    @if ($customer->company_name)
                        <p class="flex items-center font-semibold text-sm text-gray-500 dark:text-gray-400">
                            {{-- DIUBAH: Menampilkan nama toko --}}
                            {{ $customer->company_name }}

                            @if ($customer->is_flagged)
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="currentColor" class="ml-2 text-red-500">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1v12Z" />
                                </svg>
                            @endif
                        </p>
                    @endif
                </div>

                {{-- Tombol Detail --}}
                <button type="button" data-target-modal="show-customer-modal-{{ $customer->id }}"
                    class="p-2 ml-2 text-gray-500 rounded-full js-open-modal-btn dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                </button>
            </div>

            {{-- Detail Informasi: Alamat & Catatan --}}
            {{-- DIUBAH: Jarak space-y-4 menjadi space-y-3 --}}
            <div class="space-y-3">
                {{-- Alamat & Patokan --}}
                <div>
                    <h5 class="flex items-center text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {{-- DIUBAH: Ikon diberi warna ungu --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-indigo-500 dark:text-indigo-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Alamat
                    </h5>
                    <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $customer->address }}</p>
                    @if ($customer->landmark)
                        <p class="text-xs italic text-gray-500 dark:text-gray-400">
                            (Patokan: {{ $customer->landmark }})
                        </p>
                    @endif
                </div>

                {{-- Catatan --}}
                @if ($customer->note)
                    <div>
                        <h5 class="flex items-center text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                           {{-- DIUBAH: Ikon diberi warna teal/hijau toska --}}
                           <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-brand dark:text-brand-light"><path d="M15.5 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3Z"/><path d="M15 3v6h6"/></svg>
                            Catatan
                        </h5>
                        <p class="mt-1 text-sm italic text-gray-800 dark:text-gray-200">"{{ $customer->note }}"</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. Tombol Aksi --}}
        {{-- DIUBAH: Padding pt-4 mt-4 menjadi pt-3 mt-3 --}}
        <div class="flex items-center justify-end mt-3 space-x-2 border-t border-gray-200 dark:border-gray-700">
            <a href="https://wa.me/{{ $customer->phone }}" target="_blank"
                class="text-lg text-brand-deep transition transform dark:text-brand hover:scale-125 active:scale-90" title="Hubungi via WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <button type="button" data-target-modal="note-customer-modal-{{ $customer->id }}" class="p-2 text-yellow-600 rounded-full js-open-modal-btn hover:bg-yellow-50 dark:text-yellow-500 dark:hover:bg-gray-700" title="Edit Catatan">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.4 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10.6"/><path d="M18 2L22 6"/><path d="M11 17h6"/></svg>
            </button>
            <button type="button" data-target-modal="edit-customer-modal-{{ $customer->id }}" class="p-2 text-blue-600 rounded-full js-open-modal-btn hover:bg-blue-50 dark:text-blue-500 dark:hover:bg-gray-700" title="Edit Customer">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </button>
            <button type="button" data-target-modal="delete-customer-modal-{{ $customer->id }}" class="p-2 text-red-600 rounded-full js-open-modal-btn hover:bg-red-50 dark:text-red-500 dark:hover:bg-gray-700" title="Hapus Customer">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        </div>
    </div>
@empty
    {{-- Tampilan jika tidak ada data customer --}}
    <div class="flex flex-col items-center justify-center col-span-full py-16 text-center text-gray-500 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-gray-300 dark:text-gray-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Belum Ada Pelanggan</h3>
        <p class="max-w-xs mt-1 text-sm">Anda bisa menambahkan pelanggan baru melalui tombol yang tersedia.</p>
    </div>
@endforelse
