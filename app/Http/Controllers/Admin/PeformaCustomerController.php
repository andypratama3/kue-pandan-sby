<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\RegionContext;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class PeformaCustomerController extends Controller
{
    /**
     * Export ranking performa customer ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $admin = auth()->user();
        $regionId = RegionContext::regionId();

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $startOfMonth = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endOfMonth = now()->setYear($year)->setMonth($month)->endOfMonth();

        // Ambil data ranking customer untuk rentang bulan/tahun terpilih
        $ranking = $this->calculateCustomerPerformance($startOfMonth, $endOfMonth);

        $bulan = $months[str_pad($month, 2, '0', STR_PAD_LEFT)].' '.$year;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.admin.peforma-customer.export-peforma-customer', [
            'ranking' => $ranking,
            'bulan' => $bulan,
        ]);

        return $pdf->download('peforma-customer-'.$bulan.'.pdf');
    }

    /**
     * Hitung dan ranking performa semua customer berdasarkan total pembelian dan total retur dalam 1 bulan terakhir.
     *
     * @return \Illuminate\Support\Collection
     */
    public function calculateCustomerPerformance($startDate = null, $endDate = null)
    {
        // Jika tidak ada parameter, default ke 1 bulan terakhir
        if (! $startDate || ! $endDate) {
            $now = now();
            $startDate = $now->copy()->subMonth();
            $endDate = $now;
        }

        // Ambil region admin yang sedang login
        $regionId = RegionContext::regionId();

        // Ambil data total pembelian per customer untuk region tertentu
        $orders = DB::table('orders')
            ->select('customer_id', DB::raw('SUM(total_amount) as total_pembelian'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('region_id', $regionId)
            ->where('status', 'diverifikasi_admin')
            ->groupBy('customer_id')
            ->get();

        // Ambil data total retur per customer (dari order_returns JOIN orders untuk dapat customer_id) untuk region tertentu
        $returns = DB::table('order_returns')
            ->join('orders', 'order_returns.order_id', '=', 'orders.id')
            ->select('orders.customer_id', DB::raw('SUM(order_returns.total_amount_returned) as total_retur'))
            ->whereBetween('order_returns.created_at', [$startDate, $endDate])
            ->where('orders.region_id', $regionId)
            ->where('order_returns.status', '!=', 'ditolak')
            ->groupBy('orders.customer_id')
            ->get();

        // Gabungkan data pembelian dan retur ke dalam satu array indexed by customer_id
        $orderMap = $orders->keyBy('customer_id');
        $returnMap = $returns->keyBy('customer_id');

        // Ambil semua customer yang pernah transaksi/retur dalam periode
        $customerIds = $orderMap->keys()->merge($returnMap->keys())->unique();

        // Ambil nama customer dan kategori, filter region
        $customers = Customer::whereIn('id', $customerIds)
            ->where('region_id', $regionId)
            ->with('category')
            ->get()
            ->keyBy('id');

        // Filter hanya reseller (exclude supermarket) SEKALIGUS dapatkan id reseller
        $resellerIds = $customers->filter(function ($customer) {
            return strtolower($customer->category->name ?? '') === 'reseller';
        })->keys();

        // Cari pembelian tertinggi HANYA dari reseller
        $pembelian_tertinggi = $resellerIds->map(function ($id) use ($orderMap) {
            return (float) ($orderMap[$id]->total_pembelian ?? 0);
        })->max() ?: 0;

        $result = collect();

        foreach ($resellerIds as $customer_id) {
            $customer = $customers[$customer_id] ?? null;
            if (! $customer) {
                continue;
            }

            $nama_customer = $customer->name;
            $total_pembelian = (float) ($orderMap[$customer_id]->total_pembelian ?? 0);
            $total_retur = (float) ($returnMap[$customer_id]->total_retur ?? 0);

            // Skor Pembelian
            $skor_pembelian = $pembelian_tertinggi > 0 ? ($total_pembelian / $pembelian_tertinggi) * 100 : 0;

            // Skor Anti-Retur
            if ($total_pembelian > 0) {
                $skor_anti_retur = (1 - ($total_retur / $total_pembelian)) * 100;
                $skor_anti_retur = $skor_anti_retur < 0 ? 0 : $skor_anti_retur;
            } else {
                $skor_anti_retur = 0;
            }

            // Skor Akhir
            $skor_akhir = ($skor_pembelian * 0.7) + ($skor_anti_retur * 0.3);

            $result->push((object) [
                'nama_customer' => $nama_customer,
                'total_pembelian' => $total_pembelian,
                'total_retur' => $total_retur,
                'skor_akhir' => round($skor_akhir, 2),
            ]);
        }

        // Urutkan berdasarkan skor akhir desc, lalu total pembelian desc
        $sorted = $result->sortByDesc(function ($item) {
            return [$item->skor_akhir, $item->total_pembelian];
        })->values();

        // Tambahkan peringkat
        foreach ($sorted as $i => $item) {
            $item->peringkat = $i + 1;
        }

        return $sorted;
    }

    /**
     * Display a listing of customer performance.
     */
    public function index(Request $request) // [!code focus]
    {
        // Ambil filter bulan & tahun dari request, default ke bulan & tahun sekarang
        $selectedMonth = $request->input('month', now()->format('m')); // [!code focus]
        $selectedYear = $request->input('year', now()->format('Y')); // [!code focus]

        // Daftar bulan (dalam bahasa Indonesia)
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        // Daftar tahun: tahun sekarang sampai 10 tahun ke depan
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear + 9);

        // Rentang tanggal awal & akhir
        $startDate = now()->setYear($selectedYear)->setMonth($selectedMonth)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();

        // Ambil data ranking customer untuk rentang bulan/tahun terpilih
        $ranking = $this->calculateCustomerPerformance($startDate, $endDate);

        // [!code focus:start]
        // BUAT PAGINASI MANUAL
        $perPage = 10; // Tentukan jumlah item per halaman
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $ranking->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedRanking = new LengthAwarePaginator(
            $currentPageItems,
            count($ranking),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
        // [!code focus:end]

        // Nama bulan untuk judul
        $bulan = $months[$selectedMonth].' '.$selectedYear;

        return view('dashboard.admin.peforma-customer.peforma-customer', compact(
            'paginatedRanking', 'bulan', 'months', 'years', 'selectedMonth', 'selectedYear' // [!code focus]
        ))->with('ranking', $paginatedRanking); // [!code focus]
    }

    /**
     * Display the specified customer performance.
     */
    public function show($customer)
    {
        return redirect()->route('admin.peforma-customer.index');
    }
}
