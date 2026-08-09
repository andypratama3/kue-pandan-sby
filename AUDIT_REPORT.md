# Audit Report — Kue Pandan Asli

## Ringkasan Eksekutif

Sistem **Kue Pandan Asli** telah diimplementasikan dengan baik untuk **web application** dengan arsitektur multi-cabang yang kuat dan isolasi data yang ketat. Namun, implementasi **chatbot WhatsApp** tidak sesuai dengan spesifikasi diagram operasional yang diberikan. 

**Web app siap production** dengan keamanan dan business rules yang solid, tetapi **chatbot WhatsApp memiliki gap fitur yang signifikan** dan berperan hanya sebagai **customer service informasi dasar**, bukan sebagai sistem pemesanan otomatis seperti yang digambarkan dalam diagram.

**Status readiness:**
- ✅ **Web app**: Siap production dengan isolasi data kuat, validasi harga anti-spoofing, dan workflow lengkap
- ⚠️ **Chatbot WhatsApp**: Hanya implementasi dasar, belum sesuai spesifikasi diagram operasional
- ✅ **Testing**: 70 test passed dengan coverage memadai untuk business logic utama

## Status Kesesuaian Flow Chatbot WhatsApp

| Sub-flow (ref. bagian 2.x) | Status | Bukti (file:baris) | Catatan |
|----------------------------|--------|--------------------|---------|
| 2.1 Flow Utama Percakapan | ❌ Belum diimplementasikan | `WhatsAppReplyService.php:1-200` | Hanya intent dasar tanpa flow multi-step |
| 2.2 Pesan Sambutan | ✅ Sudah sesuai | `WhatsAppReplyService.php:101-117` | Sambutan dengan nama region dinamis |
| 2.3 Menu / Katalog | ⚠️ Sebagian/perlu perbaikan | `WhatsAppReplyService.php:144-156` | Data produk dari DB, tetapi tidak sesuai daftar diagram |
| 2.4 Form Pemesanan | ❌ Belum diimplementasikan | `WhatsAppWebhookController.php:1-150` | Tidak ada parsing form atau pembuatan order |
| 2.5 Opsi Pengiriman | ❌ Belum diimplementasikan | Tidak ada kode terkait | Tidak ada implementasi |
| 2.6 Perhitungan Ongkir | ❌ Belum diimplementasikan | Tidak ada kode terkait | Tidak ada tabel jarak atau service ongkir |
| 2.7 Jadwal Pengantaran | ❌ Belum diimplementasikan | Tidak ada kode terkait | Tidak ada slot waktu |
| 2.8 Konfirmasi Pembayaran | ❌ Belum diimplementasikan | `WhatsAppReplyService.php:1-200` | Tidak ada state management untuk konfirmasi |
| 2.9 Follow-up FAQ | ⚠️ Sebagian/perlu perbaikan | `WhatsAppReplyService.php:57-90` | Intent detection dasar, tidak ada cek jam operasional real-time |
| 2.10 Alur Internal | ⚠️ Sebagian/perlu perbaikan | `ChatbotConversation.php:1-30` | Hanya logging, tidak ada integrasi ke `orders` |
| 2.11 Database Pendukung | ❌ Belum diimplementasikan | Tidak ada migration tabel jalan/jarak | Tidak ada tabel pendukung |

## Temuan Bug/Celah (diurutkan severity)

### 🔴 Critical
1. **Stateless Chatbot Architecture** — `WhatsAppWebhookController.php:84-120` — Chatbot tidak memiliki state management untuk multi-turn conversations, setiap pesang diproses independen tanpa konteks sebelumnya. **Lang reproduksi**: Kirim "hi" lalu "harga kue ijo" → bot balas dengan harga. Kirim "sudah transfer" → bot tidak tahu konteks sebelumnya dan merespon sebagai intent "lainnya". **Rekomendasi fix**: Implementasi session state per `sender_number` dengan kolom `current_step` di `chatbot_conversations`.

### 🟠 High
1. **Hardcode Nama "Pak Dian" dalam Business Logic** — Spesifikasi diagram menyebutkan fallback ke "Pak Dian" untuk jarak >14km, tetapi ini nama personal yang di-hardcode. **Bukti**: Spesifikasi bagian 2.6. **Rekomendasi fix**: Buat tabel `staff` dengan kolom `role` dan `name`, atau konfig variabel per region.
2. **Inkonsistensi Data Produk** — `ProductSeeder.php` vs Diagram — Nama produk di seeder berbeda dengan diagram ("Kue Koci Hitam" di diagram tidak ada di seeder; "Kue Mix"/"Kue Mix Mini" di seeder tidak ada di diagram). **Rekomendasi fix**: Sinkronisasi data produk antara diagram dan database.

### 🟡 Medium
1. **Isolasi Region Chatbot Lemah** — `WhatsAppWebhookController.php:94-102` — Region ditentukan berdasarkan `sender_number` terakhir di `chatbot_conversations`, tidak ada mapping nomor WA → region yang eksplisit. **Risiko**: Nomor WA bisa salah cabang jika pernah chat di cabang lain. **Rekomendasi fix**: Tabel mapping `whatsapp_numbers` dengan `region_id`.
2. **Tidak Ada Database Jalan/Jarak** — Spesifikasi bagian 2.11 menyebutkan "Database Jalan" untuk perhitungan ongkir, tetapi tidak ada migration tabel terkait. **Rekomendasi fix**: Buat migration `distance_routes` dengan `origin`, `destination`, `distance_km`, `region_id`.
3. **File Upload tanpa Validasi Mime Type** — `ReturnController.php` dan controller upload lainnya tidak memvalidasi mime type dengan ketat. **Rekomendasi fix**: Tambah validasi `mimetypes:image/jpeg,image/png,image/jpg`.
4. **Hardcode Alamat Outlet** — `WhatsAppReplyService.php:22-36` — Alamat outlet di-hardcode di array PHP, bukan dari database. **Rekomendasi fix**: Tambah kolom `address`, `operating_hours`, `contact_email` di tabel `regions`.

### 🟢 Low
1. **Komentar Artifact `[!code ...]`** — Tersebar di beberapa file controller. **Rekomendasi fix**: Bersihkan komentar debug.
2. **File Legacy Tidak Terpakai** — `OldCustomerController.php`, `OldKurirCustomerController.php`, `WebhookController.php` (legacy). **Rekomendasi fix**: Hapus atau archive.
3. **Timezone Configuration Default UTC** — `config/app.php:66` — Masih `UTC` bukan `Asia/Jakarta`. **Rekomendasi fix**: Update ke timezone Indonesia.

## Gap Fitur (bukan bug, belum diimplementasikan)

1. **Form Pemesanan WhatsApp** — Tidak ada parsing template form atau ekstraksi field (nama pemesan, alamat, tanggal kirim).
2. **Perhitungan Ongkir Otomatis** — Tidak ada service/helper untuk menghitung ongkir berdasarkan jarak.
3. **State Management Multi-turn** — Tidak ada session state untuk tracking step percakapan.
4. **Auto-create Order dari WhatsApp** — Chatbot hanya menyimpan percakapan, tidak membuat order di database.
5. **Integrasi Order Chatbot → Web App** — Tidak ada mekanisme notifikasi ke admin/kurir untuk follow-up order dari chatbot.
6. **Handoff ke Human (Operator)** — Tidak ada mekanisme handoff dari bot ke operator manusia.
7. **Database Jarak/Alamat** — Tidak ada tabel referensi untuk perhitungan ongkir.

## Hasil Test Suite (aktual, dijalankan ulang)

```
Tests:    8 skipped, 70 passed (209 assertions)
Duration: 88.12s
Exit Code: 0
```

**Test yang skipped:**
- API token tests (4 test) — API support not enabled
- Email verification tests (3 test) — Email verification not enabled  
- Registration test (1 test) — Registration support not enabled

**Coverage test memadai untuk:**
- Isolasi region multi-cabang
- Business logic checkout dengan anti-spoofing harga
- Role-based access control
- Flow retur dan verifikasi admin
- WhatsApp provider parsing

## Checklist Production Readiness

- [✅] APP_DEBUG=false — `.env.example:4` sudah set `false`
- [✅] Semua secret tidak ter-commit — `.gitignore:10-12` mengabaikan `.env`
- [⚠️] Rate limiting semua webhook — Hanya Meta webhook yang di-throttle (60/menit), Fonnte tidak
- [✅] Index DB lengkap — Migration memiliki index untuk `region_id`, `status`, `sender_number`
- [✅] Error handling webhook tidak memicu retry storm — `WhatsAppWebhookController.php:50-70` return 200 meski error internal
- [❌] Queue untuk job berat — Semua proses sync (PDF generation, WhatsApp reply)
- [✅] Storage link terdokumentasi — `README.md:117` menyebutkan `php artisan storage:link`
- [⚠️] Localization — `config/app.php` masih `en`/`UTC`, tetapi controller handle timezone manual
- [✅] Database constraints — Foreign key constraints dan cascade delete
- [✅] File upload validation — Validasi size (2MB) ada di beberapa controller
- [⚠️] Session driver — Menggunakan `database` (scalable), tetapi tidak ada cleanup job
- [✅] HTTPS enforcement — Middleware `TrustProxies` ada

## Rekomendasi Prioritas (Top 5 yang harus dikerjakan sebelum go-live)

1. **Implement State Management Chatbot** — Tambah kolom `current_step`, `session_data` (JSON) di `chatbot_conversations` untuk multi-turn conversations.
2. **Buat Mapping WhatsApp Number → Region** — Tabel `whatsapp_business_numbers` dengan `phone_number`, `region_id`, `provider`.
3. **Implement Basic Order Flow via WhatsApp** — Minimal: parsing form sederhana dan notifikasi ke admin untuk follow-up manual.
4. **Database Referensi Jarak** — Migration `distance_routes` untuk perhitungan ongkir dasar.
5. **Queue untuk Heavy Operations** — Implement job queue untuk PDF generation dan WhatsApp reply blast.

## Kesimpulan

**Web application sudah siap production** dengan arsitektur yang solid dan keamanan yang baik. Isolasi multi-cabang berfungsi dengan baik, business rules diimplementasikan dengan benar, dan test coverage memadai.

**Chatbot WhatsApp belum siap production** sesuai spesifikasi diagram. Implementasi saat ini hanya berupa **customer service informasi dasar** tanpa kemampuan pemesanan otomatis. **Disarankan untuk:**
1. Klarifikasi requirement: Apakah chatbot memang dimaksudkan untuk auto-order creation atau hanya asisten informasi?
2. Jika perlu auto-order, implement state management dan parsing form terlebih dahulu.
3. Jika hanya asisten informasi, perbaiki dokumentasi untuk menghindari misalignment ekspektasi.

**Rekomendasi deployment bertahap:**
- Phase 1: Deploy web app (siap production)
- Phase 2: Deploy chatbot sebagai informasi dasar (current state)
- Phase 3: Enhance chatbot dengan flow pemesanan (future enhancement)