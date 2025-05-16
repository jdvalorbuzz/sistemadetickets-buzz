<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Notifications\TicketCreatedNotification;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    
    // Asegurar que se establezca el user_id antes de crear el ticket
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $data['user_id'] = auth()->id();
        }
        
        return $data;
    }
    
    public function getTitle(): string
    {
        return auth()->user()->isClient() 
            ? 'Nuevo Ticket de Soporte' 
            : 'Crear Ticket';
    }
    
    public function getSubheading(): ?string
    {
        return auth()->user()->isClient()
            ? 'Por favor proporciona todos los detalles para que podamos ayudarte mejor'
            : null;
    }
    
    protected function afterCreate(): void
    {
        // Obtenemos el ticket recién creado
        $ticket = $this->record;
        
        // Procesar los archivos adjuntos si existen
        $data = $this->data;
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            foreach ($data['attachments'] as $path) {
                // Obtener información del archivo a partir de la ruta
                $storagePath = storage_path('app/public/' . $path);
                if (file_exists($storagePath)) {
                    $fileName = basename($path);
                    $fileSize = filesize($storagePath);
                    $fileType = mime_content_type($storagePath);
                    
                    // Crear el registro de archivo adjunto
                    \App\Models\TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => auth()->id(),
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'context' => 'ticket',
                    ]);
                }
            }
        }
        
        // Si el usuario que crea el ticket es un cliente, enviar notificación a todos los administradores
        if (auth()->user()->isClient()) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new TicketCreatedNotification($ticket));
            }
            
            // También mostramos una notificación en la interfaz
            Notification::make()
                ->title('Ticket creado correctamente')
                ->success()
                ->body('Los administradores han sido notificados de tu nuevo ticket.')
                ->send();
        }
    }
}
