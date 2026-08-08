<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Nilai diambil dari env TRUSTED_PROXIES (lihat ::proxies()).
     * Default '' = tidak ada proxy yang dipercaya (header X-Forwarded-* dari
     * client tidak akan dipercaya). Jika aplikasi berada di balik HTTPS
     * terminator / load balancer / CDN, set TRUSTED_PROXIES di .env:
     *   - TRUSTED_PROXIES=*   — percayai proxy mana pun (paling umum).
     *   - TRUSTED_PROXIES=203.0.113.10,198.51.100.7 — daftar IP/CIDR (lebih ketat).
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Get the trusted proxies.
     *
     * @return array<int, string>|string|null
     */
    protected function proxies()
    {
        $fromEnv = env('TRUSTED_PROXIES');

        if (is_string($fromEnv) && $fromEnv !== '') {
            return $fromEnv === '*'
                ? '*'
                : array_map('trim', explode(',', $fromEnv));
        }

        return $this->proxies;
    }
}
