<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReplyAdded extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Nueva respuesta a su ticket #{$this->ticket->id} - {$this->ticket->title}")
                    ->greeting("Estimado/a {$notifiable->name},")
                    ->line("Su ticket ha recibido una nueva respuesta de nuestro equipo de soporte.")
                    ->line("Ticket: #{$this->ticket->id} - {$this->ticket->title}")
                    ->line("Respuesta:")
                    ->line($this->truncateContent($this->reply->content))
                    ->action('Ver respuesta completa', url("/tickets/{$this->ticket->id}"))
                    ->line('Si necesita más ayuda, puede responder directamente a este email o desde la plataforma.');
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
            'message' => "Nueva respuesta a su ticket #{$this->ticket->id}"
        ];
    }
    
    /**
     * Truncate content for preview.
     *
     * @param string $content
     * @return string
     */
    private function truncateContent($content)
    {
        if (strlen($content) > 500) {
            return substr($content, 0, 500) . '...';
        }
        
        return $content;
    }
}
