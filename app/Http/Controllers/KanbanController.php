<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\KanbanStatus;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KanbanController extends Controller
{
    /**
     * Mostrar el tablero Kanban con tickets agrupados por estado.
     */
    public function index(Request $request)
    {
        // Filtrar por departamento si se proporciona
        $departmentId = $request->input('department_id');
        
        // Base de la consulta
        $query = Ticket::with(['kanbanStatus', 'user', 'department']);
        
        // Filtros
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        // Para clientes, mostrar solo sus tickets
        if (auth()->user()->isClient()) {
            $query->where('user_id', auth()->id());
        }
        
        // Solo tickets activos (no cerrados/archivados)
        $query->whereNotIn('status', ['closed', 'archived']);
        
        // Obtener tickets 
        $tickets = $query->get();
        
        // Obtener estados Kanban
        $kanbanStatuses = KanbanStatus::orderBy('order')
            ->with(['tickets' => function ($query) use ($departmentId) {
                $query->orderBy('kanban_order');
                
                if ($departmentId) {
                    $query->where('department_id', $departmentId);
                }
                
                if (auth()->user()->isClient()) {
                    $query->where('user_id', auth()->id());
                }
                
                $query->whereNotIn('status', ['closed', 'archived']);
            }])
            ->get();
            
        // Departamentos para filtro
        $departments = Department::where('is_active', true)->get();
        
        return view('tickets.kanban', compact('tickets', 'kanbanStatuses', 'departments', 'departmentId'));
    }
    
    /**
     * Actualizar el estado Kanban de un ticket.
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'status_id' => 'required|exists:kanban_statuses,id',
            'order' => 'required|integer|min:0'
        ]);
        
        $ticket = Ticket::findOrFail($validated['ticket_id']);
        
        // Verificar permisos
        if (! Gate::allows('update', $ticket)) {
            return response()->json(['error' => 'No tiene permiso para actualizar este ticket'], 403);
        }
        
        $ticket->update([
            'kanban_status_id' => $validated['status_id'],
            'kanban_order' => $validated['order']
        ]);
        
        // Si el kanban_status tiene una correspondencia con el status del ticket, actualizarlo
        $kanbanStatus = KanbanStatus::find($validated['status_id']);
        
        if ($kanbanStatus->name === 'Cerrado' && $ticket->status !== 'closed') {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id()
            ]);
        } elseif ($kanbanStatus->name === 'En Progreso' && $ticket->status !== 'in_progress') {
            $ticket->update(['status' => 'in_progress']);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Crear un nuevo estado Kanban.
     */
    public function createStatus(Request $request)
    {
        // Verificar permisos
        if (! Gate::allows('manage_kanban')) {
            abort(403, 'No tiene permiso para gestionar los estados Kanban');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'department_id' => 'nullable|exists:departments,id',
            'is_default' => 'boolean'
        ]);
        
        // Obtener el último orden
        $lastOrder = KanbanStatus::max('order') ?? 0;
        
        // Crear nuevo estado
        $status = KanbanStatus::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'order' => $lastOrder + 1,
            'department_id' => $validated['department_id'] ?? null,
            'is_default' => $validated['is_default'] ?? false
        ]);
        
        // Si es default, actualizar otros estados
        if ($validated['is_default'] ?? false) {
            KanbanStatus::where('id', '!=', $status->id)
                ->where('department_id', $validated['department_id'] ?? null)
                ->update(['is_default' => false]);
        }
        
        return redirect()->back()
            ->with('success', 'Estado Kanban creado correctamente.');
    }
    
    /**
     * Actualizar el orden de los estados Kanban.
     */
    public function updateStatusOrder(Request $request)
    {
        // Verificar permisos
        if (! Gate::allows('manage_kanban')) {
            return response()->json(['error' => 'No tiene permiso para gestionar los estados Kanban'], 403);
        }
        
        $validated = $request->validate([
            'statuses' => 'required|array',
            'statuses.*.id' => 'required|exists:kanban_statuses,id',
            'statuses.*.order' => 'required|integer|min:0'
        ]);
        
        foreach ($validated['statuses'] as $status) {
            KanbanStatus::where('id', $status['id'])->update(['order' => $status['order']]);
        }
        
        return response()->json(['success' => true]);
    }
}
