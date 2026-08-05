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

    <!-- External Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.1.0/dist/medium-zoom.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- External Styles -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<!-- PRELOADER -->
<div id="preloader" class="fixed top-0 left-0 z-50 flex items-center justify-center w-full h-full bg-white">
    <div class="w-16 h-16 border-t-4 border-b-4 border-green-500 rounded-full animate-spin"></div>
</div>

<body class="flex flex-col min-h-screen" style="overflow-x:hidden;">
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
        class="fixed top-0 left-0 z-50 flex items-center justify-between w-full py-3 transition-all duration-300 shadow-sm bg-white/80">
        <!-- Logo Kiri -->
        <div class="flex items-center flex-shrink-0 gap-2 pl-2 md:pl-6">
            <img src="{{ asset('assets/homepage/logo.png') }}" alt="Logo"
                class="object-cover w-10 h-10 rounded-full">
            <a href="/"
                class="font-semibold text-[#8BA870] text-xl tracking-tight hover:text-black active:text-black transition-colors duration-200 md:hidden lg:inline">Kue
                Pandan Asli</a>
        </div>
        <!-- Menu Tengah Absolute Center Boxed -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] max-w-full">
            <ul class="items-center justify-center hidden w-full gap-8 text-sm font-medium text-gray-700 md:flex">
                <li>
                    <a href="#tentang-kami" class="transition hover:text-green-600">Tentang Kami</a>
                </li>
                <li>
                    <a href="#produk-kami" class="transition hover:text-green-600">Produk Kami</a>
                </li>
                <li>
                    <a href="#testimoni" class="transition hover:text-green-600">Testimoni</a>
                </li>
                <li>
                    <a href="#outlet-location" class="transition hover:text-green-600">Outlet Location</a>
                </li>
            </ul>
        </div>
        <!-- Search & Order Kanan -->
        <div class="flex items-center flex-shrink-0 gap-3 pr-2 md:pr-6">
            <!-- Form wrapper untuk tombol Login -->
            <form action="{{ route('login') }}" method="GET" class="hidden md:block">
                <button type="submit"
                    class="flex items-center gap-2 bg-[#8BA870] text-white rounded-full px-5 py-1.5 font-medium text-sm hover:bg-[#7a965e] transition">
                    <!-- Ikon Login Font Awesome -->
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>
            </form>
        </div>

        <!-- Hamburger Menu (Mobile) -->
        <div class="flex items-center ml-2 mr-4 md:hidden">
            <button id="hamburger-btn"
                class="text-white bg-[#8BA870]/80 hover:bg-[#8BA870] focus:outline-none rounded-lg p-2 transition-all duration-200 active:scale-95 active:bg-[#7a965e] transform hover:scale-105">
                <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="fixed top-[68px] left-0 w-full h-[calc(100vh-68px)] bg-gray-900/60 z-40 opacity-0 pointer-events-none transition-all duration-300">
        <div class="flex flex-col w-4/5 h-full max-w-xs gap-6 p-8 text-lg font-medium text-gray-700 bg-white shadow-xl rounded-r-2xl animate-slidein"
            style="animation: slidein 0.3s cubic-bezier(.4,0,.2,1)">
            <a href="#tentang-kami" class="text-left transition hover:text-green-600">Tentang Kami</a>
            <a href="#produk-kami" class="text-left transition hover:text-green-600">Produk Kami</a>
            <a href="#testimoni" class="text-left transition hover:text-green-600">Testimoni</a>
            <a href="#outlet-location" class="text-left transition hover:text-green-600">Outlet Location</a>

            <!-- Separator -->
            <div class="my-4 border-t border-gray-200"></div>

            <!-- Tombol/Login Link -->
            <a href="{{ route('login') }}"
                class="block w-full text-center bg-[#8BA870] text-white rounded-full px-5 py-2 font-medium text-sm hover:bg-[#7a965e] transition">
                Login
            </a>
        </div>
    </div>

    <!-- Spacer agar konten tidak tertutup navbar -->
    <div class="h-[68px] md:h-[68px]"></div>

    <!-- Banner Section -->
    <section class="relative w-full h-[400px] md:h-[520px] flex items-center justify-center overflow-hidden">
        <img src="{{ asset('assets/homepage/hero-image.jpg') }}" alt="Hero Image" loading="lazy"
            class="absolute inset-0 object-cover object-center w-full h-full brightness-90">
        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center" data-aos="fade-down">
            <h1 class="mb-4 text-4xl font-extrabold text-white md:text-6xl drop-shadow-lg">Kue Pandan Asli</h1>
            <p class="max-w-2xl mb-8 text-lg font-medium text-center text-white md:text-2xl drop-shadow">Kue Ijo Adalah
                Kue tradisional Indonesia, cocok untuk semua acara spesial Anda.</p>
            <!-- Kotak dengan 3 tombol WhatsApp Admin -->
            <div
                class="max-w-md p-3 mx-auto border shadow-lg bg-white/30 backdrop-blur-sm rounded-xl border-white/20 sm:max-w-2xl">
                <h3 class="mb-2 text-sm font-semibold text-center text-white drop-shadow-md">Pesan Sekarang</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 sm:gap-2">
                    <!-- Admin Surabaya -->
                    <a href="https://wa.me/6282144834303?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Surabaya*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                        target="_blank"
                        class="flex items-center justify-center gap-2 bg-greenlight hover:bg-[#20b858] text-white px-4 py-2 rounded-full shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 active:scale-95 w-full">
                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-white rounded-full">
                            <svg class="w-3 h-3 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Admin Surabaya</span>
                    </a>

                    <!-- Admin Malang -->
                    <a href="https://wa.me/6282131338971?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Malang*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                        target="_blank"
                        class="flex items-center justify-center gap-2 bg-greenlight hover:bg-[#20b858] text-white px-4 py-2 rounded-full shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 active:scale-95 w-full">
                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-white rounded-full">
                            <svg class="w-3 h-3 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Admin Malang</span>
                    </a>

                    <!-- Admin Bali -->
                    <a href="https://wa.me/6282338901223?text=Hai%20admin%20*Kue%20Pandan%20Asli%20Bali*%2C%20perkenalan%20nama%20saya%20(isi%20nama%20anda)%20.%20Saya%20ingin%20.."
                        target="_blank"
                        class="flex items-center justify-center gap-2 bg-greenlight hover:bg-[#20b858] text-white px-4 py-2 rounded-full shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 active:scale-95 w-full">
                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-white rounded-full">
                            <svg class="w-3 h-3 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Admin Bali</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#8BA870]/40 to-transparent"></div>
    </section>

    <!-- About Us Section -->
    <section id="tentang-kami" class="w-full py-8" style="background-color: #f2f6f5;">
        <div class="w-[90%] max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-12" data-aos="fade-up">
            <!-- Left: Image -->
            <div class="w-full md:w-1/2">
                <img src="{{ asset('assets/homepage/about-us.jpg') }}" alt="About Us" loading="lazy"
                    class="w-full h-[400px] object-cover rounded-2xl shadow-lg">
            </div>
            <!-- Right: Content (tab only on this side) -->
            <div class="flex flex-col items-start w-full md:w-1/2">
                <div x-data="{ tab: 'tentang' }" class="w-full">
                    <div class="relative flex gap-8 mb-4 border-b border-gray-200">
                        <button @click="tab = 'tentang'"
                            class="px-1 pb-2 text-lg font-semibold transition-colors duration-200 border-b-2 focus:outline-none"
                            :class="tab === 'tentang' ? 'text-[#8BA870] border-[#8BA870]' :
                                'text-gray-500 hover:text-[#8BA870] border-b-2 border-transparent'">
                            Tentang
                        </button>
                        <button @click="tab = 'visi'"
                            class="px-1 pb-2 text-lg font-semibold transition-colors duration-200 border-b-2 focus:outline-none"
                            :class="tab === 'visi' ? 'text-[#8BA870] border-[#8BA870]' :
                                'text-gray-500 hover:text-[#8BA870] border-b-2 border-transparent'">
                            Visi
                        </button>
                        <button @click="tab = 'misi'"
                            class="px-1 pb-2 text-lg font-semibold transition-colors duration-200 border-b-2 focus:outline-none"
                            :class="tab === 'misi' ? 'text-[#8BA870] border-[#8BA870]' :
                                'text-gray-500 hover:text-[#8BA870] border-b-2 border-transparent'">
                            Misi
                        </button>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#2C3E50] mb-6" x-show="tab === 'tentang'">Kue
                        Pandan Asli</h2>
                    <div x-show="tab === 'tentang'" x-transition:enter="transition ease-out duration-800"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div x-data="{ open: false }">
                            <p class="mb-8 leading-relaxed text-gray-600">
                            <div x-show="!open" class="text-justify">
                                Kue Pandan Asli, kami adalah perusahaan kuliner yang berfokus pada produksi dan
                                pengembangan kue tradisional berbahan alami tanpa bahan pengawet, tanpa pewarna
                                tambahan, tanpa pengharum tambahan dan tanpa pemanis buatan. Kami berfokus pada bahan
                                bahan alami mulai dari pewarna kami menggunakan 100% pandan pada seluruh produk kami.
                                Kami berkomitmen menghadirkan kue tradisional dengan bahan baku premium, alami dan
                                kekinian.<br>
                                <button @click="open = true"
                                    class="inline-flex items-center gap-2 bg-[#8BA870] text-white px-4 py-1.5 rounded-full font-semibold shadow hover:bg-[#7a965e] transition mt-8 group">
                                    <svg class="w-4 h-4 text-white transition-transform group-hover:translate-x-1"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                    <span>Selengkapnya</span>
                                </button>
                            </div>
                            <div x-show="open" class="text-justify">
                                Kue Pandan Asli, kami adalah perusahaan kuliner yang berfokus pada produksi dan
                                pengembangan kue tradisional berbahan alami tanpa bahan pengawet, tanpa pewarna
                                tambahan, tanpa pengharum tambahan dan tanpa pemanis buatan. Kami berfokus pada bahan
                                bahan alami mulai dari pewarna kami menggunakan 100% pandan pada seluruh produk kami.
                                Kami berkomitmen menghadirkan kue tradisional dengan bahan baku premium, alami dan
                                kekinian.<br><br>
                                Kue Pandan Asli mengenalkan kembali warisan kuliner nusantara melalui produk-produk
                                unggulan seperti Kue Ijo Pandan, Kue Pulut Srikaya, Kue Lumpur Surga, Kue Ongol, Kue Ubi
                                Nanas dan Koci Ketan Hitam dengan mengedepankan 100% bahan alami yang telah melalui
                                quality control tim kami mulai dari pemilihan daun pandan yang hijau tua segar, gula
                                jawa murni dari suplier sampai dengan pemilihan nanas langsung dari petani lokal.
                                Mengangkat Kue Tradisional yang dikemas secara premium, cantik dan estetik agar sesuai
                                dengan perkembangan zaman.<br><br>
                                Kami percaya bahwasanya makanan tidak hanya soal rasa, tetapi juga pengalaman dan nilai
                                budaya. Oleh karena itu, setiap produk kami dikemas dengan visual yang photogenic dan
                                instagramable, menjadikannya pilihan utama sebagai oleh-oleh yang tersedia mulai dari
                                Paket Hampers A (Anggun), Hampers B (Bagus) dan Hampers C (Cantik) dan Paket Tumpeng kue
                                tradisional mulai dari Tumpeng Mini dan Tumpeng Besar yang cocok untuk berbagai
                                acara.<br>
                                <button @click="open = false"
                                    class="inline-flex items-center gap-2 bg-gray-200 text-[#8BA870] px-4 py-1.5 rounded-full font-semibold shadow hover:bg-gray-300 transition mt-8 group">
                                    <svg class="w-4 h-4 text-[#8BA870] group-hover:-translate-x-1 transition-transform"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                                    </svg>
                                    <span>Tutup Selengkapnya</span>
                                </button>
                            </div>
                            </p>
                        </div>
                    </div>
                    <div x-show="tab === 'visi'" x-transition:enter="transition ease-out duration-800"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <h2 class="text-3xl md:text-4xl font-bold text-[#2C3E50] mb-6">Visi Kami</h2>
                        <div class="mb-8 leading-relaxed text-gray-600">
                            <p class="mb-4 text-justify">Menjadi pelopor dalam pelestarian dan pengembangan kue
                                tradisional Indonesia berbahan alami, dengan menghadirkan produk yang tidak hanya lezat
                                dan sehat, tetapi juga dikemas secara modern dan menarik.</p>
                            <p class="text-justify">Kami ingin membawa warisan kuliner nusantara ke generasi masa kini
                                dan mendatang, sehingga kue tradisional tetap relevan, dicintai, dan menjadi kebanggaan
                                bangsa.</p>
                        </div>
                    </div>
                    <div x-show="tab === 'misi'" x-transition:enter="transition ease-out duration-800"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <h2 class="text-3xl md:text-4xl font-bold text-[#2C3E50] mb-6">Misi Kami</h2>
                        <ul class="pl-6 mb-8 space-y-2 leading-relaxed text-gray-600 list-disc">
                            <li class="text-justify">
                                <span class="font-semibold text-[#8BA870]">Mengutamakan bahan alami:</span> Seluruh
                                produk dibuat dari bahan baku alami, tanpa bahan pengawet, tanpa pewarna tambahan, tanpa
                                pengharum tambahan dan tanpa pemanis buatan.
                            </li>
                            <li class="text-justify">
                                <span class="font-semibold text-[#8BA870]">Inovasi berkelanjutan:</span> Terus
                                mengembangkan varian kue tradisional dengan sentuhan modern, baik dari segi rasa maupun
                                tampilan.
                            </li>
                            <li class="text-justify">
                                <span class="font-semibold text-[#8BA870]">Tanggung jawab produk:</span> Menjaga
                                kualitas mulai dari pemilihan bahan, proses produksi, hingga pengemasan dan pengiriman
                                ke konsumen.
                            </li>
                            <li class="text-justify">
                                <span class="font-semibold text-[#8BA870]">Pelayanan prima:</span> Memberikan
                                pengalaman terbaik bagi pelanggan melalui produk berkualitas, pelayanan ramah, dan
                                kemasan eksklusif.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="produk-kami" class="w-full pt-4 pb-8 bg-white">
        <!-- [DIUBAH DI SINI] Menambahkan x-init untuk AOS dan x-effect untuk me-refresh animasi -->
        <div class="w-[90%] max-w-7xl mx-auto" x-data="{ kategori: 'semua' }" x-init="AOS.init({ once: true, duration: 800 })"
            x-effect="$nextTick(() => { AOS.refreshHard() })">
            <!-- Section Header -->
            <div class="mb-6 text-center">
                <span class="text-[#8BA870] font-medium tracking-wider text-sm mb-3 block">PRODUK UNGGULAN</span>
                <h2 class="text-4xl md:text-5xl font-bold text-[#2C3E50] mb-6">Produk Kami</h2>
                <p class="max-w-3xl mx-auto text-lg leading-relaxed text-gray-600">
                    Nikmati berbagai varian kue pandan berkualitas tinggi yang dibuat dengan resep tradisional dan
                    bahan-bahan pilihan
                </p>
            </div>
            <!-- Tombol Filter Kategori -->
            <div class="flex flex-wrap justify-center gap-2 mb-4 md:gap-4">
                <!-- Tombol baru untuk menampilkan semua produk -->
                <button @click="kategori = 'semua'"
                    :class="kategori === 'semua' ? 'bg-[#8BA870] text-white' : 'bg-gray-100 text-[#2C3E50]'"
                    class="px-5 py-2 font-semibold transition rounded-full shadow">Semua</button>
                <button @click="kategori = 'produk'"
                    :class="kategori === 'produk' ? 'bg-[#8BA870] text-white' : 'bg-gray-100 text-[#2C3E50]'"
                    class="px-5 py-2 font-semibold transition rounded-full shadow">Ala Carte</button>
                <button @click="kategori = 'hampers'"
                    :class="kategori === 'hampers' ? 'bg-[#8BA870] text-white' : 'bg-gray-100 text-[#2C3E50]'"
                    class="px-5 py-2 font-semibold transition rounded-full shadow">Hampers</button>
                <button @click="kategori = 'tumpeng'"
                    :class="kategori === 'tumpeng' ? 'bg-[#8BA870] text-white' : 'bg-gray-100 text-[#2C3E50]'"
                    class="px-5 py-2 font-semibold transition rounded-full shadow">Tumpeng</button>
            </div>

            <!-- Judul untuk Ala Carte -->
            <div x-show="kategori === 'semua' || kategori === 'produk'" class="mt-10 mb-4">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">
                    <h3 class="inline-block text-2xl font-bold text-[#2C3E50] pb-2 border-b-4 border-[#8BA870]">
                        Produk Ala Carte
                    </h3>
                </div>
            </div>

            <!-- Kategori: Produk -->
            <div x-show="kategori === 'semua' || kategori === 'produk'"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="grid grid-cols-1 gap-8 pb-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:pb-12">

                <!-- [DIUBAH DI SINI] Menambahkan kembali data-aos -->
                <div class="relative transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in" x-data="{
                        open: false,
                        prices: [
                            { label: 'Isi 3 Pcs (Kemasan Mika)', value: 9000 },
                            { label: 'Isi 5 Pcs (Kemasan Mika)', value: 15000 },
                            { label: 'Isi 12 Pcs (Kemasan Thinwall)', value: 40000 }
                        ],
                        selectedPrice: { label: 'Isi 3 Pcs (Kemasan Mika)', value: 9000 }
                    }" :class="open ? 'z-30' : 'z-0'">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-ijo.jpg') }}" alt="Kue Ijo" loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Ijo</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue ijo adalah kue tradisional terbuat dari tepung tapioka dengan warna hijaunya asli
                                khas harum daun pandan segar. Teksturnya super kenyal dan lembut, wangi dengan taburan
                                kelapa. Menambah cita rasa gurih dan sedikit manis yang menjadikan kue Ijo cocok
                                dinikmati pada suasana apapun.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-left border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8BA870]">
                                    <div>
                                        <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                        <span class="block font-bold text-lg text-[#8BA870]">Rp <span
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
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                    <ul>
                                        <template x-for="price in prices" :key="price.label">
                                            <li @click="selectedPrice = price; open = false"
                                                class="p-3 cursor-pointer hover:bg-gray-100">
                                                <span class="font-semibold text-gray-800" x-text="price.label"></span>
                                                <span class="block text-sm text-[#8BA870]">Rp <span
                                                        x-text="price.value.toLocaleString('id-ID')"></span></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in" x-data="{
                        open: false,
                        prices: [
                            { label: 'Isi 10 Pcs (Kemasan Mika)', value: 10000 },
                            { label: 'Isi 30 Pcs (Kemasan Thinwall)', value: 40000 }
                        ],
                        selectedPrice: { label: 'Isi 10 Pcs (Kemasan Mika)', value: 10000 }
                    }" :class="open ? 'z-30' : 'z-0'">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-ongol.jpg') }}" alt="Kue Ongol"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Ongol Ongol</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue Ongol kami terbuat dari tepung tapioka yang ditambahkan gula merah jawa dan sedikit
                                tambahan air pandan asli membuat warna kue ini bewarna coklat cantik. Kue yang memiliki
                                tekstur kenyal dan legit dan terasa manis dimulut. Kue Ongol yang memiliki wangi khas
                                gula jawa merah sangat cocok dipadukan dengan parutan kelapa segar yang akan memadukan
                                antara gurih dan manis pada kue ini.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-left border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8BA870]">
                                    <div>
                                        <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                        <span class="block font-bold text-lg text-[#8BA870]">Rp <span
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
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                    <ul>
                                        <template x-for="price in prices" :key="price.label">
                                            <li @click="selectedPrice = price; open = false"
                                                class="p-3 cursor-pointer hover:bg-gray-100">
                                                <span class="font-semibold text-gray-800" x-text="price.label"></span>
                                                <span class="block text-sm text-[#8BA870]">Rp <span
                                                        x-text="price.value.toLocaleString('id-ID')"></span></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in" x-data="{
                        open: false,
                        prices: [
                            { label: 'Isi 5 Pcs (Kemasan Mika)', value: 17500 },
                            { label: 'Isi 10 Pcs (Kemasan Thinwall)', value: 40000 }
                        ],
                        selectedPrice: { label: 'Isi 5 Pcs (Kemasan Mika)', value: 17500 }
                    }" :class="open ? 'z-30' : 'z-0'">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-pulut.jpg') }}" alt="Kue Pulut Srikaya"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Pulut Srikaya</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue Pulut yang dibuat dari beras ketan utuh yang akan menciptakan tekstur punel dan
                                sedikit legit. Perpaduan warna Putih dari beras ketan dan Ungu alami dari bunga telang
                                (butterfly pea tea) menambah keindahan visual pada kue ini. Kue Pulut ini dilengkapi
                                dengan saus srikaya yang membuat perpaduan antara manis dan sedikit gurih dari saus dan
                                gurih dari kue pulut itu sendiri menjadikan perpaduan rasa yang lengkap dan lezat.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-left border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8BA870]">
                                    <div>
                                        <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                        <span class="block font-bold text-lg text-[#8BA870]">Rp <span
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
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                    <ul>
                                        <template x-for="price in prices" :key="price.label">
                                            <li @click="selectedPrice = price; open = false"
                                                class="p-3 cursor-pointer hover:bg-gray-100">
                                                <span class="font-semibold text-gray-800" x-text="price.label"></span>
                                                <span class="block text-sm text-[#8BA870]">Rp <span
                                                        x-text="price.value.toLocaleString('id-ID')"></span></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/selai-srikaya.jpg') }}" alt="Selai Srikaya"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Selai Srikaya</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Selai yang terbuat dari perpaduan santan kelapa, telur, air pandan yang menghasilkan
                                rasa yang gurih dan manis pada selai ini. Selai yang memiliki tekstur kental dan
                                memiliki warna oren pekat dihasilkan dari telur. Selai itu cocok dipadukan dengan kue
                                yang memiliki cita rasa netral maupun gurih untuk menambahkan cita rasa manis pada rasa
                                kue tersebut.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <div>
                                    <span class="text-xs text-gray-500">Isi 160 ml (Kemasan Botol Kaca)</span>
                                    <span class="block font-bold text-lg text-[#8BA870]">Rp 60.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in" x-data="{
                        open: false,
                        prices: [
                            { label: 'Cup @ 100 ml', value: 6000 },
                            { label: 'Cup @ 200 ml', value: 12000 }
                        ],
                        selectedPrice: { label: 'Cup @ 100 ml', value: 6000 }
                    }" :class="open ? 'z-30' : 'z-0'">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-lumpur-surga.jpg') }}" alt="Kue Lumpur Surga"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Lumpur Surga</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue yang memiliki dua lapisan yaitu lapisan bawah bewarna hijau yang dihasilkan dari air
                                pandan asli dan lapisan atas mirip dengan vla yang terbuat dari santan. Kue yang
                                memiliki tekstur lembut dan lumer dimulut sangat nikmat jika disantap dalam keadaan
                                dingin. Memiliki Cita rasa manis dari kue nya dan gurih asin dari vla nya berpadu
                                menghasilkan rasa nikmat yang nyaman ketika masuk dimulut.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <button @click="open = !open"
                                    class="flex items-center justify-between w-full px-3 py-2 text-left border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8BA870]">
                                    <div>
                                        <span class="text-xs text-gray-500" x-text="selectedPrice.label"></span>
                                        <span class="block font-bold text-lg text-[#8BA870]">Rp <span
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
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                    <ul>
                                        <template x-for="price in prices" :key="price.label">
                                            <li @click="selectedPrice = price; open = false"
                                                class="p-3 cursor-pointer hover:bg-gray-100">
                                                <span class="font-semibold text-gray-800" x-text="price.label"></span>
                                                <span class="block text-sm text-[#8BA870]">Rp <span
                                                        x-text="price.value.toLocaleString('id-ID')"></span></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-ubi-nanas.jpeg') }}" alt="Kue Ubi Nanas"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Ubi Nanas</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue Ubi Nanas Adalah Kue yang terbuat dari Singkong atau ubi kayu yang empuk dan
                                dikombinasi dengan buah nanas serta ditaburi parutan kelapa.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <div>
                                    <span class="text-xs text-gray-500">Isi 4 Pcs (Kemasan Mika)</span>
                                    <span class="block font-bold text-lg text-[#8BA870]">Rp 10.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-koci-ketan-hitam.jpg') }}"
                            alt="Kue Koci Ketan Hitam"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Koci Ketan Hitam</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue Koci Ketan Hitam Adalah Kue yang terbuat dari ketan hitam pilihan yang telah
                                dihaluskan. Dengan isian kelapa parut dan gula merah. Berbentuk segitiga kerucut.
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="relative w-full">
                                <div>
                                    <span class="text-xs text-gray-500">Isi 6 Pcs (Kemasan Thinwall)</span>
                                    <span class="block font-bold text-lg text-[#8BA870]">Rp 40.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-mix-mini.jpeg') }}" alt="Kue Mix Tradisional"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Mix Mini</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue MIX mini ( Kemasan Mika)
                                Berisi :
                                Kue ijo 3 pcs,
                                Kue Ongol-ongol 4 pcs dan
                                Kue Pulut Srikaya 2 pcs
                                Kue ini cocok untuk dijadikan sebagai oleh-oleh atau sebagai cemilan ringan
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 25.000</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/kue-mix-kueijo.jpeg') }}" alt="Kue Mix Premium"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-orange-500 rounded-full top-3 right-3">
                            Ala Carte
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Kue Mix (Kue Ijo & Kue Pulut)</h3>
                        <div x-data="{ open: false }">
                            <p class="mb-3 text-sm text-justify text-gray-600" :class="open ? '' : 'line-clamp-2'">
                                Kue MIX (Kemasan Thinwall) Berisi : Kue ijo 4 pcs, Kue Pulut Srikaya 5 pcs
                            </p>
                            <button @click="open = !open"
                                class="text-[#8BA870] text-xs font-semibold focus:outline-none hover:underline mb-2">
                                <span x-show="!open">Selengkapnya</span>
                                <span x-show="open">Tutup</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 40.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Judul untuk Hampers -->
            <div x-show="kategori === 'semua' || kategori === 'hampers'" class="mt-10 mb-4">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">
                    <h3 class="inline-block text-2xl font-bold text-[#2C3E50] pb-2 border-b-4 border-[#8BA870]">
                        Pilihan Hampers Spesial
                    </h3>
                </div>
            </div>

            <!-- Kategori: Hampers -->
            <div x-show="kategori === 'semua' || kategori === 'hampers'"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="grid grid-cols-1 gap-8 pb-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:pb-12">

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/hampers-a.jpg') }}" alt="Hampers A (Anggun)"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute top-3 right-3 bg-[#7079a8] text-white px-2 py-1 rounded-full text-xs font-medium">
                            Hampers</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Hampers A (Anggun)</h3>
                        <p class="mb-3 text-sm text-justify text-gray-600">Berisi: Kue Ijo (12 pcs), Kue Ongol-ongol
                            (30 pcs), Kue Pulut Srikaya (10 pcs), Lumpur Surga (4 cup @100ml).</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 160.000</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/hampers-b.jpg') }}" alt="Hampers B (Bagus)"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute top-3 right-3 bg-[#7079a8] text-white px-2 py-1 rounded-full text-xs font-medium">
                            Hampers</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Hampers B (Bagus)</h3>
                        <p class="mb-3 text-sm text-justify text-gray-600">Berisi: Kue Ijo (12 pcs), Kue Ongol-ongol
                            (30 pcs), Kue Pulut Srikaya (10 pcs).</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 130.000</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/hampers-c.png') }}" alt="Hampers C (Cantik)"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute top-3 right-3 bg-[#7079a8] text-white px-2 py-1 rounded-full text-xs font-medium">
                            Hampers</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Hampers C (Cantik)</h3>
                        <p class="mb-3 text-sm text-justify text-gray-600 line-clamp-2">Kue Ijo (5 pcs), Kue
                            Ongol-ongol (12 pcs), Kue Pulut Srikaya (4 pcs), Kue Ubi Nanas (8 pcs).</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 65.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Judul untuk Tumpeng -->
            <div x-show="kategori === 'semua' || kategori === 'tumpeng'" class="mt-10 mb-4">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">
                    <h3 class="inline-block text-2xl font-bold text-[#2C3E50] pb-2 border-b-4 border-[#8BA870]">
                        Tumpeng Kue Tradisional
                    </h3>
                </div>
            </div>

            <!-- Kategori: Tumpeng -->
            <div x-show="kategori === 'semua' || kategori === 'tumpeng'"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="grid grid-cols-1 gap-8 pb-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 md:pb-12">

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/tumpeng-mini.jpg') }}" alt="Tumpeng Mini"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full top-3 right-3">
                            Tumpeng</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Tumpeng Mini Mix</h3>
                        <p class="mb-3 text-sm text-justify text-gray-600 ">Berisi: Kue Ijo (25 pcs), Kue Pulut (20
                            pcs), Kue Ongol-ongol (50 pcs), Lumpur Surga (6 cup). Tumpeng mini mix ini cocok untuk
                            syukuran, ulang tahun, atau acara spesial lainnya.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 250.000</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-2xl hover:shadow-xl hover:-translate-y-1"
                    data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="{{ asset('assets/homepage/product/tumpeng-besar.jpg') }}" alt="Tumpeng Besar"
                            loading="lazy"
                            class="object-cover w-full h-48 transition-transform duration-300 hover:scale-105 cursor-zoom-in zoomable">
                        <div
                            class="absolute px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full top-3 right-3">
                            Tumpeng</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-[#2C3E50] mb-2">Tumpeng Besar Mix</h3>
                        <p class="mb-3 text-sm text-justify text-gray-600">Berisi: Kue Ijo (50 pcs), Kue Pulut (40
                            pcs), Kue Ongol-ongol (100 pcs), Lumpur Surga (12 cup). Tumpeng besar mix untuk acara
                            keluarga, kantor, arisan, atau perayaan penting lainnya.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8BA870] font-bold text-lg">Rp 500.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- Testimonials Section -->
    <section id="testimoni" class="w-full py-8 bg-white" style="background-color: #f2f6f5;">
        <div class="w-[90%] max-w-7xl mx-auto">
            <div class="mb-10 text-center">
                <span class="text-[#8BA870] font-medium tracking-wider text-sm mb-3 block">TESTIMONI</span>
                <h2 class="text-3xl md:text-4xl font-bold text-[#2C3E50] mb-4">Apa Kata Pelanggan Kami?</h2>
                <p class="max-w-2xl mx-auto text-lg leading-relaxed text-gray-600">
                    Kepuasan pelanggan adalah prioritas kami. Berikut beberapa testimoni dari pelanggan setia Kue Pandan
                    Asli Malang.
                </p>
            </div>

            <!-- Testimonial Carousel Container -->
            <div class="relative" data-aos="zoom-out-down">
                <!-- Navigation Arrows -->
                <button id="testimonial-prev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-[#8BA870] rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#8BA870] focus:ring-opacity-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <button id="testimonial-next"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-[#8BA870] rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#8BA870] focus:ring-opacity-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </button>

                <!-- Carousel Container -->
                <div id="testimonial-carousel" class="overflow-hidden rounded-2xl">
                    <div id="testimonial-track" class="flex transition-transform duration-700 ease-in-out">

                        <!-- Testimonial 1 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Mbak Muanansa</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Malang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Content -->
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>

                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "Sebenarnya saya tau ini dari temen yang kasih rekomendasi, baru beberapa
                                            kali coba cocok dan kedepan akan jadi langganan terus sih sepertinya."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>

                                        <!-- Star Rating -->
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Pak Handoko</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "Saya sekeluarga cocok, kalau ada acara kantor langsung pesen kesini, orang
                                            orang kantor juga pada nanyain beli dimana? Saya beli di kue pandan asli
                                            surabaya"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 3 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-1.jpeg') }}"
                                                alt="Testimoni Adhitya" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Adhitya</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Denpasar</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "Kue langganan keluarga , dari dulu sering beli sampe kalau mau kasih
                                            hampers mau ngasih oleh oleh pasti langsung beli di kue pandan asli, semua
                                            orang dikeluarga suka dan cocok sama rasanya."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Bu Nanik</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Surbaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "Alhamdulillah sudah 4x pesan di kue pandan asli di malang selalu cocok sama
                                            rasanya, kaya pas aja dimakan dan semua rasanya pas, enak dan nyaman di
                                            mulut"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">4.5</span>
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Pak Zainal</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "saya sebenernya tau dari rekan sejawat saya , kok enak jadi langanan saya
                                            seterusnya. Nyonya kalau ada acara arisan atau pas cucu main kerumah pasti
                                            pesen buat acara, jadinya keterusan"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Pak Riko</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Malang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "anak saya selalu belikan saya, kalau pas pengen selalu dibelikan disini
                                            katanya kue nya sehat dan aman karena pakai gula asli dan tanpa campuran
                                            pewarna maupun pengawet."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 7 -->
                        <div class="min-w-full px-4">
                            <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
                                <div class="flex flex-col md:flex-row min-h-[450px]">
                                    <div class="relative w-full md:w-1/3 lg:w-1/4">
                                        <div class="relative overflow-hidden h-80 md:h-full">
                                            <img src="{{ asset('assets/homepage/testimonial/testimoni-7.jpeg') }}"
                                                alt="Testimoni Maya Sari" loading="lazy"
                                                class="object-cover object-center w-full h-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent">
                                            </div>
                                            <div class="absolute bottom-4 left-4 right-4">
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Bu Kristika</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Denpasar, Bali</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "Kalau ada acara kaya arisan, kumpul keluarga atau kumpul kumpul bareng
                                            teman langsung pesan di kue pandan asli, simple enak praktis tinggal wa dan
                                            semua orang pada suka sama rasanya."
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">5.0</span>
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
                                                <div class="p-3 text-center rounded-lg bg-white/90 backdrop-blur-sm">
                                                    <h4 class="text-sm font-bold text-gray-800">Pak Pras</h4>
                                                    <p class="text-[#8BA870] text-xs font-medium">Surabaya</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full md:w-2/3 lg:w-3/4 p-8 md:p-12 flex flex-col justify-center bg-gradient-to-r from-transparent to-[#8BA870]/5">
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-2 h-8 bg-[#8BA870] rounded-full"></div>
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-[#2C3E50]">Testimoni
                                                        Pelanggan</h3>
                                                    <p class="text-sm text-gray-500">Verified Customer</p>
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote
                                            class="relative mb-6 text-lg italic leading-relaxed text-gray-700 md:text-xl">
                                            <div class="absolute -top-2 -left-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                            "langganan orang kantor ini sampai setiap kali ada acara saya selalu nrima
                                            tumpeng punya nya kue pandan asli , saya sampai hapal ini hampers, tumpeng
                                            dan bingkisan pasti punya kue pandan asli, saya akui rasanya memang mantap
                                            joss enak"
                                            <div
                                                class="absolute -bottom-4 -right-2 text-4xl text-[#8BA870]/30 font-serif">
                                                "</div>
                                        </blockquote>
                                        <div
                                            class="p-3 border border-gray-200 rounded-lg shadow-sm bg-white/80 backdrop-blur-sm">
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
                                                <span class="ml-2 text-sm font-bold text-[#8BA870]">4.5</span>
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
                    <button class="testimonial-dot w-3 h-3 rounded-full bg-[#8BA870] transition-all duration-300"
                        data-slide="0"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="1"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="2"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="3"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="4"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="5"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="6"></button>
                    <button
                        class="w-3 h-3 transition-all duration-300 bg-gray-300 rounded-full testimonial-dot hover:bg-gray-400"
                        data-slide="7"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Outlet Location Section -->
    <section id="outlet-location" class="w-full py-8" style="background-color: bg-whhite;">
        <div class="w-[90%] max-w-5xl mx-auto">
            <div class="mb-10 text-center">
                <span class="text-[#8BA870] font-medium tracking-wider text-sm mb-3 block">Outlet Location</span>
                <h2 class="text-3xl md:text-4xl font-bold text-[#2C3E50] mb-2">Lokasi Outlet Kami</h2>
                <p class="max-w-2xl mx-auto text-lg leading-relaxed text-gray-600">
                    Temukan outlet Kue Pandan Asli terdekat di kota Anda.
                </p>
            </div>
            <div class="flex justify-center gap-4 mb-8" data-aos="fade-down">
                <button id="btn-surabaya"
                    class="outlet-btn bg-[#8BA870] text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-[#7a965e] transition">Surabaya</button>
                <button id="btn-malang"
                    class="outlet-btn bg-[#8BA870] text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-[#7a965e] transition">Malang</button>
                <button id="btn-denpasar"
                    class="outlet-btn bg-[#8BA870] text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-[#7a965e] transition">Denpasar</button>

            </div>
            <div id="outlet-content" data-aos="fade-up"
                class="flex flex-col items-center gap-6 p-6 shadow-2xl bg-soft-green rounded-2xl md:flex-row">
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
                        alt="Outlet Surabaya" class="object-cover w-full mb-2 shadow h-36 rounded-xl">
                    <h3 id="outlet-title" class="text-2xl font-bold text-[#2C3E50] mb-1">Pusat Surabaya</h3>
                    <p id="outlet-address" class="mb-1 text-gray-700">Jalan Lebak Jaya II Gading,
                        Tambaksari, Surabaya, Jawa Timur 60134 (Rumah pagar hitam)</p>

                    <!-- Jam Buka -->
                    <p id="outlet-hours-info" class="flex items-center gap-2 mb-1 text-sm text-gray-500">
                        <i class="text-lg fas fa-calendar-days"></i> <span id="outlet-hours-text">Buka Setiap Hari,
                            06.00 - 23.00</span>
                    </p>

                    <!-- WhatsApp -->
                    <a id="outlet-contact" href="#" target="_blank"
                        class="flex items-center gap-2 mb-2 text-sm text-gray-500 transition hover:text-green-600">
                        <i class="text-lg fab fa-whatsapp"></i>
                        <span id="outlet-contact-text">Telp: -</span>
                    </a>

                    <!-- Email -->
                    <a id="outlet-email" href="#"
                        class="flex items-center gap-2 text-sm text-gray-500 transition hover:text-blue-600">
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

                    <div class="w-full mt-2 text-gray-600">
                        <h4 class="mb-3 text-base font-medium text-gray-800">Social Media :</h4>

                        <div class="flex flex-row justify-center gap-3 text-center md:justify-start">

                            <a id="social-instagram" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-gray-300 hover:text-pink-500 hover:-translate-y-1">
                                <i class="text-2xl fab fa-instagram"></i>
                                <span id="social-instagram-text"
                                    class="w-full text-xs font-medium truncate">-</span>
                            </a>

                            <a id="social-tiktok" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-gray-300 hover:text-black hover:-translate-y-1">
                                <i class="text-2xl fab fa-tiktok"></i>
                                <span id="social-tiktok-text" class="w-full text-xs font-medium truncate">-</span>
                            </a>

                            <a id="social-facebook" href="#" target="_blank"
                                class="flex flex-col items-center justify-center w-24 gap-1 p-2 transition-all duration-200 border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-gray-300 hover:text-blue-600 hover:-translate-y-1">
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
                            class="w-full flex justify-center items-center gap-2 bg-[#8BA870] text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-[#7a965e] transition">
                            <i class="fa-solid fa-location-arrow"></i> Google Maps </a>
                    </div>
                </div>

            </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto text-white bg-black">
        <div class="w-full max-w-screen-xl p-4 py-6 mx-auto lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="/" class="flex flex-col items-start">
                        <img src="{{ asset('assets/homepage/logo.png') }}" alt="Logo"
                            class="object-cover w-8 h-8 mb-2 rounded-full">
                        <span class="text-2xl font-semibold text-left whitespace-nowrap">Kue Pandan Asli</span>
                    </a>
                    <p class="max-w-xs mt-2 text-sm text-gray-400">Kue Pandan Asli, kami adalah perusahaan kuliner
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
                        <h2 class="mb-6 text-sm font-semibold uppercase">Useful Link</h2>
                        <ul class="font-medium text-gray-400">
                            <li class="mb-4">
                                <a href="#tentang-kami" class="hover:underline">Tentang Kami</a>
                            </li>
                            <li class="mb-4">
                                <a href="#testimoni" class="hover:underline">Testimoni</a>
                            </li>
                            <li class="mb-4">
                                <a href="#outlet-location" class="hover:underline">Outlet Location</a>
                            </li>
                            <li>
                                <a href="#produk-kami" class="hover:underline">Produk Kami</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold uppercase">Katalog</h2>
                        <ul class="font-medium text-gray-400">
                            <li class="mb-4">
                                <a href="#" class="hover:underline">Download Katalog</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-700 sm:mx-auto lg:my-8" />
            <div class="p-4 sm:flex sm:items-center sm:justify-center">
                <span class="text-sm text-gray-400 sm:text-center">© Copyright 2010 - 2025 <a href="#"
                        class="hover:underline">Kue Pandan
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
