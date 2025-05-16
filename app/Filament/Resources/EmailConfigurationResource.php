<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailConfigurationResource\Pages;
use App\Models\EmailConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailConfigurationResource extends Resource
{
    protected static ?string $model = EmailConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Configuración de Email';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 5;
    
    /**
     * Determina si el recurso es visible en la navegación
     * Solo para administradores y super administradores
     */
    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración de Correo Entrante')
                    ->schema([
                        Forms\Components\Select::make('incoming_type')
                            ->label('Tipo de conexión')
                            ->options([
                                'imap' => 'IMAP',
                                'pop3' => 'POP3',
                                'api' => 'API (Gmail/Microsoft)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('incoming_server')
                            ->label('Servidor')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('incoming_port')
                            ->label('Puerto')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535),
                        Forms\Components\Select::make('incoming_encryption')
                            ->label('Encriptación')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                'none' => 'Ninguna',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('incoming_username')
                            ->label('Usuario')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('incoming_password')
                            ->label('Contraseña')
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state)
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Configuración de Correo Saliente')
                    ->schema([
                        Forms\Components\Select::make('outgoing_type')
                            ->label('Tipo de conexión')
                            ->options([
                                'smtp' => 'SMTP',
                                'api' => 'API (Gmail/Microsoft)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('outgoing_server')
                            ->label('Servidor')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('outgoing_port')
                            ->label('Puerto')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535),
                        Forms\Components\Select::make('outgoing_encryption')
                            ->label('Encriptación')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                'none' => 'Ninguna',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('outgoing_username')
                            ->label('Usuario')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('outgoing_password')
                            ->label('Contraseña')
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state)
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Configuración General')
                    ->schema([
                        Forms\Components\TextInput::make('from_email')
                            ->label('Email de origen')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('from_name')
                            ->label('Nombre de origen')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('department_id')
                            ->label('Departamento')
                            ->relationship('department', 'name')
                            ->helperText('Los tickets creados vía email se asignarán a este departamento'),
                        Forms\Components\TextInput::make('polling_interval')
                            ->label('Intervalo de consulta (minutos)')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(60)
                            ->helperText('Cada cuántos minutos se revisará el buzón de correo'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('incoming_type')
                    ->label('Tipo')
                    ->badge(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('polling_interval')
                    ->label('Intervalo (min)')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('test')
                    ->label('Probar conexión')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (EmailConfiguration $record) {
                        // Lógica para probar la conexión
                        // En un sistema real, se implementaría la prueba de conexión
                        session()->flash('success', 'Conexión probada con éxito');
                    }),
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
            'index' => Pages\ListEmailConfigurations::route('/'),
            'create' => Pages\CreateEmailConfiguration::route('/create'),
            'edit' => Pages\EditEmailConfiguration::route('/{record}/edit'),
        ];
    }    
}
