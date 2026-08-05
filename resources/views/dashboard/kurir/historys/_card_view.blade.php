@forelse ($orders as $order)
<div class="px-3 py-2 border-l-8 border-brand rounded-lg shadow-md bg-gray-50 dark:bg-gray-700">
    {{-- BAGIAN ATAS: INVOICE & TOMBOL DETAIL ICON --}}
    <div class="flex items-start justify-between pb-1 border-b dark:border-gray-600">
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->invoice_number }}</p>
        </div>
        <button type="button"
            class="text-xl text-blue-500 js-open-modal-btn hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
            data-target-modal="showOrderModal" data-order-id="{{ $order->id }}">
            <i class="fas fa-info-circle"></i>
        </button>
    </div>

    {{-- BAGIAN TENGAH: CUSTOMER (KIRI) & TOTAL (KANAN) --}}
    <div class="flex items-start justify-between mt-1">
        {{-- SISI KIRI: DATA CUSTOMER --}}
        <div class="pr-4">
            <p class="mb-2 font-bold text-gray-800 text-md dark:text-gray-200">
                {{ $order->customer->name ?? '-' }}
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-300">
                <i class="mr-1 fas fa-store"></i> {{ $order->customer->company_name ?? '' }}
            </p>
            <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $order->updated_at->isoFormat('D MMM YYYY, HH:mm') }}
                    </p>
                </span>
            </div>
        </div>

        {{-- SISI KANAN: TOTAL HARGA & STATUS --}}
        <div class="text-right shrink-0">
            {{-- Status Lunas & Retur --}}
            <div class="flex flex-row items-end justify-end mt-2 mb-2 space-x-1">
                <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $order->payment_status['class'] }}">
                    {{ $order->payment_status['text'] }}
                </span>
                @if ($order->has_return)
                <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                    Retur
                </span>
                @endif
            </div>
            {{-- Total Harga --}}
            <div>
                @if ($order->has_return)
                <p class="text-xs leading-tight line-through text-slate-400">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
                <p class="text-xl font-bold text-brand-deep dark:text-brand-light">
                    Rp {{ number_format($order->final_total, 0, ',', '.') }}
                </p>
                @else
                <p class="text-lg font-bold text-brand-deep dark:text-brand-light">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="py-10 text-center">
    <p class="text-sm text-gray-500">Tidak ada data history pesanan</p>
</div>
@endforelse
