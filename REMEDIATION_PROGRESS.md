# Remediation Progress Report — Kue Pandan Asli

## Progress Overview
**Date**: 9 Agustus 2026  
**Status**: 🟡 In Progress - Waiting for Business Decisions

---

## ✅ Completed Items

### Item #7 — Validasi Mime Type File Upload
- **Status**: ✅ Selesai
- **File yang diubah**:
  - `app/Http/Controllers/ReturnController.php`
  - `app/Http/Controllers/ProductController.php`
  - `app/Http/Controllers/Kurir/PesananController.php`
  - `app/Actions/Fortify/UpdateUserProfileInformation.php`
- **Migration baru**: Tidak ada
- **Test ditambahkan/diupdate**:
  - `tests/Feature/FileUploadValidationTest.php` (baru, 3 test cases)
  - Factories dibuat: `RegionFactory`, `CustomerFactory`, `ProductFactory`, `ProductVariantFactory`, `CategoryFactory`, `OrderFactory`, `CustomerCategoryFactory`
- **Hasil test suite**: **73 passed, 8 skipped** (217 assertions)
- **Perubahan**:
  - Menambahkan validasi `mimetypes:image/jpeg,image/png,image/gif` untuk semua endpoint upload (bukti bayar, bukti retur, gambar produk, foto profil)
  - Validasi sekarang mengecek **konten file** bukan hanya ekstensi, mencegah file palsu (text file di-rename ke .jpg)
- **Temuan tambahan**: Tidak ada bug baru

---

## 🟡 Partially Completed Items

### Item #2 — Hilangkan Hardcode "Pak Dian" dan Nama Personal Lain
- **Status**: ⚠️ Migration selesai, implementasi tertunda
- **Migration baru**: `2026_08_09_085011_add_escalation_contact_to_regions_table.php`
- **Kolom ditambahkan**: `escalation_contact_name`, `escalation_contact_phone` ke tabel `regions`
- **Model updated**: `app/Models/Region.php` (fillable)
- **Catatan**: 
  - Migration sudah dijalankan dan kolom tersedia
  - Belum ada implementasi kode yang menggunakan "Pak Dian" ditemukan di codebase saat ini
  - Referensi "Pak Dian" hanya disebutkan di spesifikasi diagram (bagian 2.6 tentang ongkir >14km)
  - **Kesimpulan**: Fitur perhitungan ongkir otomatis (termasuk fallback "Pak Dian") belum diimplementasikan sama sekali
- **Next Steps**: Implementasi akan dilakukan bersamaan dengan Item #5 (Tabel Referensi Jarak/Ongkir)

---

## ⛔ Blocked Items (Butuh Keputusan User)

### Item #1 — State Management Chatbot (Multi-turn Conversation)
**Butuh keputusan**:
> Apakah chatbot WhatsApp dimaksudkan untuk membuat order otomatis di database (auto-order), atau tetap sebagai asisten informasi yang order akhirnya tetap diinput manual oleh kurir di web app?

**Implikasi**:
- **Opsi A (info-only)**: State management ringan, hanya untuk menjaga konteks FAQ
- **Opsi B (auto-order)**: State machine penuh dengan validasi ketat dan integrasi ke tabel `orders`

---

### Item #3 — Sinkronisasi Data Produk (Diagram vs Seeder vs Bot)
**Butuh konfirmasi**:
> Daftar produk final per kategori (Tumpeng/Hampers/Produk Ala Carte) — mana yang benar?

**Inkonsistensi ditemukan**:
- **Di Diagram**: Kue Koci Hitam (ada)
- **Di ProductSeeder**: Kue Mix, Kue Mix Mini (ada), Kue Koci Hitam (tidak ada)
- **Di WhatsAppReplyService**: Mengambil data dari DB secara live ✅ (sudah benar)

**Rekomendasi**: Konfirmasi daftar produk resmi, lalu update seeder untuk konsistensi

---

### Item #4 — Perkuat Mapping Nomor WhatsApp → Region
**Butuh klarifikasi fakta lapangan**:
> Apakah tiap cabang punya nomor WhatsApp Business terpisah, atau satu nomor WA melayani semua cabang?

**Implikasi**:
- **Nomor beda per cabang** → Buat tabel `whatsapp_business_numbers` dengan mapping `phone_number` → `region_id`
- **Satu nomor untuk semua** → Bot harus tanya cabang mana ke customer secara eksplisit

---

### Item #6 — Order Flow via WhatsApp (Form Parsing + Auto/Manual Order)
**Bergantung pada keputusan Item #1**

---

## 📋 Ready to Execute (Tidak Butuh Keputusan)

### Item #5 — Tabel Referensi Jarak/Ongkir
**Siap dikerjakan**

### Item #8 — Alamat Outlet & Jam Operasional dari Database
**Siap dikerjakan**

### Item #9 — Rate Limiting Webhook Fonnte
**Siap dikerjakan**

### Item #10 — Queue untuk Operasi Berat
**Siap dikerjakan**

### Item #11 — Timezone & Locale Aplikasi
**Siap dikerjakan**

### Item #12 — Bersihkan Kode Legacy
**Dikerjakan terakhir setelah semua item lain stabil**

---

## 🔍 Pertanyaan untuk User

Sebelum melanjutkan remediation, mohon konfirmasi hal-hal berikut:

1. **[CRITICAL] Chatbot WhatsApp Purpose**: Auto-order atau info-only? (Item #1 & #6)
2. **[HIGH] Mapping Nomor WA**: Apakah tiap cabang punya nomor WA Business terpisah? (Item #4)
3. **[MEDIUM] Daftar Produk**: Daftar produk final yang harus ada di seeder? (Item #3)
4. **[LOW] Infrastruktur Queue**: Apakah Redis tersedia untuk production, atau pakai database queue? (Item #10)

---

## Next Steps

**Setelah mendapat jawaban**:
1. Lanjutkan Item #5, #8, #9, #11 (tidak bergantung keputusan)
2. Implementasi Item #1, #4, #6 sesuai keputusan
3. Queue implementation (Item #10)
4. Cleanup legacy code (Item #12)
5. Audit final & laporan lengkap

**Estimated Timeline**: 2-3 hari kerja setelah keputusan bisnis dikonfirmasi
