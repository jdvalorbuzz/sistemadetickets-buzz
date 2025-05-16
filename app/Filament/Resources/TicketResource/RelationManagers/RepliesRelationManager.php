<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Reply;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FilamentNotification;
use App\Notifications\TicketRepliedNotification;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';
    
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        // Verificar que la consulta no sea nula antes de modificarla
        if ($query !== null) {
            // Si el usuario es cliente, solo mostrar sus propias respuestas
            if (auth()->check() && auth()->user()->isClient()) {
                $query->where(function($query) {
                    $query->where('user_id', auth()->id())
                          ->orWhere('is_from_admin', true);
                });
            }
        } else {
            // Usar un modelo base como fallback si la consulta es nula
            $query = \App\Models\Reply::query()->where('ticket_id', $this->getOwnerRecord()->id);
            
            // Aplicar filtro para clientes
            if (auth()->check() && auth()->user()->isClient()) {
                $query->where(function($query) {
                    $query->where('user_id', auth()->id())
                          ->orWhere('is_from_admin', true);
                });
            }
        }
        
        return $query;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->label('Contenido de la respuesta')
                    ->required()
                    ->placeholder('Escribe tu respuesta aquí...')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('reply-attachments')
                    ->columnSpanFull()
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
                
                FileUpload::make('attachments')
                    ->label('Archivos adjuntos')
                    ->multiple()
                    ->maxFiles(3)
                    ->acceptedFileTypes(['image/*', 'application/pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt'])
                    ->directory('reply-attachments')
                    ->visibility('public')
                    ->maxSize(5120) // 5MB
                    ->downloadable()
                    ->previewable()
                    ->columnSpanFull()
                    ->hint('Máximo 3 archivos. Tamaño máximo: 5MB por archivo.')
                    ->helperText('Formatos permitidos: imágenes, PDF, Word, Excel y texto.'),
                    
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
                    
                Forms\Components\Hidden::make('is_from_admin')
                    ->default(fn() => Auth::user()->isAdmin()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Replied by')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('content')
                    ->limit(100),
                    
                Tables\Columns\IconColumn::make('is_from_admin')
                    ->label('Admin Reply')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Añadir respuesta')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        $data['is_from_admin'] = Auth::user()->isAdmin();
                        
                        return $data;
                    })
                    ->using(function (array $data, RelationManager $livewire): Reply {
                        // Create the reply
                        $reply = $livewire->getRelationship()->create($data);
                        
                        // Process attachments if any
                        if (isset($data['attachments']) && !empty($data['attachments'])) {
                            foreach ($data['attachments'] as $path) {
                                // Get file information from the path
                                $storagePath = storage_path('app/public/' . $path);
                                if (file_exists($storagePath)) {
                                    $fileName = basename($path);
                                    $fileSize = filesize($storagePath);
                                    $fileType = mime_content_type($storagePath);
                                    
                                    // Create the attachment record
                                    \App\Models\TicketAttachment::create([
                                        'ticket_id' => $reply->ticket_id,
                                        'reply_id' => $reply->id,
                                        'user_id' => Auth::id(),
                                        'file_name' => $fileName,
                                        'file_path' => $path,
                                        'file_type' => $fileType,
                                        'file_size' => $fileSize,
                                        'context' => 'reply',
                                    ]);
                                }
                            }
                        }
                        
                        return $reply;
                    })
                    ->successNotification(fn (): \Filament\Notifications\Notification => 
                        \Filament\Notifications\Notification::make()
                            ->title('Respuesta añadida')
                            ->body('Tu respuesta ha sido agregada correctamente')
                            ->success()
                    )
                    ->after(function (Reply $reply) {
                        // Enviar notificación al dueño del ticket si la respuesta es de un admin
                        if ($reply->is_from_admin && $reply->ticket->user_id !== Auth::id()) {
                            $reply->ticket->user->notify(new TicketRepliedNotification($reply));
                            
                            FilamentNotification::make()
                                ->title('Notificación enviada')
                                ->success()
                                ->body('Se ha notificado al cliente sobre tu respuesta.')
                                ->send();
                        }
                        
                        // Enviar notificación a los admins si la respuesta es de un cliente
                        if (!$reply->is_from_admin) {
                            $admins = \App\Models\User::where('role', 'admin')->get();
                            foreach ($admins as $admin) {
                                $admin->notify(new TicketRepliedNotification($reply));
                            }
                            
                            FilamentNotification::make()
                                ->title('Notificación enviada')
                                ->success()
                                ->body('Los administradores han sido notificados de tu respuesta.')
                                ->send();
                        }
                        
                        // Actualizar el estado del ticket a 'in_progress' si estaba 'open'
                        $ticket = $reply->ticket;
                        if ($ticket->status === 'open') {
                            $ticket->status = 'in_progress';
                            $ticket->save();
                        }
                    }),
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
}
