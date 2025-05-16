<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reply;
use App\Models\Ticket;

class TicketRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $reply;
    protected $ticket;

    public function __construct(Reply $reply)
    {
        $this->reply = $reply;
        $this->ticket = $reply->ticket;
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
        $isAdmin = $this->reply->is_from_admin;
        $subject = $isAdmin ? 'El equipo de soporte ha respondido a tu ticket' : 'Se ha añadido una respuesta a un ticket';
        $viewUrl = $isAdmin ? '/admin/tickets/' . $this->ticket->id : '/tickets/' . $this->ticket->id;
        
        return (new MailMessage)
                    ->subject($subject . ': ' . $this->ticket->title)
                    ->greeting('Hola ' . $notifiable->name . '!')
                    ->line('Se ha recibido una nueva respuesta en el ticket: ' . $this->ticket->title)
                    ->line('Respuesta: ' . substr($this->reply->content, 0, 100) . '...')
                    ->line('Estado del ticket: ' . ucfirst($this->ticket->status))
                    ->action('Ver Ticket', url($viewUrl))
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
            'reply_id' => $this->reply->id,
            'title' => $this->ticket->title,
            'message' => 'Se ha respondido a tu ticket',
            'is_admin_reply' => $this->reply->is_from_admin,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
