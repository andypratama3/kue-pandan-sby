# 🎂 Kue Pandan Asli — Dokumentasi Lengkap Proyek

> **Ringkasan menyeluruh dari aplikasi web manajemen order & delivery toko kue tradisional "Kue Pandan Asli"** (Sistem Reseller & Kurir Multi-Cabang).

---

## 1. Gambaran Umum

**Kue Pandan Asli** adalah aplikasi web berbasis **Laravel 10 + Livewire 3 + Tailwind CSS** yang digunakan untuk mendigitalisasi proses bisnis toko kue tradisional yang beroperasi dengan model **Reseller** (toko/kios yang menjual kembali) dan **Kurir** (tenaga pengantar sekaligus penagih pembayaran).

Aplikasi ini berfungsi sebagai:
- **Pusat data operasional** bagi Admin untuk memantau penjualan, mengelola master data, dan membuat laporan.
- **Platform pelaporan real-time** bagi Kurir untuk menginput pesanan dan memperbarui status pengiriman.

### 🔄 Alur Bisnis Unik

```
Pelanggan ──pesan via WhatsApp──▶ Kurir
                                     │
                              Kurir menerima pesanan & pembayaran
                                     │
                        Kurir menginput pesanan + upload bukti bayar
                                     │
                        Admin toko mengkonfirmasi (verifikasi) pesanan
                                     │
                        Admin membuat laporan penjualan PDF & kirim invoice
                                     │
                              Reseller / Supermarket (Customer)
```

1. **Pemesanan** — Pelanggan memesan kue via **WhatsApp** langsung ke Kurir (tidak ada e-commerce online).
2. **Aksi Kurir** — Kurir menerima pesanan, mengelola pembayaran, lalu menginput data pesanan + bukti pengiriman/pembayaran ke aplikasi web.
3. **Aksi Admin** — Admin mengkonfirmasi pesanan, mengelola master data (Kurir, Customer, Produk), membuat laporan harian (PDF), dan mengirim invoice ke tiap Reseller.

### 🌏 Multi-Cabang (Multi-Region)

Sistem mendukung **3 cabang/region** yang terisolasi data:
| Region | Slug | Zona Waktu |
|--------|------|------------|
| Surabaya | `surabaya` | Asia/Jakarta (WIB) |
| Malang | `malang` | Asia/Jakarta (WIB) |
| Denpasar | `denpasar` | Asia/Makassar (WITA) |

Setiap user (admin & kurir) terikat ke satu `region_id`; semua data (produk, customer, order, laporan) difilter berdasarkan region.

---

## 2. Teknologi & Stack

### Backend
| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| PHP | ^8.1 | Bahasa pemrograman |
| Laravel | ^10.10 | Framework utama |
| Laravel Fortify | ^1.31 | Autentikasi backend |
| Laravel Jetstream | ^4.3 | UI autentikasi + profile |
| Laravel Sanctum | ^3.3 | API tokens / session auth |
| Livewire | ^3.0 | Komponen interaktif (halaman homepage) |
| Spatie Laravel Permission | ^6.21 | Role-based access control (`admin`, `kurir`) |
| Barryvdh Laravel DomPDF | ^3.1 | Generate PDF (invoice, rekap, laporan) |
| Intervention Image | ^3.11 | Kompresi gambar bukti pembayaran (JPG) |
| Guzzle HTTP | ^7.2 | HTTP client (untuk webhook Meta/WhatsApp) |
| Laravel Tinker | ^2.8 | REPL debugging |

### Frontend
| Teknologi | Keterangan |
|-----------|------------|
| Tailwind CSS 3 | Styling utama |
| Alpine.js 3 | Interaktivitas UI (CDN) |
| Argon Dashboard (Tailwind) | Template dashboard admin/kurir |
| Chart.js | Grafik penjualan & performa (CDN) |
| jQuery + daterangepicker | Filter rentang tanggal laporan PDF |
| AOS (Animate On Scroll) | Animasi scroll di homepage |
| medium-zoom | Zoom foto produk di homepage |
| Font Awesome | Ikon |
| Vite 6 | Build tool aset frontend |

### Database & Lainnya
- **MySQL / MariaDB**
- **Vite** (`npm run dev`) + Composer (PHP dependencies)
- **Session driver**: `database` (tabel `sessions`)

---

## 3. Struktur Direktori Utama

```
web-app-toko-kue/
├── app/
│   ├── Actions/                    # Action Jetstream (Fortify)
│   │   ├── Fortify/                # CreateNewUser, ResetUserPassword, dll
│   │   └── Jetstream/              # DeleteUser
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Courier, Order, PeformaKurir, PeformaCustomer, OldCustomer (legacy)
│   │   │   ├── Kurir/              # Pesanan, OldKurirCustomer (legacy)
│   │   │   ├── Chatbot/            # WebhookController (Meta WhatsApp)
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── KurirDashboardController.php
│   │   │   ├── CustomerController.php      # CRUD customer (dipakai admin & kurir)
│   │   │   ├── HistoryOrderController.php  # History + invoice + export PDF
│   │   │   ├── ProductController.php       # CRUD produk (admin)
│   │   │   └── ReturnController.php        # Flow retur (kurir)
│   │   ├── Middleware/             # Middleware bawaan Laravel
│   │   └── Responses/LoginResponse.php     # Redirect post-login berdasarkan role
│   ├── Livewire/Homepage.php       # Komponen homepage (toggle menu mobile)
│   ├── Models/                     # 10 model Eloquent
│   ├── Policies/DashboardPolicy.php # Kosong (belum diimplementasi)
│   ├── Providers/                  # ServiceProvider (Fortify, Jetstream, dll)
│   └── View/Components/            # AppLayout, GuestLayout
├── config/                         # Konfigurasi Laravel + fortify/jetstream/permission
├── database/
│   ├── migrations/                 # 28 file migrasi (Jul 2025 – Jan 2026)
│   └── seeders/                    # RoleAndUser, Product, CustomerCategory, DatabaseSeeder
├── public/
│   ├── assets/argon/               # Aset template Argon Dashboard
│   └── assets/icon/                # Ikon admin/kurir + ikon notifikasi
├── resources/
│   ├── css/                        # app.css, argon-dashboard-tailwind.css
│   ├── js/                         # 20+ file JS (charts, carousel, live-search, dll)
│   └── views/
│       ├── dashboard/admin/        # 9 modul view admin
│       ├── dashboard/kurir/        # 6 modul view kurir
│       ├── livewire/homepage.blade.php  # Landing page publik (1985 baris)
│       ├── layouts/                # argon, app, guest, partials
│       └── auth/                   # Login, register, 2FA, dll (Jetstream)
├── routes/
│   ├── web.php                     # Seluruh route aplikasi
│   └── api.php                     # /user + webhook Meta
├── tests/                          # Test bawaan Jetstream (auth, API, profile)
└── README.md
```

---

## 4. Skema Database (Relasi Data)

### Daftar Tabel & Migrasi

| Tabel | Migrasi | Kolom Penting |
|-------|---------|---------------|
| `users` | 2014_10_12_000000 + update | name, email, password, region_id (FK), note, two_factor |
| `roles` / `permissions` / `model_has_roles` | 2025_07_22_004736 (spatie) | Permission tables |
| `regions` | 2025_08_01_082606 | name, slug |
| `customers` | 2025_08_01_094446 + 3 update | name, company_name, address, landmark, phone, opening_hours, payment_type, note, region_id, customer_category_id, added_by_user_id (kurir penambah), is_flagged |
| `customer_categories` | (seeder) | name: `Reseller`, `Supermarket` |
| `categories` | 2025_08_13_082903 | name, slug (`Produk`, `Hampers`, `Tumpeng`) |
| `products` | 2025_07_28_035358 + 2 update | name, description, image_path, tag, is_active, category_id, region_id (kolom `price` asli dihapus — harga dipindah ke `product_variants`) |
| `product_variants` | 2025_08_13_083055 + update | name, price, is_active (FK product_id) |
| `orders` | 2025_08_15_050259 + 5 update | invoice_number (unique), customer_id, phone, address, payment_method, payment_proof, note, rejection_note, total_amount, status, created_by_user_id, region_id, paid_at, picked_up_at, delivered_at, received_by_buyer_at |
| `order_items` | 2025_08_20_060447 | product_id, product_name, variant_id, variant_name, quantity, price, subtotal |
| `order_returns` | 2025_08_31_150451 | order_id, courier_id, region_id, status, total_amount_returned, return_proof, reason, admin_notes |
| `order_return_products` | 2025_08_31_150709 | product_id, product_variant_id, quantity, price, subtotal |
| `visit_logs` | 2026_01_07_103455 | ip_address, created_at (tracking visitor homepage) |
| `sessions` | 2025_07_14_094928 | Session database |
| `personal_access_tokens` | 2019_12_14_000001 | Sanctum tokens |
| `password_reset_tokens`, `failed_jobs` | Bawaan | — |

### Diagram Relasi Eloquent

```
Region 1───* User (admin/kurir)
Region 1───* Customer
Region 1───* Product
Region 1───* Order
Region 1───* OrderReturn

Category 1───* Product
Product 1───* ProductVariant
Product 1───* OrderItem

User (kurir) 1───* Customer (added_by_user_id)
User (kurir) 1───* Order (created_by_user_id)

Customer 1───* Order
Customer *───1 CustomerCategory

Order 1───* OrderItem
Order 1───* OrderReturn (hasOne aktif)
OrderReturn 1───* OrderReturnProduct
OrderReturnProduct *───1 Product / ProductVariant
```

### Format Nomor Invoice

```
INV/{tanggal dmy}/{region_id 2 digit}/{kurir_id 3 digit}/{customer_id 3 digit}/{urutan harian 3 digit}
Contoh: INV/050826/01/003/007/012
```

---

## 5. Manajemen Role & Autentikasi

### Role (Spatie Permission)
- **`admin`** — Akses penuh modul admin di region-nya.
- **`kurir`** — Akses modul kurir (pesanan, customer miliknya, history).
- User tanpa region valid → `403 AKSES DITOLAK`.

### Alur Login (custom `LoginResponse`)
1. User login melalui halaman Jetstream/Fortify (`/login`).
2. `LoginResponse` memeriksa `region` dan `role` user:
   - Admin → `admin/dashboard/{region-slug}`
   - Kurir → `kurir/dashboard/{region-slug}`
   - Tidak valid → fallback ke `fortify.home`
3. Middleware protection: `auth:sanctum`, `jetstream.auth_session`, `verified`, lalu `role:admin` / `role:kurir` per group route.

### Autentikasi 2FA
User model menggunakan `TwoFactorAuthenticatable` (Fortify) — 2FA tersedia.

---

## 6. Route Map (routes/web.php)

### Publik (tanpa login)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `/` | Homepage + **log visitor unik per IP per hari** ke `visit_logs` |
| GET | `/privacy-policy` | Halaman kebijakan privasi |
| POST | `/logout` | Logout manual (invalidate session) |

### Autentikasi
| GET | `/dashboard` | Redirect berdasarkan role + region |

### Admin (`/admin/*`, middleware `role:admin`)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `dashboard/{region}` | Dashboard dengan grafik penjualan & visitor |
| GET/PUT | `profile` , `profile/password` | Kelola profil admin |
| Resource | `products` | CRUD produk + varian |
| Resource | `couriers` | CRUD kurir + `note` + `performance-data` (JSON chart) |
| PUT/POST/Resource | `customers/...` | CRUD customer, update note, toggle flag |
| GET | `customers/{customer}/rekap/download` | **Rekap order customer PDF** |
| GET | `orders` | Daftar pesanan (menunggu verifikasi) |
| GET/POST | `orders/{id}/details` , `verify` , `reject` , DELETE | Verifikasi/tolak/hapus pesanan |
| GET | `historys` | History pesanan (filter bulan/tahun/kurir/search) |
| GET | `historys/{order}/invoice` + `download` | Invoice view + PDF |
| GET | `historys/export-pdf` | **Export history bulanan PDF** |
| DELETE | `historys/{id}` | Hapus history |
| GET | `peforma-kurir` (+ `/export/pdf`, `/export/{id}/pdf`) | Ranking performa kurir + PDF |
| GET | `peforma-customer` (+ `/export/pdf`) | Ranking performa customer + PDF |

### Kurir (`/kurir/*`, middleware `role:kurir`)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `dashboard/{region}` | Dashboard kurir (chart pesanan, kategori customer) |
| GET/PUT | `profile`, `profile/password` | Profil kurir |
| GET | `products` | Lihat produk (read-only) |
| Resource | `customers` | CRUD customer (hanya miliknya sendiri) |
| GET | `pesanan` | Order tracking (filter status, search, warning belum bayar) |
| GET | `pesanan/create` | Form pesanan baru |
| GET | `pesanan/{id}/details` | Detail pesanan (JSON, termasuk qty retur) |
| POST | `pesanan/{id}/update-status` | Update status: diambil → diantar → diterima_pembeli |
| POST | `pesanan/{id}/upload-proof` | Upload bukti pembayaran (otomatis status `selesai`) |
| POST | `pesanan/{order}/request-return` | Ajukan retur |
| POST | `pesanan/{order}/upload-return-proof` | Upload bukti retur |
| POST/GET | `pesanan/{order}/request-return/edit` | Edit pengajuan retur |
| POST | `orders/checkout` | **Simpan pesanan baru** (JSON) |
| GET | `customer/{id}/last-order` | Item pesanan terakhir customer (fitur "pesan ulang") |
| GET | `historys` | History pesanan kurir (filter bulan/tahun/search) |
| GET | `produk/json` | Data produk + varian aktif di region kurir (JSON, `Storage::url`) |

### API (`routes/api.php`)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `/api/user` | User saat ini (Sanctum) |
| GET | `/api/webhook/meta` | **Verifikasi webhook Meta (WhatsApp)** — `hub_challenge` |
| POST | `/api/webhook/meta` | **Terima pesan webhook WhatsApp** (echo balasan sederhana) |

---

## 7. Fitur per Modul (Detail)

### 7.1 Homepage / Landing Page (Livewire, 1985 baris view)
- Preloader, navbar sticky, hero section, galeri foto produk.
- SEO lengkap (meta keywords produk kue tradisional).
- **Google Tag Manager** (`GTM-PRXFHTTN`) + **Google Analytics** (`G-6GHRM0X2ZS`).
- Facebook domain verification meta.
- Floating WhatsApp button (wa.me).
- Animasi AOS, medium-zoom pada foto, carousel, testimoni.
- **Tracking visitor**: setiap IP unik per hari dicatat ke tabel `visit_logs` (untuk grafik admin).

### 7.2 Dashboard Admin (`AdminDashboardController`)
- **Statistik**: Income hari ini, Total sales hari ini, rata-rata sales/bulan, total customer region, customer baru hari ini.
- **Perbandingan % change**: income hari ini vs kemarin; sales bulan ini vs bulan lalu; avg bulan ini vs bulan lalu; customer minggu ini vs minggu lalu.
- **Grafik 1 (Penjualan)** — Chart.js: total order, order terverifikasi, order terverifikasi + retur. Filter: `daily`, `weekly`, `monthly`, `last_month`, `last_7_days`.
- **Grafik 2 (Visitor)** — Chart.js: jumlah kunjungan homepage. Filter sama.
- Income dihitung dengan pengurangan otomatis jika ada retur berstatus `selesai`.
- Daftar kurir region (pagination terpisah).

### 7.3 Dashboard Kurir (`KurirDashboardController`)
- Statistik order kurir (total, selesai, retur) dengan chart per periode (`last_7_days`, `daily`, `weekly`, `monthly`).
- Tampilan customer terbaru milik kurir.
- Modal CRUD customer + kategori customer langsung dari dashboard.

### 7.4 Manajemen Produk (Admin)
- CRUD produk + **varian harga** (misal "Isi 3 Kemasan Mika" = Rp 9.000).
- Upload gambar produk via `Storage::disk('public')` (folder `products/`).
- Produk bersifat **per-region** (region_id).
- Soft-delete varian: jika varian dipakai di order (foreign key constraint `23000`), varian di-set `is_active = false` daripada dihapus.
- Kategori: Produk, Hampers, Tumpeng.

### 7.5 Manajemen Kurir (Admin)
- CRUD kurir (buat user + assign role `kurir` + region admin).
- Catatan per kurir (`note`), validasi kepemilikan region.
- **Grafik performa per kurir** (endpoint JSON `performanceData`): total order, selesai, retur per periode — identik dengan logika dashboard kurir.

### 7.6 Manajemen Customer (Admin & Kurir)
- CRUD customer; kurir **hanya bisa melihat/mengedit customer yang ia input** (`added_by_user_id`); admin melihat semua customer di region-nya.
- Auto-format: nama/alamat/company_name → Title Case (`ucwords`).
- **Normalisasi nomor HP** ke format `62...`.
- Kategori customer: `Reseller` & `Supermarket` (mempengaruhi batas order aktif & warning).
- Fitur **flag** customer (admin) — penanda customer penting/bermasalah.
- **Rekap order PDF** per customer dengan rentang tanggal (dipesan vs retur vs selisih).

### 7.7 Manajemen Pesanan (Kurir)
- **Checkout pesanan** (JSON): pilih customer, produk multi-item, metode pembayaran, optional bukti bayar.
- **Batas order aktif** berdasarkan kategori customer: Reseller max **7** order aktif, Supermarket max **30** (order belum diverifikasi admin).
- Invoice number otomatis (format di atas).
- **Track status** dengan timestamp otomatis per status (`picked_up_at`, `delivered_at`, `received_by_buyer_at`).
- **Warning belum bayar**: Reseller > 5 hari tanpa bukti bayar → warning; Supermarket > 28 hari.
- **Upload bukti pembayaran** dikompres otomatis ke **JPG kualitas 60** (Intervention Image) → status otomatis `selesai`.
- **Re-order**: tombol untuk mengisi keranjang dari pesanan terakhir customer.
- Pencarian live (AJAX): invoice number / nama customer; tampilan desktop (tabel) & mobile (card).

### 7.8 Status Workflow Pesanan

```
baru
 └─▶ diambil ──▶ diantar ──▶ diterima_pembeli
                                  │
                                  ├─▶ (upload bukti bayar) ──▶ selesai
                                  ├─▶ (ajukan retur) ──▶ menunggu_retur
                                  └─▶ (upload bukti retur) ──▶ menunggu_verifikasi_admin
                                                                       │
                                                        diverifikasi_admin (admin) ──▶ history
                                                        ditolak (admin) ──▶ kembali ke diterima_pembeli + rejection_note
```

Status lengkap: `baru`, `dikemas`, `diambil`, `diantar`, `diterima_pembeli`, `selesai`, `menunggu_retur`, `menunggu_verifikasi_admin`, `diverifikasi_admin`, `dikembalikan`, `dibatalkan`.

Aturan:
- Status tidak bisa mundur (diantar → diambil ditolak, dst).
- Verifikasi admin hanya dari status `selesai` atau `menunggu_verifikasi_admin`.
- Penolakan admin: hapus file bukti bayar/retur + set `rejection_note` + status balik ke `diterima_pembeli`.
- Penghapusan pesanan: cascade delete + hapus file bukti bayar & retur.

### 7.9 Flow Retur (Barang Kembali)
1. Kurir mengajukan retur dari pesanan berstatus **`diterima_pembeli`** (pilih produk + qty + alasan ≥ 10 karakter).
2. Order berstatus `menunggu_retur`; dibuat `OrderReturn` + `OrderReturnProduct`.
3. Kurir upload **bukti retur** → status `menunggu_verifikasi_admin` + `paid_at` tercatat.
4. Admin melihat detail retur di modal verifikasi; hanya bisa retur **satu kali per order** (duplikat ditolak sistem).
5. Edit retur dimungkinkan selama status `menunggu_konfirmasi` (`editReturn`, updateOrCreate item).

### 7.10 History & Invoice (Admin & Kurir)
- Filter bulan/tahun (5 tahun ke belakang), filter kurir (admin), live search.
- Kolom dinamis: `final_total = total_amount − total_amount_returned`, badge Lunas/Belum Lunas.
- **Invoice view** + **download PDF** (DomPDF, A4 portrait, nama file: `{CustomerName}-{INV}.pdf`).
- **Export history bulanan PDF** seluruh order `diverifikasi_admin` per region.
- Admin bisa menghapus history (dengan cleanup file bukti).
- Catatan: "mengirim invoice ke Reseller" pada README diimplementasikan sebagai **download PDF invoice** (tidak ada integrasi email/notification di kode).

### 7.11 Performa Kurir (Ranking)
- Ranking kurir per rentang tanggal: jumlah order, total omzet, jumlah customer kelola.
- Urutkan berdasarkan jumlah order DESC.
- **Export PDF** ranking semua kurir (`/export/pdf`) atau per kurir (`/export/{id}/pdf`).
- Paginasi manual via `LengthAwarePaginator` (10/halaman).

### 7.12 Performa Customer (Skoring Reseller)
- Khusus customer kategori **Reseller** (supermarket dikecualikan).
- **Skor Pembelian**: (total pembelian / pembelian tertinggi) × 100 → bobot 70%.
- **Skor Anti-Retur**: (1 − retur/pembelian) × 100 → bobot 30%.
- **Skor Akhir** = 70% pembelian + 30% anti-retur; diurutkan DESC, diberi peringkat.
- Filter bulan/tahun + **Export PDF**.

### 7.14 Global: Sidenav Notification Badges (AppServiceProvider)
- `View::composer('layouts.partials.sidenav')` mengisi badge notifikasi di sidebar:
  - **Admin** → jumlah pesanan berstatus `baru` di region-nya.
  - **Kurir** → jumlah pesanan miliknya yang memiliki `rejection_note` (ditolak admin) dan belum berstatus final (`selesai`/`diverifikasi_admin`).

### 7.13 Chatbot WhatsApp (Meta Webhook)
- `GET /api/webhook/meta` — verifikasi token (`META_VERIFY_TOKEN`) saat setup Meta.
- `POST /api/webhook/meta` — menerima pesan masuk, log, membalas echo sederhana ("Halo, pesan kamu diterima: ...").
- Helper `typeMessage()` mendukung payload: typing, reading, text, image, interactive button, location request, carousel (masih template dengan placeholder).
- `detectIntent()` belum diimplementasi (kosong).

---

## 8. Alur Sistem Lengkap (Flow Diagram)

Berikut seluruh alur proses di dalam aplikasi, diurutkan dari alur bisnis tingkat tinggi hingga alur teknis per fitur. Sesuai implementasi kode aktual (`routes/web.php`, controllers, dan model).

### 8.1 Flow Master — End-to-End Bisnis

```
                    ┌────────────────────────────────────────────┐
                    │            PELANGGAN (RESELLER)            │
                    │          memesan via WhatsApp/Direct        │
                    └────────────────────┬───────────────────────┘
                                         │ pesanan + pesanan pembayaran
                                         ▼
                    ┌────────────────────────────────────────────┐
                    │              KURIR                          │
                    │   • Terima pesanan & pembayaran dari customer│
                    └───────┬──────────────┬─────────────────────┘
                            │              │
              [Input Pesanan]              │  [Tracking Status]
                    ▼                       ▼
        ┌─────────────────────┐   ┌─────────────────────────────┐
        │ checkout() → Order  │   │ diambil → diantar →         │
        │ status = "baru"     │   │ diterima_pembeli (+timestamp)│
        └────────┬────────────┘   └─────────────┬───────────────┘
                 │                             │
                 │              ┌──────────────┴──────────────┐
                 │              │ Upload bukti bayar (opsional)│
                 │              └──────────────┬──────────────┘
                 │                             ▼
                 │                    status = "selesai"
                 │                             │
                 └──────────────┬──────────────┘
                                ▼
              ┌────────────────────────────────────────────┐
              │           ADMIN TOKO (verifikasi)           │
              │  status "selesai"/"menunggu_verifikasi_admin"│
              └───────────────┬───────────────┬─────────────┘
                              │               │
                    Verifikasi  ✔          Tolak ✖
                              │               │
                    status = "diverifikasi_   │  hapus bukti + set
                       admin" (masuk history) │  rejection_note →
                              │               │  status = "diterima_pembeli"
                              ▼               ▼
              ┌──────────────────────────┐  ┌──────────────────────┐
              │ HISTORY + EXPORT:        │  │ Kurir perbaiki /     │
              │ invoice PDF, rekap,      │  │ upload ulang bukti   │
              │ laporan bulanan,         │  │ bayar / retur        │
              │ performa kurir & customer │  └──────────────────────┘
              └──────────────────────────┘
```

**Langkah detail (urutan bisnis):**
1. **Pelanggan** (Reseller/Supermarket) memesan kue & mentransfer pembayaran langsung ke **Kurir** via WhatsApp.
2. **Kurir** menginput pesanan lewat menu `pesanan/create` (checkout) → order dibuat dengan status `baru`, berisi daftar produk + varian + total.
3. **Kurir** memperbarui status pengiriman di aplikasi: `diambil → diantar → diterima_pembeli` (setiap transisi merekam timestamp).
4. Setelah pembeli menerima, **kurir upload bukti bayar** → status otomatis `selesai` (+ `paid_at`).
5. **Admin** melihat daftar pesanan (`admin/orders`), membuka detail, lalu **Verifikasi** (→ `diverifikasi_admin`) atau **Tolak** (→ kembali ke `diterima_pembeli` + catatan penolakan).
6. Order terverifikasi masuk **History** — admin bisa **download invoice PDF**, **export history bulanan**, **rekap per customer**, dan laporan **performa kurir/customer**.

### 8.2 Flow Autentikasi & Pengalihan Dashboard

```
Login (/login) → Fortify autentikasi → LoginResponse (custom)
   │
   ├── Apakah user punya region (region.name tidak kosong)?
   │       │
   │       └─ TIDAK → redirect ke /dashboard (fortify.home)
   │
   ├── Role = admin  → GET /admin/dashboard/{slug-region}
   ├── Role = kurir  → GET /kurir/dashboard/{slug-region}
   └── Role lain / tak dikenal → fallback fortify.home
```

- setiap halaman dashboard di-guard middleware `auth:sanctum`, `jetstream.auth_session`, `verified`, lalu per-group `role:admin` / `role:kurir`.
- Logout manual: `POST /logout` → invalidate session → redirect `/login`.

### 8.3 Flow Checkout Pemesanan (Kurir)

```
POST /kurir/orders/checkout  (PesananController::checkout)
   │
   1. Validasi request (customer, phone, address, payment_method, products JSON, bukti opsional)
   2. Ambil kategori customer → hitung batas order aktif:
        • Reseller   → max 7 order aktif
        • Supermarket→ max 30 order aktif
        (order aktif = status != 'diverifikasi_admin')
        → jika melebihi batas → 422 & pesanan ditolak
   3. DB::beginTransaction
   4. Upload bukti bayar opsional → storage/public/payment_proofs
   5. Buat Order (status default 'baru', total_amount = 0)
   6. Generate nomor invoice:
        INV/{tanggal dmy}/{region2d}/{kurir3d}/{customer3d}/{urutan-harian3d}
   7. Loop produk → buat OrderItem + hitung subtotal
   8. Simpan items + update total_amount
   9. DB::commit → JSON sukses {order_id, invoice_number}
```

### 8.4 Flow Tracking / Update Status Pengiriman

```
PesananController::updateOrderStatus(new_status ∈ {diambil, diantar, diterima_pembeli})

   diambil (baru/dikemas) ────────────────▶ [guard: tidak bisa balik dari diantar/diterima/selesai]
                                        tambah picked_up_at
   diantar (from diambil) ───────────────▶ [guard: tidak bisa balik dari diterima/selesai]
                                        tambah picked_up_at + delivered_at
   diterima_pembeli (from diantar) ─────▶ [guard: tidak jika status = selesai]
                                        tambah picked_up_at + delivered_at + received_by_buyer_at
```
- Status tidak dapat mundur (semua transisi hanya maju; validasi di server mengembalikan 400 bila mundur).
- Timestamp menggunakan zona waktu region kurir (`getUserTimezone()`).

### 8.5 Flow Upload Bukti Pembayaran

```
PesananController::uploadPaymentProof
   │  hanya boleh jika status ∈ {diterima_pembeli, selesai}
   ├─ Validasi file (jpeg/png/jpg, max 2MB)
   ├─ Hapus bukti lama (jika ada)
   ├─ Kompres gambar → JPG kualitas 60 (Intervention Image)
   ├─ Simpan: payment_proofs/{INV-dengan-dash}.jpg
   └─ Update DB → payment_proof = path, status = 'selesai', paid_at = now(region)
```

### 8.6 Flow Verifikasi / Penolakan oleh Admin

```
Admin buka admin/orders → klik order → GET orders/{id}/details (JSON)
   │
   ├── VERIFIKASI (orders/{id}/verify)
   │    hanya jika status ∈ {selesai, menunggu_verifikasi_admin}
   │    └── success: status = "diverifikasi_admin" → masuk history
   │
   └── IMPROVE  (orders/{id}/reject) — butuh rejection_note ≥ 10 karakter
        • jika ada retur berstatus 'menunggu_konfirmasi':
            hapus file bukti retur, status retur = 'ditolak',
            order kembali ke 'diterima_pembeli'
        • jika tidak ada retur:
            hapus bukti bayar, status = 'diterima_pembeli',
            payment_proof & paid_at = null
        • simpan rejection_note → redirect ke index + flash 'success'
```

### 8.7 Flow Retur (Pengembalian Barang)

```
Kurir — halaman detail pesanan (status = 'diterima_pembeli')
   │
   1) requestReturn (POST /pesanan/{order}/request-return)
        │  validasi qty retur per produk (tidak boleh > qty terpesan)
        │  cek: belum ada retur aktif (status != tolak) → jika ada = 422
        │  buat OrderReturn (status 'menunggu_konfirmasi') + OrderReturnProduct
        │  order.status = 'menunggu_retur'
        ▼
   2) uploadReturnProof (POST /pesanan/{order}/upload-return-proof)
        │  upload gambar → storage/public/return_proofs/RETURN-{inv}.{timestamp}.{ext}
        │  order.status = 'menunggu_verifikasi_admin', order.paid_at = now
        ▼
   3) Edit (opsional, sebelum verifikasi): editReturn dengan status 'menunggu_konfirmasi'
        → updateOrCreate produk retur + total
        ▼
   4) Admin verifikasi detail (lihat bukti retur + produk retur di modal)
        ├── verifikasi → order 'diverifikasi_admin' (masuk history, retur dianggap sah)
        └── penolakan  → retur 'ditolak', bukti dihapus, order kembali 'diterima_pembeli'
```

### 8.8 Flow History & Invoice

```
status order = 'diverifikasi_admin'  ──►  masuk halaman History
   ├── Admin: filter bulan/tahun (5 thn), filter kurir, search
   ├── Kurir : filter bulan/tahun, search (hanya order miliknya)
   ├── Baris menampilkan final_total = total_amount − total_retur
   ├── Lihat detail (modal) via GET historys/{order}/details
   ├── Download invoice PDF  → GET historys/{order}/download
   └── Export history bulanan PDF → GET historys/export-pdf
```

### 8.9 Flow Manajemen Master Data (Repeatable)

```
A) Produk (admin saja, per-region)
   Tambah/Edit → upload gambar → simpan ke storage/public/products/
   Varian: jika dipakai order (FK 23000) → di-'deactivate' (is_active=false),
   jika tidak terpakai → dihapus / soft-delete.
   Hapus produk → hapus file gambar + produk (akan error jika masih dipakai order).

B) Kurir (admin only, per-region)
   Tambah → buat user + assign role 'kurir' + region admin → notifikasi di sidenav.

C) Customer (admin: semua di region; kurir: hanya buatan sendiri)
   Tambah/Edit → normalisasi nomor HP ke '62', nama/alamat otomatis Title Case.
   → kategori Reseller/Supermarket (mempengaruhi batas order & warning belum bayar)
   → warning: Reseller > 5 hari, Supermarket > 28 hari tanpa bukti bayar.
   → flag (admin) untuk penanda customer.
```

### 8.10 Flow Laporan / Export (PDF)

```
Rekap Customer    : pilih daterange di modal customer → PDF (per order + total+selisih)
Performa Kurir   : filter daterange → ranking jumlah order & omzet → PDF
Performa Customer: filter bulan + tahun → skor pembelian (70%) + anti-retur (30%)
                   → ranking skor → PDF
History bulanan  : bulan + tahun → seluruh order diverifikasi_admin → PDF
                   (final_total sudah dikurangi retur)
```

### 8.11 Flow Webhook Meta WhatsApp (Prototipe)

```
GET  /api/webhook/meta  → Meta memverifikasi hub_challenge (META_VERIFY_TOKEN)
POST /api/webhook/meta  → terima payload pesan
   → log, ekstrak from + text
   → balas echo: "Halo, pesan kamu diterima: 'Halo...'"
   (intent/parser masih kosong — prototipe)
```

> **Catatan**: Diagram menggunakan balok ASCII agar kompatibel di semua viewer; untuk render grafis (GitHub/GitLab) bagian `1. Alur Bisnis Unik` dan `7.8 Status Workflow` dapat diubah ke sintaks **Mermaid** (`stateDiagram-v2` / `flowchart TD`) jika diperlukan.

---

## 9. Hal-Hal Teknis Menarik / Pola Kode

### Timezone per Region
`PesananController` & `ReturnController` memiliki helper:
```php
private function getUserTimezone(): string
{
    switch ($user->region_id) {
        case 3: return 'Asia/Makassar';   // Denpasar (WITA)
        case 1: case 2: return 'Asia/Jakarta'; // Surabaya & Malang (WIB)
        default: return config('app.timezone', 'UTC');
    }
}
```
Semua timestamp order (`created_at`, `paid_at`, status timeline) dibuat eksplisit dengan `Carbon::now($timezone)` agar konsisten antar cabang.

### Kompresi Gambar Bukti Pembayaran
```php
$manager = new ImageManager(new Driver());
$image = $manager->read($file)->encode(new JpegEncoder(quality: 60));
Storage::disk('public')->put($path, $image);
```
File disimpan sebagai `payment_proofs/{INV-dengan-dash}.jpg`.

### Normalisasi Input
- `User::name()` & `Customer::name/address/companyName` — Attribute Casts `set: fn($v) => ucwords(strtolower($v))`.
- `formatPhoneNumber()` — bersihkan non-digit, awalan `0`/`+62` → `62...`.

### Keamanan
- RBAC via Spatie (`role:admin` / `role:kurir` middleware).
- Check kepemilikan data berlapis: region admin, `added_by_user_id` kurir, `created_by_user_id` order.
- Validasi dengan FormRequest manual / Validator di controller.
- File upload divalidasi (image, mime, max 2MB) & disimpan di `storage/app/public`.
- `Storage::url()` / `asset('storage/...')` dengan regex strip prefix untuk path bukti.
- CSRF via Jetstream; Sanctum untuk API.

### Frontend Patterns
- AJAX live search → render partial blade `_table_rows` + `_card_view` (responsive) → `response()->json(['desktop_html' => ..., 'mobile_html' => ...])`.
- Modal CRUD dinamis dirender per baris (`_modals.blade.php`).
- Chart.js di-inject via CDN per halaman yang butuh.
- Argon Dashboard (Tailwind) sebagai template admin; homepage custom dengan AOS.

---

## 10. Seeder & Data Awal

### `RoleAndUserSeeder`
- Roles: `admin`, `kurir`.
- Regions: Surabaya, Malang, Denpasar (dengan slug).
- 6 akun default (password semua: `password`):

| Role | Email |
|------|-------|
| Admin Surabaya | pandanaslisbyadm@gmail.com |
| Admin Malang | pandanaslimalangadm@gmail.com |
| Admin Denpasar | pandanaslibaliadm@hmail.com (perhatikan typo `hmail`) |
| Kurir Surabaya | kurir.surabaya@example.com |
| Kurir Malang | kurir.malang@example.com |
| Kurir Denpasar | kurir.denpasar@example.com |

### `ProductSeeder`
- 3 kategori: Produk, Hampers, Tumpeng.
- **13 produk** dengan varian harga: Kue Ijo, Kue Lumpur Surga, Kue Ongol-Ongol, Kue Pulut Srikaya, Kue Ubi Nanas, Selai Srikaya, Kue Mix Mini, Kue Mix, Hampers A/B/C, Tumpeng Mini, Tumpeng Besar.
- Catatan: seeder ini menghapus kategori/produk/varian sebelum seed (truncate).

### `CustomerCategorySeeder`
- `Reseller`, `Supermarket`.

### `DatabaseSeeder`
- Memanggil: RoleAndUserSeeder, ProductSeeder, CustomerCategorySeeder.

---

## 11. Konfigurasi Penting (.env)

Variabel khusus aplikasi (selain Laravel standar):
```
META_VERIFY_TOKEN=          # Token verifikasi webhook Meta
META_PHONE_NUMBER_ID=       # ID nomor WhatsApp Business
META_ACCESS_TOKEN=          # Access token Graph API
META_VERSION=               # Versi Graph API (dibaca di WebhookController)
```

Konfigurasi utama lainnya: database MySQL, session database, mail (mailpit default), filesystem local/public.

---

## 12. Menjalankan Proyek

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
# set DB_* di .env, lalu:
php artisan migrate --seed
# Terminal 1:
npm run dev
# Terminal 2:
php artisan serve
# Buka http://127.0.0.1:8000 (login: akun di seeder)
```

Storage link (jika upload gambar tidak muncul):
```bash
php artisan storage:link
```

---

## 13. Testing

- `tests/Feature/*` — test bawaan Jetstream: Authentication, Registration, TwoFactor, ApiToken, Profile, PasswordReset, dll. (tidak ada test khusus modul bisnis).
- `phpunit.xml` sudah dikonfigurasi.
- `barryvdh/laravel-debugbar` tersedia di dev untuk debugging.

---

## 14. Catatan, Kelemahan & Potensi Perbaikan

| No | Temuan |
|----|--------|
| 1 | **Kode legacy duplikat**: `Admin/OldCustomerController.php` & `Kurir/OldKurirCustomerController.php` tidak terpakai (route memakai `CustomerController` root) — bisa dihapus. |
| 2 | **Komentar artifact** `[!code ...]` dari editor (VS Code highlight) tersebar di banyak file. |
| 3 | `Policies/DashboardPolicy.php` kosong; otorisasi di-handle manual via `hasRole` checks + middleware. |
| 4 | `WebhookController` masih prototipe: balasan echo saja, `detectIntent()` kosong, payload carousel berisi placeholder (`<URL_BUTTON_LABEL>`). |
| 5 | `app/Http/Controllers/Admin/OldCustomerController.php` didefinisikan dengan namespace `App\Http\Controllers\Admin` tapi kelas bernama `CustomerController` (konflik potensial naming). |
| 6 | Route `request-return/edit` punya dua definisi (POST + GET) dengan nama route sama (`requestReturn`) — GET hanya me-render view edit tanpa data. |
| 7 | `RoleAndUserSeeder` email admin Denpasar mengandung typo (`hmail.com`). |
| 8 | Seeder produk tidak menyertakan `region_id` (produk seeder tidak terikat region tertentu). |
| 9 | `Homepage.php` mereferensi properti `$this->testimonials` yang tidak didefinisikan (tidak error fatal karena view menangani fallback). |
| 10 | Penggunaan `env()` langsung di `WebhookController::__construct` (sebaiknya `config()`). |
| 11 | Grafik visitor & statistik lain tidak difilter region (visitor log global, bukan per region). |
| 12 | Fitur `peforma-kurir/{kurir}` dan `peforma-customer/{customer}` (show detail) masih stub kosong. |
| 13 | Tidak ada soft deletes untuk Order/Product (penghapusan permanen, dilindungi constraint). |
| 14 | Tidak ada integrasi email/notification — invoice & laporan hanya via **download PDF** (kontras dengan klaim README "kirim invoice"). |
| 15 | Badge notifikasi sidenav (AppServiceProvider) hanya menghitung dari kolom status `baru`/`rejection_note`, bukan sistem notifikasi terpisah. |
| 16 | Konfigurasi `config/app.php` masih default: `timezone => UTC`, `locale => en`. Zona waktu per-cabang hanya di-handle manual via helper Carbon di controller (nama bulan Indonesia di view dibuat manual via array, bukan localization). |

---

## 15. Ringkasan Eksekutif

**Kue Pandan Asli** adalah sistem ERP ringan berbasis Laravel 10 untuk toko kue tradisional multi-cabang (Surabaya, Malang, Denpasar) dengan model bisnis **reseller + kurir**. Kurir bertindak sebagai ujung tombak (input pesanan, terima pembayaran, tracking status, retur), sementara admin melakukan verifikasi, mengelola master data, dan menghasilkan laporan PDF (history bulanan, invoice per order, rekap per customer, ranking performa kurir, dan skoring performa reseller). Aplikasi sudah memiliki: RBAC dua role, isolasi data per region, timezone handling per cabang, kompresi gambar bukti pembayaran, fitur retur lengkap, pelacakan visitor homepage, integrasi awal webhook WhatsApp (Meta), dan dashboard analitik berbasis Chart.js. Beberapa area masih prototipe (chatbot, detail performa) dan ada kode legacy yang bisa dibersihkan.
