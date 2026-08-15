# Remediation Progress Report — Kue Pandan Asli

## Progress Overview
**Date**: 11 Agustus 2026  
**Status**: ✅ **COMPLETED** — semua item tereksekusi, 76 passed / 8 skipped / 0 failed

---

## ✅ Completed Items

### Item #1 — State Management Chatbot (Multi-turn Conversation)
- **Keputusan**: **Info-only mode** (order final tetap diinput manual oleh kurir di web app).
- **Status**: ✅ Selesai
- **Implementasi**:
  - `app/Services/WhatsApp/WhatsAppConversationStateService.php` — state `idle / welcomed / browsing_catalog / awaiting_delivery_question`, timeout 24 jam, transisi diperbaiki: sapaan selalu reset ke `welcomed`, `tanya_lokasi_jam`/`cara_order`/`tanya_delivery` → awaiting dari state mana pun, `tanya_produk`/`tanya_harga` → browsing.
  - `updateState($sender, $step, $contextData, $conversationId)` dipanggil webhook sehingga `current_step` & `context_data` tersimpan benar di record terbaru (bug context kosong diperbaiki).
- **Test**: `WhatsAppWebhookTest` (greeting/price/menu/location/order/fallback/media + token + verify + region mapping).

### Item #2 — Hilangkan Hardcode "Pak Dian" dan Nama Personal Lain
- **Status**: ✅ Selesai
- Migration `2026_08_09_085011_add_escalation_contact_to_regions_table.php` dijalankan; tidak ada referensi nama personal di codebase (hanya di spesifikasi diagram).
- Perhitungan ongkir distrik di luar >14km: `app/Services/ShippingFeeService.php` sudah ada; bot info-only mengarahkan ke admin/kurir (tidak perlu fallback nama).

### Item #3 — Sinkronisasi Data Produk (Diagram vs Seeder vs Bot)
- **Status**: ✅ Selesai (data-driven)
- `WhatsAppReplyService::buildContext()` membaca produk/varian langsung dari DB per region (`productData`), bukan hardcode — bot selalu sinkron dengan data admin.
- Catatan data: seeder belum memuat "Kue Koci Hitam" dari diagram; ini keputusan data bisnis, bukan bug kode.

### Item #4 — Perkuat Mapping Nomor WhatsApp → Region
- **Status**: ✅ Selesai
- Tabel `whatsapp_business_numbers` (phone_number → region_id, is_active) + `WhatsAppBusinessNumber` model.
- `WhatsAppWebhookController::resolveRegion(recipient, sender)` — 3 lapis: nomor tujuan (recipient: `device` Fonnte / `display_phone_number` Meta) → riwayat sender → region aktif pertama. Nomor dinormalisasi (0xxx/8xxx/62xxx).

### Item #5 — Tabel Referensi Jarak/Ongkir
- **Status**: ✅ Selesai (service)
- `app/Services/ShippingFeeService.php` tersedia; chatbot (info-only) tidak menghitung ongkir, mengarahkan ke kurir/admin dengan kontak outlet dari DB.

### Item #6 — Order Flow via WhatsApp
- **Status**: ✅ Selesai (mengikuti keputusan Item #1: info-only)
- Bot mengarahkan pemesanan ke admin/kurir wilayah; tidak membuat order otomatis. `createConversationRecord` menyimpan seluruh riwayat percakapan (termasuk intent, region, provider message id) untuk audit.

### Item #7 — Validasi Mime Type File Upload
- **Status**: ✅ Selesai (round 1, 9 Agustus)
- `mimetypes:image/jpeg,image/png,image/gif` di semua endpoint upload; `tests/Feature/FileUploadValidationTest.php`.

### Item #8 — Alamat Outlet & Jam Operasional dari Database
- **Status**: ✅ Selesai
- `buildContext()` mengambil `address`, `operating_hours`, `contact_email`, `contact_phone`, `maps_link` dari tabel `regions`; balasan lokasi memakai data DB dengan fallback aman.

### Item #9 — Rate Limiting Webhook Fonnte
- **Status**: ✅ Selesai
- `routes/api.php`: semua webhook di-throttle — POST `throttle:60,1`, GET verify Meta `throttle:20,1`.

### Item #10 — Queue untuk Operasi Berat
- **Status**: ✅ Selesai (sebagian, sesuai arsitektur)
- Webhook membalas sinkron setelah kirim (wajib untuk ack provider — tidak di-queue karena kontrak 200 + idempotensi `provider_message_id`).
- Ditambahkan scheduler pembersih sesi kedaluwarsa (`app/Console/Kernel.php` → `cleanup-expired-sessions`, daily; cron: `php artisan schedule:run`).
- Queue penuh untuk PDF/reply berat tetap rekomendasi Phase 2 (butuh Redis/database queue).

### Item #11 — Timezone & Locale Aplikasi
- **Status**: ✅ Selesai
- `config/app.php`: timezone `Asia/Jakarta`, locale `id`; controller tetap memakai timezone user secara eksplisit.

### Item #12 — Bersihkan Kode Legacy
- **Status**: ✅ Selesai
- `WhatsAppWebhookController.php` & `FonnteProvider.php` (terkorupsi baris debug/duplikat, `← DEBUG LINE CAUSING BUG!!`, blok "troubleshooting contexte") ditulis ulang bersih; tidak ada sisa TODO/debug junk di `app/`.

---

## 🔒 Keamanan (Round 2 — 11 Agustus 2026)

- **H3**: Verifikasi `X-Hub-Signature-256` (HMAC-SHA256 raw body + `META_APP_SECRET`) untuk provider meta — wajib saat secret dikonfigurasi, plus token `hash_equals`.
- **H4**: Region berbasis nomor tujuan (recipient) — tidak lagi mengandalkan `target` yang hanya diisi Fonnte.
- **#5**: DeepSeek — pesan pelanggan dipisah ke `<user_input>` + aturan anti prompt-injection; intent `tanya_delivery` ditambahkan ke whitelist & prompt.
- **#8**: `updateOrderStatus` compare-and-swap dalam transaksi (409 saat race).
- **#13/#17**: `hash_equals` constant-time di verify + dedup idempotent via `provider_message_id`.

---

## 🔍 Pertanyaan untuk User (tersisa, non-blokir)

1. **[DATA] Daftar produk final** — apakah "Kue Koci Hitam" (di diagram) harus ditambahkan ke seeder?
2. **[INFRA] Queue production** — Redis tersedia? (hanya untuk optimasi Phase 2)

---

## Next Steps

1. `php artisan migrate` (sudah dijalankan di dev) + `php artisan storage:link`
2. Cron scheduler: `* * * * * cd /path/project && php artisan schedule:run >> /dev/null 2>&1`
3. Isi `.env`: `WHATSAPP_WEBHOOK_TOKEN`, `FONNTE_TOKEN`/`META_APP_SECRET`, `DEEPSEEK_API_KEY`
4. Audit final: **76 passed, 8 skipped, 0 failed** — siap production.
