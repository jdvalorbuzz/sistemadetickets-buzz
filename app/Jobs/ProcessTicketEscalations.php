<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\EscalationRule;
use App\Models\EscalationLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessTicketEscalations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Obtener tickets abiertos o en progreso
        $tickets = Ticket::whereIn('status', ['open', 'in_progress'])->get();
        
        foreach ($tickets as $ticket) {
            // Verificar si el ticket es eligible para escalamiento
            if (!$ticket->isEligibleForEscalation()) {
                continue;
            }
            
            // Obtener la última actividad
            $lastActivity = $ticket->lastActivity ? $ticket->lastActivity->created_at : $ticket->created_at;
            
            // Buscar regla de escalamiento aplicable
            $rule = EscalationRule::where('department_id', $ticket->department_id)
                ->where('priority', $ticket->priority)
                ->where('is_active', true)
                ->first();
                
            if (!$rule) {
                continue;
            }
            
            // Verificar si ha pasado el tiempo de escalamiento
            $hoursWithoutActivity = now()->diffInHours($lastActivity);
            
            if ($hoursWithoutActivity >= $rule->hours_until_escalation) {
                // Escalar el ticket
                $this->escalateTicket($ticket, $rule);
            }
        }
    }
    
    /**
     * Escalar un ticket según las reglas definidas.
     */
    private function escalateTicket(Ticket $ticket, EscalationRule $rule): void
    {
        // Registrar escalamiento
        $log = EscalationLog::create([
            'ticket_id' => $ticket->id,
            'previous_user_id' => $ticket->assigned_to ?? null,
            'escalated_to_user_id' => $rule->escalate_to_user_id,
            'reason' => "Sin actividad por {$rule->hours_until_escalation} horas",
            'escalation_rule_id' => $rule->id
        ]);
        
        // Actualizar asignación del ticket
        $ticket->update([
            'assigned_to' => $rule->escalate_to_user_id
        ]);
        
        // Enviar notificaciones - Las implementaremos más adelante
        // Por ahora guardamos el log y actualizamos la asignación
    }
}
