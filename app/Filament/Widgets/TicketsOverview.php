<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketsOverview extends BaseWidget
{
    /**
     * Controla si el widget puede ser visto por el usuario actual
     */
    public static function canView(): bool
    {
        // Todos los usuarios pueden ver este widget, pero con datos diferentes
        return true;
    }
    
    protected function getStats(): array
    {
        // Obtener el usuario actual
        $user = auth()->user();
        
        // Query base que cambiará según el rol
        $ticketsQuery = Ticket::query();
        
        // Si el usuario es cliente, filtrar solo por sus tickets
        if ($user && !$user->isAdmin()) {
            $ticketsQuery->where('user_id', $user->id);
        }
        
        // Para clientes cambiamos el título para reflejar que son sus tickets
        $ticketLabel = $user && !$user->isAdmin() ? 'Mis Tickets ' : 'Tickets ';
        
        return [
            Stat::make($ticketLabel . 'Abiertos', $ticketsQuery->clone()->where('status', 'open')->count())
                ->description($user && !$user->isAdmin() ? 'En espera de atención' : 'Necesitan asignación')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('warning'),
            
            Stat::make($ticketLabel . 'En Progreso', $ticketsQuery->clone()->where('status', 'in_progress')->count())
                ->description('En proceso de resolución')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),
            
            Stat::make($ticketLabel . 'Cerrados', $ticketsQuery->clone()->where('status', 'closed')->count())
                ->description('Últimos 30 días')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart(
                    $ticketsQuery->where('status', 'closed')
                        ->where('closed_at', '>=', now()->subDays(30))
                        ->get()
                        ->groupBy(fn ($ticket) => $ticket->closed_at->format('Y-m-d'))
                        ->map(fn ($tickets) => $tickets->count())
                        ->values()
                        ->toArray()
                ),
        ];
    }
}
