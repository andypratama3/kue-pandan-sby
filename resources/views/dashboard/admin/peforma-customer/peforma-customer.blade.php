@extends('layouts.argon')
@section('title', 'Performa Customer')
@section('page_title', 'Performa Reseller')

@section('content')
    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6 min-h-[700px]">
            
            <!-- Header Section & Filter Form -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <i class="fas fa-chart-line text-base"></i>
                        </div>
                        <span>Peringkat & Performa Reseller</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Evaluasi nilai pembelian vs retur customer reseller di Cabang {{ Auth::user()->region->name ?? 'N/A' }}.
                    </p>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2.5">
                    <select name="month" class="px-3 py-2 text-xs border border-slate-200 rounded-2xl bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-2 focus:ring-indigo-500 font-semibold">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" @if($selectedMonth==$num) selected @endif>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="px-3 py-2 text-xs border border-slate-200 rounded-2xl bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-2 focus:ring-indigo-500 font-semibold">
                        @foreach($years as $year)
                            <option value="{{ $year }}" @if($selectedYear==$year) selected @endif>{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-md transition-all">
                        <i class="fas fa-filter"></i> Lihat
                    </button>
                    <a href="{{ route('admin.peforma-customer.export.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-2xl shadow-md transition-all">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </form>
            </div>

            <!-- Informational Banner -->
            <div x-data="{ show: true }" x-show="show" x-transition
                class="p-4 rounded-2xl bg-mint/80 dark:bg-brand-deep/40 border border-brand-light/80 dark:border-brand-deep flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 text-xs text-brand-deep dark:text-brand-light">
                    <i class="fas fa-info-circle text-base text-brand-deep dark:text-brand-light mt-0.5"></i>
                    <div>
                        <span class="font-bold">Info Perhitungan:</span> Ranking khusus untuk customer kategori <strong>Reseller</strong>. Skor performa dikalkulasi berdasarkan kombinasi volume total pembelian dikurangi rasio retur.
                    </div>
                </div>
                <button type="button" @click="show = false" class="text-brand-deep dark:text-brand-light hover:opacity-75">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Ranking Subtitle -->
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-200">
                <span>Ranking Periode: {{ $bulan ?? 'Bulan Ini' }} {{ $selectedYear }}</span>
                <span class="text-slate-400">Total Customer: {{ $ranking->total() }}</span>
            </div>

            <!-- Ranking Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900">
                <div class="overflow-x-auto min-h-[450px]">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] font-extrabold text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 text-center w-24">Peringkat</th>
                                <th scope="col" class="px-5 py-3.5">Nama Customer Reseller</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Total Pembelian</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Total Retur</th>
                                <th scope="col" class="px-5 py-3.5 text-center w-32">Skor Performa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($ranking as $row)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60 transition-colors">
                                    <td class="px-5 py-3.5 font-black text-center">
                                        @if($row->peringkat == 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600 shadow-md">
                                                <i class="fas fa-trophy text-sm"></i>
                                            </span>
                                        @elseif($row->peringkat == 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-600 shadow-sm">
                                                <i class="fas fa-medal text-sm"></i>
                                            </span>
                                        @elseif($row->peringkat == 3)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-900/20 text-amber-700 shadow-sm">
                                                <i class="fas fa-award text-sm"></i>
                                            </span>
                                        @else
                                            <span class="text-slate-500 font-extrabold">#{{ $row->peringkat }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-white">
                                        {{ $row->nama_customer }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-brand-deep dark:text-brand-light">
                                        Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-rose-500 dark:text-rose-400">
                                        Rp {{ number_format($row->total_retur, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                                            {{ $row->skor_akhir }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        Belum ada data performa customer untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Nav -->
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2" aria-label="Table navigation">
                {{ $ranking->withQueryString()->links() }}
            </nav>
        </div>
    </div>
@endsection
