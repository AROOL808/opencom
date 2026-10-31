<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Import ini

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paksa semua pembuatan URL (termasuk callback Socialite) menggunakan HTTPS
        if (request()->hasHeader('x-forwarded-proto') || env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
