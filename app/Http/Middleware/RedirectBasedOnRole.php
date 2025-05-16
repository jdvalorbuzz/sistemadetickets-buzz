<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y está accediendo a la ruta principal
        if (Auth::check() && $request->path() === '/') {
            $role = Auth::user()->role;
            
            // Redirigir según el rol
            switch ($role) {
                case 'super_admin':
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'support':
                    return redirect()->route('support.dashboard');
                case 'client':
                    return redirect()->route('client.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }
        
        return $next($request);
    }
}
