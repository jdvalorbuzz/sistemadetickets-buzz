<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscalationRuleResource\Pages;
use App\Models\EscalationRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EscalationRuleResource extends Resource
{
    protected static ?string $model = EscalationRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    
    protected static ?string $navigationLabel = 'Reglas de Escalamiento';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 3;
    
    /**
     * Determina si el recurso es visible en la navegación
     * Solo para administradores y super administradores
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Departamento')
                    ->required(),
                Forms\Components\Select::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('hours_until_escalation')
                    ->label('Horas hasta escalamiento')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Tiempo de inactividad en horas antes de escalar el ticket'),
                Forms\Components\Select::make('escalate_to_user_id')
                    ->relationship('escalateToUser', 'name', function ($query) {
                        return $query->whereIn('role', ['admin', 'super_admin', 'support']);
                    })
                    ->label('Escalar a usuario')
                    ->required()
                    ->helperText('Usuario al que se asignará el ticket después del escalamiento'),
                Forms\Components\Toggle::make('notify_supervisor')
                    ->label('Notificar a supervisores')
                    ->default(true)
                    ->helperText('Enviar notificación a todos los administradores cuando un ticket se escala'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('Desactiva esta regla para pausar temporalmente el escalamiento automático'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'danger' => 'urgent',
                        'warning' => 'high',
                        'success' => 'medium',
                        'secondary' => 'low',
                    ]),
                Tables\Columns\TextColumn::make('hours_until_escalation')
                    ->label('Horas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escalateToUser.name')
                    ->label('Escalar a')
                    ->sortable(),
                Tables\Columns\IconColumn::make('notify_supervisor')
                    ->label('Notificar')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Departamento'),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ])
                    ->label('Prioridad'),
                Tables\Filters\Filter::make('is_active')
                    ->label('Estado')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle()
                    ->label('Solo activas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListEscalationRules::route('/'),
            'create' => Pages\CreateEscalationRule::route('/create'),
            'edit' => Pages\EditEscalationRule::route('/{record}/edit'),
        ];
    }    
}
