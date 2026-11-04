<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliMuridMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'wali_murid') {
            abort(403, 'Anda tidak memiliki akses ke halaman Wali Murid.');
        }

        if (! auth()->user()->anak) {
            abort(403, 'Akun Wali Murid belum terhubung dengan data siswa.');
        }

        return $next($request);
    }
}
