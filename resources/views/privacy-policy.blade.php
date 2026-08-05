<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kebijakan Privasi Chatbot Meta — kuepandanasli.com</title>
    <link rel="icon" href="{{ asset('assets/homepage/faveicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />\
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,500;1,8..60,300&display=swap"
        rel="stylesheet" />
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brown: #7B3A10;
            --brown-mid: #A04E1A;
            --brown-light: #D2691E;
            --cream: #FDF6EE;
            --cream-mid: #F5E9D9;
            --cream-dark: #EDD9BE;
            --text: #2C1A0E;
            --text-mid: #5C3D20;
            --text-muted: #8B6244;
            --border: rgba(123, 58, 16, 0.15);
            --border-mid: rgba(123, 58, 16, 0.28);
            --white: #FFFCF8;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Source Serif 4', Georgia, serif;
            background-color: var(--cream);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.8;
            font-weight: 300;
        }

        /* ── NAV ── */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(253, 246, 238, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 0.5px solid var(--border-mid);
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--brown);
            text-decoration: none;
            letter-spacing: 0.3px;
        }

        .nav-label {
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 400;
        }

        /* ── HERO ── */
        .hero {
            background: var(--brown);
            padding: 64px 32px 56px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: 60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
        }

        .hero-inner {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-tag {
            display: inline-block;
            font-size: 11px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 14px;
            font-weight: 400;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 5vw, 40px);
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .hero-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.72);
            font-weight: 300;
            max-width: 540px;
            line-height: 1.7;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 24px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.88);
            border: 0.5px solid rgba(255, 255, 255, 0.22);
            font-weight: 400;
            font-family: 'Source Serif 4', serif;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #5FD67A;
            display: inline-block;
            flex-shrink: 0;
        }

        /* ── META BAR ── */
        .meta-bar {
            background: var(--white);
            border-bottom: 0.5px solid var(--border);
            padding: 14px 32px;
        }

        .meta-bar-inner {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            align-items: center;
            font-size: 13px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-item .ml {
            color: var(--text-muted);
        }

        .meta-item .mv {
            color: var(--brown);
            font-weight: 500;
        }

        /* ── LAYOUT ── */
        .page {
            max-width: 800px;
            margin: 0 auto;
            padding: 48px 32px 80px;
        }

        /* ── TOC ── */
        .toc {
            background: var(--white);
            border: 0.5px solid var(--border-mid);
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 48px;
        }

        .toc-title {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--brown);
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 2px;
        }

        .toc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 4px 16px;
        }

        .toc-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 13.5px;
            color: var(--text-mid);
            padding: 6px 4px;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
            font-weight: 300;
        }

        .toc-link:hover {
            color: var(--brown);
            background: var(--cream-mid);
            padding-left: 8px;
        }

        .toc-num {
            font-size: 11px;
            font-weight: 500;
            color: var(--brown-light);
            min-width: 18px;
        }

        /* ── SECTIONS ── */
        .section {
            margin-bottom: 48px;
            padding-bottom: 48px;
            border-bottom: 0.5px solid var(--border);
            animation: fadeUp 0.5s ease both;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section:nth-child(1) {
            animation-delay: 0.05s;
        }

        .section:nth-child(2) {
            animation-delay: 0.10s;
        }

        .section:nth-child(3) {
            animation-delay: 0.15s;
        }

        .section:nth-child(4) {
            animation-delay: 0.20s;
        }

        .section:nth-child(5) {
            animation-delay: 0.25s;
        }

        .section:nth-child(6) {
            animation-delay: 0.30s;
        }

        .section:nth-child(7) {
            animation-delay: 0.35s;
        }

        .section:nth-child(8) {
            animation-delay: 0.40s;
        }

        .section:nth-child(9) {
            animation-delay: 0.45s;
        }

        .section:nth-child(10) {
            animation-delay: 0.50s;
        }

        .section-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
        }

        .section-num {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--brown);
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            line-height: 36px;
            text-align: center;
            flex-shrink: 0;
            font-family: 'Source Serif 4', serif;
            margin-top: 2px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.3;
        }

        .section-body {
            font-size: 15.5px;
            line-height: 1.85;
            color: var(--text);
            font-weight: 300;
        }

        .section-body p+p {
            margin-top: 14px;
        }

        /* ── CALLOUT ── */
        .callout {
            background: var(--cream-mid);
            border-left: 3px solid var(--brown-light);
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin: 18px 0;
            font-size: 14.5px;
            line-height: 1.75;
            color: var(--text-mid);
        }

        /* ── TABLE ── */
        .table-wrap {
            overflow-x: auto;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            border: 0.5px solid var(--border-mid);
        }

        thead tr {
            background: var(--brown);
        }

        thead th {
            font-family: 'Source Serif 4', serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
            padding: 11px 16px;
            text-align: left;
        }

        tbody td {
            padding: 11px 16px;
            border-bottom: 0.5px solid var(--border);
            vertical-align: top;
            line-height: 1.65;
            font-weight: 300;
            color: var(--text);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) td {
            background: var(--cream-mid);
        }

        tbody td strong {
            font-weight: 500;
            color: var(--text-mid);
        }

        /* ── LIST ── */
        .policy-list {
            list-style: none;
            margin: 14px 0;
        }

        .policy-list li {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 15.5px;
            font-weight: 300;
            line-height: 1.75;
            color: var(--text);
        }

        .policy-list li::before {
            content: '›';
            color: var(--brown-light);
            font-size: 20px;
            line-height: 1.4;
            flex-shrink: 0;
            font-weight: 400;
        }

        .hl {
            color: var(--brown);
            font-weight: 500;
        }

        /* ── CONTACT CARD ── */
        .contact-card {
            background: var(--white);
            border: 0.5px solid var(--border-mid);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
        }

        .contact-card-header {
            background: var(--cream-mid);
            padding: 12px 20px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 0.5px solid var(--border);
        }

        .contact-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 12px 20px;
            border-bottom: 0.5px solid var(--border);
            font-size: 14.5px;
        }

        .contact-row:last-child {
            border-bottom: none;
        }

        .contact-label {
            min-width: 110px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            font-weight: 500;
            padding-top: 2px;
        }

        .contact-val {
            color: var(--text);
            font-weight: 300;
        }

        .contact-val a {
            color: var(--brown);
            text-decoration: none;
        }

        .contact-val a:hover {
            text-decoration: underline;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--brown);
            color: rgba(255, 255, 255, 0.65);
            text-align: center;
            padding: 32px 24px;
            font-size: 13px;
            line-height: 1.9;
            font-weight: 300;
        }

        footer strong {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        /* ── BACK TO TOP ── */
        .back-top {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 42px;
            height: 42px;
            background: var(--brown);
            color: #fff;
            border: none;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(123, 58, 16, 0.35);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.3s, transform 0.3s;
            z-index: 200;
        }

        .back-top.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .back-top:hover {
            background: var(--brown-mid);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 600px) {

            nav,
            .meta-bar {
                padding: 12px 20px;
            }

            .hero {
                padding: 48px 20px 40px;
            }

            .page {
                padding: 36px 20px 60px;
            }

            .toc {
                padding: 18px 20px;
            }

            .toc-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 19px;
            }

            .contact-label {
                min-width: 90px;
            }
        }
    </style>
</head>

<body>

    <!-- NAV -->
    <nav>
        <a href="https://www.kuepandanasli.com" class="nav-brand">Kue Pandan Asli</a>
        <span class="nav-label">Kebijakan Privasi</span>
    </nav>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-inner">
            <span class="hero-tag">kuepandanasli.com</span>
            <h1>Kebijakan Privasi<br>Chatbot Meta</h1>
            <p class="hero-desc">Menjelaskan cara kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda saat
                berinteraksi melalui chatbot WhatsApp &amp; Instagram kami.</p>
            <div class="hero-badges">
                <span class="badge"><span class="badge-dot"></span> WhatsApp Business</span>
                <span class="badge"><span class="badge-dot"></span> Instagram Messaging</span>
                <span class="badge"><span class="badge-dot"></span> Meta Platforms</span>
                <span class="badge"><span class="badge-dot"></span> UU PDP No. 27/2022</span>
            </div>
        </div>
    </div>

    <!-- META BAR -->
    <div class="meta-bar">
        <div class="meta-bar-inner">
            <div class="meta-item">
                <span class="ml">Tanggal Berlaku</span>
                <span class="mv">1 Mei 2025</span>
            </div>
            <div class="meta-item">
                <span class="ml">Terakhir Diperbarui</span>
                <span class="mv">2 Mei 2026</span>
            </div>
            <div class="meta-item">
                <span class="ml">Versi</span>
                <span class="mv">2.0</span>
            </div>
        </div>
    </div>

    <div class="page">

        <!-- TABLE OF CONTENTS -->
        <div class="toc">
            <p class="toc-title">Daftar Isi</p>
            <div class="toc-grid">
                <a href="#s1" class="toc-link"><span class="toc-num">01</span> Pendahuluan</a>
                <a href="#s2" class="toc-link"><span class="toc-num">02</span> Informasi yang Kami Kumpulkan</a>
                <a href="#s3" class="toc-link"><span class="toc-num">03</span> Tujuan Penggunaan Data</a>
                <a href="#s4" class="toc-link"><span class="toc-num">04</span> Dasar Hukum Pemrosesan</a>
                <a href="#s5" class="toc-link"><span class="toc-num">05</span> Pembagian ke Pihak Ketiga</a>
                <a href="#s6" class="toc-link"><span class="toc-num">06</span> Penyimpanan &amp; Keamanan</a>
                <a href="#s7" class="toc-link"><span class="toc-num">07</span> Hak-Hak Pengguna</a>
                <a href="#s8" class="toc-link"><span class="toc-num">08</span> Batasan Penggunaan</a>
                <a href="#s9" class="toc-link"><span class="toc-num">09</span> Hubungi Kami</a>
                <a href="#s10" class="toc-link"><span class="toc-num">10</span> Perubahan Kebijakan</a>
            </div>
        </div>

        <!-- SECTION 1 -->
        <div class="section" id="s1">
            <div class="section-header">
                <span class="section-num">1</span>
                <h2 class="section-title">Pendahuluan</h2>
            </div>
            <div class="section-body">
                <p>Kuepandanasli.com ("kami", "milik kami") mengelola chatbot pada platform Meta — yaitu WhatsApp
                    Business dan Instagram Direct — untuk melayani pertanyaan pelanggan seputar produk kue pandan,
                    pemesanan, dan layanan pelanggan kami.</p>
                <div class="callout">
                    Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan
                    melindungi informasi pribadi Anda saat berinteraksi dengan chatbot kami. Dengan memulai percakapan
                    melalui chatbot, Anda menyetujui praktik yang dijelaskan dalam dokumen ini.
                </div>
                <p>Kebijakan ini berlaku untuk semua interaksi yang terjadi melalui nomor WhatsApp Business resmi kami
                    dan akun Instagram resmi <strong>@kuepandanasli</strong>.</p>
            </div>
        </div>

        <!-- SECTION 2 -->
        <div class="section" id="s2">
            <div class="section-header">
                <span class="section-num">2</span>
                <h2 class="section-title">Informasi yang Kami Kumpulkan</h2>
            </div>
            <div class="section-body">
                <p>Saat Anda berinteraksi dengan chatbot kami, kami dapat mengumpulkan jenis informasi berikut:</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Jenis Data</th>
                                <th>Keterangan</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Identitas Pengguna</strong></td>
                                <td>Nama profil WhatsApp/Instagram, nomor telepon (WhatsApp), username (Instagram)</td>
                                <td>Meta Platform</td>
                            </tr>
                            <tr>
                                <td><strong>Pesan &amp; Percakapan</strong></td>
                                <td>Isi percakapan dengan chatbot, pertanyaan, dan permintaan yang Anda kirimkan</td>
                                <td>Interaksi langsung</td>
                            </tr>
                            <tr>
                                <td><strong>Data Pesanan</strong></td>
                                <td>Nama pemesan, alamat pengiriman, detail produk yang dipesan, dan preferensi pesanan
                                </td>
                                <td>Input pengguna</td>
                            </tr>
                            <tr>
                                <td><strong>Data Teknis</strong></td>
                                <td>Timestamp percakapan, status pesan (terkirim/dibaca), ID sesi chatbot</td>
                                <td>Sistem otomatis</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="callout">
                    Kami <strong>tidak</strong> mengumpulkan informasi sensitif seperti kata sandi, nomor rekening bank,
                    data kartu kredit, atau informasi kesehatan melalui chatbot.
                </div>
            </div>
        </div>

        <!-- SECTION 3 -->
        <div class="section" id="s3">
            <div class="section-header">
                <span class="section-num">3</span>
                <h2 class="section-title">Tujuan Penggunaan Data</h2>
            </div>
            <div class="section-body">
                <p>Kami menggunakan informasi yang dikumpulkan hanya untuk tujuan-tujuan berikut ini:</p>
                <ul class="policy-list">
                    <li>Memproses dan memenuhi pesanan produk kue pandan yang Anda lakukan melalui chatbot</li>
                    <li>Memberikan respons atas pertanyaan mengenai produk, harga, ketersediaan stok, dan jadwal
                        pengiriman</li>
                    <li>Mengirimkan konfirmasi pesanan, notifikasi status pengiriman, dan informasi terkait transaksi
                    </li>
                    <li>Menangani keluhan pelanggan dan memberikan dukungan layanan purna jual</li>
                    <li>Meningkatkan kualitas respons chatbot dan pengalaman pengguna secara keseluruhan</li>
                    <li>Mengirimkan informasi promosi dan penawaran khusus <span class="hl">hanya jika Anda telah
                            memberikan persetujuan (opt-in)</span></li>
                    <li>Mematuhi kewajiban hukum yang berlaku di wilayah Republik Indonesia</li>
                </ul>
            </div>
        </div>

        <!-- SECTION 4 -->
        <div class="section" id="s4">
            <div class="section-header">
                <span class="section-num">4</span>
                <h2 class="section-title">Dasar Hukum Pemrosesan</h2>
            </div>
            <div class="section-body">
                <p>Pemrosesan data pribadi Anda didasarkan pada kerangka hukum berikut:</p>
                <div class="callout">
                    <strong>Undang-Undang No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP)</strong> Republik
                    Indonesia, serta Kebijakan Platform Meta (Meta Platform Terms), WhatsApp Business Policy, dan
                    Instagram Platform Policy yang berlaku secara global.
                </div>
                <ul class="policy-list">
                    <li><span class="hl">Pelaksanaan perjanjian</span> — memproses pesanan dan layanan yang secara
                        eksplisit Anda minta melalui chatbot</li>
                    <li><span class="hl">Persetujuan (consent)</span> — Anda secara sukarela memulai percakapan
                        dengan chatbot kami</li>
                    <li><span class="hl">Kepentingan sah (legitimate interest)</span> — meningkatkan layanan dan
                        menjaga keamanan platform dari penyalahgunaan</li>
                    <li><span class="hl">Kewajiban hukum</span> — memenuhi persyaratan pelaporan dan dokumentasi
                        yang diwajibkan peraturan perundang-undangan</li>
                </ul>
            </div>
        </div>

        <!-- SECTION 5 -->
        <div class="section" id="s5">
            <div class="section-header">
                <span class="section-num">5</span>
                <h2 class="section-title">Pembagian Data kepada Pihak Ketiga</h2>
            </div>
            <div class="section-body">
                <p>Kami <span class="hl">tidak menjual, menyewakan, atau memperdagangkan</span> data pribadi Anda
                    kepada pihak ketiga mana pun. Data hanya dapat dibagikan dalam situasi berikut:</p>
                <ul class="policy-list">
                    <li><span class="hl">Meta Platforms Inc.</span> — sebagai penyedia infrastruktur WhatsApp
                        Business API dan Instagram API. Penggunaan data oleh Meta tunduk pada Kebijakan Data Meta yang
                        terpisah.</li>
                    <li><span class="hl">Mitra logistik/ekspedisi</span> — hanya nama dan alamat penerima yang
                        diperlukan untuk proses pengiriman pesanan kepada Anda.</li>
                    <li><span class="hl">Penyedia layanan teknologi chatbot</span> — platform/vendor chatbot yang
                        kami gunakan, terikat oleh perjanjian pemrosesan data dan kerahasiaan.</li>
                    <li><span class="hl">Otoritas hukum berwenang</span> — apabila diwajibkan oleh hukum, perintah
                        pengadilan, atau instansi pemerintah yang berwenang di Indonesia.</li>
                </ul>
                <p>Setiap pihak ketiga yang menerima data Anda diwajibkan menjaga kerahasiaan dan keamanan data tersebut
                    sesuai standar yang setara dengan kebijakan ini.</p>
            </div>
        </div>

        <!-- SECTION 6 -->
        <div class="section" id="s6">
            <div class="section-header">
                <span class="section-num">6</span>
                <h2 class="section-title">Penyimpanan &amp; Keamanan Data</h2>
            </div>
            <div class="section-body">
                <p>Data percakapan disimpan selama <span class="hl">maksimal 12 (dua belas) bulan</span> sejak
                    interaksi terakhir Anda, atau selama diperlukan untuk penyelesaian pesanan aktif dan kewajiban hukum
                    yang berlaku.</p>
                <p>Langkah-langkah perlindungan data yang kami terapkan:</p>
                <ul class="policy-list">
                    <li>Enkripsi end-to-end yang disediakan oleh infrastruktur WhatsApp untuk seluruh pesan dalam
                        transit</li>
                    <li>Akses ke data percakapan dibatasi secara ketat hanya untuk staf yang berwenang dan memiliki
                        kebutuhan operasional yang sah</li>
                    <li>Data disimpan di server yang beroperasi sesuai dengan kebijakan keamanan Meta dan standar
                        industri</li>
                    <li>Penghapusan data dilakukan secara berkala sesuai jadwal retensi yang telah ditetapkan</li>
                    <li>Peninjauan keamanan sistem dilakukan secara rutin untuk mengidentifikasi dan mengatasi
                        kerentanan</li>
                </ul>
                <div class="callout">
                    Meskipun kami menerapkan langkah-langkah keamanan yang wajar, tidak ada sistem transmisi data
                    melalui internet yang sepenuhnya aman. Kami mendorong Anda untuk tidak mengirimkan informasi yang
                    sangat sensitif melalui chatbot.
                </div>
            </div>
        </div>

        <!-- SECTION 7 -->
        <div class="section" id="s7">
            <div class="section-header">
                <span class="section-num">7</span>
                <h2 class="section-title">Hak-Hak Pengguna</h2>
            </div>
            <div class="section-body">
                <p>Sesuai dengan Undang-Undang Perlindungan Data Pribadi (UU PDP) No. 27/2022 dan kebijakan Meta, Anda
                    memiliki hak-hak berikut atas data pribadi Anda:</p>
                <ul class="policy-list">
                    <li><span class="hl">Hak Mengakses</span> — memperoleh informasi tentang data pribadi apa saja
                        yang kami simpan terkait Anda</li>
                    <li><span class="hl">Hak Memperbaiki</span> — meminta koreksi atas data yang tidak akurat,
                        tidak lengkap, atau sudah kedaluwarsa</li>
                    <li><span class="hl">Hak Menghapus</span> — meminta penghapusan data Anda, sepanjang tidak
                        bertentangan dengan kewajiban hukum yang berlaku</li>
                    <li><span class="hl">Hak Mencabut Persetujuan</span> — menarik kembali persetujuan kapan saja
                        dengan menghentikan penggunaan chatbot atau menghubungi kami secara langsung</li>
                    <li><span class="hl">Hak Mengajukan Keberatan</span> — menolak pemrosesan data untuk tujuan
                        pemasaran langsung atau tujuan tertentu lainnya</li>
                    <li><span class="hl">Hak Portabilitas Data</span> — menerima salinan data Anda dalam format
                        yang terstruktur dan dapat dibaca oleh mesin</li>
                    <li><span class="hl">Hak Mengajukan Pengaduan</span> — melaporkan dugaan pelanggaran kepada
                        Kominfo atau otoritas perlindungan data yang berwenang</li>
                </ul>
                <div class="callout">
                    Untuk menggunakan hak-hak di atas, silakan hubungi kami melalui kontak yang tercantum pada Pasal 9.
                    Kami berkomitmen untuk merespons setiap permintaan dalam waktu <strong>14 (empat belas) hari
                        kerja</strong>.
                </div>
            </div>
        </div>

        <!-- SECTION 8 -->
        <div class="section" id="s8">
            <div class="section-header">
                <span class="section-num">8</span>
                <h2 class="section-title">Batasan Penggunaan Chatbot</h2>
            </div>
            <div class="section-body">
                <p>Demi keamanan pengguna dan kepatuhan terhadap kebijakan Meta, chatbot kami memiliki ketentuan
                    penggunaan berikut:</p>
                <ul class="policy-list">
                    <li>Chatbot tidak dirancang untuk menangani data sensitif seperti informasi kartu kredit, rekening
                        bank, data kesehatan, atau nomor identitas kependudukan</li>
                    <li>Chatbot tidak mengumpulkan data anak di bawah usia 13 tahun secara sengaja. Jika Anda berusia di
                        bawah 13 tahun, harap tidak menggunakan layanan ini tanpa pendampingan orang tua</li>
                    <li>Chatbot tidak mengirimkan pesan promosi atau iklan tanpa persetujuan eksplisit pengguna (opt-in)
                        terlebih dahulu</li>
                    <li>Seluruh pengoperasian chatbot mematuhi Kebijakan Pengiriman Pesan WhatsApp Business, Instagram
                        Messaging Policy, dan Standar Komunitas Meta</li>
                    <li>Kami berhak menghentikan akses chatbot kepada pengguna yang terbukti menyalahgunakan layanan
                        atau melanggar Ketentuan Layanan Meta</li>
                </ul>
            </div>
        </div>

        <!-- SECTION 9 -->
        <div class="section" id="s9">
            <div class="section-header">
                <span class="section-num">9</span>
                <h2 class="section-title">Hubungi Kami</h2>
            </div>
            <div class="section-body">
                <p>Untuk pertanyaan mengenai kebijakan ini, permintaan terkait data pribadi Anda, atau untuk melaporkan
                    dugaan pelanggaran privasi, silakan hubungi kami melalui:</p>
                <div class="contact-card">
                    <div class="contact-card-header">Informasi Kontak Resmi</div>
                    <div class="contact-row">
                        <span class="contact-label">Nama Bisnis</span>
                        <span class="contact-val">Kue Pandan Asli</span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">Website</span>
                        <span class="contact-val"><a href="https://www.kuepandanasli.com"
                                target="_blank">www.kuepandanasli.com</a></span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">WhatsApp</span>
                        <span class="contact-val"> 082144834303</span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">Email</span>
                        <span class="contact-val"><a
                                href="mailto:kuepandanasli@gmail.com">[kuepandanasli@gmail.com]</a></span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">Instagram</span>
                        <span class="contact-val"><a href="https://instagram.com/pandanasli"
                                target="_blank">@pandanasli</a></span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">Alamat</span>
                        <span class="contact-val">Jl. Lebak Jaya II, RT.005/RW.04, Gading, Kec. Tambaksari, Surabaya,
                            Jawa Timur 60134, Indonesia</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 10 -->
        <div class="section" id="s10">
            <div class="section-header">
                <span class="section-num">10</span>
                <h2 class="section-title">Perubahan Kebijakan</h2>
            </div>
            <div class="section-body">
                <p>Kami berhak memperbarui Kebijakan Privasi ini sewaktu-waktu untuk mencerminkan perubahan operasional,
                    hukum, atau teknis. Perubahan yang bersifat signifikan akan diberitahukan melalui:</p>
                <ul class="policy-list">
                    <li>Notifikasi melalui chatbot WhatsApp atau Instagram kami</li>
                    <li>Pengumuman di halaman website kuepandanasli.com</li>
                    <li>Informasi di bagian atas halaman ini berupa tanggal "Terakhir Diperbarui"</li>
                </ul>
                <p>Pemberitahuan akan dilakukan minimal <span class="hl">30 (tiga puluh) hari</span> sebelum
                    perubahan berlaku, kecuali perubahan yang diwajibkan oleh hukum dengan segera.</p>
                <div class="callout">
                    Penggunaan chatbot yang berlanjut setelah tanggal berlakunya perubahan dianggap sebagai penerimaan
                    atas kebijakan yang diperbarui. Jika Anda tidak menyetujui perubahan tersebut, Anda dapat
                    menghentikan penggunaan layanan chatbot kami.
                </div>
            </div>
        </div>

    </div><!-- /page -->

    <footer>
        <p>Kebijakan ini dibuat sesuai dengan <strong>Kebijakan Platform Meta</strong>, WhatsApp Business Policy,<br>
            Instagram Platform Policy, dan <strong>UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi</strong>.</p>
        <br>
        <p>&copy; 2026 <strong>kuepandanasli.com</strong> — Semua hak dilindungi undang-undang.</p>
    </footer>

    <!-- BACK TO TOP BUTTON -->
    <button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        aria-label="Kembali ke atas">&#8679;</button>

    <script>
        const btn = document.getElementById('backTop');
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 300);
        });
    </script>

</body>

</html>
