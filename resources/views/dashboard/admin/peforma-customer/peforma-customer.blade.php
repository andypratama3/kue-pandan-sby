@extends('layouts.argon')
@section('title', 'Peforma Customer')
@section('page_title', 'Peforma Customer')

@section('content')

<div class="w-full p-6 bg-white rounded-lg shadow dark:bg-gray-800 min-h-[715px]">
    <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-center md:justify-between">
        <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white md:mb-0">Peforma Customer</h2>
        <form method="GET" class="flex flex-row flex-wrap items-center gap-2">
            <div class="relative">
                <select name="month" class="px-4 py-1 pr-8 text-sm border rounded appearance-none focus:ring focus:ring-blue-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    @foreach($months as $num => $name)
                    <option value="{{ $num }}" @if($selectedMonth==$num) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <select name="year" class="px-4 py-1 pr-8 text-sm border rounded appearance-none focus:ring focus:ring-blue-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    @foreach($years as $year)
                    <option value="{{ $year }}" @if($selectedYear==$year) selected @endif>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">Lihat</button>
            <a href="{{ route('admin.peforma-customer.export.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="flex items-center px-3 py-1 text-sm font-semibold text-white bg-orange-500 rounded hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700"><i class="mr-1 fas fa-file-export"></i> Export</a>
    </div>


    <div x-data="{ show: true }" x-show="show" class="mb-4">
        <div class="relative flex items-center justify-between px-4 py-3 text-black bg-green-100 rounded dark:bg-slate-700 dark:text-white dark:border dark:border-slate-600" role="alert">
            <span class="ml-2">Ranking hanya menampilkan customer kategori <b>Reseller</b>. Setiap pelanggan akan mendapatkan "Skor Performa" yang dihitung dari kombinasi "Skor Pembelian" dan "Skor Retur".
            </span>
            <button type="button" @click="show = false" class="ml-4 text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
        Ranking Peforma Customer Bulan {{ $bulan ?? '' }}
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="w-24 px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Rank</th>
                    <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Nama Customer</th>
                    <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Total Pembelian</th>
                    <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Total Retur</th>
                    <th class="px-4 py-2 text-xs font-bold text-center text-gray-700 uppercase dark:text-gray-200">Skor Akhir</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-200">
                @forelse($ranking as $row)
                <tr>
                    <td class="px-4 py-2 font-bold text-center">
                        @if($row->peringkat == 1)
                        <i class="fas fa-medal text-yellow-500 text-xl"></i>
                        @elseif($row->peringkat == 2)
                        <i class="fas fa-medal text-gray-400 text-xl"></i>
                        @elseif($row->peringkat == 3)
                        <i class="fas fa-medal text-orange-600 text-xl"></i>
                        @else
                        {{ $row->peringkat }}
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">{{ $row->nama_customer }}</td>
                    <td class="px-4 py-2 text-center">Rp. {{ number_format($row->total_pembelian, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-center">Rp. {{ number_format($row->total_retur, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-center">{{ $row->skor_akhir }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada data peforma customer bulan ini.</td>
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
