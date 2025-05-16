<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ClientTicketsTable extends TableWidget
{
    protected static ?int $sort = 20;
    
    protected int | string | array $columnSpan = 'full';
    
    /**
     * Solo visible para clientes
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }
    
    protected function getTableHeading(): string
    {
        return 'Mis Tickets Recientes';
    }
    
    protected function getTableDescription(): string
    {
        return 'Historial de sus tickets ordenados por fecha de actualización';
    }
    
    protected function getTableQuery(): Builder
    {
        return Ticket::query()
            ->where('user_id', auth()->id())
            ->latest('updated_at');
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable(),
                
            Tables\Columns\TextColumn::make('title')
                ->label('Título')
                ->searchable()
                ->limit(50),
                
            Tables\Columns\TextColumn::make('status')
                ->label('Estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'open' => 'warning',
                    'in_progress' => 'info',
                    'closed' => 'success',
                    'archived' => 'gray',
                    default => 'warning',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'open' => 'Abierto',
                    'in_progress' => 'En Progreso',
                    'closed' => 'Cerrado',
                    'archived' => 'Archivado',
                    default => $state,
                }),
                
            Tables\Columns\TextColumn::make('priority')
                ->label('Prioridad')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'low' => 'gray',
                    'medium' => 'info',
                    'high' => 'danger',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'low' => 'Normal',
                    'medium' => 'Media',
                    'high' => 'Urgente',
                    default => $state,
                }),
                
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Actualizado')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Ver')
                ->icon('heroicon-o-eye')
                ->url(fn (Ticket $record): string => TicketResource::getUrl('edit', ['record' => $record])),
        ];
    }
    
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'updated_at';
    }
    
    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
