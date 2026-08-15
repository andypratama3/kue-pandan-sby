@forelse ($orders as $order)
<tr class="border-b dark:border-gray-700">
    {{-- NO --}}
    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
        {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
    </td>
    <td class="px-4 py-2">
        <p class="mb-0.5 text-base text-gray-900 dark:text-white font-semibold leading-tight">{{ $order->invoice_number }}</p>
        <p class="mb-0 text-xs leading-tight text-slate-400">
            {{ $order->created_at->isoFormat('D MMM YYYY, HH:mm') }}
        </p>
    </td>
    <td class="px-4 py-2">
        <p class="mb-0 font-semibold leading-tight text-gray-900 text-base dark:text-white">{{ $order->customer->name ?? '-' }}
        </p>
        <p class="mb-0 text-xs leading-tight text-slate-400">
            {{ $order->customer->company_name ?? '-' }}
        </p>
    </td>
    <td class="px-4 py-2">
        <span
            class="text-xs mr-1 font-medium px-2.5 py-0.5 rounded {{ $order->payment_status['class'] }}">
            {{ $order->payment_status['text'] }}
        </span>
        @if ($order->has_return)
        <span
            class="text-xs font-medium px-2.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
            Retur
        </span>
        @endif
    </td>
    <td class="px-4 py-2">
        @if ($order->has_return) <p
            class="mb-0 font-semibold leading-tight text-brand-deep text-base dark:text-brand-light">
            Rp {{ number_format($order->final_total, 0, ',', '.') }}
        </p>
        <p class="mb-0 text-xs leading-tight line-through text-slate-400">
            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
        </p>
        @else
        <p class="mb-0 font-semibold leading-tight text-base">
            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
        </p>
        @endif
    </td>
    <td class="px-4 py-2 text-center">
        <button type="button"
            class="js-open-modal-btn text-xs px-3 py-1.5 font-semibold text-white bg-brand rounded-lg shadow-md hover:bg-brand-deep focus:outline-none focus:ring-2 focus:ring-brand-light focus:ring-opacity-75"
            data-target-modal="showOrderModal" data-order-id="{{ $order->id }}">
            Detail
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-4 py-2 text-center">
        <p class="mb-0 text-sm text-gray-500 dark:text-gray-400">Tidak ada data history pesanan</p>
    </td>
</tr>
@endforelse
