<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login dan memiliki role 'student'
        if (auth()->check() && auth()->user()->role === 'student') {
            return $next($request);
        }

        // Jika bukan student, kembalikan ke halaman sebelumnya atau beri error 403
        abort(403, 'Akses ditolak. Halaman ini khusus untuk siswa.');
    }
}
