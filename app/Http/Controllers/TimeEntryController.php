<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TimeEntryController extends Controller
{
    /**
     * Store a newly created time entry.
     */
    public function store(Request $request, Ticket $ticket)
    {
        // Verificar permisos - solo staff puede registrar tiempo
        if (! Gate::allows('track_time', $ticket)) {
            abort(403, 'No tiene permiso para registrar tiempo en este ticket');
        }
        
        $validated = $request->validate([
            'description' => 'nullable|string',
            'minutes' => 'required_without:ended_at|integer|min:1',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after:started_at',
            'is_billable' => 'boolean'
        ]);
        
        // Si se proporciona started_at y ended_at, calcular minutos
        if (!empty($validated['started_at']) && !empty($validated['ended_at'])) {
            $start = new \DateTime($validated['started_at']);
            $end = new \DateTime($validated['ended_at']);
            $validated['minutes'] = $start->diff($end)->i + ($start->diff($end)->h * 60);
        }
        
        // Crear entrada de tiempo
        $timeEntry = $ticket->timeEntries()->create([
            'user_id' => auth()->id(),
            'description' => $validated['description'] ?? null,
            'minutes' => $validated['minutes'],
            'started_at' => $validated['started_at'] ?? null,
            'ended_at' => $validated['ended_at'] ?? null,
            'is_billable' => $validated['is_billable'] ?? true
        ]);
        
        return redirect()->back()
            ->with('success', 'Tiempo registrado correctamente.');
    }
    
    /**
     * Remove the specified time entry.
     */
    public function destroy(TimeEntry $timeEntry)
    {
        // Verificar permisos - solo el creador o administradores pueden eliminar
        if (! Gate::allows('delete', $timeEntry)) {
            abort(403, 'No tiene permiso para eliminar este registro de tiempo');
        }
        
        $timeEntry->delete();
        
        return redirect()->back()
            ->with('success', 'Registro de tiempo eliminado.');
    }
}
