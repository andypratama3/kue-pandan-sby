<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Normalisasi status retur:
 *
 * Versi lama `PesananController` (sudah tidak dipakai / tidak di-route) pernah
 * membuat record `order_returns` dengan status `menunggu_verifikasi_admin` yang
 * TIDAK dikenali oleh alur retur baru (ReturnController) - alur baru memakai
 * status `menunggu_konfirmasi`. Admin `verify()` hanya memproses retur dengan
 * status `menunggu_konfirmasi`, sehingga data legacy tersebut akan menggantung.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_returns')
            ->where('status', 'menunggu_verifikasi_admin')
            ->update(['status' => 'menunggu_konfirmasi']);
    }

    public function down(): void
    {
        DB::table('order_returns')
            ->where('status', 'menunggu_konfirmasi')
            ->update(['status' => 'menunggu_verifikasi_admin']);
    }
};
