<?php

namespace App\Filament\Resources\TagResource\RelationManagers;

use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'closed' => 'Cerrado',
                        'archived' => 'Archivado'
                    ])
                    ->default('open')
                    ->required(),
                
                Forms\Components\Select::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente'
                    ])
                    ->default('medium')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
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
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'closed' => 'Cerrado',
                        'archived' => 'Archivado'
                    ]),
                
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente'
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular Ticket')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.edit', ['record' => $record])),
                Tables\Actions\DetachAction::make()
                    ->label('Desvincular'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
