<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPrivileges
{
    public function handle(Request $request, Closure $next, $privilege)
    {
        if (Auth::check() && Auth::user()->privilegios >= $privilege) {
            return $next($request);
        }

        return redirect('/')->with('error', 'No tienes los privilegios necesarios para acceder a esta página.');
    }
}
