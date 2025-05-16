<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Reply;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class PreviewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket-resource.pages.preview-ticket';
    
    public ?array $data = [];
    
    public $replyData = [];
    
    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Cargar relaciones necesarias
        $this->record->load(['user', 'department', 'replies.user', 'assignedTo']);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('take_ticket')
                ->label(fn () => $this->record->assigned_to === null ? 'Tomar Ticket' : 
                    ($this->record->assigned_to === Auth::id() ? 'Liberar Ticket' : 'Asignado a: ' . optional($this->record->assignedTo)->name))
                ->disabled(fn () => $this->record->assigned_to !== null && $this->record->assigned_to !== Auth::id() && !auth()->user()->isAdmin())
                ->color(fn () => $this->record->assigned_to === null ? 'success' : 
                    ($this->record->assigned_to === Auth::id() ? 'warning' : 'gray'))
                ->icon(fn () => $this->record->assigned_to === null ? 'heroicon-o-hand-raised' : 
                    ($this->record->assigned_to === Auth::id() ? 'heroicon-o-hand-thumb-up' : 'heroicon-o-user'))
                ->action(function () {
                    if ($this->record->assigned_to === null) {
                        // Asignar ticket
                        $this->record->update([
                            'assigned_to' => Auth::id(),
                            'status' => $this->record->status === 'open' ? 'in_progress' : $this->record->status
                        ]);
                        
                        // Crear respuesta automática
                        Reply::create([
                            'ticket_id' => $this->record->id,
                            'user_id' => Auth::id(),
                            'content' => '**' . Auth::user()->name . '** ha tomado este ticket.',
                            'is_system' => true
                        ]);
                        
                        Notification::make()
                            ->title('Ticket asignado correctamente')
                            ->success()
                            ->send();
                    } else {
                        // Liberar ticket
                        $previousAssigneeName = optional($this->record->assignedTo)->name;
                        
                        $this->record->update([
                            'assigned_to' => null
                        ]);
                        
                        // Crear respuesta automática
                        Reply::create([
                            'ticket_id' => $this->record->id,
                            'user_id' => Auth::id(),
                            'content' => '**' . Auth::user()->name . '** ha liberado este ticket para que otro agente pueda tomarlo.',
                            'is_system' => true
                        ]);
                        
                        Notification::make()
                            ->title('Ticket liberado correctamente')
                            ->success()
                            ->send();
                    }
                    
                    return redirect(static::getUrl(['record' => $this->record->id]));
                }),
            
            Action::make('change_status')
                ->label(fn () => $this->record->status === 'closed' ? 'Reabrir Ticket' : 'Cerrar Ticket')
                ->color(fn () => $this->record->status === 'closed' ? 'success' : 'warning')
                ->icon(fn () => $this->record->status === 'closed' ? 'heroicon-o-arrow-path' : 'heroicon-o-check')
                ->action(function () {
                    if ($this->record->status === 'closed') {
                        // Reabrir ticket
                        $this->record->update([
                            'status' => 'in_progress',
                            'closed_at' => null,
                            'closed_by' => null,
                        ]);
                        
                        // Crear respuesta automática
                        Reply::create([
                            'ticket_id' => $this->record->id,
                            'user_id' => Auth::id(),
                            'content' => 'El ticket ha sido reabierto por **' . Auth::user()->name . '**',
                            'is_system' => true
                        ]);
                        
                        Notification::make()
                            ->title('Ticket reabierto correctamente')
                            ->success()
                            ->send();
                    } else {
                        // Cerrar ticket
                        $this->record->update([
                            'status' => 'closed',
                            'closed_at' => now(),
                            'closed_by' => Auth::id(),
                        ]);
                        
                        // Crear respuesta automática
                        Reply::create([
                            'ticket_id' => $this->record->id,
                            'user_id' => Auth::id(),
                            'content' => 'El ticket ha sido cerrado por **' . Auth::user()->name . '**',
                            'is_system' => true
                        ]);
                        
                        Notification::make()
                            ->title('Ticket cerrado correctamente')
                            ->success()
                            ->send();
                    }
                    
                    return redirect(static::getUrl(['record' => $this->record->id]));
                }),
        ];
    }
    
    public function getFormSchema(): array
    {
        return [];
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([])
            ->disabled()
            ->columns(2);
    }

    public function getTitle(): string
    {
        return 'Vista Previa: ' . $this->record->title;
    }
    
    public function getMaxContentWidth(): string
    {
        return 'full';
    }
    
    public function getReplyFormSchema(): array
    {
        return [
            RichEditor::make('content')
                ->label('Respuesta')
                ->required()
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('ticket-replies-attachments'),
                
            FileUpload::make('attachments')
                ->label('Archivos Adjuntos')
                ->multiple()
                ->maxFiles(5)
                ->preserveFilenames()
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
        ];
    }
    
    public function getReplyForm(): Form
    {
        return $this->makeForm()
            ->schema($this->getReplyFormSchema())
            ->statePath('replyData');
    }
    
    public function submitReply()
    {
        $data = $this->replyData;
        
        // Validar que haya contenido
        if (empty($data['content'])) {
            Notification::make()
                ->title('La respuesta no puede estar vacía')
                ->danger()
                ->send();
            return;
        }
        
        // Crear respuesta
        $reply = new Reply();
        $reply->ticket_id = $this->record->id;
        $reply->user_id = Auth::id();
        $reply->content = $data['content'];
        $reply->save();
        
        // Procesar archivos adjuntos si existen
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $reply->attachments()->create([
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('ticket-replies-attachments', 'public'),
                    'mime_type' => $attachment->getMimeType(),
                    'size' => $attachment->getSize(),
                ]);
            }
        }
        
        // Si el ticket está abierto, pasarlo a en progreso
        if ($this->record->status === 'open') {
            $this->record->update(['status' => 'in_progress']);
        }
        
        // Limpiar formulario
        $this->replyData = [];
        
        Notification::make()
            ->title('Respuesta enviada correctamente')
            ->success()
            ->send();
        
        return redirect(static::getUrl(['record' => $this->record->id]));
    }
    
    public function submitReplyAndClose()
    {
        $this->submitReply();
        
        // Cerrar ticket
        $this->record->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);
        
        // Crear respuesta automática
        Reply::create([
            'ticket_id' => $this->record->id,
            'user_id' => Auth::id(),
            'content' => 'El ticket ha sido cerrado por **' . Auth::user()->name . '**',
            'is_system' => true
        ]);
        
        Notification::make()
            ->title('Ticket cerrado correctamente')
            ->success()
            ->send();
        
        return redirect(static::getUrl(['record' => $this->record->id]));
    }
}
