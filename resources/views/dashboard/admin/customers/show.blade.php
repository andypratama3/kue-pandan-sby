@props(['customer'])

<x-modal-custom id="show-customer-modal-{{ $customer->id }}" title="Detail Customer" toggle="show-customer-modal-{{ $customer->id }}" size="4xl">
    <div class="p-0 md:p-0">
        <div class="grid grid-cols-1 md:grid-cols-3">
            {{-- Kolom Kiri - Informasi Utama --}}
            <div class="p-5 md:col-span-2">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">{{ $customer->name }}</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Perusahaan</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->company_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->phone ? '+' . $customer->phone : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Alamat Lengkap</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Patokan Tempat</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->landmark ?? '-' }}</dd>
                    </div>
                    <div class="pt-4 border-t">
                        <dt class="text-sm font-medium text-gray-500">Note</dt>
                        <dd class="mt-1 prose-sm prose max-w-none dark:text-white">
                            {!! $customer->note ? nl2br(e($customer->note)) : '-' !!}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kolom Kanan - Informasi Tambahan --}}
            <div class="p-5 rounded-b-lg md:col-span-1 bg-gray-50 dark:bg-gray-800 md:rounded-b-none md:rounded-e-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Informasi Lainnya</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if ($customer->is_flagged)
                                <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Bermasalah</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-brand-deep bg-brand-light rounded-full">Normal</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kategori Customer</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ optional($customer->category)->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jam Buka</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->opening_hours ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis Pembayaran</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $customer->payment_type ?? '-' }}</dd>
                    </div>
                    <div class="pt-4 border-t">
                        <dt class="text-sm font-medium text-gray-500">Region</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ optional($customer->region)->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ditambahkan Oleh</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ optional($customer->addedBy)->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Bergabung</dt>
                        <dd class="mt-1 text-xs text-gray-900 dark:text-white">{{ $customer->created_at->format('d F Y, H:i') }}</dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-xs text-gray-900 dark:text-white">{{ $customer->updated_at->format('d F Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-modal-custom>
