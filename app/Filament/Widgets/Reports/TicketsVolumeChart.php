<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsVolumeChart extends ChartWidget
{
    protected static ?string $heading = 'Volumen de Tickets a lo Largo del Tiempo';
    
    protected static ?int $sort = 10;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = '30';
    
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        
        $dates = collect(range($days - 1, 0))->map(function ($daysAgo) {
            return Carbon::now()->subDays($daysAgo)->format('Y-m-d');
        });
        
        // En una implementación real, estos datos vendrían de la base de datos
        // Aquí generamos datos de muestra para la visualización
        $createdTickets = $this->generateSampleData($dates, 3, 8);
        $resolvedTickets = $this->generateSampleData($dates, 2, 7);
        
        return [
            'datasets' => [
                [
                    'label' => 'Tickets Creados',
                    'data' => $createdTickets,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)', // Blue
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Tickets Resueltos',
                    'data' => $resolvedTickets,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)', // Green
                    'borderColor' => 'rgb(16, 185, 129)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            })->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getFilters(): ?array
    {
        return [
            7 => 'Últimos 7 días',
            30 => 'Últimos 30 días',
            60 => 'Últimos 60 días',
            90 => 'Últimos 90 días',
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'borderWidth' => 2,
                ],
                'point' => [
                    'radius' => 2,
                    'hoverRadius' => 4,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'intersect' => false,
                    'mode' => 'index',
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
    
    private function generateSampleData($dates, $min, $max)
    {
        return $dates->map(function ($date) use ($min, $max) {
            return rand($min, $max);
        })->toArray();
    }
}
