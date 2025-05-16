<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketClosedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $ticket;
    protected $closedBy;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->closedBy = $ticket->closedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $closedByName = $this->closedBy ? $this->closedBy->name : 'Un administrador';
        
        return (new MailMessage)
                    ->subject('Ticket cerrado: ' . $this->ticket->title)
                    ->greeting('Hola ' . $notifiable->name . '!')
                    ->line('Tu ticket ha sido cerrado: ' . $this->ticket->title)
                    ->line('Cerrado por: ' . $closedByName)
                    ->line('Fecha de cierre: ' . $this->ticket->closed_at->format('d/m/Y H:i'))
                    ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
                    ->line('Si necesitas reabrir este ticket o tienes alguna otra consulta, por favor responde a esta notificación.');
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
            'title' => $this->ticket->title,
            'message' => 'Tu ticket ha sido cerrado',
            'closed_by' => $this->closedBy ? $this->closedBy->id : null,
            'closed_at' => $this->ticket->closed_at->toIso8601String(),
        ];
    }
}
