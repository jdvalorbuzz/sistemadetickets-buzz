<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $ticket;

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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nuevo Ticket: ' . $this->ticket->title)
                    ->greeting('Hola ' . $notifiable->name . '!')
                    ->line('Se ha creado un nuevo ticket con el título: ' . $this->ticket->title)
                    ->line('Descripción: ' . substr($this->ticket->description, 0, 100) . '...')
                    ->action('Ver Ticket', url('/admin/tickets/' . $this->ticket->id))
                    ->line('Gracias por usar nuestro sistema de tickets.');
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
            'message' => 'Se ha creado un nuevo ticket',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
