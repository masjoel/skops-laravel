<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $userLevel = Auth::user()->role;
        // $userLevel = session('level');

        if (!$userLevel || !in_array($userLevel, $roles)) {
            abort(403, 'Akses ditolak. Level Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
