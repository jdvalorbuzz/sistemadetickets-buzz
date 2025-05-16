<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportDashboardController extends Controller
{
    /**
     * Mostrar el dashboard para los agentes de soporte.
     */
    public function index(Request $request)
    {
        // Obtener estadísticas para el dashboard
        $userId = Auth::id();
        
        $openTicketsCount = Ticket::where('status', 'open')->count();
        $inProgressTicketsCount = Ticket::where('status', 'in_progress')->count();
        $closedTicketsCount = Ticket::where('status', 'closed')
            ->where('closed_by', $userId)
            ->count();
        
        // Filtros
        $status = $request->query('status', 'all');
        $departmentId = $request->query('department_id');
        $priority = $request->query('priority');
        $search = $request->query('search');
        
        // Query base
        $ticketsQuery = Ticket::with(['user', 'department', 'assignedTo']);
        
        // Aplicar filtros
        if ($status !== 'all') {
            $ticketsQuery->where('status', $status);
        }
        
        if ($departmentId) {
            $ticketsQuery->where('department_id', $departmentId);
        }
        
        if ($priority) {
            $ticketsQuery->where('priority', $priority);
        }
        
        if ($search) {
            $ticketsQuery->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        
        // Ver solo tickets asignados a mí (opcional)
        if ($request->has('mine')) {
            $ticketsQuery->where('assigned_to', $userId);
        }
        
        // Ver mis tickets cerrados si se solicita
        if ($request->has('my_closed')) {
            $ticketsQuery->where('status', 'closed')
                ->where('closed_by', $userId);
        }
        
        // Ordenar
        $ticketsQuery->orderBy('created_at', 'desc');
        
        // Paginar resultados
        $tickets = $ticketsQuery->paginate(15)->appends($request->query());
        
        // Obtener departamentos para filtro
        $departments = Department::where('is_active', true)->get();
        
        return view('support.dashboard', compact(
            'tickets', 'departments', 'status', 'departmentId', 'priority',
            'openTicketsCount', 'inProgressTicketsCount', 'closedTicketsCount'
        ));
    }
}
