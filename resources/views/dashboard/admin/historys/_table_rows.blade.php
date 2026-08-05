@forelse ($orders as $order)
    <tr class="border-b dark:border-gray-700">
        {{-- NO --}}
        <td class="px-4 py-3 font-medium text-center text-gray-900 dark:text-white">
            {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
        </td>
        <td class="px-4 py-2">
            <p class="mb-0 text-xs font-semibold leading-tight">{{ $order->invoice_number }}</p>
            <p class="mb-0 text-xs leading-tight text-slate-400">
                {{ $order->created_at->isoFormat('D MMM YYYY, HH:mm') }}
            </p>
        </td>
        <td class="px-4 py-2">
            <p class="mb-0 text-xs font-semibold leading-tight">{{ $order->customer->name ?? '-' }}
            </p>
            <p class="mb-0 text-xs leading-tight text-slate-400">
                {{ $order->customer->company_name ?? '-' }}</p>
        </td>
        <td class="px-4 py-2">
            <p class="mb-0 text-xs leading-tight">{{ $order->createdBy->name ?? '-' }}</p>
        </td>
        <td class="px-4 py-2">
            <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $order->payment_status['class'] }}">
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
            @if ($order->has_return)
                {{-- Tampilkan total baru dan coret total lama --}}
                <p class="mb-0 text-xs font-semibold leading-tight text-green-600 dark:text-green-400">
                    Rp {{ number_format($order->final_total, 0, ',', '.') }}
                </p>
                <p class="mb-0 text-xs leading-tight line-through text-slate-400">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
            @else
                {{-- Tampilkan total normal jika tidak ada retur --}}
                <p class="mb-0 text-xs font-semibold leading-tight">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
            @endif
        </td>

        <td class="px-4 py-2 text-center">
            {{-- Wrapper untuk dropdown --}}
            <div class="relative inline-block text-left">
                {{-- Tombol untuk membuka dropdown --}}
                <button type="button"
                    class="flex items-center justify-center w-8 h-8 text-gray-500 rounded-full js-dropdown-toggle hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-400 dark:hover:bg-gray-700"
                    data-target-dropdown="actions-dropdown-{{ $order->id }}">
                    <span class="sr-only">Buka menu aksi</span>
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                {{-- Menu dropdown, awalnya disembunyikan --}}
                <div id="actions-dropdown-{{ $order->id }}"
                    class="absolute right-0 z-10 hidden w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg js-dropdown-menu ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:ring-gray-600">
                    <div class="py-1" role="menu" aria-orientation="vertical">
                        {{-- Tombol Detail --}}
                        <button type="button"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 js-open-modal-btn hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                            data-target-modal="showOrderModal" data-order-id="{{ $order->id }}" role="menuitem">
                            <i class="w-5 mr-2 text-center fas fa-eye"></i>
                            <span>Detail</span>
                        </button>

                        {{-- Tombol WhatsApp --}}
                        @php
                            $wa_number = $order->customer->phone ?? null;
                            if ($wa_number) {
                                $wa_number = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $wa_number));
                            }
                            $customer_name = $order->customer->name ?? '-';
                            $wa_message =
                                "Yth. Bapak/Ibu *{$customer_name}*,\n\n" .
                                "Kami mengonfirmasi bahwa pesanan Anda telah selesai.\n\n" .
                                "Sebagai referensi, transaksi ini tercatat dengan nomor invoice berikut: *{$order->invoice_number}*.\n\n" .
                                "Terimakasih sudah berbelanja di Toko Kami.\n\n" .
                                "Hormat kami.\n*Admin Kue Pandan Asli*";
                            $wa_message = urlencode($wa_message);
                        @endphp
                        @if ($wa_number)
                            <a href="https://wa.me/{{ $wa_number }}?text={{ $wa_message }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                                role="menuitem">
                                <i class="w-5 mr-2 text-center text-green-500 fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                        @endif

                        {{-- Tombol Invoice --}}
                        <a href="{{ route('admin.historys.invoice', $order->id) }}" target="_blank"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                            role="menuitem">
                            <i class="w-5 mr-2 text-center fas fa-file-invoice"></i>
                            <span>Invoice</span>
                        </a>

                        {{-- Tombol Download --}}
                        <a href="{{ route('admin.historys.download', $order->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                            role="menuitem">
                            <i class="w-5 mr-2 text-center fas fa-download"></i>
                            <span>Download</span>
                        </a>

                        {{-- Delete Historys --}}
                        {{-- <a href="{{ route('admin.orders.destroy', $order->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                            role="menuitem">
                            <i class="w-5 mr-2 text-center fas fa-trash "></i>
                            <span>Delete</span>
                        </a> --}}
                        <button type="button" data-target-modal="delete-historys-modal-{{ $order->id }}"
                            class="flex items-center w-full px-4 py-2 text-sm text-left text-red-600 js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-500 dark:hover:text-white">
                            <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-trash"></i></span>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-4 py-4 text-center text-gray-500">
            <p class="mb-0 text-sm">Tidak ada data history pesanan yang cocok.</p>
        </td>
    </tr>
@endforelse
