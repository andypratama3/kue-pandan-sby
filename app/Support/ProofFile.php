<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Helper untuk bukti pembayaran / retur.
 *
 * Bukti disimpan di disk PRIVAT (default 'local' -> storage/app), bukan
 * disk 'public', agar file tidak bisa diakses siapa pun tanpa autentikasi.
 * Akses lewat route terproteksi: route('proof.show', [type, order]).
 */
class ProofFile
{
    /**
     * Simpan file bukti ke disk privat.
     */
    public static function store($file, string $dir): string
    {
        return $file->store($dir);
    }

    /**
     * Simpan file bukti dengan nama tertentu ke disk privat.
     */
    public static function storeAs($file, string $dir, string $name): string
    {
        return $file->storeAs($dir, $name);
    }

    /**
     * Tulis konten biner langsung ke disk privat.
     */
    public static function put(string $path, mixed $contents): void
    {
        Storage::put($path, $contents);
    }

    /**
     * Hapus bukti dari disk privat maupun legacy disk publik
     * (file lama yang tersimpan sebelum migrasi ini).
     */
    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $path = preg_replace('#^(storage/|public/)#', '', $path);

        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
