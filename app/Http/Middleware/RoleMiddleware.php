<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user udah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 2. Apakah role user sesuai dengan yang diminta di route
        if (auth()->user()->role !== $role) {
            // Jika tidak sesuai, lempar error 403 (Forbidden)
            abort(403,'You do not have the permission to access this resource.');
        }
        return $next($request);
    }
}
