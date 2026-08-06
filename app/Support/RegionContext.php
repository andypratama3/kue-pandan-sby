<?php

namespace App\Support;

use App\Models\Region;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Resolver cabang/branch aktif berdasarkan peran (role).
 *
 * - Admin  -> selalu region miliknya sendiri (region_id user). Tidak bisa menyimpang.
 * - Kurir  -> selalu region miliknya sendiri.
 * - Owner  -> bisa berpindah cabang; cabang aktif disimpan di session
 *             (`selected_region_id`). Default: region aktif pertama.
 */
class RegionContext
{
    /**
     * Ambil ID region yang sedang aktif.
     */
    public static function regionId(): ?int
    {
        return self::region()?->id;
    }

    /**
     * Ambil model Region yang sedang aktif.
     */
    public static function region(): ?Region
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if ($user->hasRole('owner')) {
            $selectedId = Session::get('selected_region_id');
            if ($selectedId) {
                $region = Region::where('is_active', true)->find($selectedId);
                if ($region) {
                    return $region;
                }
            }

            return Region::where('is_active', true)->orderBy('id')->first();
        }

        return $user->region;
    }

    /**
     * Slug region aktif (untuk URL dashboard).
     */
    public static function slug(): ?string
    {
        return self::region()?->slug;
    }

    public static function name(): ?string
    {
        return self::region()?->name;
    }

    public static function isOwner(): bool
    {
        return Auth::check() && Auth::user()->hasRole('owner');
    }
}
