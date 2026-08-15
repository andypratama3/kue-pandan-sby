@forelse ($customers as $customer)
    <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
        {{-- NO --}}
        <td class="px-4 py-3 font-medium text-center text-gray-900 dark:text-white">
            {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
        </td>

        {{-- NAMA PERUSAHAAN --}}
        <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ $customer->company_name ?? '-' }}
        </td>

        {{-- NAMA CUSTOMER (+ FLAG) --}}
        <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <div class="flex items-center space-x-2">
                <span>{{ $customer->name }}</span>

                <button type="button" class="toggle-flag-btn"
                    data-url="{{ route('admin.customers.toggleFlag', $customer) }}">
                    @if ($customer->is_flagged)
                        {{-- Tampilan jika customer DITANDAI (bermasalah) --}}
                        <i class="text-red-500 fas fa-flag" title="Customer Bermasalah. Klik untuk menghapus tanda."></i>
                    @else
                        {{-- Tampilan jika customer NORMAL --}}
                        <i class="text-gray-400 fas fa-flag hover:text-gray-600"
                            title="Tandai sebagai customer bermasalah."></i>
                    @endif
                </button>
            </div>
        </td>

        {{-- ALAMAT (+ LANDMARK) --}}
        <td class="px-4 py-3 text-gray-900 dark:text-white">
            {{-- DIUBAH: Menggunakan Str::limit() untuk membatasi alamat menjadi 5 kata --}}
            {{ Str::limit($customer->address, 50, '...') }}

            @if ($customer->landmark)
                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Patokan: {{ $customer->landmark }}
                </span>
            @endif
        </td>

        {{-- NOMOR TELEPON --}}
        <td class="px-4 py-3 text-center text-gray-900 dark:text-white">
            +{{ $customer->phone }}
        </td>

        {{-- NOTE --}}
        <td class="px-4 py-3 text-center">{{ Str::limit($customer->note, 20) }}</td>

        {{-- KOLOM AKSI (Tidak Berubah) --}}
        <td class="px-4 py-3 text-right">
            <div class="relative inline-block">
                {{-- Tombol Dropdown Aksi --}}
                <button data-target-dropdown="customer-actions-dropdown-{{ $customer->id }}"
                    class="px-2 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg js-dropdown-toggle hover:bg-gray-100 hover:text-brand-deep focus:z-10 focus:ring-2 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                {{-- Konten Dropdown --}}
                <div id="customer-actions-dropdown-{{ $customer->id }}"
                    class="absolute right-0 z-50 hidden mt-2 bg-white divide-y divide-gray-100 rounded shadow js-dropdown-menu w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                        {{-- Tombol Detail/Show --}}
                        <li>
                            <button type="button" data-target-modal="show-customer-modal-{{ $customer->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-eye"></i></span>
                                <span>Detail</span>
                            </button>
                        </li>
                        {{-- Tombol Edit --}}
                        <li>
                            <button type="button" data-target-modal="edit-customer-modal-{{ $customer->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-edit"></i></span>
                                <span>Edit</span>
                            </button>
                        </li>
                        {{-- Tombol Note --}}
                        <li>
                            <button type="button" data-target-modal="note-customer-modal-{{ $customer->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i
                                        class="fas fa-sticky-note"></i></span>
                                <span>Note</span>
                            </button>
                        </li>
                        {{-- Tombol Rekap --}}
                        <li>
                            <button type="button" data-target-modal="rekap-customer-modal-{{ $customer->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-file-download"></i></span>
                                <span>Rekap</span>
                            </button>
                        </li>
                    </ul>
                    <div class="py-1">
                        {{-- Tombol Delete --}}
                        <button type="button" data-target-modal="delete-customer-modal-{{ $customer->id }}"
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
        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data customer.</td>
    </tr>
@endforelse
