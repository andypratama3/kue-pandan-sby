@extends('layouts.argon')
@section('title', 'Profile Saya')
@section('page_title', 'Profile')
@section('content')
<div>
    <div class="p-0">
        <div class="space-y-10">
            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 rounded-lg bg-mint border border-brand-light dark:bg-brand-deep/40 dark:border-brand-deep">
                    <p class="text-brand-deep dark:text-brand-light">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Profile Information -->
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="p-6 rounded-lg shadow-sm bg-gray-50 dark:bg-slate-800">
                    <h3 class="mb-4 text-lg font-semibold text-slate-700 dark:text-white">Informasi Profil</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">Update your account's profile information and email address.</p>
                    @include('dashboard.kurir.profile.update-profile-form')
                </div>
            @endif

            <!-- Update Password -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="p-6 rounded-lg shadow-sm bg-gray-50 dark:bg-slate-800">
                    <h3 class="mb-4 text-lg font-semibold text-slate-700 dark:text-white">Ubah Password</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
                    @include('dashboard.kurir.profile.update-password-form')
                </div>
            @endif

            <!-- Logout Other Browser Sessions (Keep Livewire for this complex feature) -->
            <div class="p-6 rounded-lg shadow-sm bg-gray-50 dark:bg-slate-800">
                <h3 class="mb-4 text-lg font-semibold text-slate-700 dark:text-white">Logout dari Sesi Lain</h3>
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            <!-- Delete Account (Keep Livewire for this complex feature) -->
            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="p-6 rounded-lg shadow-sm bg-red-50 dark:bg-red-950/30 dark:border dark:border-red-900/50">
                    <h3 class="mb-4 text-lg font-semibold text-red-600 dark:text-red-300">Hapus Akun</h3>
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
