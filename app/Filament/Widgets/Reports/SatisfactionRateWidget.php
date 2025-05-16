<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SatisfactionRateWidget extends BaseWidget
{
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
    protected function getStats(): array
    {
        // En una implementación real, estos datos vendrían de los ratings de los tickets
        $satisfactionRate = 94; // Porcentaje
        $respondedOnTime = 98; // Porcentaje
        $firstResponseTime = 1.2; // Horas

        return [
            Stat::make('Satisfacción del Cliente', $satisfactionRate . '%')
                ->description('Basado en calificaciones')
                ->descriptionIcon('heroicon-m-face-smile')
                ->color('success')
                ->chart([88, 90, 92, 94, 93, 94]),

            Stat::make('Respuestas a Tiempo', $respondedOnTime . '%')
                ->description('Dentro del SLA')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([95, 96, 97, 98, 97, 98]),

            Stat::make('Primera Respuesta', $firstResponseTime . ' horas')
                ->description('Tiempo promedio')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->chart([2.1, 1.8, 1.5, 1.3, 1.2, 1.2]),
        ];
    }
}
