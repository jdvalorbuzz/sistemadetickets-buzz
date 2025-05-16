<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TicketRating;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketRatingsOverview extends Component
{
    public $period = 'month'; // Por defecto, mostrar datos del último mes
    public $chartData = [];
    
    public function mount()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        // Determinar la fecha de inicio según el período seleccionado
        $startDate = match($this->period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            'all' => now()->subYears(10), // Asumimos que 10 años es suficiente para "todos"
            default => now()->subMonth(),
        };
        
        // Obtener el promedio de calificaciones por día
        $ratingsData = TicketRating::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(rating) as average_rating'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Calificaciones por estrellas
        $ratingCounts = TicketRating::select(
                'rating',
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
        
        // Asegurar que tenemos valores para todas las estrellas (1-5)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = $ratingCounts[$i] ?? 0;
        }
        
        // Estadísticas generales
        $stats = [
            'total_ratings' => TicketRating::where('created_at', '>=', $startDate)->count(),
            'average_rating' => TicketRating::where('created_at', '>=', $startDate)->avg('rating') ?: 0,
            'rated_tickets_percent' => $this->calculateRatedTicketsPercent($startDate),
            'rating_distribution' => $ratingDistribution,
        ];
        
        // Preparar datos para el gráfico
        $labels = $ratingsData->pluck('date')->toArray();
        $averages = $ratingsData->pluck('average_rating')->toArray();
        $counts = $ratingsData->pluck('count')->toArray();
        
        $this->chartData = [
            'labels' => $labels,
            'averages' => $averages,
            'counts' => $counts,
            'stats' => $stats,
        ];
    }
    
    public function setPeriod($period)
    {
        $this->period = $period;
        $this->loadData();
    }
    
    /**
     * Calcular el porcentaje de tickets cerrados que han sido calificados
     */
    protected function calculateRatedTicketsPercent($startDate)
    {
        $closedTickets = Ticket::where('status', 'closed')
            ->where('closed_at', '>=', $startDate)
            ->count();
            
        if ($closedTickets === 0) {
            return 0;
        }
        
        $ratedTickets = Ticket::where('status', 'closed')
            ->where('closed_at', '>=', $startDate)
            ->whereHas('ratings')
            ->count();
            
        return round(($ratedTickets / $closedTickets) * 100, 1);
    }
    
    public function render()
    {
        return view('livewire.ticket-ratings-overview');
    }
}
