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
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            // Redirect based on user's actual role
            if ($user->role === 'pengelola') {
                return redirect('/pengelola/dashboard');
            } elseif ($user->role === 'tenant') {
                return redirect('/tenant/dashboard');
            } elseif ($user->role === 'pelanggan') {
                return redirect('/pelanggan/dashboard');
            }
            
            // Fallback
            return abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
