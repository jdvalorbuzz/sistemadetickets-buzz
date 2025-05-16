<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientReplied extends Notification implements ShouldQueue
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
        $source = $this->reply->source === 'email' ? ' (vía email)' : '';
        
        return (new MailMessage)
                    ->subject("Nueva respuesta del cliente en ticket #{$this->ticket->id}{$source}")
                    ->greeting("Hola {$notifiable->name},")
                    ->line("El cliente ha respondido al ticket #{$this->ticket->id} - {$this->ticket->title}")
                    ->line("Cliente: {$this->ticket->user->name}")
                    ->line("Departamento: {$this->ticket->department->name}")
                    ->line("Respuesta:")
                    ->line($this->truncateContent($this->reply->content))
                    ->action('Ver ticket', url("/admin/tickets/{$this->ticket->id}"))
                    ->line('Por favor, atiende esta respuesta a la brevedad posible.');
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
            'source' => $this->reply->source,
            'message' => "Nueva respuesta de cliente en #{$this->ticket->id} - {$this->ticket->title}"
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
