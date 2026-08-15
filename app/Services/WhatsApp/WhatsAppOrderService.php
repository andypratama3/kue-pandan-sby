<?php

namespace App\Services\WhatsApp;

use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Region;
use App\Models\ShippingArea;
use App\Services\ShippingFeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Alur pemesanan multi-turn lewat chatbot WhatsApp.
 *
 * Step (disimpan di context_data percakapan via state service):
 * - null / idle : belum mulai
 * - order_catalog    : daftar produk ditampilkan, menunggu pilihan produk
 * - order_variant    : menunggu pilihan varian produk
 * - order_quantity   : menunggu jumlah pesanan
 * - order_location   : menunggu area pengiriman (untuk hitung ongkir)
 * - order_address    : menunggu alamat lengkap
 * - order_confirm    : ringkasan + menunggu konfirmasi YA/TIDAK
 *
 * Semua harga diambil dari tabel DB (ProductVariant aktif milik region
 * percakapan) — bukan dari teks user, agar total pesanan selalu akurat.
 */
class WhatsAppOrderService
{
    const STEP_CATALOG = 'order_catalog';
    const STEP_VARIANT = 'order_variant';
    const STEP_QUANTITY = 'order_quantity';
    const STEP_LOCATION = 'order_location';
    const STEP_ADDRESS = 'order_address';
    const STEP_CONFIRM = 'order_confirm';

    public const ORDER_STEPS = [
        self::STEP_CATALOG,
        self::STEP_VARIANT,
        self::STEP_QUANTITY,
        self::STEP_LOCATION,
        self::STEP_ADDRESS,
        self::STEP_CONFIRM,
    ];

    public function __construct(protected ShippingFeeService $shipping)
    {
    }

    public function isOrderStep(?string $step): bool
    {
        return is_string($step) && in_array($step, self::ORDER_STEPS, true);
    }

    /**
     * Resolve balasan berdasarkan step + teks user.
     *
     * @return array{reply: string, next_step: ?string, persist: array, intent: string, order: ?Order}
     */
    public function resolve(
        ?string $step,
        string $text,
        array $stateContext,
        ?Region $region,
        string $sender,
        ?string $senderName,
    ): array {
        // Baru mulai order.
        if ($step === null || $step === '' || $step === 'idle') {
            return $this->startCatalog($region);
        }

        // Batal dari step mana pun.
        if ($this->wantsCancel($text)) {
            return [
                'reply' => "Pesanan dibatalkan. Terima kasih! 😊\nKetik \"menu\" untuk info produk atau \"pesan\" untuk mulai order lagi.",
                'next_step' => null,
                'persist' => [],
                'intent' => 'cancel_order',
                'order' => null,
            ];
        }

        return match ($step) {
            self::STEP_CATALOG => $this->pickProduct($text, $region, $stateContext),
            self::STEP_VARIANT => $this->pickVariant($text, $region, $stateContext),
            self::STEP_QUANTITY => $this->pickQuantity($text, $region, $stateContext, $sender, $senderName),
            self::STEP_LOCATION => $this->pickLocation($text, $region, $stateContext),
            self::STEP_ADDRESS => $this->pickAddress($text, $region, $stateContext, $sender, $senderName),
            self::STEP_CONFIRM => $this->confirm($text, $region, $stateContext, $sender, $senderName),
            default => $this->startCatalog($region),
        };
    }

    // ====== STEP: MULAI / KATALOG ======

    protected function startCatalog(?Region $region): array
    {
        $products = $this->activeProducts($region);

        if ($products->isEmpty()) {
            return [
                'reply' => "Maaf, katalog produk untuk wilayah ini belum tersedia. Silakan hubungi admin untuk stok & harga terbaru. 😊",
                'next_step' => null,
                'persist' => [],
                'intent' => 'start_order',
                'order' => null,
            ];
        }

        $lines = $products->map(function (Product $p, $i) {
            $min = $p->variants->min('price');

            return ($i + 1).'. '.$p->name.($min !== null ? ' (dari Rp '.number_format((float) $min, 0, ',', '.').')' : '');
        })->implode("\n");

        return [
            'reply' => "Berikut katalog Kue Pandan Asli {$region?->name}:\n\n{$lines}\n\n"
                ."Ketik *nomor* atau *nama produk* untuk memilih. Ketik \"batal\" untuk membatalkan.",
            'next_step' => self::STEP_CATALOG,
            'persist' => [
                'cart' => [],
                'delivery' => null,
                'address' => null,
            ],
            'intent' => 'start_order',
            'order' => null,
        ];
    }

    // ====== STEP: PILIH PRODUK ======

    protected function pickProduct(string $text, ?Region $region, array $stateContext): array
    {
        $products = $this->activeProducts($region);
        $product = $this->matchByIndexOrName($text, $products);

        if (! $product) {
            return [
                'reply' => "Mohon ulangi: ketik *nomor* (1-{$products->count()}) atau *nama produk* yang valid.",
                'next_step' => self::STEP_CATALOG,
                'persist' => $this->freshCart($region),
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $variants = $product->variants;

        // Simpan produk terpilih + default cart kosong.
        $persist = $this->freshCart($region);
        $persist['selected_product_id'] = $product->id;

        // Tanpa varian → langsung tanya jumlah.
        if ($variants->isEmpty()) {
            return [
                'reply' => "Anda memilih *{$product->name}*.\nBerapa jumlah yang ingin dipesan?",
                'next_step' => self::STEP_QUANTITY,
                'persist' => $persist,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $lines = $variants->map(fn ($v, $i) => ($i + 1).'. '.$v->name.' — Rp '.number_format((float) $v->price, 0, ',', '.'))->implode("\n");

        return [
            'reply' => "*{$product->name}*\nPilih varian:\n\n{$lines}\n\nKetik nomor varian (mis. 1).",
            'next_step' => self::STEP_VARIANT,
            'persist' => $persist,
            'intent' => 'checkout',
            'order' => null,
        ];
    }

    // ====== STEP: PILIH VARIAN ======

    protected function pickVariant(string $text, ?Region $region, array $stateContext): array
    {
        $productId = (int) ($stateContext['selected_product_id'] ?? 0);
        $product = Product::with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->where('id', $productId)
            ->when($region, fn ($q) => $q->where('region_id', $region->id))
            ->first();

        if (! $product || $product->variants->isEmpty()) {
            return $this->startCatalog($region);
        }

        $variant = $this->matchByIndexOrName($text, $product->variants);

        if (! $variant) {
            $lines = $product->variants->map(fn ($v, $i) => ($i + 1).'. '.$v->name.' — Rp '.number_format((float) $v->price, 0, ',', '.'))->implode("\n");

            return [
                'reply' => "Pilihan varian tidak valid. Pilih nomor berikut:\n\n{$lines}",
                'next_step' => self::STEP_VARIANT,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $persist = $stateContext;
        $persist['selected_variant_id'] = $variant->id;
        $persist['selected_variant_price'] = (float) $variant->price;

        return [
            'reply' => "Varian *{$variant->name}* (Rp ".number_format((float) $variant->price, 0, ',', '.').") dipilih.\nBerapa jumlah yang ingin dipesan? (angka, mis. 2)",
            'next_step' => self::STEP_QUANTITY,
            'persist' => $persist,
            'intent' => 'checkout',
            'order' => null,
        ];
    }

    // ====== STEP: JUMLAH ======

    protected function pickQuantity(string $text, ?Region $region, array $stateContext, string $sender, ?string $senderName): array
    {
        $productId = (int) ($stateContext['selected_product_id'] ?? 0);
        $variantId = (int) ($stateContext['selected_variant_id'] ?? 0);
        $product = Product::where('id', $productId)->first();

        if (! $product) {
            return $this->startCatalog($region);
        }

        $variant = $product->variants()->where('is_active', true)->where('id', $variantId)->first();

        if (! $variant) {
            return $this->startCatalog($region);
        }

        $quantity = (int) preg_replace('/[^0-9]/', '', $text);

        if ($quantity < 1 || $quantity > 999) {
            return [
                'reply' => "Jumlah tidak valid. Masukkan angka 1-999, misalnya *2*.",
                'next_step' => self::STEP_QUANTITY,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $cart = $stateContext['cart'] ?? [];
        $cart[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_id' => $variant->id,
            'variant_name' => $variant->name,
            'price' => (float) $variant->price,
            'quantity' => $quantity,
            'subtotal' => round((float) $variant->price * $quantity, 2),
        ];

        $persist = $stateContext;
        $persist['cart'] = $cart;
        unset($persist['selected_product_id'], $persist['selected_variant_id'], $persist['selected_variant_price']);

        return [
            'reply' => "Ditambahkan: *{$quantity}× {$product->name} ({$variant->name})* = Rp ".number_format((float) $variant->price * $quantity, 0, ',', '.').".\n"
                ."Di *area mana* pesanan dikirim? Contoh: *Rungkut* atau *Wonokromo*.",
            'next_step' => self::STEP_LOCATION,
            'persist' => $persist,
            'intent' => 'checkout',
            'order' => null,
        ];
    }

    // ====== STEP: AREA PENGIRIMAN ======

    protected function pickLocation(string $text, ?Region $region, array $stateContext): array
    {
        $areaName = trim(preg_replace('/[^a-zA-Z0-9\s,.-]/', '', $text));

        if ($areaName === '' || ! $region) {
            return [
                'reply' => "Mohon tulis nama *area/kecamatan* tujuan, contoh: *Rungkut*.",
                'next_step' => self::STEP_LOCATION,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $area = ShippingArea::where('region_id', $region->id)
            ->where('area_name', 'LIKE', "%{$areaName}%")
            ->first();

        if (! $area) {
            return [
                'reply' => "Area *{$areaName}* belum ditemukan di sistem ongkir wilayah ini.\n"
                    ."Ketik nama area lain, atau hubungi admin untuk konfirmasi.",
                'next_step' => self::STEP_LOCATION,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $result = $this->shipping->calculateByArea($region->id, $area->area_name);

        if ($result['needs_manual_check'] || $result['fee'] === null) {
            return [
                'reply' => $result['message']."\nKetik \"batal\" untuk membatalkan.",
                'next_step' => self::STEP_LOCATION,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $persist = $stateContext;
        $persist['delivery'] = [
            'area' => $area->area_name,
            'area_id' => $area->id,
            'distance_km' => (float) $area->distance_km,
            'fee' => (int) $result['fee'],
        ];

        return [
            'reply' => "Ongkir ke *{$area->area_name}*: Rp ".number_format((int) $result['fee'], 0, ',', '.').".\n"
                ."Silakan tulis *alamat lengkap* (nama jalan, no. rumah, RT/RW, kecamatan, kota, patokan).",
            'next_step' => self::STEP_ADDRESS,
            'persist' => $persist,
            'intent' => 'checkout',
            'order' => null,
        ];
    }

    // ====== STEP: ALAMAT ======

    protected function pickAddress(string $text, ?Region $region, array $stateContext, string $sender, ?string $senderName): array
    {
        $address = trim($text);

        if (mb_strlen($address) < 10) {
            return [
                'reply' => "Alamat terlalu singkat. Mohon tulis alamat lengkap (jalan, no. rumah, kecamatan, kota, patokan).",
                'next_step' => self::STEP_ADDRESS,
                'persist' => $stateContext,
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $cart = $stateContext['cart'] ?? [];
        $delivery = $stateContext['delivery'] ?? null;

        if (empty($cart) || ! $delivery) {
            return $this->startCatalog($region);
        }

        $persist = $stateContext;
        $persist['address'] = $address;

        $summary = $this->buildSummary($cart, $delivery);

        return [
            'reply' => "Berikut ringkasan pesanan Anda:\n\n{$summary['lines']}\n"
                ."📍 Alamat: {$address}\n\n"
                ."Ketik *YA* untuk konfirmasi pesanan, atau *TIDAK* / *batal* untuk membatalkan.",
            'next_step' => self::STEP_CONFIRM,
            'persist' => $persist,
            'intent' => 'checkout',
            'order' => null,
        ];
    }

    // ====== STEP: KONFIRMASI ======

    protected function confirm(string $text, ?Region $region, array $stateContext, string $sender, ?string $senderName): array
    {
        if (! $this->wantsConfirm($text)) {
            return [
                'reply' => "Pesanan dibatalkan. 😊\nKetik \"menu\" untuk info produk atau \"pesan\" untuk mulai lagi.",
                'next_step' => null,
                'persist' => [],
                'intent' => 'cancel_order',
                'order' => null,
            ];
        }

        $cart = $stateContext['cart'] ?? [];
        $delivery = $stateContext['delivery'] ?? null;
        $address = $stateContext['address'] ?? null;

        if (empty($cart) || ! $delivery || ! $address) {
            return $this->startCatalog($region);
        }

        try {
            $order = $this->createOrder($cart, $delivery, $address, $region, $sender, $senderName);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('Gagal membuat order bot: '.$e->getMessage(), [
                'sender' => $sender,
            ]);

            return [
                'reply' => "Mohon maaf, kami gagal memproses pesanan Anda saat ini. Silakan coba lagi atau hubungi admin. 🙏",
                'next_step' => null,
                'persist' => [],
                'intent' => 'checkout',
                'order' => null,
            ];
        }

        $this->notifyOrderCreated($order, $region, $sender);

        $summary = $this->buildSummary($cart, $delivery);

        return [
            'reply' => "✅ *Pesanan berhasil dibuat!*\n"
                ."Nomor invoice: *{$order->invoice_number}*\n\n"
                .$summary['lines']
                ."📍 Alamat: {$address}\n\n"
                ."Admin wilayah akan mengonfirmasi pesanan Anda. Simpan nomor invoice untuk melacak status. Terima kasih! 🙏",
            'next_step' => null,
            'persist' => [],
            'intent' => 'order_created',
            'order' => $order,
        ];
    }

    // ====== PEMBUATAN ORDER ======

    /**
     * Buat Order + OrderItem + Customer (find-or-create by phone) dalam satu
     * transaksi. Harga diambil ulang dari DB varian utk menjaga akurasi.
     */
    protected function createOrder(
        array $cart,
        array $delivery,
        string $address,
        ?Region $region,
        string $sender,
        ?string $senderName,
    ): Order {
        return DB::transaction(function () use ($cart, $delivery, $address, $region, $sender, $senderName) {
            $customer = Customer::where('phone', $sender)->orWhere('phone', (int) $sender)->first();

            if (! $customer) {
                $customer = Customer::create([
                    'name' => $senderName ?: $sender,
                    'phone' => $sender,
                    'address' => $address,
                    'region_id' => $region?->id,
                    'payment_type' => 'wa_bot',
                ]);
            }

            $customer = $customer->fresh();

            $order = Order::create([
                'customer_id' => $customer->id,
                'phone' => $sender,
                'address' => $address,
                'payment_method' => 'wa_bot',
                'note' => 'Pesanan dibuat otomatis via chatbot WhatsApp.',
                'created_by_user_id' => null,
                'region_id' => $region?->id,
                'status' => 'baru',
                'source' => 'wa_bot',
                'total_amount' => 0,
            ]);

            $total = 0;
            $items = [];

            foreach ($cart as $item) {
                $variant = \App\Models\ProductVariant::with('product')
                    ->where('id', (int) ($item['variant_id'] ?? 0))
                    ->where('is_active', true)
                    ->first();

                if (! $variant) {
                    continue;
                }

                $quantity = min(999, max(1, (int) $item['quantity']));
                $price = (float) $variant->price;
                $subtotal = $price * $quantity;
                $total += $subtotal;

                $items[] = new OrderItem([
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->name,
                    'variant_id' => $variant->id,
                    'variant_name' => $variant->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->items()->saveMany($items);
            $order->total_amount = $total + (int) ($delivery['fee'] ?? 0);
            $order->invoice_number = $this->generateInvoice($order, $region);
            $order->save();

            return $order;
        });
    }

    /**
     * Nomor invoice unik untuk order bot. Scope per hari + region + lockForUpdate
     * + retry bila bentrok (unique constraint), mengikuti pola PesananController.
     */
    protected function generateInvoice(Order $order, ?Region $region): string
    {
        $currentTime = $order->created_at ?: now();
        $invoice = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $countToday = Order::whereDate('created_at', $currentTime->toDateString())
                    ->where('source', 'wa_bot')
                    ->where('region_id', $region?->id)
                    ->lockForUpdate()
                    ->count();

                $seq = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
                $tanggal = $currentTime->format('dmy');
                $regionId = str_pad((string) ($region?->id ?? 0), 2, '0', STR_PAD_LEFT);
                $invoice = "INV/WA/{$tanggal}/{$regionId}/{$seq}";

                $order->invoice_number = $invoice;
                $order->save();

                break;
            } catch (\Illuminate\Database\QueryException $e) {
                if (($e->errorInfo[1] ?? null) === 1062 && $attempt < 3) {
                    continue;
                }

                throw $e;
            }
        }

        return $invoice;
    }

    // ====== NOTIFIKASI ======

    protected function notifyOrderCreated(Order $order, ?Region $region, string $sender): void
    {
        $cartCount = $order->items()->count();
        $adminPhone = $region?->contact_phone ?: $region?->escalation_contact_phone;

        if (! $adminPhone) {
            Log::channel('whatsapp')->info('Order bot dibuat tanpa nomor admin untuk notifikasi.', [
                'invoice' => $order->invoice_number,
            ]);

            return;
        }

        $message = "📦 *PESANAN BARU VIA BOT*\n"
            ."Invoice: {$order->invoice_number}\n"
            ."Jumlah item: {$cartCount}\n"
            ."Total: Rp ".number_format((float) $order->total_amount, 0, ',', '.')."\n"
            ."Alamat: {$order->address}\n"
            ."Pelanggan: {$order->phone}\n\n"
            ."Mohon segera konfirmasi di dashboard admin.";

        try {
            SendWhatsAppMessageJob::dispatch($adminPhone, $message);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->warning('Gagal mengirim notifikasi order bot ke admin: '.$e->getMessage());
        }
    }

    // ====== BANTUAN ======

    protected function activeProducts(?Region $region)
    {
        return Product::with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->where('is_active', true)
            ->when($region, fn ($q) => $q->where('region_id', $region->id))
            ->orderBy('name')
            ->get();
    }

    protected function freshCart(?Region $region): array
    {
        return ['cart' => [], 'delivery' => null, 'address' => null];
    }

    /**
     * Cocokkan teks user dgn nomor urut (1-based) atau nama (contains, case-insensitive).
     */
    protected function matchByIndexOrName(string $text, $collection): ?object
    {
        $text = trim($text);
        $items = $collection->values();

        if (is_numeric($text)) {
            $index = (int) $text - 1;

            return $items->get($index);
        }

        $lower = mb_strtolower($text);
        $best = null;
        $bestLen = 0;

        foreach ($items as $item) {
            $name = mb_strtolower((string) $item->name);
            $len = strlen($name);

            if ($len >= 3 && $len > $bestLen && Str::contains($name, $lower)) {
                $best = $item;
                $bestLen = $len;
            }
        }

        return $best;
    }

    protected function wantsCancel(string $text): bool
    {
        $lower = mb_strtolower(trim($text));

        return in_array($lower, ['batal', 'cancel', '0', 'tidak', 'no', 'gajadi', 'nggak jadi', 'tidak jadi', 'stop'], true);
    }

    protected function wantsConfirm(string $text): bool
    {
        $lower = mb_strtolower(trim($text));

        return in_array($lower, ['ya', 'yes', 'y', 'ok', 'oke', 'setuju', 'confirm', '1', 'lanjut', 'iya'], true)
            || Str::contains($lower, 'ya');
    }

    protected function buildSummary(array $cart, array $delivery): array
    {
        $lines = "🧾 *Detail Pesanan:*\n";

        $subTotal = 0;
        foreach ($cart as $item) {
            $sub = (float) ($item['subtotal'] ?? 0);
            $subTotal += $sub;
            $lines .= "{$item['quantity']}× {$item['product_name']} ({$item['variant_name']}) — Rp ".number_format($sub, 0, ',', '.')."\n";
        }

        $fee = (int) ($delivery['fee'] ?? 0);
        $grandTotal = $subTotal + $fee;

        $lines .= "\nSubtotal: Rp ".number_format($subTotal, 0, ',', '.')."\n"
            ."Ongkir: Rp ".number_format($fee, 0, ',', '.')."\n"
            ."*TOTAL: Rp ".number_format($grandTotal, 0, ',', '.')."*\n";

        return ['lines' => $lines, 'total' => $grandTotal];
    }
}