@php
// Failsafe: Jika $statusLabelMap tidak dikirim dari controller, buat array kosong untuk mencegah error.
$statusLabelMap = $statusLabelMap ?? [];

// Helper function untuk mendapatkan label status dari map
$labelStatus = function ($status) use ($statusLabelMap) {
return $statusLabelMap[$status] ?? ucwords(str_replace('_', ' ', $status));
};
@endphp

@forelse ($orders as $order)
<div class="relative p-3 overflow-hidden bg-white shadow-lg rounded-xl dark:bg-gray-700"
    data-order-id="{{ $order->id }}">
    {{-- Color Bar --}}
    <div class="absolute top-0 left-0 h-full w-2.5
                @switch($order->status ?? 'dikemas')
                    @case('diambil') bg-blue-500 @break
                    @case('diantar') bg-yellow-500 @break
                    @case('diterima_pembeli') bg-purple-500 @break
                    @case('menunggu_retur') bg-red-500 @break
                    @case('menunggu_verifikasi_admin') bg-orange-500 @break
                    @case('selesai') bg-green-500 @break
                    @default bg-gray-400
                @endswitch">
    </div>

    <div class="pl-2">
        {{-- [MODIFIED] Mengembalikan layout ke side-by-side dan memastikan teks bisa wrap --}}
        <div class="flex items-start justify-between gap-2">
            {{-- Sisi Kiri: Invoice & Catatan Penolakan --}}
            <div class="flex-grow min-w-0">
                <p class="text-sm font-semibold text-black dark:text-white break-words">
                    {{ $order->invoice_number ?? 'N/A' }}
                    @if ($order->show_warning)
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xs" title="Pembayaran melewati 5 hari"></i>
                    @endif
                </p>

                @if ($order->rejection_note)
                    <div class="flex items-center gap-1 mt-1 text-xs text-red-600 dark:text-red-400" title="{{ $order->rejection_note }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Verifikasi Ditolak</span>
                    </div>
                @endif
            </div>
            
            {{-- Sisi Kanan: Status Badge --}}
            <div class="flex-shrink-0 text-right">
                <span class="status-badge inline-block px-2 py-0.5 text-xs font-semibold rounded-full
                        @switch($order->status ?? 'baru')
                            @case('diambil') bg-blue-100 text-blue-800 @break
                            @case('diantar') bg-yellow-100 text-yellow-800 @break
                            @case('diterima_pembeli') bg-purple-100 text-purple-800 @break
                            @case('menunggu_retur') bg-red-100 text-red-800 @break
                            @case('menunggu_verifikasi_admin') bg-orange-100 text-orange-800 @break
                            @case('selesai') bg-green-100 text-green-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch
                        ">
                    {{ $labelStatus($order->status) }}
                </span>
            </div>
        </div>

        {{-- [MODIFIED] Menambahkan margin atas untuk memisahkan dari header --}}
        <div class="flex items-start justify-between mt-2">
            {{-- SISI KIRI: DATA CUSTOMER --}}
            <div class="pr-4">
                <p class="text-md font-bold text-gray-800 dark:text-gray-200 mb-1">
                    {{ $order->customer->name ?? '-' }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    <i class="fas fa-store mr-1"></i> {{ $order->customer->company_name ?? '' }}
                </p>
                <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $order->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                        </p>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex justify-end pt-2 space-x-2 border-t border-gray-200 dark:border-gray-600">
            <button type="button"
                class="js-open-status-modal px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                data-order-id="{{ $order->id }}">
                Ubah Status
            </button>
            <button type="button"
                class="js-open-details-modal px-2 py-1 text-xs font-medium text-gray-900 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
                data-order-id="{{ $order->id }}">
                Rincian
            </button>
        </div>
    </div>
</div>
@empty
<div class="p-4 text-sm text-center text-gray-500">
    Tidak ada pesanan yang cocok dengan pencarian Anda.
</div>
@endforelse

