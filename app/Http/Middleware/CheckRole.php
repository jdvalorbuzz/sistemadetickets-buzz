<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        
        // Si el usuario tiene alguno de los roles especificados
        foreach ($roles as $role) {
            if ($userRole === $role || 
                ($role === 'staff' && in_array($userRole, ['admin', 'super_admin', 'support']))) {
                return $next($request);
            }
        }

        // Si el usuario no tiene ninguno de los roles permitidos
        abort(403, 'No tienes permiso para acceder a esta página');
    }
}
