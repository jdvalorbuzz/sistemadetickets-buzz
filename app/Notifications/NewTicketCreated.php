<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $source = $this->ticket->source === 'email' ? ' (vía email)' : '';
        
        return (new MailMessage)
                    ->subject("Nuevo ticket #{$this->ticket->id}{$source} - {$this->ticket->title}")
                    ->greeting("Hola {$notifiable->name},")
                    ->line("Se ha creado un nuevo ticket que requiere atención.")
                    ->line("Ticket: #{$this->ticket->id} - {$this->ticket->title}")
                    ->line("Departamento: {$this->ticket->department->name}")
                    ->line("Prioridad: {$this->ticket->priority}")
                    ->line("Cliente: {$this->ticket->user->name}")
                    ->action('Ver ticket', url("/admin/tickets/{$this->ticket->id}"))
                    ->line('Por favor, atiende este ticket a la brevedad posible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'user_id' => $this->ticket->user_id,
            'user_name' => $this->ticket->user->name,
            'priority' => $this->ticket->priority,
            'source' => $this->ticket->source,
            'message' => "Nuevo ticket #{$this->ticket->id} - {$this->ticket->title}"
        ];
    }
}
