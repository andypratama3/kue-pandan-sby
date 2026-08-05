<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        // Check if user has a valid region
        if (!$user->region || !$user->region->name) {
            // If no region, redirect to a safe fallback
            $redirectUrl = config('fortify.home');
        } else {
            $regionSlug = strtolower($user->region->name);
            
            if ($user->hasRole('admin')) {
                $redirectUrl = route('admin.dashboard', ['region' => $regionSlug]);
            } elseif ($user->hasRole('kurir')) {
                $redirectUrl = route('kurir.dashboard', ['region' => $regionSlug]);
            } else {
                // Fallback ke dashboard default jika role tidak dikenali
                $redirectUrl = config('fortify.home');
            }
        }

        return $request->wantsJson()
                    ? new JsonResponse(['two_factor' => false])
                    : redirect()->intended($redirectUrl);
    }
}
