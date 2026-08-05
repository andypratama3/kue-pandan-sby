@section('title', 'Login')
@include('layouts.headicon')

<x-guest-layout>
    <!-- PRELOADER -->
    <script>
        window.addEventListener("load", function() {
            // Ambil elemen preloader
            const preloader = document.getElementById("preloader");

            // Atur jeda waktu sebelum menyembunyikan preloader
            setTimeout(function() {
                preloader.style.display = "none";
            }, 1500); // <-- Atur delay di sini (dalam milidetik). 1000 = 1 detik.
        });
    </script>
    <div id="preloader" class="fixed top-0 left-0 w-full h-full bg-white flex justify-center items-center z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-brand"></div>
    </div>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        {{-- Background --}}
        <img
            src="{{ asset('assets/homepage/bg-login.jpg') }}"
            alt="Background Login"
            class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-ink/40"></div>

        {{-- Parent container: centered & limited width --}}
        <div
            class="relative z-10
             w-full
             max-w-md md:max-w-4xl
             mx-auto
             bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-900/10
             flex overflow-hidden">
            {{-- KIRI: SIGN IN --}}
            <div class="w-full md:w-1/2 px-4 py-8 sm:px-8 sm:py-12 flex flex-col justify-center bg-white max-w-xs mx-auto md:mx-0 md:max-w-full">
                <x-slot name="logo">
                    <div class="flex justify-center mb-6">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-14 h-14">
                    </div>
                </x-slot>

                <h2 class="text-2xl font-bold text-ink mb-2 text-center">Login Area</h2>
                <p class="text-slate-400 text-sm text-center mb-2">
                    Masuk dengan email dan password Anda!
                </p>
                <hr class="border-slate-200 mb-4">

                <x-validation-errors class="mb-4" />

                @if(session('status'))
                <div class="mb-4 font-medium text-sm text-brand-deep">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    {{-- email --}}
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" class="text-brand-deep" />
                        <x-input
                            id="email"
                            class="block mt-1 w-full border-slate-200 rounded-xl px-4 py-2.5 focus:ring-brand/30 focus:border-brand"
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                    {{-- password --}}
                    <div class="mt-4 relative">
                        <x-label for="password" value="{{ __('Password') }}" class="text-brand-deep" />
                        <x-input
                            id="password"
                            class="block mt-1 w-full border-slate-200 rounded-xl px-4 py-2.5 focus:ring-brand/30 focus:border-brand pr-10"
                            type="password" name="password" required autocomplete="current-password" />
                        <button type="button" id="togglePassword" class="absolute right-4 top-9 text-gray-400 focus:outline-none" tabindex="-1">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    {{-- remember --}}
                    <div class="block mt-4">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" class="text-brand focus:ring-brand/30" />
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    {{-- actions --}}
                    <div class="flex items-center justify-between mt-6">
                        <a href="/" class="text-brand-deep hover:text-brand underline">
                            ← Kembali
                        </a>
                        <x-button class="bg-brand-deep hover:bg-brand text-white px-6 rounded-full">
                            {{ __('Login') }}
                        </x-button>
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const passwordInput = document.getElementById('password');
                        const togglePassword = document.getElementById('togglePassword');
                        const eyeIcon = document.getElementById('eyeIcon');
                        let visible = false;
                        togglePassword.addEventListener('click', function() {
                            visible = !visible;
                            passwordInput.type = visible ? 'text' : 'password';
                            eyeIcon.innerHTML = visible ?
                                `<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.442-4.362m3.31-2.547A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.422 5.255M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 3l18 18' />` :
                                `<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />`;
                        });
                    });
                </script>
            </div>

            {{-- KANAN: greeting (hide on mobile) --}}
            <div class="hidden md:flex w-1/2 relative overflow-hidden rounded-tl-3xl rounded-bl-3xl">
                <img
                    src="{{ asset('assets/homepage/login-view.png') }}"
                    alt="Background"
                    class="absolute inset-0 w-full h-full object-cover z-0"
                    style="filter: brightness(0.55);" />
                <div class="absolute inset-0 bg-gradient-to-br from-brand/20 to-brand-deep/70 z-10"></div>

                <div class="relative z-20 flex flex-col items-center justify-center w-full h-full px-4 text-center">
                    <h2 class="text-white text-3xl font-bold mb-2 drop-shadow-lg">Selamat Datang!</h2>
                    <p class="text-white drop-shadow">
                        Sistem Order & Delivery <br>Kue Pandan Asli Malang<br><br>
                        <b>App_Version [1.0.0]</b>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>