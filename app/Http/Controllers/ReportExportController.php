<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    /**
     * Verifica que el usuario sea super_admin
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function checkSuperAdminAccess()
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Solo Super Administradores pueden descargar informes.');
        }
    }
    
    /**
     * Exporta el informe en formato PDF
     */
    public function exportPdf()
    {
        // Verificar que sea super admin
        $this->checkSuperAdminAccess();
        // Obtener datos para el informe
        $data = $this->getReportData();
        
        // Generar PDF
        $pdf = PDF::loadView('exports.report-pdf', $data);
        
        // Descargar el PDF
        return $pdf->download('informe-tickets-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Exporta el informe en formato Excel
     */
    public function exportExcel()
    {
        // Verificar que sea super admin
        $this->checkSuperAdminAccess();
        return Excel::download(new TicketsExport, 'informe-tickets-' . now()->format('Y-m-d') . '.xlsx');
    }
    
    /**
     * Exporta el informe en formato CSV
     */
    public function exportCsv()
    {
        // Verificar que sea super admin
        $this->checkSuperAdminAccess();
        return Excel::download(new TicketsExport, 'informe-tickets-' . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    /**
     * Obtiene los datos para el informe
     */
    private function getReportData()
    {
        // Tickets por estado
        $ticketsByStatus = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
            
        // Tickets por prioridad
        $ticketsByPriority = Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority')
            ->toArray();
            
        // Tiempo promedio de resolución (en horas) - Compatible con SQLite
        $ticketsWithClosedAt = Ticket::whereNotNull('closed_at')->get();
        $totalHours = 0;
        $ticketCount = count($ticketsWithClosedAt);
        
        if ($ticketCount > 0) {
            foreach ($ticketsWithClosedAt as $ticket) {
                $created = new \DateTime($ticket->created_at);
                $closed = new \DateTime($ticket->closed_at);
                $interval = $created->diff($closed);
                $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
                $totalHours += $hours;
            }
            $avgResolutionTime = $totalHours / $ticketCount;
        } else {
            $avgResolutionTime = 0;
        }
            
        // Tickets cerrados por usuario - Compatible con SQLite
        $ticketsByUser = Ticket::select('users.name', DB::raw('count(*) as total'))
            ->join('users', 'tickets.closed_by', '=', 'users.id')
            ->whereNotNull('tickets.closed_by')
            ->groupBy('users.name')
            ->get()
            ->toArray();
            
        // Tickets creados por mes (últimos 6 meses) - Compatible con SQLite
        $ticketsByMonth = [];
        $sixMonthsAgo = now()->subMonths(6);
            
        // Obtenemos los tickets de los últimos 6 meses
        $tickets = Ticket::where('created_at', '>=', $sixMonthsAgo)->get();
            
        // Agrupamos manualmente por mes
        foreach ($tickets as $ticket) {
            $month = date('Y-m', strtotime($ticket->created_at));
            if (!isset($ticketsByMonth[$month])) {
                $ticketsByMonth[$month] = 0;
            }
            $ticketsByMonth[$month]++;
        }
            
        // Ordenamos por mes
        ksort($ticketsByMonth);
            
        // Formatear datos para la plantilla PDF
        $formattedTicketsByMonth = [];
        foreach ($ticketsByMonth as $month => $total) {
            $dateObj = \DateTime::createFromFormat('Y-m', $month);
            $monthName = $dateObj->format('F Y');
            $formattedTicketsByMonth[] = [
                'month' => $monthName,
                'total' => $total
            ];
        }
        
        return [
            'title' => 'Informe de Tickets - ' . now()->format('d/m/Y'),
            'ticketsByStatus' => $ticketsByStatus,
            'ticketsByPriority' => $ticketsByPriority,
            'avgResolutionTime' => round($avgResolutionTime, 2),
            'ticketsByUser' => $ticketsByUser,
            'ticketsByMonth' => $formattedTicketsByMonth,
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
            'closedTickets' => Ticket::where('status', 'closed')->count(),
            'generateDate' => now()->format('d/m/Y H:i:s')
        ];
    }
}
