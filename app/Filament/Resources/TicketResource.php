<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\FileUpload;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationGroup = 'Tickets Management';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'Ticket';
    
    protected static ?string $pluralModelLabel = 'Tickets';
    
    // Personalizar los badges y encabezados de página
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'open')->count() ? (string) static::getModel()::where('status', 'open')->count() : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'open')->count() > 5 ? 'danger' : 'warning';
    }

    /**
     * Determina si el formulario debe estar en modo lectura para el personal de soporte
     */
    public static function isReadOnlyForSupport(): bool
    {
        return auth()->check() && 
               auth()->user()->role === 'support' &&
               request()->routeIs('filament.admin.resources.tickets.edit');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Información del Ticket')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Creado el')
                                    ->content(fn (Ticket $record): string => $record?->created_at?->format('d/m/Y H:i') ?? '-')
                                    ->hiddenOn('create'),
                                    
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Actualizado el')
                                    ->content(fn (Ticket $record): string => $record?->updated_at?->format('d/m/Y H:i') ?? '-')
                                    ->hiddenOn('create'),

                                    
                                Forms\Components\Select::make('department_id')
                                    ->label('Departamento')
                                    ->relationship('department', 'name')
                                    ->options(function () {
                                        // Obtener solo departamentos activos
                                        return \App\Models\Department::where('is_active', true)
                                            ->orderBy('name')
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled(fn() => static::isReadOnlyForSupport()),
                                
                                Forms\Components\TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(fn() => static::isReadOnlyForSupport()),
                                    
                                Forms\Components\RichEditor::make('description')
                                    ->label('Descripción')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('ticket-attachments')
                                    ->columnSpanFull()
                                    ->disabled(fn() => static::isReadOnlyForSupport())
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'heading',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                                    
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'open' => 'Abierto',
                                        'in_progress' => 'En Progreso',
                                        'closed' => 'Cerrado',
                                        'archived' => 'Archivado'
                                    ])
                                    ->default('open')
                                    ->required()
                                    ->disabled(fn () => auth()->check() && (static::isReadOnlyForSupport() || !auth()->user()->isAdmin()))
                                    ->hidden(fn () => auth()->check() && !auth()->user()->isAdmin() && request()->routeIs('*.create')),
                                    
                                Forms\Components\Select::make('priority')
                                    ->label('Prioridad')
                                    ->options(function () {
                                        // Para clientes, simplificar opciones
                                        if (auth()->check() && !auth()->user()->isAdmin()) {
                                            return [
                                                'low' => 'Normal',
                                                'high' => 'Urgente'
                                            ];
                                        }
                                        
                                        // Opciones completas para administradores
                                        return [
                                            'low' => 'Baja',
                                            'medium' => 'Media',
                                            'high' => 'Alta',
                                            'urgent' => 'Urgente'
                                        ];
                                    })
                                    ->default('low')
                                    ->required()
                                    ->disabled(fn() => static::isReadOnlyForSupport()),
                                    
                                Forms\Components\Select::make('tags')
                                    ->label('Etiquetas')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->disabled(fn() => static::isReadOnlyForSupport())
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(),
                                        Forms\Components\ColorPicker::make('color')
                                            ->label('Color')
                                            ->required(),
                                    ]),
                                    
                                Forms\Components\Placeholder::make('file_uploads')
                                    ->label('Archivos adjuntos')
                                    ->content(function (Ticket $record) {
                                        if (!$record->exists) {
                                            return 'Guarde el ticket primero para adjuntar archivos';
                                        }
                                        
                                        $html = '<ul class="list-disc list-inside">';
                                        
                                        $attachments = $record->ticketAttachments;
                                        
                                        if ($attachments->isEmpty()) {
                                            return 'No hay archivos adjuntos';
                                        }
                                        
                                        foreach ($attachments as $attachment) {
                                            $url = $attachment->file_path;
                                            $filename = $attachment->file_name;
                                            
                                            $html .= "<li><a href=\"{$url}\" target=\"_blank\" class=\"text-primary-500 hover:underline\">{$filename}</a></li>";
                                        }
                                        
                                        $html .= '</ul>';
                                        
                                        return $html;
                                    })
                                    ->hiddenOn('create'),
                                    
                                FileUpload::make('attachments')
                                    ->label('Adjuntar archivos')
                                    ->disk('public')
                                    ->directory('ticket-attachments')
                                    ->multiple()
                                    ->acceptedFileTypes([
                                        'application/pdf', 
                                        'image/jpeg', 
                                        'image/png', 
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/zip',
                                    ])
                                    ->maxSize(10240) // 10MB
                                    ->disabled(fn() => static::isReadOnlyForSupport())
                                    ->visible(fn ($livewire) => 
                                        // Visible siempre en creación
                                        $livewire instanceof Pages\CreateTicket ||
                                        // O en edición
                                        $livewire instanceof Pages\EditTicket
                                    )
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!$state) {
                                            return;
                                        }
                                        
                                        $set('attachments', []);
                                    }),
                                
                                Forms\Components\Select::make('user_id')
                                    ->label('Cliente')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->visible(fn () => auth()->check() && auth()->user()->isAdmin())
                                    ->disabled(fn() => static::isReadOnlyForSupport()),
                            ])
                            ->columns(2)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->badge()
                    ->color(fn ($record): string => $record->department?->color ?? '#374151')
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'closed' => 'Cerrado',
                        'archived' => 'Archivado',
                        default => $state,
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => $state,
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Etiquetas')
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'closed' => 'Cerrado',
                        'archived' => 'Archivado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->relationship('department', 'name'),
                    
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ]),
                    
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
                    }),
                    
                // Filtro adicional para administradores y soporte
                Tables\Filters\SelectFilter::make('user')
                    ->label('Cliente')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->check() && auth()->user()->isStaff()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(''),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->visible(fn () => auth()->check() && auth()->user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->check() && auth()->user()->isAdmin()),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        // Si es cliente, solo mostrar sus tickets
        if (auth()->check() && auth()->user()->isClient()) {
            return parent::getEloquentQuery()->where('user_id', auth()->id());
        }
        
        // Para admin/soporte, mostrar todos los tickets
        return parent::getEloquentQuery();
    }
}
