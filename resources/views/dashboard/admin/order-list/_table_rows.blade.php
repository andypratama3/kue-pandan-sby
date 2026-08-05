@forelse ($orders as $order)
    <tr class="text-sm font-normal text-gray-700 border-b dark:text-gray-400 dark:border-gray-700">
        <td class="px-4 py-2 text-center">{{ $loop->iteration }}</td>
        <td class="px-4 py-2 font-mono">{{ $order->invoice_number }}</td>
        <td class="px-4 py-2">{{ $order->customer->name ?? '-' }}</td>
        <td class="px-4 py-2">{{ $order->createdBy->name ?? '-' }}</td>
        <td class="px-4 py-2">
            <span
                class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($order->status)
                                            @case('selesai') bg-brand-light text-brand-deep @break
                                            @case('menunggu_verifikasi_admin') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                                            @case('diverifikasi_admin') bg-brand-light text-brand-deep @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                {{ $labelStatus($order->status) }}
            </span>
        </td>
        <td class="px-4 py-2">
            @php
                // Cek retur aktif (tidak ditolak)
                $activeReturn = $order->returns->where('status', '!=', 'ditolak')->sortByDesc('id')->first();
                $returnedAmount = $activeReturn ? $activeReturn->total_amount_returned : 0;
                $afterReturn = $order->total_amount - $returnedAmount;
            @endphp
            @if ($activeReturn && $returnedAmount > 0)
                <span class="block text-xs text-gray-500 line-through">Rp
                    {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                <span class="block font-bold text-brand-deep">Rp
                    {{ number_format($afterReturn, 0, ',', '.') }}</span>
            @else
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            @endif
        </td>
        <td class="px-4 py-2 text-center">
            @if ($order->note)
                <button type="button"
                    class="text-gray-500 js-open-modal-btn hover:text-brand-deep dark:text-gray-400 dark:hover:text-brand-light"
                    data-target-modal="viewNoteModal" data-note="{{ $order->note }}" title="Lihat Catatan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            @else
                <span class="text-gray-400 dark:text-gray-500">-</span>
            @endif
        </td>
        <td class="px-4 py-2">
            {{-- Tombol ini akan membuka modal verifikasi --}}
            @if ($order->status == 'selesai' || $order->status == 'menunggu_verifikasi_admin')
                <button
                    class="px-3 py-1 text-xs font-bold text-white bg-brand-deep rounded js-open-modal-btn hover:bg-brand"
                    data-target-modal="verifyOrderModal" data-order-id="{{ $order->id }}">
                    Verifikasi
                </button>
            @else
                <button class="px-3 py-1 text-xs font-bold text-white bg-gray-400 rounded cursor-not-allowed" disabled>
                    Verifikasi
                </button>
            @endif

            <button
                class="px-3 py-1 text-xs font-bold text-white bg-red-600 rounded js-open-delete-modal hover:bg-red-700"
                data-order-id="{{ $order->id }}" data-invoice-number="{{ $order->invoice_number }}">
                Hapus
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="py-6 text-center text-gray-500">Tidak ada pesanan ditemukan.</td>
    </tr>
@endforelse
