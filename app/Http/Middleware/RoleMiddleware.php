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
    // yang asli
    // public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->hasAnyRole($roles)) {
    //         return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
    //     }

    //     return $next($request);
    // }


// cadangan
    public function handle($request, Closure $next, ...$roles)
    {
        $user =auth()->user();
        $guards = array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // $user = Auth::guard($guard)->user();
                // dd($user->nama);
                if (in_array($user->role, $roles)) {
                    return $next($request);
                }

                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
