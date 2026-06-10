<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_active == false) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh admin'
                ]);
        }

        return $next($request);
    }
}
