<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?string $navigationLabel = 'Permisos por Rol';
    
    protected static ?string $modelLabel = 'Permiso';
    
    protected static ?string $pluralModelLabel = 'Permisos por Rol';
    
    protected static ?int $navigationSort = 3;
    
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'super_admin';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Permiso')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Identificador')
                            ->required()
                            ->disabled(), // No permitimos cambiar el identificador
                        
                        Forms\Components\TextInput::make('display_name')
                            ->label('Nombre a mostrar')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000),
                            
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'tickets' => 'Tickets',
                                'users' => 'Usuarios',
                                'departments' => 'Departamentos',
                                'reports' => 'Informes',
                                'settings' => 'Configuración',
                            ])
                            ->required(),
                    ])->columnSpan(1),
                    
                Forms\Components\Section::make('Asignación de Roles')
                    ->description('Selecciona los roles que tendrán este permiso')
                    ->schema([
                        Forms\Components\Checkbox::make('roles.admin')
                            ->label('Administradores')
                            ->helperText('Otorga este permiso a los administradores'),
                            
                        Forms\Components\Checkbox::make('roles.support')
                            ->label('Soporte')
                            ->helperText('Otorga este permiso al personal de soporte'),
                            
                        Forms\Components\Checkbox::make('roles.client')
                            ->label('Clientes')
                            ->helperText('Otorga este permiso a los clientes'),
                    ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Permiso')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tickets' => 'Tickets',
                        'users' => 'Usuarios',
                        'departments' => 'Departamentos',
                        'reports' => 'Informes',
                        'settings' => 'Configuración',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('roles.super_admin')
                    ->label('Super Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(fn ($record): bool => true),  // Super admin siempre tiene todos los permisos
                
                Tables\Columns\IconColumn::make('roles.admin')
                    ->label('Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(function ($record): bool {
                        return DB::table('role_permissions')
                            ->where('permission_id', $record->id)
                            ->where('role', 'admin')
                            ->exists();
                    }),
                    
                Tables\Columns\IconColumn::make('roles.support')
                    ->label('Soporte')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(function ($record): bool {
                        return DB::table('role_permissions')
                            ->where('permission_id', $record->id)
                            ->where('role', 'support')
                            ->exists();
                    }),
                    
                Tables\Columns\IconColumn::make('roles.client')
                    ->label('Cliente')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(function ($record): bool {
                        return DB::table('role_permissions')
                            ->where('permission_id', $record->id)
                            ->where('role', 'client')
                            ->exists();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->multiple()
                    ->options([
                        'tickets' => 'Tickets',
                        'users' => 'Usuarios',
                        'departments' => 'Departamentos',
                        'reports' => 'Informes',
                        'settings' => 'Configuración',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Acciones masivas para asignar permisos
                    Tables\Actions\BulkAction::make('assignToAdmin')
                        ->label('Asignar a Admins')
                        ->icon('heroicon-o-user-plus')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::assignRolePermission($record, 'admin');
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('assignToSupport')
                        ->label('Asignar a Soporte')
                        ->icon('heroicon-o-user-plus')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::assignRolePermission($record, 'support');
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('assignToClients')
                        ->label('Asignar a Clientes')
                        ->icon('heroicon-o-user-plus')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::assignRolePermission($record, 'client');
                            }
                        }),
                        
                    // Acciones masivas para remover permisos
                    Tables\Actions\BulkAction::make('removeFromAdmin')
                        ->label('Quitar de Admins')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::removeRolePermission($record, 'admin');
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('removeFromSupport')
                        ->label('Quitar de Soporte')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::removeRolePermission($record, 'support');
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('removeFromClients')
                        ->label('Quitar de Clientes')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                static::removeRolePermission($record, 'client');
                            }
                        }),
                ]),
            ]);
    }
    
    private static function assignRolePermission(Permission $permission, string $role): void
    {
        // Verificar si ya existe la asignación
        $exists = DB::table('role_permissions')
            ->where('permission_id', $permission->id)
            ->where('role', $role)
            ->exists();
            
        if (!$exists) {
            DB::table('role_permissions')->insert([
                'permission_id' => $permission->id,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    private static function removeRolePermission(Permission $permission, string $role): void
    {
        DB::table('role_permissions')
            ->where('permission_id', $permission->id)
            ->where('role', $role)
            ->delete();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolePermissions::route('/'),
            'edit' => Pages\EditRolePermission::route('/{record}/edit'),
        ];
    }
}
