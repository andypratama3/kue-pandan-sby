<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\RegionContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProofController extends Controller
{
    /**
     * Sajikan bukti pembayaran/retur (file privat) hanya untuk user yang berhak:
     * - kurir pembuat pesanan
     * - admin/owner pada region pesanan
     */
    public function show(string $type, Order $order)
    {
        $user = Auth::user();

        if ($user->hasRole('kurir') && $order->created_by_user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }

        if (($user->hasRole('admin') || $user->hasRole('owner')) && $order->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        $path = null;

        if ($type === 'return') {
            $return = $order->returns()
                ->where('status', '!=', 'ditolak')
                ->latest()
                ->first();
            $path = $return?->return_proof;
        } else {
            $path = $order->payment_proof;
        }

        if (! $path) {
            abort(404, 'Bukti tidak ditemukan.');
        }

        $path = preg_replace('#^(storage/|public/)#', '', $path);

        // File baru ada di disk privat; file lama (legacy) di disk publik.
        if (Storage::exists($path)) {
            return response()->file(Storage::path($path));
        }

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        abort(404, 'Bukti tidak ditemukan.');
    }
}
