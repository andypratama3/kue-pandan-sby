# 🎂 Kue Pandan Asli - Aplikasi Order & Delivery

Aplikasi web yang dirancang untuk mengelola alur pemesanan dan pengiriman produk "Kue Pandan Asli" melalui sistem Reseller dan Kurir. Aplikasi ini berfungsi sebagai pusat data bagi Admin untuk memantau penjualan dan mengelola data, serta platform bagi Kurir untuk mengupdate status pengiriman.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 10">
  <img src="https://img.shields.io/badge/Livewire-3-4d51b3?style=for-the-badge&logo=livewire" alt="Livewire 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS 3">
  <img src="https://img.shields.io/github/license/user/repo?style=for-the-badge" alt="License">
</p>

<p align="center">
  <img src="https://ik.imagekit.io/cl6bug31k/preview.jpeg?updatedAt=1753675527882" alt="Tampilan Aplikasi Kue Pandan Asli" width="80%">
</p>

## 📋 Tentang Proyek

Aplikasi ini bertujuan untuk mendigitalisasi dan memusatkan proses bisnis pada "Toko Kue Pandan Asli" yang beroperasi dengan model Reseller dan Kurir. Admin dapat mengelola operasional harian, sementara Kurir dapat melaporkan status pengiriman secara real-time.

## 🔄 Alur Sistem

Sistem pemesanan ini memiliki alur yang unik sebagai berikut:

1.  **Pemesanan:** Pelanggan melakukan pemesanan kue melalui **WhatsApp** langsung ke **Kurir**.
2.  **Aksi Kurir:** Kurir menerima pesanan, mengelola pembayaran, dan setelah pengiriman selesai, Kurir **menginput data pesanan dan mengupload bukti pengiriman** melalui aplikasi web ini.
3.  **Aksi Admin Toko:** Admin **mengkonfirmasi pesanan** yang diinput oleh kurir, mengelola data master (Reseller & Produk), serta **membuat laporan penjualan harian (PDF)** dan **mengirimkan invoice** ke setiap Reseller.

## ✨ Fitur Utama

Fitur pada website ini terbagi berdasarkan hak akses pengguna:

### Halaman Landing Page (Publik)
-   ✅ **Hero Section:** Tampilan utama yang menarik.
-   ✅ **Foto Produk:** Galeri produk-produk yang dijual.
-   ✅ **About / Tentang:** Deskripsi singkat mengenai toko.
-   ✅ **Lokasi Toko:** Integrasi dengan Google Maps untuk menampilkan lokasi.
-   ✅ **Kontak Person:** Informasi kontak yang bisa dihubungi.
-   ✅ **Animasi:** Animasi interaktif untuk meningkatkan pengalaman pengguna.
-   ✅ **Footer:** Bagian bawah halaman dengan informasi tambahan.

### Halaman Admin
-   📊 **Dashboard dengan Grafik:** Menampilkan grafik penjualan harian.
-   🧾 **Kirim Invoice:** Mengirim tagihan/invoice ke pelanggan (Reseller) untuk setiap pesanan.
-   ✏️ **Manajemen Reseller:** Menambah dan mengelola data Reseller baru.
-   ✔️ **Menyelesaikan Pesanan:** Menandai (mencentang) pesanan yang sudah selesai.
-   👥 **Dukungan Multi-Cabang:** Admin dapat mengelola data dari 3 cabang (Bali, Malang, Dll).
-   📈 **Evaluasi Bulanan:** Fitur untuk melakukan evaluasi kinerja Kurir & Reseller.

### Halaman Kurir
-   🚚 **Update Tracking Pesanan:** Kurir dapat memperbarui status pesanan menjadi (Menunggu, Dikirim, Selesai).
-   📸 **Upload Bukti Kirim:** Kurir mengupload bukti pengiriman setelah pesanan diantar.
-   💸 **Menerima Pembayaran:** Kurir bertugas sebagai penerima pembayaran dari customer.


## 🛠️ Teknologi yang Digunakan

-   **Backend:**
    -   [PHP 8.1+](https://www.php.net/)
    -   [Laravel 10](https://laravel.com/)
    -   [Livewire 3](https://livewire.laravel.com/)
    -   [Laravel Jetstream](https://jetstream.laravel.com/)
    -   [Paket Spatie](https://spatie.be/packages)
-   **Frontend:**
    -   [Tailwind CSS](https://tailwindcss.com/)
    -   [Alpine.js](https://alpinejs.dev/)
    -   [jQuery](https://jquery.com/)
-   **Database:**
    -   MySQL / MariaDB
-   **Development Tools:**
    -   [Vite](https://vitejs.dev/)
    -   [Composer](https://getcomposer.org/) & [NPM](https://www.npmjs.com/)

## 🚀 Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat
-   PHP (Versi 8.1 atau lebih baru)
-   Composer
-   Node.js & NPM
-   Database (MySQL/MariaDB)

### Langkah-langkah Instalasi

1.  **Clone repositori ini:**
    ```bash
    git clone [https://github.com/user/repo.git](https://github.com/user/repo.git)
    ```

2.  **Masuk ke direktori proyek:**
    ```bash
    cd nama-folder-proyek
    ```

3.  **Install dependensi Composer (PHP):**
    ```bash
    composer install
    ```

4.  **Install dependensi NPM (JavaScript):**
    ```bash
    npm install
    ```

5.  **Buat file `.env`:**
    ```bash
    cp .env.example .env
    ```

6.  **Generate kunci aplikasi:**
    ```bash
    php artisan key:generate
    ```

7.  **Konfigurasi database di file `.env`:**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=user_database_anda
    DB_PASSWORD=password_database_anda
    ```

8.  **Jalankan migrasi database:**
    ```bash
    php artisan migrate
    ```
    *Jika ada data awal (seeder), jalankan juga:*
    ```bash
    php artisan db:seed
    ```

## ▶️ Menjalankan Aplikasi

Anda perlu menjalankan **dua terminal terpisah** di dalam direktori proyek:

1.  **Terminal 1 - Jalankan Vite:**
    ```bash
    npm run dev
    ```

2.  **Terminal 2 - Jalankan Server Laravel:**
    ```bash
    php artisan serve
    ```

Buka browser Anda dan akses `http://127.0.0.1:8000`.

## 📄 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT. Lihat file `LICENSE` untuk informasi lebih lanjut.
