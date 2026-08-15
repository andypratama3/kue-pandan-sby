@props(['courier'])

<x-modal-custom id="show-courier-modal-{{ $courier->id }}" title="Detail Kurir" size="4xl">
    <div class="p-0">
        <div class="flex flex-col md:flex-row">

            {{-- Kolom Kiri - Detail Kurir --}}
            <div class="w-full p-5 border-b border-gray-200 md:w-1/2 md:border-b-0 md:border-r dark:border-gray-600">
                <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">{{ $courier->name }}</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">{{ $courier->email }}</p>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Region</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $courier->region->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $courier->created_at->format('d F Y') }}</dd>
                    </div>
                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-600">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</dt>
                        <dd class="prose-sm prose text-gray-900 max-w-none dark:text-gray-300">
                            {!! $courier->note ? nl2br(e($courier->note)) : '<span class="italic text-gray-400">Tidak ada catatan.</span>' !!}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kolom Kanan - Daftar Customer yang Di-handle --}}
            <div class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-col h-full">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Customer yang Ditangani ({{ $courier->customers->count() }})
                        </h3>
                    </div>
                    <div class="flex-grow overflow-y-auto max-h-[50vh]">
                        @if($courier->customers->isNotEmpty())
                            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($courier->customers as $customer)
                                    <li class="p-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->company_name ?? $customer->name }}</p>
                                        @if($customer->company_name)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->address }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                Kurir ini belum menangani customer.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Footer Tombol Tutup --}}
        <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-600">
            <button type="button" class="js-close-modal-btn py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Tutup
            </button>
        </div>
    </div>
</x-modal-custom>