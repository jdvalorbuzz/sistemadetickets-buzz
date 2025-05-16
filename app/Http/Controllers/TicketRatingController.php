<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class TicketRatingController extends Controller
{
    /**
     * Mostrar el formulario para calificar un ticket
     */
    public function show($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        
        // Verificar que el usuario sea el propietario del ticket
        if (Auth::id() !== $ticket->user_id) {
            abort(403, 'No estás autorizado para calificar este ticket');
        }
        
        // Verificar que el ticket esté cerrado
        if ($ticket->status !== 'closed') {
            abort(400, 'Solo puedes calificar tickets cerrados');
        }
        
        // Obtener calificación existente si hay alguna
        $rating = TicketRating::where('ticket_id', $ticketId)
            ->where('user_id', Auth::id())
            ->first();
        
        return view('tickets.rate', compact('ticket', 'rating'));
    }
    
    /**
     * Guardar o actualizar la calificación de un ticket
     */
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        $ticket = Ticket::findOrFail($ticketId);
        
        // Verificar que el usuario sea el propietario del ticket
        if (Auth::id() !== $ticket->user_id) {
            return redirect()->back()->with('error', 'No estás autorizado para calificar este ticket');
        }
        
        // Verificar que el ticket esté cerrado
        if ($ticket->status !== 'closed') {
            return redirect()->back()->with('error', 'Solo puedes calificar tickets cerrados');
        }
        
        // Crear o actualizar la calificación
        TicketRating::updateOrCreate(
            [
                'ticket_id' => $ticketId,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $request->rating,
                'feedback' => $request->feedback,
            ]
        );
        
        return redirect()->route('filament.admin.resources.tickets.index')
            ->with('success', '¡Gracias por tu calificación!');
    }
}
