@props(['customer'])

<x-modal-custom id="show-customer-modal-{{ $customer->id }}" title="Detail Customer" size="3xl">
    {{-- Hapus padding default agar konten bisa menempel ke tepi modal --}}
    <div class="p-0">
        <div class="flex flex-col overflow-hidden">
            {{-- Area konten yang bisa di-scroll --}}
            <!-- <div class="overflow-y-auto max-h-[70vh] sm:max-h-[80vh]"> -->
                <div class="flex flex-col md:flex-row">
                    {{-- Kolom Kiri - Informasi Utama (Desain Baru) --}}
                    <div class="w-full px-6 text-gray-800 md:w-2/3 dark:text-gray-200 mb-4">
                        {{-- Bagian Atas: Avatar, Nama, Perusahaan --}}
                        <div class="flex items-center gap-5 mb-8">
                            {{-- Avatar Icon --}}
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-16 h-16 text-white bg-blue-600 rounded-full dark:bg-blue-500">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                            {{-- Nama dan Perusahaan --}}
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">{{ $customer->company_name ?? 'Customer Personal' }}</p>
                            </div>
                        </div>

                        {{-- Bagian Bawah: Detail Info dengan Ikon --}}
                        <dl class="space-y-5">
                            {{-- Nomor Telepon --}}
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1 text-indigo-600 dark:text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Telepon</dt>
                                    <dd class="mt-0.5 font-semibold text-gray-900 dark:text-white">{{ $customer->phone ? '+' . $customer->phone : '-' }}</dd>
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1 text-indigo-600 dark:text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat Lengkap</dt>
                                    <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $customer->address ?? '-' }}</dd>
                                </div>
                            </div>

                            {{-- Patokan Tempat --}}
                             @if($customer->landmark)
                            <div class="flex items-start gap-4 mb-1">
                                <div class="flex-shrink-0 mt-1 text-indigo-600 dark:text-indigo-400">
                                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Patokan Tempat</dt>
                                    <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $customer->landmark }}</dd>
                                </div>
                            </div>
                            @endif
                        </dl>

                        {{-- Catatan --}}
                        @if($customer->note)
                        <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-700 ">
                            <label class="mb-1 font-medium text-gray-900 text-md dark:text-gray-400">Catatan </label>
                            <div id="orderNotesContainer" class="p-2 font-semibold text-center text-gray-800 bg-yellow-100 rounded-lg dark:bg-yellow-900/20 dark:text-gray-50">
                            {!! nl2br(e($customer->note)) !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Kolom Kanan - Informasi Tambahan --}}
                    <div class="w-full px-6 border-t border-gray-200 md:w-1/3 bg-gray-50 dark:bg-gray-800 md:border-t-0 md:border-l dark:border-gray-700">
                        <h3 class="flex items-center mb-4 mt-2 text-lg font-semibold text-gray-800 dark:text-white">
                            {{-- SVG Ikon Informasi (Outline) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Informasi Lainnya
                        </h3><dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    @if ($customer->is_flagged)
                                        <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">Bermasalah</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">Normal</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori Customer</dt>
                                <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ optional($customer->category)->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jam Buka</dt>
                                <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $customer->opening_hours ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Pembayaran</dt>
                                <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $customer->payment_type ?? '-' }}</dd>
                            </div>
                            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-600">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Region</dt>
                                <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ optional($customer->region)->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditambahkan Oleh</dt>
                                <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ optional($customer->addedBy)->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt>
                                <dd class="mt-1 mb-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $customer->created_at->format('d F Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- {{-- Bagian Footer Modal (Tombol Tutup) --}}
            <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <button type="button"
                    class="js-close-modal-btn py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                    Tutup
                </button>
            </div> -->
        </div>
    </div>
</x-modal-custom>
