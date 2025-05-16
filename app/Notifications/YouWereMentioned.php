<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouWereMentioned extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $ticket;
    protected $reply;
    
    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, Reply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }
    
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }
    
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Has sido mencionado en el ticket #{$this->ticket->id}")
            ->line("{$this->reply->user->name} te ha mencionado en una respuesta.")
            ->line("Ticket: {$this->ticket->title}")
            ->line("Departamento: {$this->ticket->department->name}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->line('Puedes responder directamente desde el sistema.');
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
            'reply_id' => $this->reply->id,
            'user_id' => $this->reply->user_id,
            'user_name' => $this->reply->user->name,
            'message' => "{$this->reply->user->name} te ha mencionado en una respuesta al ticket #{$this->ticket->id}"
        ];
    }
}
