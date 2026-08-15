<?php

namespace App\Services\WhatsApp;

use App\Models\Product;
use App\Models\Region;
use Illuminate\Support\Str;

/**
 * Layar jawab bot (rule-based, 100% konteks database).
 *
 * Pertanyaan yang "di-setup" dan bisa dijawab bot:
 * - sapaan          : halo/hai/selam sepan, dll
 * - tanya_harga     : harga produk/varian di wilayah pengirim (data DB)
 * - tanya_produk    : daftar menu & kategori (data DB)
 * - tanya_lokasi_jam: alamat outlet, jam buka, kontak admin
 * - cara_order      : alur pemesanan resmi (tetap lewat admin/kurir)
 * - komplain        : status/pengalaman buruk -> dilanjutkan ke manusia
 * - lainnya         : di luar topik -> diarahkan ke admin
 */
class WhatsAppReplyService
{
    /**
     * Bangun context jawaban: region + daftar produk aktif beserta varian & harga
     * yang benar-benar tersimpan di database untuk region tersebut.
     */
    public function buildContext(?int $regionId = null): array
    {
        $region = $regionId ? Region::find($regionId) : Region::orderBy('id')->first();

        $products = Product::query()
            ->where('is_active', true)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->when($region, fn ($q) => $q->where('region_id', $region->id))
            ->orderBy('name')
            ->get();

        // Outlet info sekarang dari database, bukan hardcode
        $outlet = null;
        if ($region) {
            $outlet = [
                'address' => $region->address ?? 'Alamat tidak tersedia',
                'hours' => $this->formatOperatingHours($region->operating_hours ?? null) ?? 'Jam operasional tidak tersedia',
                'contact' => $region->contact_email ?? 'Kontak tidak tersedia',
                'phone' => $region->contact_phone ?? null,
                'maps_link' => $region->maps_link ?? null,
            ];
        }

        return [
            'region' => $region,
            'outlet' => $outlet,
            'productData' => $products->map(fn ($p) => [
                'name' => $p->name,
                'description' => $p->description,
                'variants' => $p->variants
                    ->map(fn ($v) => ['name' => $v->name, 'price' => (int) $v->price])
                    ->values()
                    ->all(),
            ])->values()->all(),
            'products' => $products,
        ];
    }

    protected function formatOperatingHours($hours): ?string
    {
        if (!$hours) {
            return 'Setiap Hari, 06.00 - 23.00'; // fallback default
        }
        
        if (is_string($hours)) {
            return $hours;
        }

        // Format array operating hours jika diperlukan
        // Contoh: ['open' => '06:00', 'close' => '23:00'] atau format lain
        if (is_array($hours) && isset($hours['open'], $hours['close'])) {
            return "Setiap Hari, {$hours['open']} - {$hours['close']}";
        }

        return 'Setiap Hari, 06.00 - 23.00'; // fallback default
    }

    public function detectIntent(string $text): string
    {
        $lower = mb_strtolower(trim($text));
        $lower = Str::of($lower)->replace(['.', ',', '!', '?', "'", '"'], '')->toString();

        if (Str::contains($lower, ['lokasi', 'alamat', 'outlet', 'cabang', 'dimana', 'di mana', 'arah', 'jam buka', 'jam berapa', 'beroperasi', 'tutup'])) {
            return 'tanya_lokasi_jam';
        }

        // Pertanyaan pengiriman/ongkir dideteksi SEBELUM 'tanya_harga' karena
        // frasa "ongkir" / "biaya kirim" / "bisa kirim" juga mengandung kata
        // "kirim"/"antar" yang harus menang atas "biaya".
        if (Str::contains($lower, ['bisa kirim', 'bisa antar', 'bisa diantar', 'pengiriman', 'ongkir', 'ongkos kirim', 'biaya kirim', 'kirim', 'antar', 'delivery', 'kurir', 'sampai kapan', 'estimasi', 'jadwal kirim', 'butuh berapa lama'])) {
            return 'tanya_delivery';
        }

        if (Str::contains($lower, ['harga', 'berapa', 'biaya', 'tarif', 'price'])) {
            return 'tanya_harga';
        }

        if (Str::contains($lower, ['menu', 'katalog', 'daftar', 'produk', 'varian', 'jenis', 'apa saja', 'list'])) {
            return 'tanya_produk';
        }

        if (Str::startsWith($lower, ['batal', 'cancel', 'gajadi', 'nggak jadi', 'tidak jadi', 'batalkan'])) {
            return 'cancel_order';
        }

        // Mulai order: kata-kata yang jelas menunjukkan niat memesan,
        // dipisah dari 'cara_order' (bertanya CARA pesan).
        if (Str::contains($lower, ['mau pesan', 'mau beli', 'saya mau pesan', 'saya mau beli', 'ingin pesan', 'ingin beli', 'mau order', 'pesan sekarang', 'beli sekarang', 'order sekarang', 'nitip', 'tambah pesanan', 'lanjut bayar', 'checkout'])
            || in_array(trim($lower), ['pesan', 'beli', 'order', 'mau', 'mau beli', 'mau pesan', 'check out', 'check-out', 'belanja'], true)) {
            return 'start_order';
        }

        if (Str::contains($lower, ['cara order', 'cara pesan', 'bagaimana', 'gimana cara', 'tutorial order', 'tutorial pesan', 'step order', 'langkah pesan'],)
            || Str::startsWith($lower, ['bagaimana', 'gimana'])) {
            return 'cara_order';
        }

        if (Str::contains($lower, ['komplain', 'complain', 'retur', 'rusak', 'telat', 'lama', 'status', 'keluhan'])) {
            return 'komplain';
        }

        if (Str::startsWith($lower, ['pagi', 'siang', 'sore', 'malam', 'halo', 'hai', 'hi ', 'assalamualaikum', 'permisi'])
            || Str::contains($lower, ['test', 'tes ', 'kak ', 'min '])) {
            return 'sapaan';
        }

        return 'lainnya';
    }

    /**
     * Susun balasan dari intents + context yang seluruh isinya berasal dari DB/config,
     * BUKAN dari tebakan. Selalu tutup dengan catatan lari ke admin/kurir resmi.
     */
    public function buildReply(string $text, string $intent, array $context): string
    {
        $regionName = $context['region']?->name ?? 'wilayah Anda';
        $outlet = $context['outlet'];

        return match ($intent) {
            'sapaan' => $this->replyGreeting($regionName),
            'tanya_harga' => $this->replyPrice($text, $context, $regionName),
            'tanya_produk' => $this->replyProducts($context, $regionName),
            'tanya_lokasi_jam' => $this->replyLocationHours($outlet, $regionName),
            'cara_order' => $this->replyHowToOrder($regionName),
        'tanya_delivery' => $this->replyDelivery($outlet, $regionName),
            'komplain' => $this->replyComplaint($regionName),
            'start_order' => "Silakan ketik nama produk yang ingin dipesan, misalnya *\"kue ijo\"* atau *\"hampers\"*. "
                ."Bot akan memandu Anda sampai pesanan selesai dibuat. 😊\nKetik *\"menu\"* untuk melihat katalog.",
            'cancel_order' => "Pesanan dibatalkan. 😊\nKetik *\"pesan\"* untuk mulai order, atau *\"menu\"* untuk lihat katalog.",
            default => $this->replyFallback($regionName),
        };
    }

    protected function replyGreeting(string $regionName): string
    {
        return "Halo, selamat datang di Kue Pandan Asli {$regionName}! 👋\n"
            ."Ketik salah satu:\n"
            ."1. \"harga\" - daftar & harga produk\n"
            ."2. \"menu\" - menu lengkap\n"
            ."3. \"lokasi\" - alamat & jam buka outlet\n"
            ."4. \"cara order\" - cara pemesanan\n"
            ."\nUntuk pemesanan resmi, Anda dapat chat admin/kurir wilayah. Terima kasih! 🙏";
    }

    protected function replyPrice(string $text, array $context, string $regionName): string
    {
        $product = $this->findProduct($context, $text);

        if ($product) {
            $lines = collect($product['variants'])
                ->map(fn ($v) => '• '.$v['name'].' = Rp '.number_format($v['price'], 0, ',', '.'))
                ->implode("\n");

            return "Harga {$product['name']} di {$regionName}:\n{$lines}\n\n"
                .'Untuk pemesanan & stok terkini, mohon hubungi admin/kurir wilayah ya. 😊';
        }

        if (empty($context['productData'])) {
            return "Maaf, informasi produk untuk {$regionName} belum tersedia saat ini. "
                .'Silakan hubungi admin dengan chat langsung untuk info harga terbaru.';
        }

        $lines = collect($context['productData'])
            ->map(function ($p) {
                $cheapeast = collect($p['variants'])->pluck('price')->min();

                return '• '.$p['name'].($cheapeast !== null ? ' (dari Rp '.$this->number($cheapeast).')' : '');
            })
            ->implode("\n");

        return "Daftar produk & harga di {$regionName}:\n{$lines}\n\n"
            ."Ketik *harga 'nama produk'* untuk detail varian, atau hubungi admin untuk pemesanan.";
    }

    protected function replyProducts(array $context, string $regionName): string
    {
        if (empty($context['productData'])) {
            return "Menu untuk {$regionName} belum tersedia saat ini. Silakan hubungi admin/kurir wilayah untuk menu terbaru. 😊";
        }

        $lines = collect($context['productData'])
            ->map(fn ($p) => '• '.$p['name'].' ('.count($p['variants']).' varian)')
            ->implode("\n");

        return "Menu Kue Pandan Asli {$regionName}:\n{$lines}\n\n"
            ."Ketik *harga 'nama produk'* untuk detail harga tiap varian.";
    }

    protected function replyLocationHours(?array $outlet, string $regionName): string
    {
        if (! $outlet) {
            return "Saat ini informasi outlet / jam buka untuk {$regionName} belum tersedia di sistem. "
                .'Silakan hubungi admin untuk lokasi terdekat Anda.';
        }

        return "📍 *Kafe Pandan Asli {$regionName}*\n"
            ."Alamat: {$outlet['address']}\n"
            ."Jam operasional: {$outlet['hours']}\n"
            ."Kontak: {$outlet['contact']}\n\n"
            .'Hubungi admin untuk info pesan atau banyak lagi. 🙏';
    }

    protected function replyHowToOrder(string $regionName): string
    {
        return "Cara memesan di Kafe Pandan Asli {$regionName}:\n"
            ."1. Pilih produk & varian (ketik \"menu\" / \"harga\").\n"
            ."2. Chat langsung admin/kurir wilayah untuk konfirmasi stok & total harga.\n"
            ."3. Pesanan diantar oleh kurir wilayah Anda.\n\n"
            .'Bot ini hanya membantu info; pemesanan resmi tetap dilayani admin/kurir ya. 😊';
    }

    protected function replyComplaint(string $regionName): string
    {
        return "Mohon maaf atas ketidaknyamanan Anda. 🙏\n"
                    .'Kami akan meneruskan keluhan ini ke admin/kurir wilayah agar segera ditindaklanjuti. '
                    ."Silakan hubungi admin langsung untuk detail (alamat tersedia di halaman Lokasi Outlet).\n"
                    .'Terima kasih atas kesabaran Anda.';
    }

    protected function replyDelivery(array $outlet, string $regionName): string
    {
        $phone = $outlet['phone'] ?? null;
        $contactLine = $phone
            ? "Silakan hubungi {$phone} untuk konfirmasi pengiriman. 📞"
            : 'Silakan hubungi admin/kurir wilayah untuk konfirmasi pengiriman.';

        return "Pengiriman Kue Pandan Asli {$regionName} diantar oleh kurir wilayah kami. 🛵\n"
            .'Estimasi dan ongkos kirim menyesuaikan jarak pengiriman dari outlet ke alamat Anda.'
            ."\n\n{$contactLine}";
    }

    protected function replyFallback(string $regionName): string
    {
        return "Maaf, saya baru bisa membantu info seputar produk, harga, jam buka, dan cara pemesanan {$regionName}. 😊\n"
            .'Ketik "menu", "harga", "lokasi", atau "cara order" untuk detail, atau hubungi admin/kurir untuk pertanyaan di luarnya.';
    }

    protected function findProduct(array $context, string $text): ?array
    {
        $lower = mb_strtolower($text);

        foreach ($context['productData'] as $product) {
            $name = mb_strtolower($product['name']);
            if ($name !== '' && Str::contains($lower, $name)) {
                return $product;
            }
        }

        // pencarian kata kunci per kata (untuk nama produk seperti "Kue Ijo")
        $tokens = array_values(array_filter(preg_split('/\s+/', $lower)));
        foreach ($context['productData'] as $product) {
            $name = mb_strtolower($product['name']);
            foreach (explode(' ', $name) as $token) {
                if (mb_strlen($token) >= 4 && Str::contains($lower, $token)) {
                    return $product;
                }
            }
        }

        return null;
    }

    protected function number(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
