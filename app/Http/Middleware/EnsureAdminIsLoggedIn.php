<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cekapakah session admin_logged_in ada
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login')->withErrors([
                'username' => 'Silakan login terlebih dahulu.'
            ]);
        }

        return $next($request);
    }
}