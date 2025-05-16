<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Departamentos';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 2;
    
    /**
     * Determina si el recurso es visible en la navegación
     * Los clientes no deben ver los departamentos
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isStaff();
    }
    
    /**
     * Determina si los formularios en este recurso deben ser de solo lectura
     * El personal de soporte solo puede ver los departamentos pero no editarlos
     */
    public static function isReadOnly(): bool
    {
        return auth()->check() && auth()->user()->role === 'support';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Departamento')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn() => static::isReadOnly()),
                            
                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->alpha()
                            ->formatStateUsing(fn (string $state): string => strtoupper($state))
                            ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                            ->disabled(fn() => static::isReadOnly()),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->disabled(fn() => static::isReadOnly()),
                            
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color identificativo')
                            ->required()
                            ->disabled(fn() => static::isReadOnly()),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->disabled(fn() => static::isReadOnly())
                            ->helperText('Los departamentos inactivos no aparecerán en los formularios de tickets'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
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
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn() => static::isReadOnly()),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn() => static::isReadOnly())
                    ->before(function (Department $record, Tables\Actions\DeleteAction $action) {
                        if ($record->tickets()->count() > 0) {
                            $action->cancel();
                            $action->failureNotification()?->send();
                            
                            return;
                        }
                    })
                    ->failureNotificationTitle('El departamento no puede ser eliminado porque tiene tickets asociados'),
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => static::isReadOnly()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn() => static::isReadOnly()),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
