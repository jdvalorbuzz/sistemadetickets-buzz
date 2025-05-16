<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEscalatedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
                    ->subject("Actualización sobre su ticket #{$this->ticket->id}")
                    ->greeting("Estimado/a {$notifiable->name},")
                    ->line("Le informamos que su ticket #{$this->ticket->id} - {$this->ticket->title} ha sido reasignado a un nuevo especialista para brindarle una mejor atención.")
                    ->line("Este cambio es parte de nuestro proceso para garantizar que todos los tickets reciban atención oportuna y especializada.")
                    ->line("No es necesario que realice ninguna acción. Nuestro equipo se pondrá en contacto con usted pronto.")
                    ->action('Ver estado del ticket', url("/tickets/{$this->ticket->id}"));
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
            'message' => "Su ticket #{$this->ticket->id} ha sido reasignado a un nuevo especialista para brindarle una mejor atención."
        ];
    }
}
