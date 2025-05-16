<?php

namespace App\Filament\Widgets\Reports;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BottlenecksAnalysisWidget extends BaseWidget
{
    protected static ?int $sort = 40;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Identificación de Cuellos de Botella';
    
    /**
     * Controla que solo administradores puedan acceder a este widget
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // En una implementación real, esta sería una consulta que identifica
                // tickets problemáticos basados en tiempo de respuesta, SLA, etc.
                Ticket::query()
                    ->where(function ($query) {
                        $query->where('status', 'open')
                              ->where('created_at', '<=', now()->subDays(7));
                    })
                    ->orWhere(function ($query) {
                        $query->where('status', 'in_progress')
                              ->where('updated_at', '<=', now()->subDays(5));
                    })
                    ->orderBy('created_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'closed' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'closed' => 'Cerrado',
                        'archived' => 'Archivado',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('waiting_time')
                    ->label('Tiempo de Espera')
                    ->getStateUsing(function ($record) {
                        $created = new \Carbon\Carbon($record->created_at);
                        $now = now();
                        $days = $created->diffInDays($now);
                        
                        if ($days > 0) {
                            return $days . ' días';
                        }
                        
                        $hours = $created->diffInHours($now);
                        return $hours . ' horas';
                    })
                    ->color(fn ($record) => 
                        $record->created_at->diffInDays(now()) > 5 ? 'danger' : 'warning'
                    ),
                    
                Tables\Columns\TextColumn::make('issue')
                    ->label('Problema Identificado')
                    ->getStateUsing(function ($record) {
                        $issues = [
                            'Sin asignación',
                            'Falta información',
                            'Esperando respuesta',
                            'Dependencia externa',
                            'Complejidad técnica'
                        ];
                        
                        // Asigna un problema aleatorio para demostración
                        return $issues[array_rand($issues)];
                    }),
            ])
            ->emptyStateHeading('No se encontraron cuellos de botella')
            ->emptyStateDescription('Todos los tickets están siendo atendidos correctamente.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
