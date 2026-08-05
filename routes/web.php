<?php

use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PeformaCustomerController;
use App\Http\Controllers\Admin\PeformaKurirController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Chatbot\WebhookController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HistoryOrderController;
use App\Http\Controllers\Kurir\PesananController;
use App\Http\Controllers\KurirDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Halaman Depan (Homepage)
Route::get('/', function (Request $request) {
    $ip = $request->ip();
    $today = Carbon::today();

    $exists = DB::table('visit_logs')
        ->where('ip_address', $ip)
        ->whereDate('created_at', $today)
        ->exists();

    if (!$exists) {
        DB::table('visit_logs')->insert([
            'ip_address' => $ip,
            'created_at' => $today,
            'updated_at' => $today,
        ]);
    }

    // $total = DB::table('visit_logs')->count();
    return view('livewire.homepage');
});

Route::get('privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');


// Rute Logout manual
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


// Grup Rute yang Dilindungi (Memerlukan Login)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    //---------- PENGALIHAN DASHBOARD UTAMA ----------//
    Route::get('dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $regionSlug = optional($user->region)->name ? strtolower($user->region->name) : null;
        if (!$regionSlug) abort(403, 'User tidak memiliki region yang valid.');

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard', ['region' => $regionSlug]);
        } elseif ($user->hasRole('kurir')) {
            return redirect()->route('kurir.dashboard', ['region' => $regionSlug]);
        }
        abort(403, 'User tidak memiliki role yang valid.');
    })->name('dashboard');


    //---------- RUTE ADMIN ----------//
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('peforma-kurir/export/pdf', [PeformaKurirController::class, 'exportPdf'])->name('peforma-kurir.export.pdf');
        Route::get('peforma-kurir/export/{id}/pdf', [PeformaKurirController::class, 'exportPdfByCourier']);
        Route::get('peforma-customer/export/pdf', [PeformaCustomerController::class, 'exportPdf'])->name('peforma-customer.export.pdf');
        Route::get('dashboard/{region}', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Admin
        Route::get('profile', [AdminDashboardController::class, 'profile'])->name('profile');
        Route::put('profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [AdminDashboardController::class, 'updatePassword'])->name('profile.password');

        // Manajemen Produk
        Route::resource('products', ProductController::class);

        // Manajemen Kurir
        Route::resource('couriers', CourierController::class)->parameters(['couriers' => 'courier']);
        Route::put('couriers/{courier}/note', [CourierController::class, 'updateNote'])->name('couriers.updateNote');
        Route::get('couriers/{courier}/performance-data', [CourierController::class, 'performanceData'])->name('couriers.performanceData');

        // Manajemen Customer
        Route::put('customers/{customer}/note', [CustomerController::class, 'updateNote'])->name('customers.updateNote');
        Route::post('customers/{customer}/flag', [CustomerController::class, 'toggleFlag'])->name('customers.toggleFlag');
        Route::resource('customers', CustomerController::class);

        // Route untuk download rekap order customer (PDF)
        Route::get('customers/{customer}/rekap/download', [CustomerController::class, 'downloadRekap'])->name('customers.rekap.download');

        // Manajemen Pesanan untuk Admin
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}/details', [AdminOrderController::class, 'details']);
        Route::post('orders/{id}/verify', [AdminOrderController::class, 'verify']);
        Route::post('orders/{id}/reject', [AdminOrderController::class, 'reject']);
        Route::delete('orders/{id}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        // Manajemen History Pesanan
        Route::get('historys', [HistoryOrderController::class, 'index'])->name('historys.index');
        Route::get('historys/{order}/invoice', [HistoryOrderController::class, 'invoice'])->name('historys.invoice');
        Route::get('historys/{order}/download', [HistoryOrderController::class, 'downloadInvoice'])->name('historys.download');
        // Endpoint JSON untuk detail history pesanan (untuk modal show)
        Route::get('historys/{order}/details', [HistoryOrderController::class, 'details'])->name('admin.historys.details'); // [!code ++]
        Route::get('historys/export-pdf', [HistoryOrderController::class, 'downloadHistoryPdf'])->name('historys.export.pdf');
        Route::delete('historys/{id}', [HistoryOrderController::class, 'destroy'])->name('historys.destroy');

        // Routes untuk Peforma Kurir
        Route::get('peforma-kurir', [PeformaKurirController::class, 'index'])->name('peforma-kurir.index');
        Route::get('peforma-kurir/{kurir}', [PeformaKurirController::class, 'show'])->name('peforma-kurir.show');

        // Routes untuk Peforma Customer
        Route::get('peforma-customer', [PeformaCustomerController::class, 'index'])->name('peforma-customer.index');
        Route::get('peforma-customer/{customer}', [PeformaCustomerController::class, 'show'])->name('peforma-customer.show');
    });

    //---------- RUTE KURIR ----------//
    Route::prefix('kurir')->name('kurir.')->middleware('role:kurir')->group(function () {
        Route::get('dashboard/{region}', [KurirDashboardController::class, 'index'])->name('dashboard');

        // Profil Kurir
        Route::get('profile', [KurirDashboardController::class, 'profile'])->name('profile');
        Route::put('profile', [KurirDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [KurirDashboardController::class, 'updatePassword'])->name('profile.password');

        // Daftar Produk (hanya lihat)
        Route::get('products', [ProductController::class, 'index'])->name('products.index');

        // Manajemen Customer
        Route::resource('customers', CustomerController::class)->parameters(['customers' => 'customer']);
        Route::put('customers/{customer}/note', [CustomerController::class, 'updateNote'])->name('customers.updateNote');

        // -- MANAJEMEN PESANAN (MENGGUNAKAN GROUP PREFIX) --
        Route::prefix('pesanan')->name('pesanan.')->group(function () {
            Route::get('/', [PesananController::class, 'index'])->name('index'); //ini sebelumnya showFilteredOrders terus tak ganti index
            Route::get('/create', [PesananController::class, 'create'])->name('create');
            Route::get('/{id}/details', [PesananController::class, 'getOrderDetails'])->name('details');
            Route::post('/{id}/update-status', [PesananController::class, 'updateOrderStatus'])->name('updateStatus');
            Route::post('/{id}/upload-proof', [PesananController::class, 'uploadPaymentProof'])->name('uploadProof');

            // Rute untuk retur, sesuai controller-nya
            Route::post('/{order}/request-return', [ReturnController::class, 'requestReturn'])->name('requestReturn');
            Route::post('/{order}/upload-return-proof', [ReturnController::class, 'uploadReturnProof'])->name('uploadReturnProof');
            
            Route::post('/{order}/request-return/edit', [ReturnController::class, 'editReturn'])->name('requestReturn');
            Route::get('/{order}/request-return/edit', function(){
                return view("dashboard.kurir.pesanan.edit");
            });
        });

        // Rute di luar grup 'pesanan'
        Route::post('/orders/checkout', [PesananController::class, 'checkout'])->name('orders.checkout');
        Route::get('/customer/{id}/last-order', [PesananController::class, 'getLastOrder'])->name('customer.lastOrder');

        // History Pesanan
        Route::get('historys', [HistoryOrderController::class, 'index'])->name('historys.index');
        Route::get('historys/{order}/details', [HistoryOrderController::class, 'details'])->name('historys.details');

        // Endpoint JSON untuk data produk di halaman pesanan
        Route::get('produk/json', function () {
            $regionId = Auth::user()->region_id;
            return Product::where('is_active', true)->where('region_id', $regionId)
                ->with(['variants' => fn($q) => $q->where('is_active', true)->select('id', 'product_id', 'name', 'price')])
                ->get(['id', 'name', 'image_path'])
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    // PERBAIKAN: Gunakan Storage::url() untuk menghasilkan URL yang benar
                    'image' => $p->image_path ? Storage::url($p->image_path) : null,
                    'variants' => $p->variants->map(fn($v) => ['id' => $v->id, 'name' => $v->name, 'price' => $v->price]),
                ]);
        })->name('produk.json');
    });
});
