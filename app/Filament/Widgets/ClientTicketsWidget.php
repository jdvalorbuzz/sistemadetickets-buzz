<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ClientTicketsWidget extends BaseWidget
{
    protected static ?int $sort = 10; // Asegurar que aparezca primero
    
    /**
     * Solo visible para clientes
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }
    
    protected function getStats(): array
    {
        $userId = Auth::id();
        
        // Tickets abiertos del cliente
        $openTickets = Ticket::where('user_id', $userId)
            ->where('status', 'open')
            ->count();
            
        // Tickets en progreso del cliente
        $inProgressTickets = Ticket::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->count();
            
        // Tickets cerrados en los últimos 30 días
        $closedTickets = Ticket::where('user_id', $userId)
            ->where('status', 'closed')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
            
        return [
            Stat::make('Mis Tickets Abiertos', $openTickets)
                ->description('En espera de atención')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Mis Tickets En Progreso', $inProgressTickets)
                ->description('En proceso de resolución')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
                
            Stat::make('Mis Tickets Cerrados', $closedTickets)
                ->description('Últimos 30 días')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
