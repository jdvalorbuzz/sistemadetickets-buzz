<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscalationLogResource\Pages;
use App\Models\EscalationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EscalationLogResource extends Resource
{
    protected static ?string $model = EscalationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Logs de Escalamiento';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 4;
    
    /**
     * Determina si el recurso es visible en la navegación
     * Para administradores, super administradores y soporte
     */
    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin', 'support']);
    }
    
    /**
     * Los logs son solo de lectura, no se pueden editar
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_id')
                    ->label('ID del Ticket')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('previous_user_id')
                    ->relationship('previousUser', 'name')
                    ->label('Usuario anterior')
                    ->disabled(),
                Forms\Components\Select::make('escalated_to_user_id')
                    ->relationship('escalatedToUser', 'name')
                    ->label('Escalado a')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('reason')
                    ->label('Motivo')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('escalation_rule_id')
                    ->relationship('escalationRule', 'id')
                    ->label('Regla aplicada')
                    ->disabled(),
                Forms\Components\Textarea::make('created_at')
                    ->label('Fecha de escalamiento')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i:s') : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.title')
                    ->label('Título del Ticket')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.department.name')
                    ->label('Departamento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('previousUser.name')
                    ->label('De Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escalatedToUser.name')
                    ->label('A Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ticket.department_id')
                    ->relationship('ticket.department', 'name')
                    ->label('Departamento'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Fecha de escalamiento'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEscalationLogs::route('/'),
            'view' => Pages\ViewEscalationLog::route('/{record}'),
        ];
    }    
}
