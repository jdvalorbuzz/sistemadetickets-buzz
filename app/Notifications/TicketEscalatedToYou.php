<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\EscalationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEscalatedToYou extends Notification implements ShouldQueue
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
                    ->subject("Ticket #{$this->ticket->id} ha sido escalado a ti - {$this->ticket->title}")
                    ->line("Un ticket ha sido escalado y asignado a ti.")
                    ->line("Ticket: #{$this->ticket->id} - {$this->ticket->title}")
                    ->line("Motivo de escalamiento: {$this->log->reason}")
                    ->line("Departamento: {$this->ticket->department->name}")
                    ->line("Prioridad: {$this->ticket->priority}")
                    ->action('Ver detalles del ticket', url("/admin/tickets/{$this->ticket->id}"))
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
            'log_id' => $this->log->id,
            'reason' => $this->log->reason,
            'previous_user_id' => $this->log->previous_user_id,
            'message' => "Un ticket ha sido escalado y asignado a ti: #{$this->ticket->id} - {$this->ticket->title}"
        ];
    }
}
