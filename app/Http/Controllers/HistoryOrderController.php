<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class HistoryOrderController extends Controller
{
    /**
     * Endpoint JSON untuk detail history pesanan & retur (untuk modal show)
     * VERSI DIPERBAIKI
     */
    public function details(Order $order)
    {
        // Pastikan admin/kurir hanya bisa mengakses order yang relevan dengan mereka
        $user = Auth::user();
        if ($user->hasRole('admin') && $user->region_id !== $order->region_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($user->hasRole('kurir') && $order->created_by_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        try {
            // Eager load semua relasi yang dibutuhkan untuk modal
            $order->load([
                'customer',
                'createdBy', // Relasi untuk kurir
                'items',
                'returns' => function ($query) {
                    // Ambil retur yang aktif (tidak ditolak) dan urutkan dari yang terbaru
                    $query->where('status', '!=', 'ditolak')->latest();
                },
                'returns.returnedProducts.product', // Relasi ke produk yg diretur
                'returns.returnedProducts.variant'  // Relasi ke varian yg diretur
            ]);

            $paidAtFormatted = $order->paid_at ? Carbon::parse($order->paid_at)->isoFormat('D MMMM YYYY, HH:mm') : 'Belum Lunas';

            // Ambil retur aktif pertama dari koleksi yang sudah di-load
            $activeReturn = $order->returns->first();

            // Format data agar mudah dikonsumsi oleh JavaScript
            $formattedOrder = [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'customer_name' => $order->customer->name ?? 'N/A',
                'customer_phone' => $order->customer->phone ?? 'N/A',
                'customer_company' => $order->customer->company_name ?? 'N/A',
                'customer_address' => $order->customer->address ?? 'N/A',
                'payment_method' => $order->payment_method ?? '-',
                'total_amount' => $order->total_amount ?? 0,
                'note' => $order->note,
                'created_at' => $order->created_at->isoFormat('D MMMM YYYY, HH:mm'),
                'paid_at' => $paidAtFormatted,

                // --- PERBAIKAN PATH BUKTI PEMBAYARAN ---
                // 'payment_proof_url' => $order->payment_proof ? Storage::url(preg_replace('#^(storage/|public/)#', '', $order->payment_proof)) : null,
                'payment_proof_url' => $order->payment_proof ? asset('storage/' . preg_replace('#^(storage/|public/)#', '', $order->payment_proof)) : null,

                'items' => $order->items->map(fn($item) => [
                    'name' => $item->product_name,
                    'variant' => $item->variant_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ])->toArray(),

                // Sertakan detail retur jika ada
                'return_details' => $activeReturn ? [
                    'status' => $activeReturn->status,
                    'total_amount_returned' => $activeReturn->total_amount_returned,

                    // --- PERBAIKAN PATH BUKTI RETUR ---
                    // 'return_proof_url' => $activeReturn->return_proof ? Storage::url(preg_replace('#^(storage/|public/)#', '', $activeReturn->return_proof)) : null,
                    'return_proof_url' => $activeReturn->return_proof ? asset('storage/' . preg_replace('#^(storage/|public/)#', '', $activeReturn->return_proof)) : null,

                    'returned_products' => $activeReturn->returnedProducts->map(function ($p) {
                        $productName = $p->product ? $p->product->name : 'Produk Telah Dihapus';
                        $variantName = $p->variant ? $p->variant->name : null;
                        return [
                            'name' => $productName,
                            'variant' => $variantName,
                            'quantity' => $p->quantity,
                            'price' => $p->price,
                            'subtotal' => $p->subtotal,
                        ];
                    })->toArray()
                ] : null,
            ];

            return response()->json($formattedOrder);
        } catch (\Exception $e) {
            Log::error('Error fetching history details for order ID ' . $order->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal.'], 500);
        }
    }

    public function downloadInvoice($orderId)
    {
        $order = \App\Models\Order::with([
            'customer',
            'createdBy',
            'items',
            'returns' => function ($query) {
                $query->where('status', '!=', 'ditolak')->latest();
            },
            'returns.returnedProducts.product',
            'returns.returnedProducts.variant',
        ])->findOrFail($orderId);
        $isPdf = true;
        $pdf = \PDF::loadView('dashboard.admin.historys.invoice', compact('order', 'isPdf'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'margin_top'    => 20,
                'margin_right'  => 20,
                'margin_bottom' => 20,
                'margin_left'   => 20,
            ]);
        $customerName = preg_replace('/[^A-Za-z0-9]/', '', $order->customer->name ?? 'Customer');
        $invoiceNumber = str_replace(['/', '\\'], '-', $order->invoice_number);
        $filename = $customerName . '-' . $invoiceNumber . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy($id)
    {
        $admin = Auth::user();
        DB::beginTransaction();

        try {
            $order = Order::where('region_id', $admin->region_id)->with('returns')->findOrFail($id);

            // 1. Hapus bukti pembayaran utama
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            // 2. Hapus bukti retur (jika ada)
            foreach ($order->returns as $return) {
                if ($return->return_proof) {
                    Storage::disk('public')->delete($return->return_proof);
                }
            }

            // 3. Hapus data pesanan dari database
            // (Relasi seperti order_items dan returns akan terhapus otomatis jika foreign key di-set cascade)
            $invoiceNumber = $order->invoice_number;
            $order->delete();

            DB::commit();
            $routeName = 'admin.historys.index';
            return redirect()->route($routeName)->with('success', 'Pesanan "' . $invoiceNumber . '" berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus pesanan ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus pesanan.'], 500);
        }
    }

    public function invoice($orderId)
    {
        $order = \App\Models\Order::with([
            'customer',
            'createdBy',
            'items',
            'returns' => function ($query) {
                $query->where('status', '!=', 'ditolak')->latest();
            },
            'returns.returnedProducts.product',
            'returns.returnedProducts.variant',
        ])->findOrFail($orderId);
        $isPdf = false;
        return view('dashboard.admin.historys.invoice', compact('order', 'isPdf'));
    }

    public function downloadHistoryPdf(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        // Query dasar - tambahkan eager load 'returns'
        $ordersQuery = Order::with(['customer', 'createdBy', 'returns' => fn($q) => $q->where('status', '!=', 'ditolak')->latest()])
            ->where('status', 'diverifikasi_admin')
            ->where('region_id', $user->region_id);

        // Terapkan filter bulan dan tahun
        $ordersQuery->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear);

        $orders = $ordersQuery->latest()->get();

        // [!code focus:start]
        // TAMBAHKAN LOGIKA INI - Kalkulasi total akhir untuk setiap pesanan
        foreach ($orders as $order) {
            $order->has_return = $order->returns->isNotEmpty();
            $order->final_total = $order->has_return ? $order->total_amount - $order->returns->first()->total_amount_returned : $order->total_amount;
        }
        // [!code focus:end]

        // Siapkan data untuk view PDF
        $bulan = $months[$selectedMonth] . ' ' . $selectedYear;
        $regionName = $user->region->name ?? 'Semua Region';

        $pdf = Pdf::loadView('dashboard.admin.historys.history-export', [
            'orders' => $orders,
            'bulan' => $bulan,
            'regionName' => $regionName
        ]);

        return $pdf->download('history-pesanan-' . $bulan . '.pdf');
    }

    /**
     * MODIFIKASI: Method index untuk menangani filter
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->hasRole('admin') ? 'admin' : 'kurir';

        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));
        $selectedCourier = $request->input('courier');
        $search = $request->input('search');

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 5);

        $ordersQuery = Order::with(['customer', 'createdBy', 'returns' => fn($q) => $q->where('status', '!=', 'ditolak')->latest()])
            ->where('status', 'diverifikasi_admin');

        if ($role === 'admin') {
            $ordersQuery->where('region_id', $user->region_id);
        } else { // Untuk kurir
            $ordersQuery->where('created_by_user_id', $user->id);
        }

        $couriers = [];
        foreach ($ordersQuery->get() as $order) {
            if ($order->createdBy) {
                $couriers[$order->createdBy->id] = [
                    "id" => $order->createdBy->id,
                    "name" => $order->createdBy->name
                ];
            }
        }

        $ordersQuery->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear);

        $ordersQuery->when($selectedCourier, function ($query, $selectedCourier) {
            $query->where('created_by_user_id', "=", $selectedCourier);
        });

        $ordersQuery->when($search, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('company_name', 'like', "%{$searchTerm}%");
                    });
            });
        });


        $couriers = array_values($couriers);

        $orders = $ordersQuery->latest()->paginate(10);

        foreach ($orders as $order) {
            $order->has_return = $order->returns->isNotEmpty();
            $order->final_total = $order->has_return ? $order->total_amount - $order->returns->first()->total_amount_returned : $order->total_amount;
            $order->payment_status = $order->paid_at
                ? ['text' => 'Lunas', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300']
                : ['text' => 'Belum Lunas', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'];
        }


        // [!code focus:start]
        // MODIFIKASI: Handle request AJAX untuk live search
        if ($request->ajax()) {
            $response = [];

            if ($role === 'admin') {
                $desktopHtml = view('dashboard.admin.historys._table_rows', compact('orders'))->render();
                $response['desktop_html'] = $desktopHtml;
            } else { // Untuk Kurir, render keduanya
                $desktopHtml = view('dashboard.kurir.historys._table_rows', compact('orders'))->render();
                $mobileHtml = view('dashboard.kurir.historys._card_view', compact('orders'))->render();
                $response['desktop_html'] = $desktopHtml;
                $response['mobile_html'] = $mobileHtml;
            }

            return response()->json($response);
        }
        // [!code focus:end]

        $viewData = compact('orders', 'months', 'years', 'couriers', 'selectedMonth', 'selectedYear', 'selectedCourier');

        $viewName = $role === 'admin'
            ? 'dashboard.admin.historys.index'
            : 'dashboard.kurir.historys.index';

        return view($viewName, $viewData);
    }
}
