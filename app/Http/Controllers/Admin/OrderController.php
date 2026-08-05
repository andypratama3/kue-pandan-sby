<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan pada region admin.
     * Logika peringatan diperbarui sesuai kategori customer.
     */
    public function index(Request $request)
    {
        $admin = Auth::user();
        $orders = Order::with(['customer.category', 'createdBy'])
            ->where('region_id', $admin->region_id)
            ->where('status', '!=', 'diverifikasi_admin');

        $search = $request->input('search');

        $newOrdersCount = Order::where('region_id', $admin->region_id)
            ->where('status', 'pending')->count();

        foreach ($orders as $order) {
            $order->show_warning = false;
            if (is_null($order->payment_proof) && $order->customer && $order->customer->category) {
                $categoryName = strtolower($order->customer->category->name);
                $warningDays = 0;
                if ($categoryName === 'reseller') {
                    $warningDays = 5;
                } elseif ($categoryName === 'supermarket') {
                    $warningDays = 28;
                }
                if ($warningDays > 0) {
                    $daysSinceCreation = Carbon::parse($order->created_at)->diffInDays(now());
                    if ($daysSinceCreation >= $warningDays) {
                        $order->show_warning = true;
                    }
                }
            }
        }

        $orders->when($search, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($sub) use ($searchTerm) {
                        $sub->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('company_name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('createdBy', function ($sub) use ($searchTerm) {
                        $sub->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        });


        $orders = $orders->latest()->get();

        return view('dashboard.admin.order-list.index', compact('orders', 'newOrdersCount'));
    }

    /**
     * Mengambil detail pesanan untuk modal verifikasi.
     * Diperbarui agar selaras dengan detail yang ada di PesananController.
     */
    public function details($id)
    {
        $admin = Auth::user();
        try {
            // Eager loading tetap sama
            $order = Order::with([
                'customer',
                'createdBy',
                'items',
                'returns',
                'returns.returnedProducts.product',
                'returns.returnedProducts.variant'
            ])
                ->where('region_id', $admin->region_id)
                ->findOrFail($id);

            $paidAtLabel = '';
            $paidAtFormatted = null;

            if ($order->paid_at) {
                $createdAt = Carbon::parse($order->created_at)->startOfDay();
                $paidAt = Carbon::parse($order->paid_at)->startOfDay();
                $diffInDays = $createdAt->diffInDays($paidAt);

                if ($diffInDays == 1) $paidAtLabel = ' (Harian)';
                elseif ($diffInDays >= 2 && $diffInDays <= 7) $paidAtLabel = ' (Mingguan)';

                $paidAtFormatted = Carbon::parse($order->paid_at)->isoFormat('D MMMM YYYY, HH:mm');
            }

            $activeReturn = $order->returns()->where('status', '!=', 'ditolak')->latest()->first();

            $formattedOrder = [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->isoFormat('D MMMM YYYY, HH:mm'),
                'paid_at' => $paidAtFormatted,
                'paid_at_label' => $paidAtLabel,
                // 'payment_proof' => $order->payment_proof,
                'payment_proof' => $order->payment_proof ? asset('storage/' . preg_replace('#^(storage/|public/)#', '', $order->payment_proof)) : null,
                'note' => $order->note,
                'customer' => [
                    'name' => $order->customer->name ?? 'N/A',
                    'company_name' => $order->customer->company_name ?? null,
                    'phone' => $order->customer->phone ?? 'N/A',
                    'address' => $order->customer->address ?? 'N/A',
                ],
                'kurir_name' => $order->createdBy->name ?? '-',
                'items' => $order->items->map(fn($item) => [
                    'id' => $item->product_id,
                    'name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'variant_name' => $item->variant_name,
                ])->toArray(),

                // Perbaikan pada bagian ini
                'return_details' => $activeReturn ? [
                    // 'return_proof' => $activeReturn->return_proof,
                    'return_proof' => $activeReturn->return_proof ? asset('storage/' . preg_replace('#^(storage/|public/)#', '', $activeReturn->return_proof)) : null,
                    'total_amount_returned' => $activeReturn->total_amount_returned,
                    'returned_products' => $activeReturn->returnedProducts->map(function ($p) {
                        // **PENGECEKAN AMAN**
                        // Cek apakah relasi product ada sebelum mengakses propertinya
                        $productName = $p->product ? $p->product->name : 'Produk Telah Dihapus';
                        // Cek apakah relasi variant ada
                        $variantName = $p->variant ? $p->variant->name : null;

                        return [
                            'name' => $productName,
                            'variant_name' => $variantName,
                            'quantity' => $p->quantity,
                            'price' => $p->price
                        ];
                    })->toArray()
                ] : null,
            ];

            return response()->json($formattedOrder);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error fetching order details for order ID ' . $id . ' by admin: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal.'], 500);
        }
    }

    /**
     * Verifikasi pesanan (ubah status menjadi diverifikasi_admin).
     */
    public function verify($id)
    {
        $admin = Auth::user();
        // DIUBAH: Mencari pesanan dalam dua kemungkinan status
        $order = Order::where('region_id', $admin->region_id)
            ->whereIn('status', ['selesai', 'menunggu_verifikasi_admin']) // Perbaikan di sini
            ->findOrFail($id);

        $order->status = 'diverifikasi_admin';
        $order->save();
        return response()->json(['message' => 'Pesanan berhasil diverifikasi.']);
    }

    /**
     * Tolak verifikasi pesanan (kembalikan status ke diterima_pembeli).
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|min:10',
        ]);

        $admin = Auth::user();
        $order = Order::where('region_id', $admin->region_id)
            ->whereIn('status', ['selesai', 'menunggu_verifikasi_admin'])
            ->findOrFail($id);

        // (Logika penolakan lainnya tetap sama)
        $returnRequest = $order->returns()->where('status', 'menunggu_konfirmasi')->first();
        if ($returnRequest) {
            if ($returnRequest->return_proof) {
                Storage::disk('public')->delete($returnRequest->return_proof);
            }
            $returnRequest->status = 'ditolak';
            $returnRequest->admin_notes = 'Verifikasi retur ditolak oleh admin.';
            $returnRequest->save();
            $order->status = 'diterima_pembeli';
        } else {
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }
            $order->status = 'diterima_pembeli';
            $order->payment_proof = null;
            $order->paid_at = null;
        }

        $order->rejection_note = $validated['rejection_note'];
        $order->save();

        // [!code block:start]
        // Ganti respons JSON dengan redirect dan pesan flash
        return redirect()->route('admin.orders.index')->with('success', 'Verifikasi pesanan berhasil ditolak.');
        // [!code block:end]
    }

    /**
     * Hapus pesanan secara permanen dari database.
     * Termasuk menghapus file bukti bayar/retur yang tersimpan.
     */
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
            $order->delete();

            DB::commit();
            return response()->json(['message' => 'Pesanan berhasil dihapus secara permanen.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus pesanan ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus pesanan.'], 500);
        }
    }
}
