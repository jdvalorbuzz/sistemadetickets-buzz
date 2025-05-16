<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\EscalationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEscalated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $log;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, EscalationLog $log)
    {
        $this->ticket = $ticket;
        $this->log = $log;
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
                    ->subject("Ticket #{$this->ticket->id} escalado - {$this->ticket->title}")
                    ->line("El ticket #{$this->ticket->id} ha sido escalado.")
                    ->line("Motivo: {$this->log->reason}")
                    ->line("De: " . ($this->log->previousUser ? $this->log->previousUser->name : 'Sin asignación previa'))
                    ->line("A: {$this->log->escalatedToUser->name}")
                    ->action('Ver ticket', url("/admin/tickets/{$this->ticket->id}"))
                    ->line('Por favor, revise el ticket a la brevedad.');
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
            'log_id' => $this->log->id, 
            'reason' => $this->log->reason,
            'previous_user_id' => $this->log->previous_user_id,
            'escalated_to_user_id' => $this->log->escalated_to_user_id,
            'message' => "Ticket #{$this->ticket->id} escalado - {$this->ticket->title}"
        ];
    }
}
