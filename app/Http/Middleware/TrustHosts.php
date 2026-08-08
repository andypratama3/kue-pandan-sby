<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * Default: semua subdomain dari APP_URL + localhost agar dev tetap jalan.
     * Untuk production, isi TRUSTED_HOSTS dengan daftar host (dipisah koma),
     * misal: TRUSTED_HOSTS=kuepandanasli.com,www.kuepandanasli.com
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        $configured = env('TRUSTED_HOSTS');

        if (is_string($configured) && $configured !== '') {
            return array_map('trim', explode(',', $configured));
        }

        return [
            $this->allSubdomainsOfApplicationUrl(),
            'localhost',
            '127.0.0.1',
            '[::1]',
        ];
    }
}
