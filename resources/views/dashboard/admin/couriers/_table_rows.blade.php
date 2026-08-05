@forelse ($couriers as $courier)
    <tr class="border-b dark:border-gray-700">
        <td class="px-4 py-3 font-medium text-center text-gray-900 dark:text-white">
            {{ ($couriers->currentPage() - 1) * $couriers->perPage() + $loop->iteration }}
        </td>
        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ $courier->name }}</th>
        <td class="px-4 py-3 text-center">{{ $courier->email }}</td>
        <td class="px-4 py-3 text-center">{{ $courier->region->name }}</td>
        <td class="px-4 py-3 text-center">{{ Str::limit($courier->note, 20) }}</td>
        <td class="px-4 py-3 text-center">{{ $courier->created_at->format('d M Y') }}</td>
        <td class="px-4 py-3 text-right">
            <div class="relative inline-block">
                <button data-target-dropdown="courier-actions-dropdown-{{ $courier->id }}"
                    class="px-2 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg js-dropdown-toggle hover:bg-gray-100 hover:text-brand-deep focus:z-10 focus:ring-2 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div id="courier-actions-dropdown-{{ $courier->id }}"
                    class="absolute right-0 z-50 hidden mt-2 bg-white divide-y divide-gray-100 rounded shadow js-dropdown-menu w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                        <li>
                            <button type="button" data-target-modal="show-courier-modal-{{ $courier->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-eye"></i></span>
                                <span>Detail</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" data-target-modal="edit-courier-modal-{{ $courier->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-edit"></i></span>
                                <span>Edit</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" data-target-modal="note-courier-modal-{{ $courier->id }}"
                                class="flex items-center w-full px-4 py-2 text-left js-open-modal-btn hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="inline-block w-6 mr-2 text-center"><i
                                        class="fas fa-sticky-note"></i></span>
                                <span>Note</span>
                            </button>
                        </li>
                        <li>
                            <button type="button"
                                class="flex items-center w-full px-4 py-2 text-left js-open-performance-modal hover:bg-gray-100 dark:hover:bg-gray-600"
                                data-target-modal="performance-modal-{{ $courier->id }}"
                                data-courier-id="{{ $courier->id }}">
                                <span class="inline-block w-6 mr-2 text-center"><i class="fas fa-chart-line"></i></span>
                                <span>Performa</span>
                            </button>
                        </li>
                    </ul>
                    <div class="py-1">
                        <button type="button" data-target-modal="delete-courier-modal-{{ $courier->id }}"
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
    <tr class="border-b dark:border-gray-700">
        <td colspan="7" class="px-4 py-3 text-center text-gray-500">Belum ada data kurir di region ini.</td>
    </tr>
@endforelse
