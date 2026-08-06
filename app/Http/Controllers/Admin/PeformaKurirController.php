<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Support\RegionContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class PeformaKurirController extends Controller
{
    /**
     * Export ranking performa kurir ke PDF.
     */
    public function exportPdf(Request $request)
    {
        try {
            $admin = auth()->user();
            $regionId = RegionContext::regionId();

            /**
             * =========================
             * Handle Date Range
             * =========================
             */
            $dates = explode(' - ', $request->daterange ?? '');

            $startDate = ! empty($dates[0])
                ? Carbon::parse($dates[0])->startOfDay()
                : Carbon::now()->startOfMonth()->startOfDay();

            $endDate = ! empty($dates[1])
                ? Carbon::parse($dates[1])->endOfDay()
                : Carbon::now()->endOfDay();

            /**
             * =========================
             * Query Orders
             * =========================
             */
            $orders = Order::with(['items', 'customer', 'createdBy', 'returns'])
                ->where('region_id', $regionId)
                ->where('status', 'diverifikasi_admin')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            /**
             * =========================
             * Ranking Kurir
             * =========================
             */
            $ranking = $orders
                ->groupBy('created_by_user_id')
                ->map(function ($orders, $kurirId) {
                    $kurir = $orders->first()->createdBy;

                    return [
                        'kurir_id' => $kurirId,
                        'nama_kurir' => $kurir?->name ?? '-',
                        'jumlah_order' => $orders->count(),
                        'total' => $orders->sum(function ($o) {
                            $activeReturn = $o->returns->where('status', '!=', 'ditolak')->first();

                            return $o->total_amount - ($activeReturn?->total_amount_returned ?? 0);
                        }),
                        'orders' => $orders,
                    ];
                })
                ->sortByDesc('jumlah_order')
                ->values();

            /**
             * =========================
             * Ambil Customer Count + Rank
             * =========================
             */
            $kurirs = User::whereIn('id', $ranking->pluck('kurir_id'))
                ->withCount('customers')
                ->get()
                ->keyBy('id');

            $ranking = $ranking->map(function ($item, $i) use ($kurirs) {
                $user = $kurirs[$item['kurir_id']] ?? null;
                $item['total_customer'] = $user?->customers_count ?? 0;
                $item['rank'] = $i + 1;

                return $item;
            });

            /**
             * =========================
             * Generate PDF
             * =========================
             */
            $pdf = Pdf::loadView('dashboard.admin.peforma-kurir.export-peforma-kurir', [
                'ranking' => $ranking,
                'daterange' => $request->daterange
                    ?? $startDate->toDateString().' - '.$endDate->toDateString(),
            ])->setPaper('a4', 'portrait')->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 72,
                'defaultFont' => 'DejaVu Sans',
            ]);

            $filename = sprintf(
                'performa-kurir_%s_to_%s.pdf',
                $startDate->toDateString(),
                $endDate->toDateString()
            );

            return $pdf->download($filename);
        } catch (\Throwable $e) {

            // Log error (WAJIB, biar gampang debug)
            Log::error('Export PDF Performa Kurir Gagal', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'error',
                'Gagal mengekspor PDF. Silakan coba lagi.'
            );
        }
    }

    public function exportPdfByCourier(Request $request, $id)
    {
        try {
            $admin = auth()->user();
            $regionId = RegionContext::regionId();

            $dates = explode(' - ', $request->daterange ?? '');

            $startDate = ! empty($dates[0])
                ? Carbon::parse($dates[0])->startOfDay()
                : Carbon::now()->startOfMonth()->startOfDay();

            $endDate = ! empty($dates[1])
                ? Carbon::parse($dates[1])->endOfDay()
                : Carbon::now()->endOfDay();

            $orders = Order::with(['items', 'customer', 'createdBy', 'returns'])
                ->where('created_by_user_id', $id)
                ->where('region_id', $regionId)
                ->where('status', 'diverifikasi_admin')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            /**
             * =========================
             * Ranking Kurir
             * =========================
             */
            $ranking = $orders
                ->groupBy('created_by_user_id')
                ->map(function ($orders, $kurirId) {
                    $kurir = $orders->first()->createdBy;

                    return [
                        'kurir_id' => $kurirId,
                        'nama_kurir' => $kurir?->name ?? '-',
                        'jumlah_order' => $orders->count(),
                        'total' => $orders->sum(function ($o) {
                            $activeReturn = $o->returns->where('status', '!=', 'ditolak')->first();

                            return $o->total_amount - ($activeReturn?->total_amount_returned ?? 0);
                        }),
                        'orders' => $orders,
                    ];
                })
                ->sortByDesc('jumlah_order')
                ->values();

            $kurirs = User::whereIn('id', $ranking->pluck('kurir_id'))
                ->withCount('customers')
                ->get()
                ->keyBy('id');

            $ranking = $ranking->map(function ($item, $i) use ($kurirs) {
                $user = $kurirs[$item['kurir_id']] ?? null;
                $item['total_customer'] = $user?->customers_count ?? 0;
                $item['rank'] = $i + 1;

                return $item;
            });

            /**
             * =========================
             * Generate PDF
             * =========================
             */
            $pdf = Pdf::loadView('dashboard.admin.peforma-kurir.export-peforma-kurir', [
                'ranking' => $ranking,
                'daterange' => $request->daterange
                    ?? $startDate->toDateString().' - '.$endDate->toDateString(),
            ])->setPaper('a4', 'portrait')->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 72,
                'defaultFont' => 'DejaVu Sans',
            ]);

            $filename = sprintf(
                'performa-kurir_%s_to_%s.pdf',
                $startDate->toDateString(),
                $endDate->toDateString()
            );

            return $pdf->download($filename);
        } catch (\Throwable $e) {

            // Log error (WAJIB, biar gampang debug)
            Log::error('Export PDF Performa Kurir Gagal', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'error',
                'Gagal mengekspor PDF. Silakan coba lagi.'
            );
        }
    }

    /**
     * Display a listing of courier performance.
     */
    public function index(Request $request)
    {
        $admin = auth()->user();
        $regionId = RegionContext::regionId();

        $dates = explode(' - ', $request->daterange ?? '');

        $startDate = ! empty($dates[0])
            ? $dates[0]
            : Carbon::now()->startOfMonth()->toDateString();

        $endDate = ! empty($dates[1])
            ? $dates[1]
            : Carbon::now()->toDateString();

        $orders = \App\Models\Order::where('region_id', $regionId)
            ->where('status', 'diverifikasi_admin')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            })
            ->get();

        $ranking = $orders->groupBy('created_by_user_id')
            ->map(function ($orders, $kurirId) {

                $totalAmount = $orders->sum('total_amount');

                return [
                    'kurir_id' => $kurirId,
                    'jumlah_order' => $orders->count(),
                    'total' => $totalAmount,
                ];
            })
            ->sortByDesc('jumlah_order')
            ->values();

        $kurirIds = $ranking->pluck('kurir_id')->all();
        $kurirs = \App\Models\User::whereIn('id', $kurirIds)->get()->keyBy('id');

        $ranking = $ranking->map(function ($item, $i) use ($kurirs) {
            $user = $kurirs[$item['kurir_id']] ?? null;
            $item['nama_kurir'] = $user ? $user->name : '-';
            $item['total_customer'] = $user ? $user->customers()->count() : 0;
            $item['rank'] = $i + 1;

            return $item;
        });

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

        return view('dashboard.admin.peforma-kurir.peforma-kurir', [
            'ranking' => $paginatedRanking, // [!code focus]
        ]);
    }

    /**
     * Display the specified courier performance.
     */
    public function show($kurir)
    {
        return redirect()->route('admin.peforma-kurir.index');
    }
}
