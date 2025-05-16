<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsOverviewChart extends ChartWidget
{
    protected static ?string $heading = 'Resumen de Tickets';
    
    protected static ?int $sort = 1;
    
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
    
    protected function getData(): array
    {
        $data = [
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'archived' => Ticket::where('status', 'archived')->count(),
        ];
        
        return [
            'datasets' => [
                [
                    'label' => 'Tickets por estado',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.7)', // warning - open
                        'rgba(59, 130, 246, 0.7)', // info - in_progress
                        'rgba(16, 185, 129, 0.7)', // success - closed
                        'rgba(156, 163, 175, 0.7)', // gray - archived
                    ],
                ],
            ],
            'labels' => [
                'Abiertos', 
                'En Progreso', 
                'Cerrados', 
                'Archivados'
            ],
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}
