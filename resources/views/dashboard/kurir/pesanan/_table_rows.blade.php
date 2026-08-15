@php
// Failsafe: Jika $statusLabelMap tidak dikirim dari controller, buat array kosong untuk mencegah error.
$statusLabelMap = $statusLabelMap ?? [];

// Helper function untuk mendapatkan label status dari map
$labelStatus = function ($status) use ($statusLabelMap) {
return $statusLabelMap[$status] ?? ucwords(str_replace('_', ' ', $status));
};
@endphp

@forelse ($orders as $order)
<tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60 transition-colors" data-order-id="{{ $order->id }}">
    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
        {{-- Penomoran yang benar untuk paginasi --}}
        {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
        @if ($order->show_warning)
        <i class="fas fa-exclamation-triangle text-yellow-500" title="Pembayaran melewati 5 hari"></i>
        @endif
    </td>
    <td class="px-6 py-1 text-gray-900 whitespace-nowrap dark:text-white">
        <p class="font-semibold text-gray-900 text-base dark:text-white">{{ $order->invoice_number }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $order->created_at->isoFormat('D MMM YYYY, HH:mm') }}
        </p>
        @if ($order->rejection_note)
            <div class="flex items-center gap-1 mt-1 text-xs text-red-600 dark:text-red-400" title="{{ $order->rejection_note }}">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Verifikasi Ditolak</span>
            </div>
        @endif
    </td>
    <td class="px-6 py-1 text-gray-500 whitespace-nowrap dark:text-gray-300">
        <p class="font-semibold text-gray-800 text-base dark:text-gray-200">
            {{ optional($order->customer)->name ?? 'Pelanggan Dihapus' }}</p>
        <p class="text-xs text-gray-600 dark:text-gray-300">
            {{ $order->customer->company_name ?? '' }}
        </p>
    </td>
    <td class="px-6 py-4 text-sm whitespace-nowrap">
        <span class="status-badge px-2.5 py-1 text-xs font-semibold rounded-full
            @switch($order->status ?? 'baru')
                @case('diambil') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                @case('diantar') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                @case('diterima_pembeli') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 @break
                @case('menunggu_retur') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                @case('menunggu_verifikasi_admin') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300 @break
                @case('selesai') bg-brand-light text-brand-deep dark:bg-brand-deep/60 dark:text-brand-light @break
                @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
            @endswitch
        ">
            {{ $labelStatus($order->status) }}
        </span>
    </td>
    <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
        <div class="flex items-center justify-center space-x-2">
            <button type="button"
                class="js-open-status-modal px-3 py-1.5 text-xs font-medium text-white bg-brand rounded-lg hover:bg-brand-deep transition-colors"
                data-order-id="{{ $order->id }}">
                Ubah Status
            </button>
            <button type="button"
                class="js-open-details-modal px-3 py-1.5 text-xs font-medium text-gray-900 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600"
                data-order-id="{{ $order->id }}">
                Rincian
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
        Tidak ada pesanan yang cocok dengan pencarian Anda.
    </td>
</tr>
@endforelse
