# 🤖 Prompt untuk AI Coding Agent (DeepSeek V4 Flash) — Implementasi WhatsApp Fonnte ⇄ Meta Cloud API
## Project: Kue Pandan Asli — Sistem Reseller & Kurir Multi-Cabang

> **Penting soal peran DeepSeek V4 Flash di sini**: dalam dokumen ini, **DeepSeek V4 Flash berperan sebagai AI coding agent** — seperti Cursor/Windsurf/Claude Code — yang kamu jalankan **sekali** untuk mengerjakan tugas implementasi ini di codebase-mu. DeepSeek V4 Flash **TIDAK** ikut berjalan terus-menerus di aplikasi produksi. Semua isi §4 di bawah adalah **satu prompt utuh** yang tinggal kamu paste ke agent tersebut, lalu agent itu yang akan menulis kode, membuat file, dan menjalankan test di project Laravel-mu.
>
> Terpisah dari itu, hasil pekerjaan si agent nanti **mungkin** mencakup fitur *auto-reply AI untuk pelanggan* (bot yang jawab pertanyaan WhatsApp secara otomatis). Fitur runtime itu — jika kamu mau — akan memanggil API model AI **saat aplikasi jalan di production**, dan model itu **bisa apa saja** (DeepSeek, model lain, atau tanpa AI sama sekali dan cukup rule-based). Itu keputusan terpisah dari "siapa yang mengerjakan coding-nya". Detailnya ada di §5 (ditandai jelas sebagai *opsional, fitur runtime*, bukan bagian dari si agent).

---

## 0. TL;DR — Bisa Switch ke Meta?

**Bisa.** Kuncinya: jangan hardcode ke salah satu vendor. Buat **interface `WhatsAppProviderInterface`** dengan dua kontrak method (`sendMessage`, `parseIncoming`), lalu buat dua implementasi: `FonnteProvider` dan `MetaCloudProvider`. Controller webhook, service AI, dan seluruh logic bisnis **tidak tahu-menahu** provider mana yang aktif — mereka hanya bicara ke interface. Provider aktif ditentukan satu baris di `.env`:

```env
WHATSAPP_PROVIDER=fonnte   # atau: meta
```

Trade-off masing-masing provider (ringkas — detail di §2 & §3):

| | **Fonnte** | **Meta Cloud API** |
|---|---|---|
| Setup awal | Scan QR, ~5 menit | Meta app, Business Portfolio, verifikasi bisnis, App Review — bisa berhari-hari |
| Nomor WA | Nomor WA biasa (personal/business) yang sudah dipakai | Wajib nomor baru yang didaftarkan khusus ke WABA (tidak bisa pakai nomor WA personal yang sudah aktif) |
| Auth kirim pesan | Token sederhana per device | OAuth access token (Graph API), perlu system user + permission |
| Biaya | Biaya langganan bulanan ke Fonnte + tarif WA normal | Gratis mulai tier tertentu, tapi kena **conversation-based/per-message pricing** resmi Meta setelah window tertentu |
| Balas pesan di luar 24 jam | Bebas, tidak ada pembatasan format | **Wajib pakai Message Template** yang sudah di-approve Meta (tidak bisa kirim teks bebas di luar window 24 jam) |
| Reliabilitas & skala resmi | Bergantung reliabilitas pihak ketiga (Fonnte) | Resmi dari Meta, SLA & throughput lebih tinggi, cocok untuk skala besar/multi-partner |
| Risiko banned | Risiko lebih tinggi (memakai celah non-official WA client di balik layar) | Resmi, risiko banned jauh lebih rendah selama ikut kebijakan konten |
| Cocok untuk | Toko kecil–menengah, mulai cepat, budget terbatas (situasi **Kue Pandan Asli** saat ini) | Bisnis yang sudah/berencana scale besar, butuh kepatuhan resmi, atau punya banyak nomor cabang terverifikasi |

> **Rekomendasi untuk Kue Pandan Asli saat ini**: mulai dengan **Fonnte** (setup cepat, cocok untuk 3 cabang kecil), tapi bangun kodenya dengan abstraksi provider ini dari awal — supaya kalau bisnis berkembang dan butuh kepatuhan resmi/skala lebih besar, tinggal pindah ke Meta Cloud API tanpa rewrite total.

---

## 1. Latar Belakang & Alasan Perubahan

Berdasarkan dokumentasi project (lihat `README.md` project), kondisi saat ini:

| Area | Kondisi Sekarang | Masalah |
|---|---|---|
| Channel WhatsApp | Meta WhatsApp Cloud API (`Chatbot/WebhookController.php`) | Setup ribet (perlu Facebook Business verifikasi, Meta App Review), `detectIntent()` **kosong**, balasan hanya echo statis |
| Env vars | `META_VERIFY_TOKEN`, `META_PHONE_NUMBER_ID`, `META_ACCESS_TOKEN`, `META_VERSION` | Tidak dipakai maksimal, `env()` dipanggil langsung di constructor (bukan `config()`) |
| AI/NLU | Tidak ada | Tidak bisa jawab pertanyaan pelanggan soal produk, harga, ketersediaan |
| Alur order | Tetap manual: pelanggan chat kurir langsung, kurir input manual ke web | Bot belum bisa bantu kurir/pelanggan sama sekali |

**Rencana**: Ganti Meta Cloud API → **Fonnte** (lebih simpel, cukup scan QR, tidak perlu Business verification), dan tambahkan **DeepSeek V4 Flash** sebagai lapisan AI untuk:
1. Menjawab pertanyaan pelanggan (FAQ produk, harga varian, jam buka, cara order) — otomatis, 24 jam.
2. Mendeteksi **intent** pesan masuk (nanya harga, nanya stok, komplain, mau reorder, dll).
3. Membantu kurir: pelanggan bisa cek status pesanan terakhir via chat (opsional, fase 2).
4. Tetap **fallback ke manusia** (kurir/admin) untuk hal yang tidak bisa dijawab bot (negosiasi harga khusus, komplain serius, dll).

---

## 2. Ringkasan API Fonnte yang Relevan

> Sumber: [docs.fonnte.com](https://docs.fonnte.com/)

### 2.1 Autentikasi
- Fonnte menggunakan **Token** (bukan Bearer), didapat dari halaman **Device** di dashboard Fonnte.
- Header: `Authorization: TOKEN` (token langsung, tanpa prefix `Bearer`).
- Token bersifat sensitif — simpan di `.env`, jangan hardcode.

### 2.2 Kirim Pesan — `POST https://api.fonnte.com/send`
Parameter penting yang dipakai project ini:

| Parameter | Wajib | Keterangan |
|---|---|---|
| `target` | ✅ | Nomor WA tujuan (format `62xxxxxxxxxx`), bisa multi nomor dipisah koma |
| `message` | opsional | Isi pesan teks, mendukung emoji, max 60.000 karakter |
| `url` | opsional* | Link publik gambar/file/audio/video (untuk kirim foto produk) — *hanya paket super/advanced/ultra* |
| `typing` | opsional | `true` untuk menampilkan indikator "sedang mengetik" (bagus dipakai saat menunggu respons AI) |
| `duration` | opsional | Durasi typing indicator custom (detik), cocok untuk menyamarkan latency panggilan DeepSeek |
| `inboxid` | opsional | ID pesan masuk yang ingin dibalas (didapat dari webhook), device harus aktifkan "inbox" di pengaturan |
| `countryCode` | opsional | Default `62`, otomatis mengganti awalan `0` jadi `62` |
| `delay` | opsional | Delay antar pesan saat kirim ke banyak nomor (format string, misal `"2"` atau `"2-5"`) |

Contoh call PHP (pola project — Guzzle sudah tersedia di `composer.json`):

```php
$response = $client->post('https://api.fonnte.com/send', [
    'headers' => ['Authorization' => config('services.fonnte.token')],
    'form_params' => [
        'target'  => $sender,      // format 62xxxxxxxxxx
        'message' => $replyText,
        'typing'  => true,
        'duration'=> 2,
        'inboxid' => $inboxId,     // agar Fonnte tahu ini balasan pesan masuk
    ],
]);
```

### 2.3 Terima Pesan — Webhook
- URL webhook diatur di **Fonnte Dashboard → Device → Edit**.
- **Wajib** set **Auto Read = ON**, kalau tidak webhook tidak akan terpanggil.
- Fonnte akan **POST JSON** ke URL tersebut setiap ada pesan masuk. Payload penting:

| Field | Keterangan |
|---|---|
| `device` | Nomor device Fonnte (toko) |
| `sender` | Nomor pengirim (calon customer) |
| `message` | Isi pesan teks |
| `name` | Nama tampilan pengirim di WhatsApp |
| `member` | Jika dari grup, ini nomor member pengirim |
| `location` | `lat,long` jika pengirim share lokasi |
| `url`, `filename`, `extension` | Jika pengirim kirim lampiran (hanya device paket lengkap) |
| `timestamp` | Waktu pesan diterima |
| `inboxid` | ID untuk dipakai membalas via parameter `inboxid` saat kirim balasan |

> Catatan penting: **autoreply bawaan Fonnte otomatis nonaktif** begitu webhook dipakai — semua balasan jadi tanggung jawab kode kita.

### 2.4 Perbedaan Kunci vs Meta Cloud API (existing code)
| Aspek | Meta Cloud API (lama) | Fonnte (baru) |
|---|---|---|
| Verifikasi awal | `GET` webhook + `hub_challenge` token | Tidak perlu — cukup pasang URL & aktifkan Auto Read |
| Auth kirim pesan | Access Token Meta Graph API | Token Fonnte (per device) |
| Setup device | Facebook Business, App Review | Scan QR WhatsApp biasa |
| Reply pesan masuk | Kirim manual via Graph API | Sama, tapi tinggal set `inboxid` |
| Kirim lampiran | Perlu upload media dulu ke Meta | Cukup kirim `url` publik langsung |

---

## 3. Ringkasan API Meta WhatsApp Cloud yang Relevan

> Sumber: [developers.facebook.com](https://developers.facebook.com/docs/) → WhatsApp Business Platform / Cloud API.

### 3.1 Setup Awal (jauh lebih panjang dari Fonnte)
1. Buat **Meta App** dengan use case "Connect with customers through WhatsApp" di [App Dashboard](https://developers.facebook.com/apps).
2. Hubungkan app ke **WhatsApp Business Account (WABA)** — buat baru atau pakai yang sudah ada.
3. Dapat **test phone number** gratis untuk development (belum bisa dipakai produksi ke banyak pengguna).
4. Untuk produksi: **daftarkan nomor bisnis asli** lewat WhatsApp Manager, verifikasi kepemilikan, lalu **register nomor via API** (registrasi nomor **hanya bisa lewat API**, tidak bisa dari dashboard).
5. Buat **System User** di Business Settings → assign asset app & WABA → generate **permanent access token** dengan permission `business_management`, `whatsapp_business_messaging`, `whatsapp_business_management`.

### 3.2 Autentikasi
- **OAuth Bearer token** (bukan token statis sederhana seperti Fonnte).
- Header: `Authorization: Bearer <SYSTEM_USER_ACCESS_TOKEN>`.
- Token temporer (dari Quickstart) cepat expired — wajib pakai token permanen dari System User untuk production.

### 3.3 Kirim Pesan — `POST https://graph.facebook.com/v23.0/<PHONE_NUMBER_ID>/messages`

Contoh body request pesan teks:
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "<WHATSAPP_USER_PHONE_NUMBER>",
  "type": "text",
  "text": { "body": "Hello!" }
}
```

Poin penting:
- **`PHONE_NUMBER_ID`** (bukan nomor telepon itu sendiri) adalah identifier yang dipakai di URL endpoint.
- Pesan teks bebas **hanya bisa dikirim dalam customer service window 24 jam** setelah pelanggan terakhir mengirim pesan. Di luar window itu, **wajib memakai Message Template** yang sudah melalui proses approval Meta (template harus didaftarkan & disetujui dulu, tidak bisa on-the-fly).
- Mendukung tipe pesan: text, image, audio, video, document, location, contacts, interactive (list/reply buttons/CTA URL), reaction, sticker.

### 3.4 Terima Pesan — Webhook
- Dibuat & dikonfigurasi di **App Dashboard → WhatsApp → Configuration**, subscribe ke field `messages`.
- Payload webhook berbentuk **nested JSON** (`entry[].changes[].value.messages[]`), jauh lebih dalam strukturnya dibanding Fonnte:
```json
{
  "object": "whatsapp_business_account",
  "entry": [{
    "id": "...",
    "changes": [{
      "value": {
        "messaging_product": "whatsapp",
        "metadata": { "display_phone_number": "...", "phone_number_id": "..." },
        "contacts": [{ "profile": { "name": "Sheena Nelson" }, "wa_id": "16505551234" }],
        "messages": [{
          "from": "16505551234",
          "id": "wamid...",
          "timestamp": "1749416383",
          "type": "text",
          "text": { "body": "Does it come in another color?" }
        }]
      },
      "field": "messages"
    }]
  }]
}
```
- **Wajib verifikasi awal** (`GET` request dengan query `hub.challenge` + `hub.verify_token` yang harus dicocokkan dengan token yang kita set) — inilah kenapa `WebhookController` lama project ini punya method `GET` khusus verifikasi. Fonnte **tidak** butuh langkah ini.
- Webhook retry otomatis oleh Meta jika endpoint kita mengembalikan selain HTTP 200, sampai 7 hari.
- Payload maksimal 3MB.
- Bisa dibatasi keamanannya pakai **mutual TLS (mTLS)** atau **IP allowlist** (IP Meta bisa berubah — mTLS lebih direkomendasikan daripada allowlist statis).

### 3.5 Perbedaan Kunci vs Fonnte (ringkas)
| Aspek | Fonnte | Meta Cloud API |
|---|---|---|
| Endpoint kirim | `POST api.fonnte.com/send` (form-encoded) | `POST graph.facebook.com/v23.0/<phone_id>/messages` (JSON) |
| Auth | Token statis per device | OAuth Bearer, sistem permission bertingkat |
| Verifikasi webhook | Tidak perlu | Wajib (`GET` + `hub.challenge`) |
| Balas di luar 24 jam | Bebas | Wajib Message Template ter-approve |
| Struktur payload masuk | Flat (`sender`, `message`, dst) | Nested (`entry.changes.value.messages[]`) |
| Setup nomor | Pakai nomor WA yang sudah aktif | Wajib registrasi nomor baru khusus lewat API |

---

## 4. Prompt Utama — Tempel Ini ke AI Coding Agent-mu (dijalankan dengan DeepSeek V4 Flash)

> Ini satu-satunya file yang perlu kamu paste utuh ke agent. Agent akan membaca, memahami konteks project, lalu mengeksekusi seluruh tugas: membuat file, menulis kode, menjalankan migrasi/test. Kamu tidak perlu menjalankan langkah manual apa pun dari isi blok ini sendiri — itu tugas agent.

````markdown
# TASK PROMPT — Implementasi WhatsApp Integration Multi-Provider (Fonnte ⇄ Meta Cloud API)
# Untuk dieksekusi oleh AI coding agent (model: DeepSeek V4 Flash)
# Project: Kue Pandan Asli (Laravel 10 + Livewire 3 + Tailwind)

## PERAN KAMU (agent)
Kamu adalah senior Laravel engineer yang mengerjakan project "Kue Pandan Asli", sebuah
aplikasi manajemen order & delivery toko kue tradisional dengan model bisnis Reseller +
Kurir multi-cabang (Surabaya, Malang, Denpasar). Tugasmu: membangun ulang integrasi
WhatsApp webhook (sebelumnya prototipe Meta Cloud API yang belum jadi, `detectIntent()`
kosong) menjadi arsitektur **multi-provider yang bisa di-switch lewat `.env`** antara
**Fonnte** dan **Meta WhatsApp Cloud API**, tanpa mengubah kode bisnis saat berpindah
provider. Kamu mengerjakan ini sebagai satu sesi coding task — bukan sebagai komponen
yang akan ikut berjalan di aplikasi setelah selesai.

Opsional: implementasikan juga fitur *auto-reply AI* untuk menjawab pertanyaan
pelanggan (lihat poin 9 di bawah) — fitur ini berjalan di *runtime* aplikasi memakai
API call ke model AI pilihan pemilik project (lewat config, bisa diganti-ganti), dan
terpisah sepenuhnya dari kamu sebagai agent yang sedang mengerjakan task ini sekarang.

## KONTEKS PROJECT (WAJIB DIPATUHI)
- Stack: PHP 8.1, Laravel 10.10, Livewire 3, Spatie Permission (role admin/kurir),
  Barryvdh DomPDF, Intervention Image, Guzzle HTTP (sudah terpasang).
- Struktur route: publik di `routes/web.php`, API/webhook di `routes/api.php`.
- Controller lama yang akan digantikan: `app/Http/Controllers/Chatbot/WebhookController.php`
  (berisi `typeMessage()` helper dan `detectIntent()` yang masih kosong).
- Model relevan: `Product`, `ProductVariant`, `Category`, `Customer`, `Order`, `OrderItem`,
  `Region`. Produk terikat per-region (`region_id`), begitu juga customer & order.
- Data produk contoh (dari seeder): Kue Ijo, Kue Lumpur Surga, Kue Ongol-Ongol, Kue Pulut
  Srikaya, Kue Ubi Nanas, Selai Srikaya, Kue Mix Mini, Kue Mix, Hampers A/B/C, Tumpeng
  Mini, Tumpeng Besar — masing-masing punya varian (`product_variants`) dengan harga
  berbeda.
- Alur bisnis SAAT INI: pelanggan pesan ke KURIR langsung via WhatsApp pribadi kurir,
  bukan ke nomor toko. Kurir yang input pesanan manual ke web app.
- ATURAN PENTING: bot TIDAK BOLEH membuat/mengubah data `Order` secara otomatis pada
  fase awal ini. Bot murni untuk **FAQ & informasi produk**, bukan mengambil alih proses
  checkout kurir. Order tetap diinput manual oleh kurir seperti sekarang (fase 2 baru
  dipertimbangkan otomasi order via chat, dengan approval kurir).

## TUJUAN IMPLEMENTASI

1. **Buat abstraksi provider WhatsApp** — ini yang membuat sistem bisa switch Fonnte ⇄
   Meta Cloud API tanpa rewrite:

   - Interface `app/Contracts/WhatsAppProviderInterface.php`:
     ```php
     interface WhatsAppProviderInterface
     {
         /** Kirim pesan teks. Return array normalized: ['success' => bool, 'message_id' => ?string, 'raw' => array] */
         public function sendMessage(string $target, string $message, array $context = []): array;

         /** Opsional: kirim indikator "sedang mengetik". No-op jika provider tidak mendukung. */
         public function sendTyping(string $target, int $durationSeconds = 2): void;

         /**
          * Normalisasi payload webhook masuk (format tiap provider beda-beda) menjadi
          * DTO seragam: ['sender' => string, 'name' => ?string, 'text' => ?string,
          * 'type' => string, 'raw_reply_context' => mixed].
          */
         public function parseIncoming(array $payload): array;

         /** Untuk provider yang butuh verifikasi GET webhook (Meta). Return response array atau null jika tidak relevan. */
         public function verifyWebhook(array $query): ?array;
     }
     ```
   - Implementasi `app/Services/WhatsApp/FonnteProvider.php` (lihat detail Fonnte di §2 dokumen
     analisis) dan `app/Services/WhatsApp/MetaCloudProvider.php` (lihat detail Meta di §3
     dokumen analisis — termasuk penanganan `hub.challenge` di `verifyWebhook()`, format
     JSON body `messaging_product/to/type/text.body`, parsing struktur nested
     `entry[].changes[].value.messages[]`, dan header `Authorization: Bearer`).
   - Binding provider aktif di `app/Providers/AppServiceProvider.php` (atau service provider
     khusus `WhatsAppServiceProvider`), pilih implementasi berdasarkan
     `config('services.whatsapp.provider')`:
     ```php
     $this->app->bind(WhatsAppProviderInterface::class, function () {
         return match (config('services.whatsapp.provider')) {
             'meta' => app(MetaCloudProvider::class),
             default => app(FonnteProvider::class),
         };
     });
     ```
   - Konfigurasi di `config/services.php`:
     ```php
     'whatsapp' => [
         'provider' => env('WHATSAPP_PROVIDER', 'fonnte'), // 'fonnte' | 'meta'
     ],
     'fonnte' => [
         'token' => env('FONNTE_TOKEN'),
     ],
     'meta_whatsapp' => [
         'access_token'    => env('META_ACCESS_TOKEN'),
         'phone_number_id' => env('META_PHONE_NUMBER_ID'),
         'verify_token'    => env('META_VERIFY_TOKEN'),
         'api_version'     => env('META_API_VERSION', 'v23.0'),
     ],
     'deepseek' => [
         'api_key' => env('DEEPSEEK_API_KEY'),
         'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
         'model' => env('DEEPSEEK_MODEL', 'deepseek-v4-flash'),
     ],
     ```
   - Tambahkan variabel baru ke `.env.example`: `WHATSAPP_PROVIDER`, `FONNTE_TOKEN`,
     `META_ACCESS_TOKEN`, `META_PHONE_NUMBER_ID`, `META_VERIFY_TOKEN`,
     `META_API_VERSION`, `DEEPSEEK_API_KEY`, `DEEPSEEK_BASE_URL`, `DEEPSEEK_MODEL`.
     (`META_*` yang lama di project sudah cocok dipakai ulang — tinggal disambungkan ke
     `MetaCloudProvider` alih-alih `WebhookController` lama.)
   - Endpoint webhook: **satu route generik** yang bisa melayani kedua provider —
     `POST /api/webhook/whatsapp` menerima dari Fonnte, dan
     `GET|POST /api/webhook/whatsapp/meta` untuk Meta (Meta butuh `GET` terpisah untuk
     verifikasi awal `hub.challenge`, yang tidak dibutuhkan Fonnte). Controller yang
     menangani cukup satu (`WhatsAppWebhookController`), tinggal delegasikan parsing ke
     `WhatsAppProviderInterface::parseIncoming()` / `verifyWebhook()` sesuai provider aktif.

2. **Service Layer per-provider**:
   - `FonnteProvider::sendMessage()` — Gunakan Guzzle (sudah ada di composer.json), header
     `Authorization: <token>` (BUKAN Bearer — sesuai dokumentasi Fonnte), form params
     `target`, `message`, `typing`, `duration`, `inboxid`. Tangani semua response error
     Fonnte (`token invalid`, `input invalid`, `insufficient quota`, `target invalid`, dll)
     dan log via `Log::channel('whatsapp')`.
   - `MetaCloudProvider::sendMessage()` — `POST https://graph.facebook.com/{version}/{phone_number_id}/messages`,
     header `Authorization: Bearer {access_token}`, body JSON
     `{messaging_product: "whatsapp", to, type: "text", text: {body}}`. Ingat batasan
     **customer service window 24 jam**: jika pesan terakhir dari pelanggan sudah lewat
     24 jam, `sendMessage()` untuk Meta harus mengembalikan error jelas (`window_expired`)
     alih-alih mencoba kirim teks bebas — sistem TIDAK diminta mengurus Message Template
     approval pada fase ini (dokumentasikan sebagai keterbatasan, fase 2 jika perlu).
   - `MetaCloudProvider::verifyWebhook()` — cocokkan `hub_verify_token` dari
     `config('services.meta_whatsapp.verify_token')` dengan query `hub.verify_token`,
     balas `hub.challenge` sebagai plain text jika cocok, atau 403 jika tidak.
     `FonnteProvider::verifyWebhook()` cukup return `null` (tidak dipakai).

3. **Controller tunggal (provider-agnostic)** — `app/Http/Controllers/Chatbot/WhatsAppWebhookController.php`:
   - Constructor inject `WhatsAppProviderInterface $provider` (resolve otomatis sesuai
     `.env` berkat binding di §1). Jika kamu juga mengimplementasikan fitur AI opsional
     di poin 8, inject juga `DeepSeekService $ai` — kalau tidak, controller cukup
     memakai rule-based/template reply sederhana untuk poin d dan f di bawah.
   - `verify(Request $request)` — dipanggil route `GET`, delegasikan ke
     `$provider->verifyWebhook($request->query())`. Untuk Fonnte ini tidak pernah
     ke-trigger (tidak perlu didaftarkan route GET-nya), untuk Meta ini WAJIB ada.
   - `handle(Request $request)`:
     a. `$incoming = $provider->parseIncoming($request->all());` — hasil DTO seragam
        (`sender`, `name`, `text`, `type`, dst) TIDAK PEDULI provider mana yang mengirim.
     b. Tentukan `region` pengirim (jika belum ada mapping nomor→region, gunakan
        default region dari `config('app.default_region_id')` atau minta user pilih
        cabang di awal percakapan — putuskan pendekatan termudah dan dokumentasikan).
     c. Kirim `typing` indicator dulu (`$provider->sendTyping()`) sebelum proses balasan,
        supaya UX tidak terasa diam. Untuk provider yang tidak mendukung typing (mis.
        Meta tidak punya fitur ini di endpoint biasa), method ini cukup no-op.
     d. Tentukan balasan: jika fitur AI opsional (poin 8) diimplementasikan, panggil
        `DeepSeekService::detectIntent($incoming['text'])`; jika tidak, gunakan
        pencocokan kata kunci sederhana (mis. "harga", "jam buka", "produk") untuk
        menentukan jenis pertanyaan.
     e. Berdasarkan hasil poin d, ambil data relevan dari DB (produk+varian aktif di
        region, jam operasional dari `customers.opening_hours` bila relevan, dll).
     f. Susun balasan: jika pakai AI, panggil `DeepSeekService::generateReply()` dengan
        context tsb; jika tidak, susun balasan dari template teks statis yang menyisipkan
        data DB langsung.
     g. Kirim balasan via `$provider->sendMessage($incoming['sender'], $reply, $incoming)`
        — parameter provider-spesifik (mis. `inboxid` Fonnte) dikirim lewat array
        `$context`/`$incoming`, ditangani masing-masing implementasi provider secara
        internal, BUKAN oleh controller.
     h. Log seluruh percakapan ke tabel baru `chatbot_conversations` (lihat §4 migrasi),
        sertakan kolom `provider` (`fonnte`/`meta`) untuk audit, TANPA menyimpan data
        sensitif berlebihan.
     i. Return `response()->json(['status' => true])`.
   - Tangani pesan non-teks (lokasi, gambar) dengan balasan sopan bahwa saat ini bot
     hanya bisa membaca teks (foto/lokasi diteruskan manual ke admin/kurir — TODO fase 2).

4. **Migrasi baru** — `chatbot_conversations`:
   ```php
   Schema::create('chatbot_conversations', function (Blueprint $table) {
       $table->id();
       $table->string('provider')->default('fonnte'); // 'fonnte' | 'meta'
       $table->string('sender_number');
       $table->string('sender_name')->nullable();
       $table->foreignId('region_id')->nullable()->constrained();
       $table->text('incoming_message');
       $table->string('detected_intent')->nullable();
       $table->text('bot_reply')->nullable();
       $table->boolean('handled_by_ai')->default(false);
       $table->timestamps();
   });
   ```

5. **Route** — tambahkan di `routes/api.php`:
   ```php
   Route::post('/webhook/whatsapp', [\App\Http\Controllers\Chatbot\WhatsAppWebhookController::class, 'handle']);
   Route::get('/webhook/whatsapp/meta', [\App\Http\Controllers\Chatbot\WhatsAppWebhookController::class, 'verify']);
   Route::post('/webhook/whatsapp/meta', [\App\Http\Controllers\Chatbot\WhatsAppWebhookController::class, 'handle']);
   ```
   Hapus (atau nonaktifkan dengan komentar) route `/api/webhook/meta` lama yang mengarah
   ke `Chatbot\WebhookController` lama — sudah tergantikan sepenuhnya oleh controller
   generik ini. Jangan hapus file controller lama sampai migrasi terbukti stabil di
   production; cukup unregister route-nya dulu.

6. **Keamanan**:
   - **Fonnte**: validasi field wajib payload tidak kosong (tidak ada signature
     verification bawaan di dokumentasi Fonnte saat ini — cek ulang dokumentasi terkini
     sebelum go-live apakah sudah ada mekanisme signature/IP allowlist baru).
   - **Meta**: verifikasi `hub.verify_token` saat `GET` (wajib, lihat §3.4), dan
     pertimbangkan **X-Hub-Signature-256** (HMAC SHA256 memakai App Secret) untuk
     memvalidasi bahwa payload `POST` benar-benar berasal dari Meta — cek dokumentasi
     resmi Graph API webhook security untuk detail implementasi HMAC terkininya.
   - Rate limit endpoint webhook (`throttle:60,1` per menit) agar tidak disalahgunakan.
   - Jangan pernah log isi token/API key apa pun (`FONNTE_TOKEN`, `META_ACCESS_TOKEN`,
     `DEEPSEEK_API_KEY` jika dipakai) ke log file.

7. **Testing** — buat `tests/Feature/WhatsAppWebhookTest.php`:
   - Mock `WhatsAppProviderInterface` (bind fake implementation di container), dan mock
     `DeepSeekService` juga bila poin 8 diimplementasikan (jangan panggil API asli di test).
   - Test dijalankan **dua kali** (data provider Pest/PHPUnit) untuk memastikan behavior
     controller identik baik saat `WHATSAPP_PROVIDER=fonnte` maupun `=meta`.
   - Test: pesan "halo" → balasan ramah/sapaan.
   - Test: pesan "berapa harga kue ijo" → balasan berisi harga varian Kue Ijo dari DB seed.
   - Test: payload tidak lengkap → tetap return 200 tanpa exception (idempoten, baik
     Fonnte maupun Meta akan retry jika gagal).
   - Test khusus Meta: `GET /api/webhook/whatsapp/meta` dengan `hub.verify_token` benar
     → return `hub.challenge`; dengan token salah → 403.
   - Test unit terpisah untuk `FonnteProvider::parseIncoming()` dan
     `MetaCloudProvider::parseIncoming()` masing-masing memakai payload contoh asli dari
     dokumentasi resmi (§2.3 dan §3.4), pastikan keduanya menghasilkan DTO seragam yang
     sama strukturnya.

8. **[OPSIONAL — hanya kerjakan jika pemilik project memang mau fitur AI auto-reply berjalan di production]**
   Fitur ini terpisah dari task-mu sebagai coding agent: kamu hanya menulis kodenya
   sekarang, tapi kode inilah yang nanti akan memanggil API model AI setiap kali ada
   pesan masuk **saat aplikasi berjalan** — model AI itu dikonfigurasi lewat `.env`,
   bebas mau pakai DeepSeek atau provider lain, dan pemilihan model itu tidak ada
   hubungannya dengan model yang kamu (agent) pakai untuk mengerjakan task ini.

   Buat `app/Services/DeepSeekService.php`:
   - Method `chat(array $messages, array $options = []): string` — panggil endpoint
     chat completion (format OpenAI-compatible: `POST {base_url}/chat/completions`,
     body `{model, messages, temperature, max_tokens}`, header
     `Authorization: Bearer {api_key}`).
   - Method `detectIntent(string $userMessage): array` — panggil model dengan
     system prompt khusus yang MEMAKSA output JSON strict, contoh skema:
     ```json
     {
       "intent": "tanya_harga | tanya_produk | tanya_lokasi_jam | cara_order | komplain | sapaan | lainnya",
       "confidence": 0.0,
       "entities": { "produk": null, "kategori": null }
     }
     ```
     Parse JSON dengan try/catch, fallback ke intent `lainnya` jika parsing gagal.
   - Method `generateReply(string $userMessage, array $context): string` — untuk
     menyusun jawaban natural berbahasa Indonesia berdasarkan context (daftar produk +
     harga + region) yang di-fetch dari database, BUKAN dari halusinasi model. Selalu
     sisipkan actual data produk dari DB ke dalam prompt (retrieval, bukan tebakan AI).
   - Timeout request: 15 detik, dengan fallback pesan default jika timeout/error
     (lihat §5 System Prompt untuk model runtime ini).
   - Tambahkan `config/services.php`:
     ```php
     'deepseek' => [
         'api_key' => env('DEEPSEEK_API_KEY'),
         'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
         'model' => env('DEEPSEEK_MODEL', 'deepseek-v4-flash'),
     ],
     ```
     dan `.env.example`: `DEEPSEEK_API_KEY`, `DEEPSEEK_BASE_URL`, `DEEPSEEK_MODEL`.

## BATASAN KERAS (JANGAN DILANGGAR)
- JANGAN membuat/menghapus/mengubah data `Order`, `Customer`, atau `OrderItem` dari
  dalam webhook chatbot ini. Ini murni Q&A/FAQ layer.
- JANGAN hardcode token/API key di kode — selalu lewat `.env` + `config()`.
- JANGAN gunakan `env()` langsung di luar file config (perbaiki pola lama yang salah
  di `WebhookController` lama sebagai referensi apa yang TIDAK boleh diulang).
- Jika kamu mengimplementasikan fitur AI opsional (poin 8): JANGAN biarkan model
  "mengarang" harga/stok produk — semua angka HARUS berasal dari query database aktual,
  model hanya menyusun kalimat.
- Semua balasan bot HARUS berbahasa Indonesia, sopan, singkat (maks ±500 karakter per
  pesan agar nyaman dibaca di WhatsApp), dan menyebutkan bahwa untuk pemesanan resmi
  pelanggan tetap dilayani oleh kurir yang bertugas di wilayahnya.

## BATASAN KERAS TAMBAHAN (khusus arsitektur multi-provider)
- JANGAN biarkan `WhatsAppWebhookController` atau `DeepSeekService` memiliki logika
  `if (provider == fonnte) ... else ...` — semua percabangan provider-spesifik HARUS
  ada di dalam `FonnteProvider`/`MetaCloudProvider`, bukan di controller.
- JANGAN asumsikan `sendTyping()` selalu berefek — provider yang tidak mendukungnya
  harus no-op dengan aman, bukan melempar exception.
- JANGAN campur konfigurasi dua provider dalam satu array config yang sama tanpa
  namespace jelas (`fonnte.*` terpisah dari `meta_whatsapp.*`).

## DELIVERABLE YANG DIHARAPKAN (WAJIB)
1. `app/Contracts/WhatsAppProviderInterface.php`
2. `app/Services/WhatsApp/FonnteProvider.php`
3. `app/Services/WhatsApp/MetaCloudProvider.php`
4. `app/Http/Controllers/Chatbot/WhatsAppWebhookController.php`
5. Migrasi `chatbot_conversations` (dengan kolom `provider`)
6. Update `config/services.php` (bagian `whatsapp`, `fonnte`, `meta_whatsapp`) + `.env.example`
7. Update `routes/api.php` (route generik + route khusus verifikasi Meta)
8. Binding provider di service provider (`WHATSAPP_PROVIDER` env-driven)
9. `tests/Feature/WhatsAppWebhookTest.php` + unit test parsing per-provider
10. Ringkasan singkat di akhir: apa yang diubah, apa yang perlu diisi manual di `.env`
    untuk **masing-masing** provider, dan langkah manual di dashboard
    Fonnte/Meta App Dashboard (lihat §7 & §8 checklist di dokumen analisis untuk detail
    per provider).

## DELIVERABLE OPSIONAL (hanya jika poin 8 di atas dikerjakan)
11. `app/Services/DeepSeekService.php`
12. Bagian `deepseek` di `config/services.php` + `.env.example`
````

---

## 5. System Prompt Runtime untuk Fitur AI Opsional (dipakai di dalam `DeepSeekService::generateReply()`)

> **Ini bukan prompt untuk agent.** Ini adalah *system message* yang dikirim ke API model AI **saat aplikasi berjalan di production** (bagian dari poin 8 opsional di atas), setiap kali ada pesan pelanggan masuk. Kalau kamu (atau agent) memutuskan tidak mengimplementasikan fitur AI opsional ini, lewati saja bagian ini.

Gunakan prompt berikut sebagai **system message** saat memanggil model AI dari `generateReply()`:

```
Kamu adalah asisten WhatsApp resmi toko kue tradisional "Kue Pandan Asli".
Tugasmu HANYA menjawab pertanyaan seputar produk, harga, kategori (Produk/Hampers/
Tumpeng), jam operasional, dan cara pemesanan — berdasarkan DATA yang diberikan di
bawah, BUKAN pengetahuan umummu.

ATURAN:
1. Jawab dalam Bahasa Indonesia yang ramah, singkat, dan natural (bukan seperti robot).
2. Jangan pernah menyebutkan harga atau stok yang TIDAK ADA di data konteks di bawah.
   Jika data tidak tersedia, katakan dengan jujur akan diteruskan ke kurir/admin.
3. Jangan pernah membuat janji pengiriman, diskon, atau kebijakan yang tidak ada di data.
4. Jika pelanggan ingin memesan, arahkan mereka untuk menghubungi kurir wilayah mereka
   (jangan berpura-pura bisa memproses pesanan).
5. Jika pertanyaan di luar topik toko kue (politik, hal pribadi, dll), tolak dengan
   sopan dan arahkan kembali ke topik toko.
6. Maksimal 3-4 kalimat per balasan, hindari format markdown (WhatsApp polos teks).
7. Jangan gunakan emoji berlebihan (maks 1-2 jika relevan).

DATA KONTEKS SAAT INI:
{{context_json}}

Pertanyaan pelanggan: "{{user_message}}"
```

> `{{context_json}}` diisi dinamis oleh `WhatsAppWebhookController` berisi daftar
> produk+varian+harga aktif di region pengirim (hasil query Eloquent, bukan hasil AI).

---

## 6. System Prompt untuk Provider Abstraction (opsional, dipakai jika ingin membuat interface secara terpisah)

Jika ingin agent fokus dulu pada layer abstraksi sebelum menyentuh fitur AI opsional, gunakan prompt ringkas ini sebagai task terpisah:

```
Buat interface WhatsAppProviderInterface di app/Contracts/ dengan method sendMessage(),
sendTyping(), parseIncoming(), dan verifyWebhook(). Implementasikan FonnteProvider dan
MetaCloudProvider sesuai spesifikasi format request/response masing-masing vendor
(rujuk dokumen analisis §2 untuk Fonnte, §3 untuk Meta Cloud API). Pastikan kedua
implementasi mengembalikan bentuk data yang identik strukturnya dari parseIncoming(),
supaya kode pemanggil (controller/service) tidak perlu tahu provider mana yang aktif.
Tambahkan binding otomatis berbasis config('services.whatsapp.provider') di service
provider Laravel. Sertakan unit test untuk parseIncoming() masing-masing provider
memakai contoh payload asli dari dokumentasi resmi.
```

---

## 7. Checklist Setup Manual — Fonnte

- [ ] Daftar akun Fonnte & tambahkan **Device** (scan QR dengan nomor WA toko).
- [ ] Salin **Token** device dari dashboard Fonnte → isi ke `.env` → `FONNTE_TOKEN`.
- [ ] Set `.env` → `WHATSAPP_PROVIDER=fonnte`.
- [ ] Buka **Device → Edit** → isi **Webhook URL** dengan
      `https://domain-kamu.com/api/webhook/whatsapp`.
- [ ] **Wajib** set toggle **Auto Read = ON** pada device tersebut.
- [ ] Aktifkan fitur **Inbox** pada device jika ingin memakai `inboxid` untuk reply
      (disyaratkan agar parameter `inboxid` berfungsi saat kirim balasan).
- [ ] Uji kirim pesan test ke nomor device Fonnte, pastikan balasan otomatis muncul.

## 8. Checklist Setup Manual — Meta WhatsApp Cloud API

- [ ] Buat **Meta App** dengan use case *Connect with customers through WhatsApp* di
      [App Dashboard](https://developers.facebook.com/apps).
- [ ] Hubungkan ke **WhatsApp Business Account (WABA)** baru/existing, catat
      **WABA ID** dan **test phone number ID**.
- [ ] (Produksi) Registrasi **nomor bisnis asli** lewat WhatsApp Manager + verifikasi
      kepemilikan, lalu register via API (tidak bisa lewat dashboard saja).
- [ ] Buat **System User** di Business Settings → assign asset app & WABA (Full control)
      → **Generate token** dengan permission `business_management`,
      `whatsapp_business_messaging`, `whatsapp_business_management`.
- [ ] Isi `.env`: `META_ACCESS_TOKEN`, `META_PHONE_NUMBER_ID`, `META_API_VERSION`
      (mis. `v23.0`), dan `META_VERIFY_TOKEN` (buat string rahasia sendiri, bebas).
- [ ] Set `.env` → `WHATSAPP_PROVIDER=meta`.
- [ ] Di **App Dashboard → WhatsApp → Configuration**, daftarkan callback URL
      `https://domain-kamu.com/api/webhook/whatsapp/meta` + verify token yang sama
      dengan `META_VERIFY_TOKEN`, lalu **subscribe ke field `messages`**.
- [ ] Pastikan app dalam mode **Live** (bukan Dev) — beberapa webhook tidak terkirim di
      mode Dev.
- [ ] Kirim pesan test dari WA pribadi ke test number → balas dari sana → cek apakah
      customer service window 24 jam aktif → coba kirim balasan teks bebas dari sistem.

## 9. Checklist Umum (berlaku untuk provider manapun)

- [ ] Daftar API key DeepSeek → isi ke `.env` → `DEEPSEEK_API_KEY`.
- [ ] Jalankan migrasi baru: `php artisan migrate`.
- [ ] Pantau tabel `chatbot_conversations` (kolom `provider` menunjukkan mana yang
      dipakai) untuk quality-check jawaban bot beberapa hari pertama sebelum benar-benar
      menonaktifkan proses manual lama.
- [ ] Simulasikan switch provider: ubah `WHATSAPP_PROVIDER` di `.env`, `php artisan
      config:clear`, kirim pesan test lagi — pastikan tidak ada kode lain yang perlu
      diubah.

---

## 10. Catatan Tambahan / Risiko

| Risiko | Mitigasi |
|---|---|
| Fonnte butuh paket **super/advanced/ultra** untuk kirim gambar via `url` | Fase awal cukup balasan teks saja; kirim gambar produk bisa fase 2 |
| Meta: balasan di luar window 24 jam wajib Message Template ter-approve | Untuk fase awal, dokumentasikan keterbatasan ini; kalau perlu balasan di luar window, siapkan template & App Review sebelumnya (proses bisa berhari-hari) |
| Meta: setup awal jauh lebih rumit & makan waktu (App Review, verifikasi bisnis) | Kalau target go-live cepat, mulai dari Fonnte, sisakan Meta sebagai opsi migrasi nanti |
| Biaya panggilan DeepSeek per pesan | Terapkan cache jawaban FAQ umum (misal Redis, TTL 1 jam) untuk pertanyaan yang identik |
| Bot salah jawab / halusinasi | Selalu suntik data DB nyata ke prompt (retrieval-based), bukan biarkan model menebak |
| Nomor WA toko berbeda dari nomor kurir pribadi | Perlu keputusan bisnis: apakah bot ini melengkapi (bukan menggantikan) chat langsung ke kurir |
| Auto Read wajib ON di Fonnte / App harus Live di Meta | Tanpa ini webhook tidak akan pernah terpanggil sama sekali, di provider manapun |
| Drift antara dua implementasi provider seiring waktu | Wajib test suite yang menjalankan skenario sama di kedua provider (§4 poin 8) agar perilaku tetap konsisten saat salah satu vendor update API-nya |

---

*Dokumen ini disusun berdasarkan analisis dokumentasi project "Kue Pandan Asli", dokumentasi resmi [Fonnte](https://docs.fonnte.com/), dan dokumentasi resmi [Meta for Developers — WhatsApp Business Platform](https://developers.facebook.com/docs/). Verifikasi ulang detail API kedua vendor (terutama versi Graph API, kebijakan pricing/template, dan mekanisme keamanan webhook terbaru) di dokumentasi resmi masing-masing sebelum deploy ke production, karena dokumentasi pihak ketiga maupun resmi dapat berubah sewaktu-waktu.*
