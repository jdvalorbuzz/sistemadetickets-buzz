<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ResolutionTimeWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
    
    protected function getStats(): array
    {
        // Calcular promedio de tiempo entre creación y cierre en horas
        $avgLowPriority = $this->getResolutionTimeByPriority('low');
        $avgMediumPriority = $this->getResolutionTimeByPriority('medium');
        $avgHighPriority = $this->getResolutionTimeByPriority('high');
        
        return [
            Stat::make('Tiempo Medio - Baja', number_format($avgLowPriority, 1) . ' horas')
                ->description('Prioridad baja')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),
                
            Stat::make('Tiempo Medio - Media', number_format($avgMediumPriority, 1) . ' horas')
                ->description('Prioridad media')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
                
            Stat::make('Tiempo Medio - Alta/Urgente', number_format($avgHighPriority, 1) . ' horas')
                ->description('Prioridad alta y urgente')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }

    private function getResolutionTimeByPriority(string $priority): float
    {
        // En una implementación real, este código calcularía el promedio real basado en los datos
        // Aquí proporcionamos valores de muestra para demostración
        $sampleData = [
            'low' => 24.5,
            'medium' => 12.3,
            'high' => 4.8,
        ];
        
        return $sampleData[$priority] ?? 0;
        
        /* Código real para producción:
        return Ticket::where('priority', $priority)
            ->whereNotNull('closed_at')
            ->get()
            ->avg(function ($ticket) {
                $created = new Carbon($ticket->created_at);
                $closed = new Carbon($ticket->closed_at);
                return $created->diffInHours($closed);
            }) ?? 0;
        */
    }
}
