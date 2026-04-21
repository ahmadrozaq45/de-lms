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
        // 1. Cek apakah user sudah login
        if (!auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        // 2. Cek apakah role user sesuai
        if (auth()->user()->role !== $role) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Forbidden: You do not have ' . $role . ' access.'], 403)
                : abort(403, 'You do not have the permission to access this resource.');
        }
        return $next($request);
    }
}
