<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials.sidenav', function ($view) {
            if (!Auth::check()) {
                return; // Keluar jika pengguna tidak login
            }

            $user = Auth::user();
            $newOrdersCount = 0;
            $rejectedOrdersCount = 0;

            // Logika untuk Admin
            if ($user->hasRole('admin') && $user->region_id) {
                $newOrdersCount = Order::where('region_id', $user->region_id)
                    ->where('status', 'baru')->count();
            }

            // Logika untuk Kurir
            if ($user->hasRole('kurir')) {
                // PERBAIKAN: Menghitung pesanan yang ditolak selama statusnya belum final.
                // Ini akan mencakup penolakan pada status 'baru', 'diterima_pembeli', dll.
                // selama pesanan tersebut belum selesai atau masuk histori.
                $rejectedOrdersCount = Order::where('created_by_user_id', $user->id)
                    ->whereNotNull('rejection_note')
                    ->whereNotIn('status', ['selesai', 'diverifikasi_admin'])
                    ->count();
            }

            // Kirim kedua variabel ke view sidenav
            $view->with('newOrdersCount', $newOrdersCount)
                ->with('rejectedOrdersCount', $rejectedOrdersCount);
        });
    }
}
