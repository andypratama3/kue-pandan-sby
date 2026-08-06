<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Impor Auth
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException; // [!code ++]

class ReturnController extends Controller
{
    // Isolasi data: retur hanya boleh dilakukan oleh kurir pembuat pesanan.
    private function assertOwnOrder(Order $order): void
    {
        $user = Auth::user();
        if (! $user || $order->created_by_user_id !== $user->id || $order->region_id !== $user->region_id) {
            abort(403, 'AKSES DITOLAK');
        }
    }

    // [!code block:start]
    // --- Helper Functions for Timezone ---
    private function getUserTimezone()
    {
        $user = Auth::user();
        if (! $user || is_null($user->region_id)) {
            return config('app.timezone', 'UTC');
        }

        switch ($user->region_id) {
            case 3: // Denpasar
                return 'Asia/Makassar'; // WITA
            case 1: // Surabaya
            case 2: // Malang
                return 'Asia/Jakarta'; // WIB
            default:
                return config('app.timezone', 'UTC');
        }
    }

    private function nowInUserTimezone()
    {
        return Carbon::now($this->getUserTimezone());
    }
    // [!code block:end]

    public function requestReturn(Request $request, Order $order)
    {
        $this->assertOwnOrder($order);

        $validated = $request->validate([
            'return_quantities' => 'required|array|min:1',
            'return_quantities.*' => 'required|integer|min:1',
            'reason' => 'string|min:10|nullable',
        ]);

        if ($order->status !== 'diterima_pembeli') {
            return response()->json(['message' => 'Hanya pesanan dengan status "Diterima Pembeli" yang bisa mengajukan retur.'], 422);
        }

        if ($order->returns()->where('status', '!=', 'ditolak')->exists()) {
            return response()->json(['message' => 'Pesanan ini sudah memiliki pengajuan retur yang aktif.'], 422);
        }

        DB::beginTransaction();
        try {
            $kurir = Auth::user(); // Dapatkan user (kurir) yang sedang login

            // Buat Catatan Pengajuan Retur Utama
            $orderReturn = $order->returns()->create([
                'status' => 'menunggu_konfirmasi',
                'courier_id' => $kurir->id,
                'region_id' => $kurir->region_id, // Asumsi relasi `region_id` ada di model User
                'reason' => $validated['reason'],
            ]);

            // DIUBAH: Menggunakan relasi `items()` yang benar dan membuat key
            $orderItems = $order->items()->with('product')->get()->keyBy(function ($item) {
                return $item->product_id.'-'.($item->variant_id ?? 0);
            });

            $totalAmountReturned = 0;

            // Simpan Detail Produk yang Diretur
            foreach ($validated['return_quantities'] as $key => $quantity) {
                if ($quantity <= 0) {
                    continue;
                }

                if (! isset($orderItems[$key])) {
                    throw ValidationException::withMessages(['return_quantities' => "Produk dengan key '{$key}' tidak ditemukan."]);
                }

                // DIUBAH: Menggunakan variabel $orderItem
                $orderItem = $orderItems[$key];

                // DIUBAH: Mengakses properti langsung dari $orderItem
                if ($quantity > $orderItem->quantity) {
                    throw ValidationException::withMessages(['return_quantities' => "Jumlah retur untuk '{$orderItem->product_name}' melebihi jumlah pembelian."]);
                }

                [$productId, $variantId] = explode('-', $key);

                // DIUBAH: Mengambil harga dari $orderItem
                $price = $orderItem->price;
                $subtotal = $price * $quantity;
                $totalAmountReturned += $subtotal; // Akumulasi total

                $orderReturn->returnedProducts()->create([
                    'product_id' => $productId,
                    'product_variant_id' => $variantId == '0' ? null : $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total nilai retur pada tabel induk
            $orderReturn->total_amount_returned = $totalAmountReturned;
            $orderReturn->save();

            // Ubah Status Pesanan
            $order->status = 'menunggu_retur';
            $order->save();

            DB::commit();

            // Siapkan dan Kirim Respon
            // DIUBAH: Memuat relasi 'items' bukan 'products'
            $order->load('customer', 'items.product', 'items.variant');

            return response()->json([
                'message' => 'Pengajuan pengembalian produk berhasil dikirim.',
                'order' => $this->formatOrderDetails($order),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    /**
     * Helper function to format order details for JSON response.
     */
    private function formatOrderDetails(Order $order)
    {
        return [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->translatedFormat('d M Y, H:i'),
            'paid_at' => $order->paid_at ? $order->paid_at->translatedFormat('d M Y, H:i') : null,
            'customer' => [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'address' => $order->customer->address,
            ],
            // DIUBAH: Melakukan iterasi pada $order->items
            'products' => $order->items->map(function ($item) {
                // Hitung jumlah yang sudah diretur untuk produk ini
                $returnedQuantity = DB::table('order_returns')
                    ->join('order_return_products', 'order_returns.id', '=', 'order_return_products.order_return_id')
                    // DIUBAH: Menggunakan $item->order_id, $item->product_id, dan $item->variant_id
                    ->where('order_returns.order_id', $item->order_id)
                    ->where('order_return_products.product_id', $item->product_id)
                    ->where('order_return_products.product_variant_id', $item->variant_id)
                    ->where('order_returns.status', '!=', 'ditolak') // Hanya hitung retur yang tidak ditolak
                    ->sum('order_return_products.quantity');

                return [
                    // DIUBAH: Mengambil data dari $item
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'image_url' => $item->product && $item->product->image_path ? Storage::url($item->product->image_path) : null,
                    'returned_quantity' => $returnedQuantity,
                ];
            }),
            'picked_up_at' => $order->picked_up_at ? $order->picked_up_at->translatedFormat('d M Y, H:i') : null,
            'delivered_at' => $order->delivered_at ? $order->delivered_at->translatedFormat('d M Y, H:i') : null,
            'received_by_buyer_at' => $order->received_by_buyer_at ? $order->received_by_buyer_at->translatedFormat('d M Y, H:i') : null,
        ];
    }

    // Fungsi untuk mengunggah bukti retur
    public function uploadReturnProof(Request $request, Order $order)
    {
        $this->assertOwnOrder($order);

        $request->validate([
            // Nama 'payment_proof' berasal dari input form HTML, jadi tidak perlu diubah
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Cari data retur terbaru yang statusnya menunggu_konfirmasi
            $orderReturn = $order->returns()->where('status', 'menunggu_konfirmasi')->latest()->first();

            // Jika tidak ada data retur, kirim error
            if (! $orderReturn) {
                return response()->json(['message' => 'Tidak ada pengajuan retur aktif untuk pesanan ini.'], 400);
            }

            $file = $request->file('payment_proof');

            // 1. Hapus bukti retur lama jika ada (LOGIKA REPLACE)
            if ($orderReturn->return_proof) {
                Storage::disk('public')->delete($orderReturn->return_proof);
            }

            // 2. Buat nama file baru yang unik dengan timestamp
            $sanitizedInvoiceNumber = str_replace('/', '-', $order->invoice_number);
            $timestamp = time(); // Tambahkan timestamp saat ini
            $extension = $file->getClientOriginalExtension();
            $fileName = 'RETURN-'.$sanitizedInvoiceNumber.'_'.$timestamp.'.'.$extension; // Gabungkan
            $directory = 'return_proofs';

            // 3. Simpan file baru menggunakan storeAs
            $path = $file->storeAs($directory, $fileName, 'public');

            // 4. Simpan path file yang benar ke tabel order_returns
            $orderReturn->return_proof = $path;
            $orderReturn->save();

            // Ubah status di tabel orders
            // [!code block:start]
            // PERBAIKAN: Menggunakan helper untuk mendapatkan waktu regional
            $order->paid_at = $this->nowInUserTimezone();
            // [!code block:end]
            $order->status = 'menunggu_verifikasi_admin';
            $order->save();

            return response()->json([
                'message' => 'Bukti retur berhasil diunggah.',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengunggah file: '.$e->getMessage()], 500);
        }

        return response()->json(['message' => 'File tidak ditemukan.'], 400);
    }

    public function editReturn(Request $request, Order $order)
    {
        $this->assertOwnOrder($order);

        $validated = $request->validate([
            'order_return_id' => 'required|exists:order_returns,id',
            'return_quantities' => 'required|array|min:1',
            'return_quantities.*' => 'required|integer|min:1',
            'reason' => 'nullable|string|min:10',
        ]);

        DB::beginTransaction();

        try {
            $kurir = Auth::user();

            // ================= AMBIL RETUR BERDASARKAN ID =================
            $orderReturn = $order->returns()
                ->where('id', $validated['order_return_id'])
                ->where('status', 'menunggu_konfirmasi')
                ->firstOrFail();

            // Update alasan
            $orderReturn->update([
                'reason' => $validated['reason'],
            ]);

            // ================= AMBIL ITEM PESANAN =================
            $orderItems = $order->items()
                ->get()
                ->keyBy(fn ($item) => $item->product_id.'-'.($item->variant_id ?? 0));

            $totalAmountReturned = 0;

            foreach ($validated['return_quantities'] as $key => $quantity) {
                if ($quantity <= 0) {
                    continue;
                }

                if (! isset($orderItems[$key])) {
                    throw ValidationException::withMessages([
                        'return_quantities' => "Produk dengan key '{$key}' tidak ditemukan.",
                    ]);
                }

                $orderItem = $orderItems[$key];

                if ($quantity > $orderItem->quantity) {
                    throw ValidationException::withMessages([
                        'return_quantities' => "Jumlah retur untuk '{$orderItem->product_name}' melebihi jumlah pembelian.",
                    ]);
                }

                [$productId, $variantId] = explode('-', $key);

                $price = $orderItem->price;
                $subtotal = $price * $quantity;
                $totalAmountReturned += $subtotal;

                // ================= UPDATE / INSERT PRODUK RETUR =================
                $orderReturn->returnedProducts()->updateOrCreate(
                    [
                        'product_id' => $productId,
                        'product_variant_id' => $variantId == '0' ? null : $variantId,
                    ],
                    [
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]
                );
            }

            // ================= UPDATE TOTAL =================
            $orderReturn->update([
                'total_amount_returned' => $totalAmountReturned,
            ]);

            // ================= UPDATE STATUS ORDER =================
            $order->update([
                'status' => 'menunggu_retur',
            ]);

            DB::commit();

            $order->load('customer', 'items.product', 'items.variant');

            return response()->json([
                'message' => 'Pengajuan retur berhasil diperbarui.',
                'order' => $this->formatOrderDetails($order),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}
