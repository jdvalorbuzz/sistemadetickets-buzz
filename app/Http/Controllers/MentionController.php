<?php

namespace App\Http\Controllers;

use App\Models\Mention;
use Illuminate\Http\Request;

class MentionController extends Controller
{
    /**
     * Mostrar la lista de menciones para el usuario autenticado.
     */
    public function index()
    {
        $mentions = Mention::where('user_id', auth()->id())
            ->with(['mentionable.ticket', 'mentionedBy'])
            ->latest()
            ->paginate(15);
            
        return view('mentions.index', compact('mentions'));
    }
    
    /**
     * Marcar una mención como leída.
     */
    public function markAsRead(Mention $mention)
    {
        // Verificar permisos
        $this->authorize('view', $mention);
        
        $mention->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Mención marcada como leída');
    }
    
    /**
     * Marcar todas las menciones del usuario como leídas.
     */
    public function markAllAsRead()
    {
        Mention::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return redirect()->back()->with('success', 'Todas las menciones han sido marcadas como leídas');
    }
}
