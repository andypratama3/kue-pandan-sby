@extends('layouts.argon')
@section('title', 'Peforma Kurir')
@section('page_title', 'Peforma Kurir')

@section('content')
    <div class="w-full p-6 bg-white rounded-lg shadow dark:bg-gray-800 min-h-[715px]">
        <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-center md:justify-between">
            <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white md:mb-0">Peforma Kurir</h2>
            <form method="GET" class="flex flex-row flex-wrap items-center gap-2">
                {{-- <div class="relative"> --}}
                {{-- Kelas 'appearance-none' di bawah ini berfungsi untuk MENGHAPUS panah dropdown bawaan browser. --}}
                {{-- <select name="month"
                        class="px-4 py-1 pr-8 text-sm border rounded appearance-none focus:ring focus:ring-blue-200">
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" @if ($selectedMonth == $num) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select> --}}
                {{-- Icon dropdown custom dihapus --}}
                {{-- </div> --}}


                <div class="relative">
                    {{-- Kelas 'appearance-none' di bawah ini berfungsi untuk MENGHAPUS panah dropdown bawaan browser. --}}
                    <div class="mb-4">
                        <label for="daterange" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Pilih
                            Rentang
                            Tanggal</label>
                        <input type="text" name="daterange" id="daterange" value="{{ request('daterange') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            autocomplete="off">
                    </div>
                    {{-- Icon dropdown custom dihapus --}}
                </div>

                {{-- <div class="relative"> --}}
                {{-- Kelas 'appearance-none' di bawah ini juga MENGHAPUS panah dropdown bawaan browser. --}}
                {{-- <select name="year"
                        class="px-4 py-1 pr-8 text-sm border rounded appearance-none focus:ring focus:ring-blue-200">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @if ($selectedYear == $year) selected @endif>
                                {{ $year }}</option>
                        @endforeach
                    </select> --}}
                {{-- Icon dropdown custom dihapus --}}
                {{-- </div> --}}

                <button type="submit"
                    class="px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">Lihat</button>

            </form>
        </div>

        <div x-data="{ show: true }" x-show="show" class="mb-4">
            <div class="relative flex items-center justify-between px-4 py-3 text-black bg-green-100 rounded dark:bg-slate-700 dark:text-white dark:border dark:border-slate-600"
                role="alert">
                <span class="ml-2">Kurir yang berhasil mengantarkan pesanan <b>"Sudah diverifikasi Admin"</b> maka akan
                    mendapat poin 1, Kurir yang mengantarkan pesanan terbanyak adalah kurir yang memiliki poin skor
                    tertinggi</span>
                <button type="button" @click="show = false"
                    class="ml-4 text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
            Ranking Peforma Kurir {{ request('daterange') }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="w-24 px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">
                            Rank</th>
                        <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Nama
                            Kurir</th>
                        <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Total
                            Customer</th>
                        <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">
                            Jumlah Pesanan Selesai</th>
                        <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">
                            Total</th>
                        <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($ranking as $row)
                        <tr>
                            <td class="px-4 py-2 font-bold text-center">
                                @if ($row['rank'] == 1)
                                    <i class="fas fa-medal text-yellow-500 text-xl"></i>
                                @elseif($row['rank'] == 2)
                                    <i class="fas fa-medal text-gray-400 text-xl"></i>
                                @elseif($row['rank'] == 3)
                                    <i class="fas fa-medal text-orange-600 text-xl"></i>
                                @else
                                    {{ $row['rank'] }}
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">{{ $row['nama_kurir'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['total_customer'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['jumlah_order'] }}</td>
                            <td class="px-4 py-2 text-center  block font-bold text-green-600">Rp
                                {{ number_format($row['total'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <form
                                    action="peforma-kurir/export/{{ $row['kurir_id'] }}/pdf?daterange={{ request('daterange') }}"
                                    method="get">
                                    <button type="submit"
                                        class="flex items-center px-3 py-1 text-sm font-semibold text-white bg-orange-500 rounded hover:bg-orange-600">
                                        <i class="mr-1 fas fa-file-export"></i> Export
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada data
                                peforma kurir {{ request('daterange') }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $ranking->withQueryString()->links() }}
        </div>
    </div>
@endsection

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#daterange').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' - ',
                    applyLabel: 'Pilih',
                    cancelLabel: 'Batal',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                },
                opens: 'center',
                autoUpdateInput: false
            });
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush
