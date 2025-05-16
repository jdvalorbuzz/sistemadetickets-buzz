<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Notifications\TicketClosedNotification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;
    
    // Guarda el estado original del ticket antes de actualizar
    protected ?string $originalStatus = null;
    
    public function mount($record): void
    {
        parent::mount($record);
        
        // Mostrar mensaje para el personal de soporte
        if (auth()->check() && auth()->user()->role === 'support') {
            \Filament\Notifications\Notification::make()
                ->title('Modo de solo respuestas')
                ->body('Como miembro del equipo de soporte, puedes responder al ticket y cerrarlo, pero no modificar su información base.')
                ->info()
                ->persistent()
                ->send();
        }
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ticket = Ticket::find($this->record->id);
        $this->originalStatus = $ticket->status;
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('closeTicket')
                ->label('Cerrar Ticket')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => !in_array($this->record->status, ['closed', 'archived']) && auth()->user()->isStaff())
                ->action(function () {
                    $ticket = $this->record;
                    $ticket->status = 'closed';
                    $ticket->closed_at = now();
                    $ticket->closed_by = auth()->id();
                    $ticket->save();
                    
                    // Enviar notificación al propietario del ticket si no es quien lo cierra
                    if ($ticket->user_id !== auth()->id()) {
                        $ticket->user->notify(new TicketClosedNotification($ticket));
                    }
                    
                    Notification::make()
                        ->title('Ticket cerrado')
                        ->success()
                        ->body('El ticket ha sido cerrado correctamente.')
                        ->send();
                        
                    $this->redirect(TicketResource::getUrl('index'));
                }),
            Actions\Action::make('archiveTicket')
                ->label('Archivar Ticket')
                ->color('gray')
                ->icon('heroicon-o-archive-box')
                ->visible(fn () => $this->record->status === 'closed' && auth()->user()->isAdmin())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->status = 'archived';
                    $this->record->save();
                    
                    Notification::make()
                        ->title('Ticket archivado')
                        ->success()
                        ->body('El ticket ha sido archivado correctamente.')
                        ->send();
                    
                    $this->redirect(TicketResource::getUrl('index'));
                }),
        ];
    }
    
    protected function afterSave(): void
    {
        $ticket = $this->getRecord();
        
        if ($ticket->isDirty('status')) {
            $oldStatus = $ticket->getOriginal('status');
            $newStatus = $ticket->status;
            
            // Si el ticket se cerró, enviar notificación
            if (in_array($newStatus, ['closed', 'archived']) && !in_array($oldStatus, ['closed', 'archived'])) {
                $ticket->closed_at = now();
                $ticket->closed_by = auth()->id();
                $ticket->save();
                
                // Notificar al propietario del ticket que su ticket ha sido cerrado
                $ticket->user->notify(new TicketClosedNotification($ticket));
                
                // Si el usuario actual es el propietario del ticket, mostrar un modal para calificar
                if (auth()->id() === $ticket->user_id) {
                    $this->showRatingModal($ticket);
                }
            }
        }
    }
    
    protected function showRatingModal($ticket): void
    {
        // Este método mostrará un modal para calificar el ticket
        Notification::make()
            ->title('¿Cómo calificarías el servicio recibido?')
            ->body('Tu opinión es importante para nosotros y nos ayuda a mejorar nuestro servicio.')
            ->actions([
                \Filament\Notifications\Actions\Action::make('rate')
                    ->button()
                    ->color('success')
                    ->label('Calificar ahora')
                    ->url(route('tickets.rate.show', ['ticket' => $ticket->id])),
                \Filament\Notifications\Actions\Action::make('later')
                    ->close()
                    ->label('Más tarde'),
            ])
            ->persistent()
            ->send();
    }
    
    // No usamos widgets para los archivos adjuntos, lo hacemos directamente en la vista
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
