@extends('layouts.argon')
@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Akun')

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if (session('success'))
        <div class="p-4 text-xs font-semibold text-brand-deep rounded-2xl bg-mint dark:bg-brand-deep/40 dark:text-brand-light border border-brand-light dark:border-brand-deep flex items-center gap-2 shadow-sm" role="alert">
            <i class="fas fa-check-circle text-base text-brand-deep"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- User Header Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col sm:flex-row items-center gap-5">
        <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-brand/20 shadow-lg flex-shrink-0">
            <img src="{{ asset('/assets/icon/admin.png') }}" alt="Admin Avatar" class="object-cover w-full h-full" />
        </div>
        <div class="space-y-1 text-center sm:text-left">
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">
                {{ Auth::user()->name ?? 'Admin' }}
            </h2>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 text-xs">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-brand-light text-brand-deep dark:bg-brand-deep dark:text-brand-light">
                    <i class="fas fa-user-shield mr-1"></i> Role: Administrator
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <i class="fas fa-map-marker-alt mr-1"></i> Cabang: {{ \App\Support\RegionContext::name() ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Forms Grid -->
    <div class="space-y-6">
        <!-- Profile Information -->
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-id-card text-brand"></i>
                        <span>Informasi Profil Akun</span>
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Perbarui nama pengguna dan alamat email utama Anda.</p>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    @include('dashboard.admin.profile.update-profile-form')
                </div>
            </div>
        @endif

        <!-- Update Password -->
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-lock text-brand-deep"></i>
                        <span>Perbarui Kata Sandi</span>
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Gunakan kata sandi yang kuat dan unik demi keamanan akses admin.</p>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    @include('dashboard.admin.profile.update-password-form')
                </div>
            </div>
        @endif

        <!-- Logout Other Browser Sessions -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-desktop text-brand"></i>
                    <span>Sesi Perangkat Terhubung</span>
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Kelola dan keluar dari sesi login perangkat lain jika diperlukan.</p>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

        <!-- Delete Account -->
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <div class="p-6 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 rounded-3xl space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-rose-700 dark:text-rose-400 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Hapus Akun Pengguna</span>
                    </h3>
                    <p class="text-xs text-rose-600/80 dark:text-rose-400/80 mt-1">Tindakan ini permanen dan akan menghapus seluruh data akun Anda.</p>
                </div>
                <div class="pt-2 border-t border-rose-200/60 dark:border-rose-900/40">
                    @livewire('profile.delete-user-form')
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
