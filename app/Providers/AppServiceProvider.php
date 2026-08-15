<?php

namespace App\Providers;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Order;
use App\Services\WhatsApp\MetaCloudProvider;
use App\Support\RegionContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WhatsAppProviderInterface::class, fn () => app(MetaCloudProvider::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials.sidenav', function ($view) {
            if (! Auth::check()) {
                return; // Keluar jika pengguna tidak login
            }

            $user = Auth::user();
            $newOrdersCount = 0;
            $rejectedOrdersCount = 0;

            // Logika untuk Admin (badge pesanan baru per cabang aktif)
            if (($user->hasRole('admin') || $user->hasRole('owner')) && RegionContext::regionId()) {
                $newOrdersCount = Order::where('region_id', RegionContext::regionId())
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
