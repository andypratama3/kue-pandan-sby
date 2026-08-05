<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderReturnProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

/**
 * Controller PesananController menangani semua logika bisnis yang terkait dengan
 * pesanan untuk peran Kurir, termasuk pembuatan, pelacakan, pembaruan status,
 * dan proses retur.
 */
class PesananController extends Controller
{
    // --- Helper Functions for Timezone ---

    /**
     * Mendapatkan zona waktu pengguna berdasarkan ID region mereka.
     * Ini memastikan bahwa semua timestamp yang ditampilkan atau disimpan
     * sesuai dengan lokasi geografis kurir.
     *
     * @return string Nama zona waktu (misal: 'Asia/Jakarta' atau 'Asia/Makassar').
     */
    private function getUserTimezone(): string
    {
        $user = Auth::user();
        // Fallback ke timezone default jika user tidak memiliki region_id
        if (!$user || is_null($user->region_id)) {
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

    /**
     * Mendapatkan objek Carbon dengan waktu saat ini sesuai zona waktu pengguna.
     *
     * @return \Carbon\Carbon
     */
    private function nowInUserTimezone(): Carbon
    {
        return Carbon::now($this->getUserTimezone());
    }

    // --- End of Helper Functions ---

    /**
     * Menampilkan halaman daftar pesanan (Order Tracking) dengan fitur pencarian dan filter status.
     * Fungsi ini menangani permintaan GET awal dan permintaan AJAX untuk pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $loggedInUser = Auth::user();
        $search = $request->input('search');
        $activeStatus = $request->input('status', 'semua');

        // Daftar status untuk tab filter di halaman
        $filterableStatuses = [
            'semua' => 'Semua',
            'diambil' => 'Diambil',
            'diantar' => 'Diantar',
            'diterima_pembeli' => 'Diterima',
            'selesai' => 'Selesai',
            'menunggu_retur' => 'Retur',
        ];

        // Pemetaan nama status dari database ke label yang lebih ramah pengguna
        $statusLabelMap = [
            'baru' => 'Baru',
            'dikemas' => 'Dikemas',
            'diambil' => 'Diambil',
            'diantar' => 'Diantar',
            'diterima_pembeli' => 'Diterima',
            'selesai' => 'Selesai',
            'menunggu_retur' => 'Menunggu Retur',
            'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
            'diverifikasi_admin' => 'Valid',
            'dikembalikan' => 'Retur',
            'dibatalkan' => 'Dibatalkan',
        ];

        // Validasi jika kurir tidak memiliki region
        if (is_null($loggedInUser->region_id)) {
            Log::warning('User ' . $loggedInUser->id . ' does not have a region_id.');
            $error = 'Region Anda tidak terdaftar. Silakan hubungi administrator.';
            $orders = new LengthAwarePaginator([], 0, 10);
            return view('dashboard.kurir.pesanan.index', compact('orders', 'error', 'statusLabelMap', 'filterableStatuses', 'activeStatus'));
        }

        try {
            // Query dasar untuk mengambil pesanan milik kurir yang login
            $ordersQuery = Order::where('created_by_user_id', $loggedInUser->id)
                ->where('status', '!=', 'diverifikasi_admin')
                ->with('customer');

            // Terapkan filter status jika bukan 'semua'
            if ($activeStatus !== 'semua') {
                $ordersQuery->where('status', $activeStatus);
            }

            // Terapkan filter pencarian pada nomor invoice atau nama pelanggan
            if ($search) {
                $ordersQuery->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $orders = $ordersQuery->latest()->paginate(10);
            $currentTime = $this->nowInUserTimezone();

            // Tambahkan flag 'show_warning' jika pesanan belum lunas > 5 hari
            foreach ($orders as $order) {
                $order->show_warning = false;
                if (is_null($order->payment_proof)) {
                    $daysSinceCreation = $order->created_at->diffInDays($currentTime);
                    if ($daysSinceCreation >= 5) {
                        $order->show_warning = true;
                    }
                }
            }

            // Jika permintaan adalah AJAX, kembalikan response JSON berisi HTML
            if ($request->ajax()) {
                $viewData = compact('orders', 'statusLabelMap');
                $desktopHtml = view('dashboard.kurir.pesanan._table_rows', $viewData)->render();
                $mobileHtml = view('dashboard.kurir.pesanan._card_view', $viewData)->render();
                return response()->json(['desktop_html' => $desktopHtml, 'mobile_html' => $mobileHtml]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching orders for courier ' . $loggedInUser->id . ': ' . $e->getMessage());
            $error = 'Gagal memuat pesanan. Terjadi kesalahan pada server.';
            $orders = new LengthAwarePaginator([], 0, 10);
            return view('dashboard.kurir.pesanan.index', compact('orders', 'error', 'statusLabelMap', 'filterableStatuses', 'activeStatus'));
        }

        return view('dashboard.kurir.pesanan.index', compact('orders', 'statusLabelMap', 'filterableStatuses', 'activeStatus'));
    }

    /**
     * Menampilkan halaman formulir untuk membuat pesanan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        // Mengambil daftar customer yang berada di region yang sama dengan kurir
        $customers = Customer::select('id', 'company_name', 'name', 'address', 'phone', 'note')
            ->where('region_id', $user->region_id)
            ->where('added_by_user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard.kurir.pesanan.create', compact('customers'));
    }

    /**
     * Memproses dan menyimpan data pesanan baru yang dikirim dari formulir checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        try {
            // Validasi input dari request
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'payment_method' => 'required|string',
                'note' => 'nullable|string',
                'products' => 'required|json',
                'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        }

        $customer = Customer::with('category')->find($validated['customer_id']);
        if (!$customer) {
            return response()->json(['message' => 'Customer tidak ditemukan.'], 404);
        }

        // Cek batas maksimal pesanan aktif berdasarkan kategori customer
        $categoryName = strtolower($customer->category->name ?? '');
        $maxOrder = 0;
        if ($categoryName === 'reseller') $maxOrder = 7;
        elseif ($categoryName === 'supermarket') $maxOrder = 30;

        if ($maxOrder > 0) {
            $activeOrderCount = Order::where('customer_id', $customer->id)
                ->where('created_by_user_id', Auth::id())
                ->where('status', '!=', 'diverifikasi_admin')
                ->count();

            if ($activeOrderCount >= $maxOrder) {
                $message = "Batas maksimal pesanan aktif untuk customer kategori {$categoryName} adalah {$maxOrder}. Pesanan sebelumnya harus diverifikasi admin terlebih dahulu.";
                return response()->json(['message' => $message], 422);
            }
        }

        // Memulai transaksi database untuk memastikan integritas data
        DB::beginTransaction();
        try {
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            $loggedInUser = Auth::user();
            $currentTime = $this->nowInUserTimezone();

            // Buat instance Order baru
            $order = new Order([
                'customer_id' => $validated['customer_id'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'total_amount' => 0, // Akan di-update setelah item ditambahkan
                'payment_method' => $validated['payment_method'],
                'payment_proof' => $paymentProofPath,
                'note' => $validated['note'],
                'created_by_user_id' => $loggedInUser->id,
                'region_id' => $loggedInUser->region_id,
            ]);

            // Set timestamp secara eksplisit sesuai zona waktu kurir
            $order->created_at = $currentTime;
            $order->updated_at = $currentTime;
            $order->save();

            // Generate nomor invoice unik
            $orderCountToday = Order::whereDate('created_at', $currentTime->toDateString())->count();
            $dailySequenceNumber = str_pad($orderCountToday, 3, '0', STR_PAD_LEFT);
            $tanggal = $currentTime->format('dmy');
            $formattedRegionId = str_pad($loggedInUser->region_id, 2, '0', STR_PAD_LEFT);
            $formattedKurirId = str_pad($loggedInUser->id, 3, '0', STR_PAD_LEFT);
            $formattedCustomerId = str_pad($validated['customer_id'], 3, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV/{$tanggal}/{$formattedRegionId}/{$formattedKurirId}/{$formattedCustomerId}/{$dailySequenceNumber}";

            $order->invoice_number = $invoiceNumber;

            // Proses produk/item yang dipesan
            $products = json_decode($validated['products'], true);
            $totalAmount = 0;
            $orderItems = [];

            foreach ($products as $product) {
                $subtotal = $product['quantity'] * $product['price'];
                $totalAmount += $subtotal;

                $orderItems[] = new OrderItem([
                    'product_id' => $product['product_id'],
                    'product_name' => $product['product_name'],
                    'variant_id' => $product['variant_id'] ?? null,
                    'variant_name' => $product['variant_name'] ?? null,
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Simpan semua item pesanan dan update total_amount
            $order->items()->saveMany($orderItems);
            $order->total_amount = $totalAmount;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil disimpan.',
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan pesanan. Terjadi kesalahan internal.'], 500);
        }
    }

    /**
     * Mengambil dan menampilkan detail lengkap dari sebuah pesanan.
     *
     * @param  int  $id ID Pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderDetails($id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        try {
            // Ambil data pesanan beserta relasi customer dan items
            $order = Order::with(['customer', 'items.product'])
                ->where('id', $id)
                ->where('created_by_user_id', Auth::id())
                ->firstOrFail();

            $timezone = $this->getUserTimezone();
            $paidAtLabel = '';
            $paidAtFormatted = null;

            // Format tanggal pelunasan dan hitung label (Harian/Mingguan)
            if ($order->paid_at) {
                $createdAtLocal = $order->created_at->copy()->setTimezone($timezone)->startOfDay();
                $paidAtLocal = $order->paid_at->copy()->setTimezone($timezone)->startOfDay();
                $diffInDays = $createdAtLocal->diffInDays($paidAtLocal);

                if ($diffInDays == 1) $paidAtLabel = ' (Harian)';
                elseif ($diffInDays >= 2 && $diffInDays <= 7) $paidAtLabel = ' (Mingguan)';

                $paidAtFormatted = $order->paid_at->isoFormat('D MMMM YYYY, HH:mm');
            }

            // Cek apakah ada proses retur yang aktif untuk pesanan ini
            $activeReturn = $order->returns()->where('status', '!=', 'ditolak')->latest()->first();

            // Siapkan data pesanan yang akan dikirim sebagai response JSON
            $formattedOrder = [
                'id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'note' => $order->note,
                'rejection_note' => $order->rejection_note,
                'created_at' => $order->created_at->isoFormat('D MMMM YYYY, HH:mm'),
                'paid_at' => $paidAtFormatted,
                'paid_at_label' => $paidAtLabel,
                'payment_proof' => $order->payment_proof,
                'picked_up_at' => $order->picked_up_at ? $order->picked_up_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                'delivered_at' => $order->delivered_at ? $order->delivered_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                'received_by_buyer_at' => $order->received_by_buyer_at ? $order->received_by_buyer_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                'timezone' => $timezone,
                'customer' => [
                    'company_name' => $order->customer->company_name ?? 'N/A',
                    'name' => $order->customer->name ?? 'N/A',
                    'phone' => $order->customer->phone ?? 'N/A',
                    'address' => $order->customer->address ?? 'N/A',
                ],
                'products' => $order->items->map(function ($item) {
                    // Hitung jumlah produk yang sudah diretur
                    $returnedQuantity = DB::table('order_returns')
                        ->join('order_return_products', 'order_returns.id', '=', 'order_return_products.order_return_id')
                        ->where('order_returns.order_id', $item->order_id)
                        ->where('order_return_products.product_id', $item->product_id)
                        ->where('order_return_products.product_variant_id', $item->variant_id)
                        ->where('order_returns.status', '!=', 'ditolak')
                        ->sum('order_return_products.quantity');

                    return [
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'name' => $item->product_name,
                        'variant_name' => $item->variant_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'image_url' => $item->product->image_path ?? null,
                        'returned_quantity' => $returnedQuantity,
                    ];
                })->toArray(),
                'order_return' => $activeReturn ? [
                    'id' => $activeReturn->id,
                    'status' => $activeReturn->status,
                    'return_proof' => $activeReturn->return_proof,
                    'total_amount_returned' => $activeReturn->total_amount_returned,
                    'created_at' => $activeReturn->created_at->setTimezone($timezone)->isoFormat('D MMMM YYYY, HH:mm'),
                ] : null,
            ];

            return response()->json($formattedOrder);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching order details for order ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal.'], 500);
        }
    }

    /**
     * Memproses pengajuan pengembalian barang (retur) untuk sebuah pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID Pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestReturn(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'return_quantities' => 'required|array|min:1',
                'return_quantities.*' => 'required|integer|min:1',
            ]);

            $order = Order::with('items')->where('id', $id)
                ->where('created_by_user_id', Auth::id())
                ->firstOrFail();

            // Retur hanya bisa diajukan jika status pesanan 'diterima_pembeli'
            if ($order->status !== 'diterima_pembeli') {
                return response()->json(['message' => 'Pengajuan retur hanya bisa dilakukan jika status pesanan "Diterima Pembeli".'], 400);
            }

            $currentTime = $this->nowInUserTimezone();

            // Buat record OrderReturn baru
            $orderReturn = new OrderReturn();
            $orderReturn->order_id = $order->id;
            $orderReturn->status = 'menunggu_verifikasi_admin';
            $orderReturn->created_at = $currentTime;
            $orderReturn->updated_at = $currentTime;
            $orderReturn->save();

            $totalReturnValue = 0;
            // Loop melalui setiap produk yang ingin diretur
            foreach ($validated['return_quantities'] as $key => $returnQty) {
                list($productId, $variantId) = explode('-', $key);
                $variantId = ($variantId == 0) ? null : $variantId;

                $orderItem = $order->items()
                    ->where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->first();

                if (!$orderItem || $returnQty > $orderItem->quantity) {
                    throw new \Exception("Kuantitas retur tidak valid untuk produk: " . ($orderItem->product_name ?? 'N/A'));
                }

                $subtotalReturn = $returnQty * $orderItem->price;
                $totalReturnValue += $subtotalReturn;

                // Buat record untuk setiap produk yang diretur
                OrderReturnProduct::create([
                    'order_return_id' => $orderReturn->id,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'quantity' => $returnQty,
                    'price' => $orderItem->price,
                    'subtotal' => $subtotalReturn,
                ]);
            }

            // Update total nilai retur dan status pesanan utama
            $orderReturn->total_amount_returned = $totalReturnValue;
            $orderReturn->save();

            $order->status = 'menunggu_retur';
            $order->updated_at = $currentTime;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Permintaan retur berhasil diajukan.',
                'order' => ['status' => 'menunggu_retur']
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Data tidak valid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing return request for order ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengunggah bukti pembayaran untuk sebuah pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID Pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        try {
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $order = Order::where('id', $id)
                ->where('created_by_user_id', Auth::id())
                ->firstOrFail();

            if (!in_array($order->status, ['diterima_pembeli', 'selesai'])) {
                return response()->json([
                    'message' => 'Bukti pembayaran hanya bisa diunggah setelah pesanan diterima oleh pembeli.'
                ], 403);
            }

            // Hapus file lama
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $file = $request->file('payment_proof');

            $sanitizedInvoiceNumber = str_replace('/', '-', $order->invoice_number);

            // 🔧 SET KUALITAS GAMBAR (30–80 biasanya ideal)
            $quality = 60;

            // Paksa output JPG agar konsisten & ringan
            $fileName = $sanitizedInvoiceNumber . '.jpg';
            $path = 'payment_proofs/' . $fileName;

            $manager = new ImageManager(new Driver());

            $image = $manager
                ->read($file)
                ->encode(new JpegEncoder(quality: $quality));

            // ✅ SIMPAN FILE
            Storage::disk('public')->put($path, $image);

            // Update DB
            $order->update([
                'payment_proof' => $path,
                'status' => 'selesai',
                'paid_at' => $this->nowInUserTimezone(),
            ]);

            return response()->json([
                'message' => 'Bukti pembayaran berhasil diunggah & dikompres.'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);

        } catch (\Exception $e) {
            Log::error('Upload payment proof error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui status pengiriman pesanan (diambil, diantar, diterima).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID Pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        try {
            $validated = $request->validate(['new_status' => 'required|string|in:diambil,diantar,diterima_pembeli']);

            $order = Order::where('id', $id)->where('created_by_user_id', Auth::id())->firstOrFail();
            $newStatus = $validated['new_status'];
            $updateData = ['status' => $newStatus];
            $currentTime = $this->nowInUserTimezone();

            // Logika untuk mencatat timestamp setiap perubahan status
            switch ($newStatus) {
                case 'diambil':
                    // Mencegah status diubah mundur
                    if (in_array($order->status, ['diantar', 'diterima_pembeli', 'selesai'])) {
                        return response()->json(['message' => 'Status tidak dapat diubah kembali ke "Diambil".'], 400);
                    }
                    if (is_null($order->picked_up_at)) $updateData['picked_up_at'] = $currentTime;
                    break;
                case 'diantar':
                    if (in_array($order->status, ['diterima_pembeli', 'selesai'])) {
                        return response()->json(['message' => 'Status tidak dapat diubah kembali ke "Diantar".'], 400);
                    }
                    if (is_null($order->picked_up_at)) $updateData['picked_up_at'] = $currentTime;
                    if (is_null($order->delivered_at)) $updateData['delivered_at'] = $currentTime;
                    break;
                case 'diterima_pembeli':
                    if ($order->status === 'selesai') {
                        return response()->json(['message' => 'Status sudah "Selesai".'], 400);
                    }
                    if (is_null($order->picked_up_at)) $updateData['picked_up_at'] = $currentTime;
                    if (is_null($order->delivered_at)) $updateData['delivered_at'] = $currentTime;
                    if (is_null($order->received_by_buyer_at)) $updateData['received_by_buyer_at'] = $currentTime;
                    break;
            }

            $updateData['updated_at'] = $currentTime;
            $order->update($updateData);

            // Ambil ulang data terbaru untuk dikirim kembali ke frontend
            $updatedOrder = Order::find($id);

            return response()->json([
                'message' => 'Status pesanan berhasil diperbarui.',
                'order' => [
                    'id' => $updatedOrder->id,
                    'status' => $updatedOrder->status,
                    'picked_up_at' => $updatedOrder->picked_up_at ? $updatedOrder->picked_up_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                    'delivered_at' => $updatedOrder->delivered_at ? $updatedOrder->delivered_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                    'received_by_buyer_at' => $updatedOrder->received_by_buyer_at ? $updatedOrder->received_by_buyer_at->isoFormat('D MMMM YYYY, HH:mm') : null,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating order status for order ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal.'], 500);
        }
    }

    /**
     * Mengambil item dari pesanan terakhir seorang customer.
     * Berguna untuk fitur 'pesan ulang' (re-order).
     *
     * @param  int  $id ID Customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastOrder($id)
    {
        // Cari pesanan terakhir berdasarkan customer ID
        $lastOrder = Order::where('customer_id', $id)->latest()->first();

        if (!$lastOrder) {
            return response()->json(['items' => []]);
        }

        // Muat relasi items dari pesanan tersebut
        $lastOrder->load('items');

        // Format data item untuk dimasukkan ke keranjang (cart)
        $cartItems = $lastOrder->items->map(function ($item) {
            return [
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'variant_id'   => $item->variant_id,
                'variant_name' => $item->variant_name,
                'price'        => $item->price,
                'qty'          => $item->quantity,
            ];
        });

        return response()->json(['items' => $cartItems]);
    }
}
