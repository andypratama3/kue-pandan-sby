<!DOCTYPE html>
<html lang="id" style="scroll-behavior: smooth;">

<head>
    <meta name="facebook-domain-verification" content="jaqulg6tnt8xz910m0q7dmtvgkizw4" />
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-PRXFHTTN');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6GHRM0X2ZS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-6GHRM0X2ZS');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="Kue, Kue Pandan, Kue Pandan Asli, Kue Pandan Ijo, Kue Pandan Tradisional, Kue Tradisional, Kue Ijo, Malang, Kue Malang, Oleh oleh Malang, Kue Ijo Alami, Kue Natural, Kue Asli Pandan, Kue Ketan, Kue Pulut, Kue Singkong, Kelapa, Kelapa parut, Kue Ongol, Kue Aren, Kue Talang, Kue Estetik, Kue Srikaya, Kue Coklat, Kue Manis, Kue Ubi Nanas, Kue Ubi, Kue Nanas, Lumpur Surga, Tumpeng Kue, Tumpeng Kue Tradisional, Paket Hampers, Paket Tumpeng, Kue Enak, Kue Lezat, Kue Kekinian, Kue Instagram, Kue Estetik, Camilan Kekinian, Camilan Instgramable, Kue Photogenic, Kue Lembut, Kue Pandan Wangi, Kue Pandan Lembut, Kue Homemade, Buah Tangan Malang, Kue Kuno, Kue 100% Pandan, Kue Delicious, Kue Santan, Kue Gurih, Kue Asin, Kue Tanpa Pengawet, Bahan Baku Alami, Makanan sehat, Saus Srikaya, Kue Srikaya, Kue Basah, Kue Kenyal, Kue Gula Jawa, Saus Gula Jawa, Kue Hijau, Asli Pandan, Kue Ubi Madu, Pandan Homemade">
    <meta name="description"
        content="Kue Pandan Asli, kami adalah perusahaan kuliner yang berfokus pada produksi dan pengembangan kue tradisional berbahan alami tanpa campuran pengawet dan pewarna. Kami berfokus pada bahan bahan alami mulai dari pewarna kami menggunakan 100% pandan pada seluruh produk kami. Kami berkomitmen menghadirkan kue tradisional dengan bahan baku premium, alami dan kekinian.">

    @section('title', 'Homepage')
    @include('layouts.headicon')
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/script_homepage.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- External Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.1.0/dist/medium-zoom.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- External Styles -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand: #6f8f5f;
            --brand-deep: #3f5d3a;
            --ink: #1f2a33;
            --mint: #eef3ec;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--ink);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Sora', 'Plus Jakarta Sans', system-ui, sans-serif;
            letter-spacing: -0.02em;
        }
        ::selection { background: rgba(111, 143, 95, 0.25); }
        .font-display { font-family: 'Sora', 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>

<body class="flex flex-col min-h-screen" style="overflow-x:hidden;">

    <!-- PRELOADER -->
    <div id="preloader"
        class="fixed top-0 left-0 z-50 flex flex-col items-center justify-center w-full h-full bg-white transition-opacity duration-500">
        <img src="{{ asset('assets/homepage/logo.png') }}" alt="Kue Pandan Asli" class="w-14 h-14 mb-4 rounded-full">
        <div class="w-40 h-[2px] overflow-hidden rounded-full bg-mint">
            <div class="w-1/3 h-full bg-brand animate-[preloader_1.2s_ease-in-out_infinite]"></div>
        </div>
        <style>
            @keyframes preloader {
                0% { transform: translateX(-120%); }
                100% { transform: translateX(320%); }
            }
        </style>
    </div>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PRXFHTTN" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
    </noscript>

    <!-- End Google Tag Manager (noscript) -->
    <!-- Floating WhatsApp Button
  <a class="fixed z-50 bottom-6 right-6"
    href="https://wa.me/6282144834303?text=Hai%20admin%20*Pandan%20Asli%20Malang*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
    target="_blank" aria-label="Chat via WhatsApp" style="
    background-color: #212121;
    border-radius: 40px;
    padding: 12px;
    box-shadow: 0 6px 32px 0 rgba(44,62,80,0.16);
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
  "> -->
    <!-- WhatsApp
    <svg width="26" height="26" viewBox="0 0 26 26" fill="white" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd"
        d="M18.8663 15.5805C18.5434 15.4191 16.9607 14.6413 16.666 14.533C16.3713 14.4257 16.1568 14.3726 15.9413 14.6955C15.7268 15.0161 15.1103 15.742 14.9229 15.9565C14.7344 16.172 14.547 16.198 14.2253 16.0377C13.9035 15.8752 12.8657 15.5361 11.6361 14.4398C10.6795 13.5861 10.0328 12.532 9.84533 12.2092C9.65792 11.8875 9.82475 11.713 9.98617 11.5527C10.1313 11.4086 10.3079 11.1768 10.4693 10.9894C10.6307 10.8009 10.6838 10.6665 10.7911 10.451C10.8994 10.2365 10.8453 10.049 10.764 9.88763C10.6838 9.72621 10.0403 8.14129 9.77167 7.49671C9.51058 6.86946 9.24517 6.95504 9.048 6.94421C8.8595 6.93554 8.645 6.93338 8.4305 6.93338C8.216 6.93338 7.86717 7.01354 7.5725 7.33638C7.27675 7.65813 6.44583 8.43705 6.44583 10.022C6.44583 11.6058 7.5985 13.1365 7.75992 13.3521C7.92133 13.5666 10.0295 16.8188 13.2589 18.213C14.0281 18.5445 14.6272 18.7428 15.0941 18.8901C15.8654 19.136 16.5674 19.1014 17.1221 19.018C17.7396 18.9259 19.0266 18.239 19.2953 17.4872C19.5628 16.7354 19.5628 16.0908 19.4827 15.9565C19.4025 15.8221 19.188 15.742 18.8652 15.5805H18.8663ZM12.9924 23.6005H12.9881C11.07 23.6008 9.18707 23.0852 7.53675 22.1076L7.14675 21.8758L3.09292 22.9396L4.17517 18.9876L3.92058 18.5825C2.84823 16.8755 2.28074 14.9 2.28367 12.8841C2.28583 6.97996 7.08933 2.17646 12.9968 2.17646C15.8568 2.17646 18.5456 3.29229 20.5671 5.31596C21.5642 6.30892 22.3544 7.48974 22.8922 8.7901C23.43 10.0905 23.7046 11.4845 23.7001 12.8917C23.6979 18.7959 18.8944 23.6005 12.9924 23.6005ZM22.1054 3.77871C20.9118 2.57721 19.4916 1.62454 17.9271 0.975912C16.3626 0.32728 14.6849 -0.00441593 12.9913 4.43923e-05C5.89117 4.43923e-05 0.1105 5.77963 0.108333 12.883C0.105043 15.1437 0.698072 17.3652 1.82758 19.3235L0 26L6.82933 24.2082C8.71853 25.2375 10.8356 25.7768 12.987 25.7769H12.9924C20.0926 25.7769 25.8733 19.9973 25.8754 12.8928C25.8807 11.1998 25.5502 9.52265 24.903 7.95825C24.2559 6.39384 23.3051 4.97327 22.1054 3.77871Z"
        fill="white" />
    </svg>
  </a> Icon -->

    <!-- Navbar -->
    <nav id="navbar"
        class="fixed top-0 left-0 z-50 flex items-center justify-between w-full h-16 md:h-[72px] bg-white/90 transition-all duration-300">
        <!-- Logo Kiri -->
        <div class="flex items-center flex-shrink-0 gap-2.5 pl-4 md:pl-10">
            <img src="{{ asset('assets/homepage/logo.png') }}" alt="Logo Kue Pandan Asli"
                class="object-cover w-9 h-9 md:w-10 md:h-10 rounded-full ring-2 ring-brand/20">
            <a href="/"
                class="font-display font-semibold text-brand-deep text-lg tracking-tight hover:text-black active:text-black transition-colors duration-200 md:hidden lg:inline">Kue
                Pandan Asli</a>
        </div>
        <!-- Menu Tengah -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[560px] max-w-full hidden lg:block">
            <ul class="flex items-center justify-center w-full gap-9 text-[13px] font-semibold tracking-wide text-slate-600">
                <li>
                    <a href="#tentang-kami" class="transition hover:text-brand-deep">Tentang Kami</a>
                </li>
                <li>
                    <a href="#produk-kami" class="transition hover:text-brand-deep">Produk</a>
                </li>
                <li>
                    <a href="#testimoni" class="transition hover:text-brand-deep">Testimoni</a>
                </li>
                <li>
                    <a href="#outlet-location" class="transition hover:text-brand-deep">Outlet</a>
                </li>
            </ul>
        </div>
        <!-- Search & Order Kanan -->
        <div class="flex items-center flex-shrink-0 gap-3 pr-4 md:pr-10">
            <form action="{{ route('login') }}" method="GET" class="hidden md:block">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-deep text-white rounded-full px-5 py-2 font-semibold text-xs tracking-wide hover:bg-brand active:scale-[0.98] transition">
                    Masuk
                </button>
            </form>
            <!-- Hamburger Menu (Mobile) -->
            <button id="hamburger-btn"
                class="text-white bg-brand-deep hover:bg-brand focus:outline-none rounded-xl p-2.5 transition-all duration-200 active:scale-95 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="fixed top-[64px] md:top-[72px] left-0 w-full h-[calc(100vh-64px)] bg-slate-950/50 z-40 opacity-0 pointer-events-none transition-all duration-300 lg:hidden">
        <div class="flex flex-col w-4/5 h-full max-w-xs gap-1 p-8 text-base font-medium text-slate-700 bg-white shadow-xl rounded-r-3xl"
            style="animation: slidein 0.3s cubic-bezier(.4,0,.2,1)">
            <a href="#tentang-kami" class="py-2 text-left transition hover:text-brand-deep">Tentang Kami</a>
            <a href="#produk-kami" class="py-2 text-left transition hover:text-brand-deep">Produk</a>
            <a href="#testimoni" class="py-2 text-left transition hover:text-brand-deep">Testimoni</a>
            <a href="#outlet-location" class="py-2 text-left transition hover:text-brand-deep">Outlet</a>

            <div class="my-4 border-t border-slate-200"></div>

            <a href="{{ route('login') }}"
                class="block w-full text-center bg-brand-deep text-white rounded-full px-5 py-2.5 font-semibold text-sm hover:bg-brand transition">
                Masuk
            </a>
        </div>
    </div>

    <!-- Spacer agar konten tidak tertutup navbar -->
    <div class="h-[64px] md:h-[72px]"></div>

    <!-- Banner Section -->
    <section class="relative w-full min-h-[560px] md:min-h-[620px] flex items-center overflow-hidden">
        <img src="{{ asset('assets/homepage/hero-image.jpg') }}" alt="Kue Pandan Asli" loading="lazy"
            class="absolute inset-0 object-cover object-center w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-900/60 to-transparent"></div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-5 md:px-10 py-24 md:py-28">
            <div class="max-w-xl" data-aos="fade-up">
                <span
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/20 bg-white/10 backdrop-blur-sm text-[11px] font-semibold tracking-[0.18em] uppercase text-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    Kue Tradisional Nusantara
                </span>
                <h1 class="mt-6 text-4xl md:text-6xl font-bold text-white leading-[1.05] tracking-tight">
                    Kue Pandan<br class="hidden md:block"> Asli
                </h1>
                <p class="mt-5 text-base md:text-lg leading-relaxed text-slate-200/90 max-w-md">
                    100% pewarna alami daun pandan, tanpa pengawet, tanpa pewarna tambahan. Nikmati warisan
                    kuliner yang dikemas premium untuk setiap momen spesial Anda.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#produk-kami"
                        class="inline-flex items-center gap-2 bg-emerald-300 text-emerald-950 px-6 py-3 rounded-full text-sm font-bold hover:bg-white active:scale-[0.98] transition">
                        Lihat Produk
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="#outlet-location"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold text-white border border-white/30 hover:bg-white/10 active:scale-[0.98] transition">
                        Cari Outlet
                    </a>
                </div>

                <div class="mt-10 pt-6 border-t border-white/15">
                    <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-slate-300/80 mb-3">Pesan langsung via WhatsApp</p>
                    <div class="flex flex-wrap gap-2.5">
                        <a href="https://wa.me/6282144834303?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Surabaya*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                            target="_blank"
                            class="inline-flex items-center gap-2.5 bg-white text-slate-800 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold hover:bg-emerald-50 transition">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#25D366]">
                                <i class="fab fa-whatsapp text-white text-sm"></i>
                            </span>
                            Surabaya
                        </a>
                        <a href="https://wa.me/6282131338971?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Malang*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                            target="_blank"
                            class="inline-flex items-center gap-2.5 bg-white text-slate-800 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold hover:bg-emerald-50 transition">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#25D366]">
                                <i class="fab fa-whatsapp text-white text-sm"></i>
                            </span>
                            Malang
                        </a>
                        <a href="https://wa.me/6282338901223?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Bali*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                            target="_blank"
                            class="inline-flex items-center gap-2.5 bg-white text-slate-800 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold hover:bg-emerald-50 transition">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#25D366]">
                                <i class="fab fa-whatsapp text-white text-sm"></i>
                            </span>
                            Bali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    <!-- About Us Section -->
    <section id="tentang-kami" class="w-full py-16 md:py-24 bg-mint">
        <div class="w-[92%] max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16 items-center" data-aos="fade-up">
            <!-- Left: Image -->
            <div class="relative">
                <div class="absolute -top-6 -left-6 w-full h-full border-2 border-brand/30 rounded-3xl hidden md:block"></div>
                <img src="{{ asset('assets/homepage/about-us.jpg') }}" alt="Kue Pandan Asli" loading="lazy"
                    class="relative w-full h-[380px] md:h-[440px] object-cover rounded-3xl shadow-xl shadow-emerald-900/10">
                <div class="absolute bottom-6 -left-2 md:-left-6 bg-white rounded-2xl shadow-lg px-5 py-4">
                    <p class="font-display text-3xl font-bold text-brand-deep leading-none">100%</p>
                    <p class="mt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pewarna Alami Daun Pandan</p>
                </div>
            </div>
            <!-- Right: Content -->
            <div class="flex flex-col items-start">
                <span class="text-brand-deep font-semibold tracking-[0.18em] text-xs uppercase">Tentang Kami</span>
                <div x-data="{ tab: 'tentang' }" class="w-full mt-5">
                    <div class="flex gap-8 mb-6 border-b border-emerald-900/10">
                        <button @click="tab = 'tentang'"
                            class="px-1 pb-3 font-display text-sm font-semibold transition-colors duration-200 focus:outline-none"
                            :class="tab === 'tentang' ? 'text-brand-deep border-b-2 border-brand-deep' :
                                'text-slate-400 hover:text-brand-deep border-b-2 border-transparent'">
                            Tentang
                        </button>
                        <button @click="tab = 'visi'"
                            class="px-1 pb-3 font-display text-sm font-semibold transition-colors duration-200 focus:outline-none"
                            :class="tab === 'visi' ? 'text-brand-deep border-b-2 border-brand-deep' :
                                'text-slate-400 hover:text-brand-deep border-b-2 border-transparent'">
                            Visi
                        </button>
                        <button @click="tab = 'misi'"
                            class="px-1 pb-3 font-display text-sm font-semibold transition-colors duration-200 focus:outline-none"
                            :class="tab === 'misi' ? 'text-brand-deep border-b-2 border-brand-deep' :
                                'text-slate-400 hover:text-brand-deep border-b-2 border-transparent'">
                            Misi
                        </button>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-ink mb-5 leading-tight" x-show="tab === 'tentang'">Warisan
                        Kuliner<br class="hidden md:block"> Nusantara</h2>
                    <div x-show="tab === 'tentang'" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div x-data="{ open: false }">
                            <div class="text-[15px] leading-relaxed text-slate-600">
                                <p class="mb-4">Kue Pandan Asli adalah perusahaan kuliner yang berfokus pada produksi kue
                                    tradisional berbahan alami — tanpa pengawet, tanpa pewarna tambahan, tanpa pengharum
                                    dan tanpa pemanis buatan. Pewarna kami 100% berasal dari daun pandan segar pada seluruh
                                    produk.</p>
                                <div x-show="open" class="space-y-4">
                                    <p>Kami mengenalkan kembali warisan kuliner nusantara melalui produk unggulan seperti
                                        Kue Ijo Pandan, Kue Pulut Srikaya, Kue Lumpur Surga, Kue Ongol, Kue Ubi Nanas dan
                                        Koci Ketan Hitam, dengan bahan alami yang melewati quality control ketat — mulai
                                        dari daun pandan hijau tua, gula jawa murni, hingga nanas dari petani lokal.</p>
                                    <p>Kami percaya makanan bukan hanya soal rasa, tetapi juga pengalaman dan nilai budaya.
                                        Setiap produk dikemas photogenic dan instagramable — menjadi pilihan utama oleh-oleh
                                        dalam Paket Hampers A/B/C serta Tumpeng Mini dan Tumpeng Besar.</p>
                                </div>
                                <button @click="open = !open"
                                    class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-brand-deep hover:text-brand transition">
                                    <span x-show="!open">Selengkapnya</span>
                                    <span x-show="open">Tutup</span>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div x-show="tab === 'visi'" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <h2 class="text-3xl md:text-4xl font-bold text-ink mb-5 leading-tight">Visi Kami</h2>
                        <div class="text-[15px] leading-relaxed text-slate-600 space-y-4">
                            <p>Menjadi pelopor dalam pelestarian dan pengembangan kue tradisional Indonesia berbahan alami,
                                dengan menghadirkan produk yang tidak hanya lezat dan sehat, tetapi juga dikemas secara
                                modern dan menarik.</p>
                            <p>Kami ingin membawa warisan kuliner nusantara ke generasi masa kini dan mendatang, sehingga
                                kue tradisional tetap relevan, dicintai, dan menjadi kebanggaan bangsa.</p>
                        </div>
                    </div>
                    <div x-show="tab === 'misi'" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <h2 class="text-3xl md:text-4xl font-bold text-ink mb-5 leading-tight">Misi Kami</h2>
                        <ul class="space-y-4 text-[15px] leading-relaxed text-slate-600">
                            <li class="flex gap-3">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                                <span><strong class="font-semibold text-brand-deep">Bahan alami:</strong> seluruh
                                    produk dibuat dari bahan baku alami, tanpa pengawet, pewarna, pengharum dan pemanis
                                    buatan.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                                <span><strong class="font-semibold text-brand-deep">Inovasi berkelanjutan:</strong>
                                    mengembangkan varian kue tradisional dengan sentuhan modern, baik dari rasa maupun
                                    tampilan.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                                <span><strong class="font-semibold text-brand-deep">Tanggung jawab produk:</strong>
                                    menjaga kualitas dari pemilihan bahan, proses produksi, hingga pengemasan dan
                                    pengiriman.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                                <span><strong class="font-semibold text-brand-deep">Pelayanan prima:</strong>
                                    memberikan pengalaman terbaik melalui produk berkualitas, pelayanan ramah, dan kemasan
                                    eksklusif.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="produk-kami" class="w-full py-16 md:py-24 bg-white">
        <div class="w-[92%] max-w-7xl mx-auto" x-data="{ kategori: 'semua' }" x-init="AOS.init({ once: true, duration: 800 })"
            x-effect="$nextTick(() => { AOS.refreshHard() })">
            <!-- Section Header -->
            <div class="mb-10 text-center">
                <h2 class="text-3xl md:text-5xl font-bold text-ink mb-4">Produk Kami</h2>
                <p class="max-w-xl mx-auto text-base md:text-lg leading-relaxed text-slate-500">
                    Varian kue pandan berkualitas, dibuat dengan resep tradisional dan bahan pilihan.
                </p>
            </div>
            <!-- Tombol Filter Kategori -->
            <div class="flex flex-wrap justify-center gap-2 mb-10 md:gap-3">
                <button @click="kategori = 'semua'"
                    :class="kategori === 'semua' ? 'bg-brand-deep text-white border-brand-deep' : 'bg-white text-slate-600 border-slate-200 hover:border-brand hover:text-brand-deep'"
                    class="px-5 py-2 text-xs font-bold tracking-wide transition rounded-full border">Semua</button>
                @foreach ($products->pluck('tag')->unique() as $tag)
                    <button @click="kategori = '{{ $tag }}'"
                        :class="kategori === '{{ $tag }}' ? 'bg-brand-deep text-white border-brand-deep' : 'bg-white text-slate-600 border-slate-200 hover:border-brand hover:text-brand-deep'"
                        class="px-5 py-2 text-xs font-bold tracking-wide transition rounded-full border">{{ $tag }}</button>
                @endforeach
            </div>

            <!-- Grid Produk (dari database) -->
            <div class="grid grid-cols-1 gap-8 pb-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:pb-12">
                @forelse ($products as $product)
                    <div x-show="kategori === 'semua' || kategori === '{{ $product['tag'] }}'"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="relative transition-all duration-300 bg-white rounded-2xl border border-slate-200/70 hover:border-brand/40 hover:shadow-lg hover:shadow-emerald-900/5"
                        data-aos="fade-up" x-data="{
                            open: false,
                            prices: {{ Illuminate\Support\Js::from($product['variants']) }},
                            selectedPrice: {{ Illuminate\Support\Js::from($product['variants'][0] ?? ['label' => 'Per Cup', 'value' => 0]) }}
                        }" :class="open ? 'z-30' : 'z-0'">
                        <div class="relative overflow-hidden rounded-t-2xl">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy"
                                class="object-cover w-full h-52 transition-transform duration-500 hover:scale-105 cursor-zoom-in zoomable">
                            <div
                                class="absolute px-2.5 py-1 text-[10px] font-bold tracking-widest uppercase bg-white/95 text-brand-deep rounded-full top-3 right-3 shadow-sm">
                                {{ $product['tag'] }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-lg font-semibold text-ink mb-2">{{ $product['name'] }}</h3>
                            <div x-data="{ descOpen: false }">
                                <p class="mb-3 text-sm leading-relaxed text-slate-500" :class="descOpen ? '' : 'line-clamp-2'">
                                    {{ $product['description'] }}
                                </p>
                                @if (mb_strlen((string) $product['description']) > 120)
                                    <button @click="descOpen = !descOpen"
                                        class="text-brand-deep text-xs font-semibold focus:outline-none hover:underline mb-2">
                                        <span x-show="!descOpen">Selengkapnya</span>
                                        <span x-show="descOpen">Tutup</span>
                                    </button>
                                @endif
                            </div>
                            @if (count($product['variants']) > 1)
                                <div class="flex items-center justify-between mt-4">
                                    <div class="relative w-full">
                                        <button @click="open = !open"
                                            class="flex items-center justify-between w-full px-3 py-2.5 text-left border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand/30">
                                            <div>
                                                <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                                <span class="block font-bold text-lg text-brand-deep">Rp <span
                                                        x-text="selectedPrice.value.toLocaleString('id-ID')"></span></span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-transition
                                            class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg shadow-emerald-900/5">
                                            <ul>
                                                <template x-for="price in prices" :key="price.label">
                                                    <li @click="selectedPrice = price; open = false"
                                                        class="p-3 cursor-pointer hover:bg-mint">
                                                        <span class="font-semibold text-gray-800" x-text="price.label"></span>
                                                        <span class="block text-sm text-brand-deep">Rp <span
                                                                x-text="price.value.toLocaleString('id-ID')"></span></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between mt-4">
                                    <div class="relative w-full">
                                        <div>
                                            <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                            <span class="block font-bold text-lg text-brand-deep">Rp <span
                                                    x-text="selectedPrice.value.toLocaleString('id-ID')"></span></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-slate-500">Belum ada produk yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>




    <!-- Testimonials Section -->
    <section id="testimoni" class="w-full py-16 md:py-24 bg-mint">
        <div class="w-[92%] max-w-7xl mx-auto">
            <div class="mb-12 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-ink mb-4">Apa Kata Pelanggan Kami?</h2>
                <p class="max-w-xl mx-auto text-base md:text-lg leading-relaxed text-slate-500">
                    Kepuasan pelanggan adalah prioritas kami — dari kue pandan, hampers, hingga tumpeng.
                </p>
            </div>

            <!-- Testimonial Carousel Container -->
            <div class="relative" data-aos="fade-up">
                <!-- Navigation Arrows -->
                <button id="testimonial-prev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white text-brand-deep rounded-full p-3 shadow-md shadow-emerald-900/10 transition-all duration-300 hover:bg-brand-deep hover:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <button id="testimonial-next"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white text-brand-deep rounded-full p-3 shadow-md shadow-emerald-900/10 transition-all duration-300 hover:bg-brand-deep hover:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </button>

                <!-- Carousel Container -->
                <div id="testimonial-carousel" class="overflow-hidden">
                    <div id="testimonial-track" class="flex transition-transform duration-700 ease-in-out">

                        <!-- Testimonial 1 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white rounded-2xl border border-slate-200/70">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <!-- Large Portrait Photo Container -->
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-3.jpeg') }}"
                                                alt="Testimoni Mbak Muanansa" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Mbak Muanansa</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Malang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Content -->
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>

                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "Sebenarnya saya tau ini dari temen yang kasih rekomendasi, baru beberapa
                                            kali coba cocok dan kedepan akan jadi langganan terus sih sepertinya."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>

                                        <!-- Star Rating -->
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 2 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-2.jpeg') }}"
                                                alt="Testimoni Pak Handoko" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Pak Handoko</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "Saya sekeluarga cocok, kalau ada acara kantor langsung pesen kesini, orang
                                            orang kantor juga pada nanyain beli dimana? Saya beli di kue pandan asli
                                            surabaya"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 4 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-4.jpeg') }}"
                                                alt="Testimoni Budi Santoso" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Bu Nanik</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Surbaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "Alhamdulillah sudah 4x pesan di kue pandan asli di malang selalu cocok sama
                                            rasanya, kaya pas aja dimakan dan semua rasanya pas, enak dan nyaman di
                                            mulut"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <!-- Half Star SVG -->
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill="#D1D5DB"
                                                            d="M9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                        <path
                                                            d="M9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 9.9,1.1" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">4.5</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 5 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-5.jpeg') }}"
                                                alt="Testimoni Sari Dewi" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Pak Zainal</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "saya sebenernya tau dari rekan sejawat saya , kok enak jadi langanan saya
                                            seterusnya. Nyonya kalau ada acara arisan atau pas cucu main kerumah pasti
                                            pesen buat acara, jadinya keterusan"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 6 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-6.jpeg') }}"
                                                alt="Testimoni Andi Wijaya" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Pak Riko</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Malang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "anak saya selalu belikan saya, kalau pas pengen selalu dibelikan disini
                                            katanya kue nya sehat dan aman karena pakai gula asli dan tanpa campuran
                                            pewarna maupun pengawet."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 8 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-8.jpeg') }}"
                                                alt="Testimoni Rizki Pratama" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/95 border border-slate-100">
                                                    <h4 class="text-sm font-semibold text-ink">Pak Pras</h4>
                                                    <p class="text-brand-deep text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-brand/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-9 bg-brand"></div>
                                                <div>
                                                    <h3 class="font-display text-xl md:text-2xl font-semibold text-ink">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-xs uppercase tracking-widest text-slate-400">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg leading-relaxed text-slate-600 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                            "langganan orang kantor ini sampai setiap kali ada acara saya selalu nrima
                                            tumpeng punya nya kue pandan asli , saya sampai hapal ini hampers, tumpeng
                                            dan bingkisan pasti punya kue pandan asli, saya akui rasanya memang mantap
                                            joss enak"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-brand-deep/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-slate-200 rounded-lg bg-white/80 backdrop-blur-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-600">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <polygon
                                                            points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                    </svg>
                                                    <!-- Half Star SVG -->
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill="#D1D5DB"
                                                            d="M9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 15,17.8 13.8,11.9 18.2,7.6 12.2,6.6 " />
                                                        <path
                                                            d="M9.9,1.1 7.6,6.6 1.6,7.6 6,11.9 4.8,17.8 9.9,14.8 9.9,1.1" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-bold text-brand-deep">4.5</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Dots Indicator -->
                <div class="flex justify-center gap-2 mt-6">
                    <button class="testimonial-dot w-2.5 h-2.5 rounded-full bg-brand-deep transition-all duration-300"
                        data-slide="0"></button>
                    <button
                        class="w-2.5 h-2.5 transition-all duration-300 bg-slate-300 rounded-full testimonial-dot hover:bg-brand"
                        data-slide="1"></button>
                    <button
                        class="w-2.5 h-2.5 transition-all duration-300 bg-slate-300 rounded-full testimonial-dot hover:bg-brand"
                        data-slide="3"></button>
                    <button
                        class="w-2.5 h-2.5 transition-all duration-300 bg-slate-300 rounded-full testimonial-dot hover:bg-brand"
                        data-slide="4"></button>
                    <button
                        class="w-2.5 h-2.5 transition-all duration-300 bg-slate-300 rounded-full testimonial-dot hover:bg-brand"
                        data-slide="5"></button>
                    <button
                        class="w-2.5 h-2.5 transition-all duration-300 bg-slate-300 rounded-full testimonial-dot hover:bg-brand"
                        data-slide="7"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Outlet Location Section -->
    <section id="outlet-location" class="w-full py-16 md:py-24 bg-white">
        <div class="w-[92%] max-w-5xl mx-auto">
            <div class="mb-12 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-ink mb-3">Lokasi Outlet Kami</h2>
                <p class="max-w-xl mx-auto text-base md:text-lg leading-relaxed text-slate-500">
                    Temukan outlet Kue Pandan Asli terdekat di kota Anda.
                </p>
            </div>
            <div class="flex justify-center gap-3 mb-10" data-aos="fade-down">
                <button id="btn-surabaya"
                    class="outlet-btn bg-brand-deep text-white px-5 py-2.5 text-sm font-semibold rounded-full border border-brand-deep transition">Surabaya</button>
                <button id="btn-malang"
                    class="outlet-btn bg-white text-brand-deep px-5 py-2.5 text-sm font-semibold rounded-full border border-brand/30 hover:border-brand transition">Malang</button>

            </div>
            <div id="outlet-content" data-aos="fade-up"
                class="flex flex-col items-center gap-6 p-6 border border-slate-200/70 rounded-3xl md:p-10 md:flex-row">
                <!-- Google Maps -->
                <div class="flex items-center justify-center w-full md:w-1/2">
                    <iframe id="outlet-map"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.9681779681105!2d112.775769691843!3d-7.244461222219543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9b0b298d195%3A0x1b301a8958c157c6!2sKue%20Ijo%20Pandan%20Asli!5e0!3m2!1sid!2sid!4v1753154879994!5m2!1sid!2sid"
                        width="100%" height="500"
                        style="border:0; border-radius:1rem; box-shadow:0 2px 16px 0 rgba(44,62,80,0.08); max-width: 480px; min-width: 320px; display: block; margin: 0 auto; background: #eee;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <!-- Outlet Info -->
                <div class="flex flex-col w-full gap-4 md:w-1/2">
                    <img id="outlet-img" loading="lazy" src="{{ asset('assets/homepage/b1.jpg') }}"
                        alt="Outlet Surabaya" class="object-cover w-full mb-2 rounded-xl h-36 border border-slate-200/70">
                    <h3 id="outlet-title" class="text-2xl font-bold text-ink mb-1">Pusat Surabaya</h3>
                    <p id="outlet-address" class="mb-1 text-slate-600">Jalan Lebak Jaya II Gading,
                        Tambaksari, Surabaya, Jawa Timur 60134 (Rumah pagar hitam)</p>

                    <!-- Jam Buka -->
                    <p id="outlet-hours-info" class="flex items-center gap-2 mb-1 text-sm text-slate-500">
                        <i class="text-lg fas fa-calendar-days"></i> <span id="outlet-hours-text">Buka Setiap Hari,
                            06.00 - 23.00</span>
                    </p>

                    <!-- WhatsApp -->
                    <a id="outlet-contact" href="#" target="_blank"
                        class="flex items-center gap-2 mb-2 text-sm text-slate-500 transition hover:text-brand-deep">
                        <i class="text-lg fab fa-whatsapp"></i>
                        <span id="outlet-contact-text">Telp: -</span>
                    </a>

                    <!-- Email -->
                    <a id="outlet-email" href="#"
                        class="flex items-center gap-2 text-sm text-slate-500 transition hover:text-brand-deep">
                        <i class="text-lg far fa-envelope"></i>
                        <span id="outlet-email-text">pandanaslisbyadm@gmail.com</span>
                    </a>

                    <!-- Sosial Media -->
                    {{-- <div class="flex items-center gap-4 text-gray-600">
                        <span class="text-base font-medium">Social Media :</span>
                        <div class="flex items-center gap-4 text-base">
                            <a id="social-instagram" href="#" target="_blank"
                                class="transition hover:text-pink-500" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a id="social-tiktok" href="#" target="_blank"
                                class="transition hover:text-black" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a id="social-facebook" href="#" target="_blank"
                                class="transition hover:text-blue-600" aria-label="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                        </div>
                    </div> --}}

                    {{-- <div class="flex flex-row w-full gap-3 mt-2 text-gray-600">
                        <h4 class="text-base font-medium text-gray-800">Social Media :</h4>

                        <a id="social-instagram" href="#" target="_blank"
                            class="flex items-center gap-3 transition-colors duration-200 hover:text-pink-500">
                            <i class="w-6 text-xl text-center fab fa-instagram"></i>
                            <span id="social-instagram-text" class="text-sm font-medium">-</span>
                        </a>

                        <a id="social-tiktok" href="#" target="_blank"
                            class="flex items-center gap-3 transition-colors duration-200 hover:text-black">
                            <i class="w-6 text-xl text-center fab fa-tiktok"></i>
                            <span id="social-tiktok-text" class="text-sm font-medium">-</span>
                        </a>

                        <a id="social-facebook" href="#" target="_blank"
                            class="flex items-center gap-3 transition-colors duration-200 hover:text-blue-600">
                            <i class="w-6 text-xl text-center fab fa-facebook"></i>
                            <span id="social-facebook-text" class="text-sm font-medium">-</span>
                        </a>
                    </div> --}}

                    <div class="w-full mt-2 text-slate-500">
                        <h4 class="mb-3 text-sm font-semibold text-slate-600">Sosial Media</h4>

                        <div class="flex flex-row justify-center gap-3 text-center md:justify-start">

                            <a id="social-instagram" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-slate-200 rounded-lg hover:border-brand/40 hover:text-brand-deep hover:-translate-y-0.5">
                                <i class="text-2xl fab fa-instagram"></i>
                                <span id="social-instagram-text"
                                    class="w-full text-xs font-medium truncate">-</span>
                            </a>

                            <a id="social-tiktok" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-slate-200 rounded-lg hover:border-brand/40 hover:text-brand-deep hover:-translate-y-0.5">
                                <i class="text-2xl fab fa-tiktok"></i>
                                <span id="social-tiktok-text" class="w-full text-xs font-medium truncate">-</span>
                            </a>

                            <a id="social-facebook" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-slate-200 rounded-lg hover:border-brand/40 hover:text-brand-deep hover:-translate-y-0.5">
                                <i class="text-2xl fab fa-facebook"></i>
                                <span id="social-facebook-text" class="w-full text-xs font-medium truncate">-</span>
                            </a>

                        </div>
                    </div>

                    {{-- <div class="grid w-full grid-cols-3 gap-3 mt-4 text-center text-gray-600">
                        <a id="social-instagram" href="#" target="_blank"
                            class="flex flex-col items-center justify-center p-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm hover:shadow-md hover:border-pink-500 hover:text-pink-500 hover:-translate-y-1">
                            <i class="mb-1 text-2xl fab fa-instagram"></i>
                            <span id="social-instagram-text" class="w-full text-xs font-medium truncate">-</span>
                        </a>

                        <a id="social-tiktok" href="#" target="_blank"
                            class="flex flex-col items-center justify-center p-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm hover:shadow-md hover:border-gray-800 hover:text-gray-800 hover:-translate-y-1">
                            <i class="mb-1 text-2xl fab fa-tiktok"></i>
                            <span id="social-tiktok-text" class="w-full text-xs font-medium truncate">-</span>
                        </a>

                        <a id="social-facebook" href="#" target="_blank"
                            class="flex flex-col items-center justify-center p-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm hover:shadow-md hover:border-blue-600 hover:text-blue-600 hover:-translate-y-1">
                            <i class="mb-1 text-2xl fab fa-facebook"></i>
                            <span id="social-facebook-text" class="w-full text-xs font-medium truncate">-</span>
                        </a>
                    </div> --}}

                    <div class="w-full">
                        <a id="outlet-directions" href="https://maps.app.goo.gl/FBLH5zD3sq1wBYit8" target="_blank"
                            class="w-full flex justify-center items-center gap-2 bg-brand-deep text-white px-5 py-2.5 rounded-full font-semibold transition hover:bg-brand">
                            <i class="fa-solid fa-location-arrow"></i> Google Maps </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto text-white bg-ink">
        <div class="w-full max-w-screen-xl p-4 py-6 mx-auto lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="/" class="flex flex-col items-start">
                        <img src="{{ asset('assets/homepage/logo.png') }}" alt="Logo"
                            class="object-cover w-8 h-8 mb-2 rounded-full">
                        <span class="text-2xl font-semibold text-left whitespace-nowrap">Kue Pandan Asli</span>
                    </a>
                    <p class="max-w-xs mt-2 text-sm text-white/60">Kue Pandan Asli, kami adalah perusahaan kuliner
                        yang berfokus pada produksi dan pengembangan kue tradisional berbahan alami tanpa campuran
                        pengawet dan pewarna.</p>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    {{-- <div>
                        <h2 class="mb-6 text-sm font-semibold uppercase">Social Media</h2>
                        <ul class="font-medium text-gray-400">
                            <li class="mb-4">
                                <a href="https://www.instagram.com/pandanaslimalang/"
                                    class="hover:underline">Instagram</a>
                            </li>
                            <li class="mb-4">
                                <a href="https://www.tiktok.com/@pandanasli_malang"
                                    class="hover:underline">Tiktok</a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/profile.php?id=61557399493559&ref=pro_upsell_xav_ig_profile_page_web#"
                                    class="hover:underline">Facebook</a>
                            </li>
                        </ul>
                    </div> --}}
                    <div>
                        <h2 class="mb-6 text-xs font-semibold uppercase tracking-widest text-brand">Menu</h2>
                        <ul class="text-sm text-white/60">
                            <li class="mb-3">
                                <a href="#tentang-kami" class="transition hover:text-white">Tentang Kami</a>
                            </li>
                            <li class="mb-3">
                                <a href="#testimoni" class="transition hover:text-white">Testimoni</a>
                            </li>
                            <li class="mb-3">
                                <a href="#outlet-location" class="transition hover:text-white">Outlet</a>
                            </li>
                            <li>
                                <a href="#produk-kami" class="transition hover:text-white">Produk Kami</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-xs font-semibold uppercase tracking-widest text-brand">Katalog</h2>
                        <ul class="text-sm text-white/60">
                            <li class="mb-3">
                                <a href="#" class="transition hover:text-white">Download Katalog</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-white/10 sm:mx-auto lg:my-8" />
            <div class="p-4 sm:flex sm:items-center sm:justify-center">
                <span class="text-sm text-white/50 sm:text-center">© Copyright 2010 - 2025 <a href="#"
                        class="hover:text-white">Kue Pandan
                        Asli</a><br>All Rights Reserved.
                </span>
            </div>
        </div>
    </footer>

    <!-- Pass asset URLs to JavaScript -->
    <script>
        window.assetUrls = {
            outletImages: {
                surabaya: "{{ asset('assets/homepage/b1.jpg') }}",
                malang: "{{ asset('assets/homepage/b2.jpg') }}",
                denpasar: "{{ asset('assets/homepage/b3.jpg') }}"
            }
        };
    </script>
</body>

</html>
