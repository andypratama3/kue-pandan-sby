@extends('layouts.argon')
@section('title', 'Performa Kurir')
@section('page_title', 'Performa Tim Kurir')

@section('content')
    <div class="space-y-6">
        <!-- Main Card Container -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6 min-h-[700px]">
            
            <!-- Header Section & Date Range Form -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-chart-bar text-base"></i>
                        </div>
                        <span>Peringkat Performa Pengiriman Kurir</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Pantau produktivitas pengantaran dan omset penagihan kurir di Cabang {{ Auth::user()->region->name ?? 'N/A' }}.
                    </p>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="daterange" id="daterange" value="{{ request('daterange') }}"
                            placeholder="Pilih Rentang Tanggal..."
                            class="w-full px-3.5 py-2 text-xs font-semibold border border-slate-200 rounded-2xl bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-2 focus:ring-brand"
                            autocomplete="off">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-deep rounded-2xl shadow-md transition-all">
                        <i class="fas fa-filter"></i> Lihat
                    </button>
                </form>
            </div>

            <!-- Informational Banner -->
            <div x-data="{ show: true }" x-show="show" x-transition
                class="p-4 rounded-2xl bg-mint/80 dark:bg-brand-deep/40 border border-brand-light/80 dark:border-brand-deep flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 text-xs text-brand-deep dark:text-brand-light">
                    <i class="fas fa-info-circle text-base text-brand-deep dark:text-brand-light mt-0.5"></i>
                    <div>
                        <span class="font-bold">Sistem Poin:</span> Setiap pesanan yang berhasil diantar dan <strong>verifikasi admin</strong> memberikan poin produktivitas. Kurir dengan omset & pesanan selesai tertinggi berada di peringkat teratas.
                    </div>
                </div>
                <button type="button" @click="show = false" class="text-brand-deep dark:text-brand-light hover:opacity-75">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Subtitle -->
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-200">
                <span>Ranking Performa Kurir: {{ request('daterange') ?: 'Semua Waktu' }}</span>
                <span class="text-slate-400">Total Personel: {{ $ranking->total() }}</span>
            </div>

            <!-- Ranking Table Container -->
            <div class="overflow-hidden border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900">
                <div class="overflow-x-auto min-h-[450px]">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] font-extrabold text-slate-400 uppercase bg-slate-50/80 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 text-center w-24">Peringkat</th>
                                <th scope="col" class="px-5 py-3.5">Nama Kurir</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Total Customer Dilayani</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Pesanan Selesai</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Total Setoran</th>
                                <th scope="col" class="px-5 py-3.5 text-center w-28">Export Rekap</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($ranking as $row)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60 transition-colors">
                                    <td class="px-5 py-3.5 font-black text-center">
                                        @if ($row['rank'] == 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600 shadow-md">
                                                <i class="fas fa-trophy text-sm"></i>
                                            </span>
                                        @elseif($row['rank'] == 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-600 shadow-sm">
                                                <i class="fas fa-medal text-sm"></i>
                                            </span>
                                        @elseif($row['rank'] == 3)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-900/20 text-amber-700 shadow-sm">
                                                <i class="fas fa-award text-sm"></i>
                                            </span>
                                        @else
                                            <span class="text-slate-500 font-extrabold">#{{ $row['rank'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-full bg-brand-light dark:bg-brand-deep text-brand-deep dark:text-brand-light font-extrabold flex items-center justify-center text-[11px]">
                                                {{ strtoupper(substr($row['nama_kurir'], 0, 1)) }}
                                            </div>
                                            <span>{{ $row['nama_kurir'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center font-bold text-slate-600 dark:text-slate-300">
                                        {{ $row['total_customer'] }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-light text-brand-deep dark:bg-brand-deep dark:text-brand-light">
                                            {{ $row['jumlah_order'] }} Pesanan
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black text-brand-deep dark:text-brand-light">
                                        Rp {{ number_format($row['total'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <form action="peforma-kurir/export/{{ $row['kurir_id'] }}/pdf?daterange={{ request('daterange') }}" method="get">
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm">
                                                <i class="fas fa-file-pdf text-[10px]"></i> PDF
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                        Belum ada data performa kurir untuk rentang tanggal {{ request('daterange') }}.
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
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush
