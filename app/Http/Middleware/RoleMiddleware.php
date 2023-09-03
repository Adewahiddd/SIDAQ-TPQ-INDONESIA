<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole($roles)) {
            return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
        }

        return $next($request);
    }

    // public function handle($request, Closure $next, ...$roles)
    // {
    //     $user = $request->user();

    //     if (!$user || !$user->hasAnyRole(...$roles)) {
    //         dd($roles);
    //         return response()->json(['error' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 401);
    //     }

    //     return $next($request);
    // }


}
