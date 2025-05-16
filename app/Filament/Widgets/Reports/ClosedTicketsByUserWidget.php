<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ClosedTicketsByUserWidget extends ChartWidget
{
    protected static ?string $heading = 'Tickets cerrados por administrador';
    
    protected static ?int $sort = 3;
    
    protected function getData(): array
    {
        $closedTicketsData = Ticket::select('users.name', DB::raw('count(*) as total'))
            ->join('users', 'tickets.closed_by', '=', 'users.id')
            ->whereIn('tickets.status', ['closed', 'archived'])
            ->whereNotNull('tickets.closed_by')
            ->groupBy('users.name')
            ->get();
            
        return [
            'datasets' => [
                [
                    'label' => 'Tickets cerrados',
                    'data' => $closedTicketsData->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB', '#FF6384', '#4BC0C0', '#FF9F40', '#9966FF', 
                        '#FFCD56', '#C9CBCF', '#7C4DFF', '#18FFFF', '#FF8A80'
                    ],
                ],
            ],
            'labels' => $closedTicketsData->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
    
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}
