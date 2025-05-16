<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestTickets extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int|string|array $columnSpan = 'full';
    
    /**
     * Controla si el widget puede ser visto por el usuario actual
     */
    public static function canView(): bool
    {
        // Restringir este widget sólo para administradores
        return auth()->check() && auth()->user()->isAdmin();
    }
    
    public function table(Table $table): Table
    {
        // Obtener el usuario actual
        $user = auth()->user();
        
        // Query base que se filtrará según el rol
        $query = Ticket::query()->latest()->limit(5);
        
        // Si es un cliente, solo mostrar sus tickets
        if ($user && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'closed' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    }),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'info',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.edit', ['record' => $record])),
            ]);
    }
}
