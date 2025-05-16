<?php

namespace App\Filament\Widgets\Reports;

use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopAgentsWidget extends ChartWidget
{
    protected static ?string $heading = 'Rendimiento de Agentes';
    
    protected static ?int $sort = 20;
    
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
    
    protected function getData(): array
    {
        // En una implementación real, estos datos vendrían de la base de datos
        // Aquí generamos datos de muestra para la visualización
        $adminUsers = User::where('role', 'admin')->get();
        
        $labels = $adminUsers->pluck('name')->toArray();
        if (empty($labels)) {
            $labels = ['Admin 1', 'Admin 2', 'Admin 3', 'Admin 4']; // Fallback para demostración
        }
        
        // Datos de muestra para tickets resueltos
        $resolvedTickets = array_map(function() {
            return rand(15, 40);
        }, array_fill(0, count($labels), 0));
        
        // Datos de muestra para tiempo promedio de resolución (horas)
        $avgResolutionTime = array_map(function() {
            return round(rand(40, 120) / 10, 1);
        }, array_fill(0, count($labels), 0));
        
        return [
            'datasets' => [
                [
                    'label' => 'Tickets Resueltos',
                    'data' => $resolvedTickets,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)', // Blue
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Tiempo Promedio (horas)',
                    'data' => $avgResolutionTime,
                    'type' => 'line',
                    'backgroundColor' => 'rgba(224, 36, 36, 0.7)', // Red
                    'borderColor' => 'rgb(224, 36, 36)',
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Tickets Resueltos',
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Tiempo Promedio (horas)',
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
