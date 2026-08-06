<?php

namespace App\Http\Responses;

use App\Support\RegionContext;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();
        $redirectUrl = config('fortify.home');

        // Owner: tidak terikat region — arahkan ke admin dashboard cabang aktif
        if ($user && $user->hasRole('owner')) {
            $regionSlug = RegionContext::slug();
            if ($regionSlug) {
                $redirectUrl = route('admin.dashboard', ['region' => $regionSlug]);
            }
        } elseif ($user && $user->region && $user->region->name) {
            $regionSlug = $user->region->slug;

            if ($user->hasRole('admin')) {
                $redirectUrl = route('admin.dashboard', ['region' => $regionSlug]);
            } elseif ($user->hasRole('kurir')) {
                $redirectUrl = route('kurir.dashboard', ['region' => $regionSlug]);
            }
        }

        return $request->wantsJson()
                    ? new JsonResponse(['two_factor' => false])
                    : redirect()->intended($redirectUrl);
    }
}
