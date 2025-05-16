<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsByPriority extends ChartWidget
{
    protected static ?string $heading = 'Tickets por Prioridad';
    
    protected static ?int $sort = 2;
    
    /**
     * Controla si el widget puede ser visto por el usuario actual
     */
    public static function canView(): bool
    {
        // Solo los administradores pueden ver este widget
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        // Obtener el usuario actual
        $user = auth()->user();
        
        // Query base
        $query = Ticket::query();
        
        // Si es un cliente, solo mostrar sus tickets
        if ($user && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $data = $query->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->priority => $item->count];
            });
            
        return [
            'datasets' => [
                [
                    'label' => 'Tickets por prioridad',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        'rgba(168, 170, 173, 0.7)', // low - gray
                        'rgba(101, 163, 217, 0.7)', // medium - blue
                        'rgba(242, 166, 54, 0.7)',  // high - orange
                        'rgba(235, 87, 87, 0.7)',   // urgent - red
                    ],
                    'borderColor' => [
                        'rgb(168, 170, 173)',
                        'rgb(101, 163, 217)',
                        'rgb(242, 166, 54)',
                        'rgb(235, 87, 87)',
                    ],
                ],
            ],
            'labels' => $data->keys()->map(function ($key) {
                return ucfirst($key);
            })->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'pie';
    }
}
