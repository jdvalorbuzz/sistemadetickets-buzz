<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Muestra la página de inicio del sistema de tickets.
     * Redirige a los usuarios según su rol.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si el usuario está autenticado, redirigir según su rol
        if (Auth::check()) {
            $user = Auth::user();
            
            // Redirigir según el rol
            switch ($user->role) {
                case 'super_admin':
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'support':
                    return redirect()->route('support.dashboard');
                case 'client':
                    return redirect()->route('client.dashboard', ['user' => $user->id]);
                default:
                    return redirect('/dashboard');
            }
        }
        
        // Si no está autenticado, mostrar la página de bienvenida
        return view('welcome');
    }
}
