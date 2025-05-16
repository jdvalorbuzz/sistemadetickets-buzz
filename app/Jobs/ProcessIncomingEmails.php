<?php

namespace App\Jobs;

use App\Models\EmailConfiguration;
use App\Models\Ticket;
use App\Models\Reply;
use App\Models\User;
use App\Models\TicketAttachment;
use App\Notifications\NewTicketCreated;
use App\Notifications\NewReplyAdded;
use App\Notifications\ClientReplied;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessIncomingEmails implements ShouldQueue
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
        $configs = EmailConfiguration::all();
        
        foreach ($configs as $config) {
            $this->processEmailsForConfig($config);
        }
    }
    
    /**
     * Procesar emails para una configuración específica.
     */
    private function processEmailsForConfig(EmailConfiguration $config): void
    {
        try {
            // Crear cliente IMAP o servicio API según configuración
            $emailClient = $this->createEmailClient($config);
            
            // Obtener emails no procesados
            $emails = $emailClient->getUnreadEmails();
            
            foreach ($emails as $email) {
                // Determinar si es un nuevo ticket o una respuesta
                $this->processEmail($email, $config);
                
                // Marcar como leído
                $emailClient->markAsRead($email->id);
            }
        } catch (\Exception $e) {
            // Registrar el error pero permitir que el job continúe
            \Log::error('Error procesando emails: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear un cliente de email basado en la configuración.
     * 
     * @param EmailConfiguration $config
     * @return object
     */
    private function createEmailClient(EmailConfiguration $config)
    {
        // Implementación básica para IMAP
        // En un sistema real, esto podría ser una clase separada o un servicio
        
        switch ($config->incoming_type) {
            case 'imap':
                return new class($config) {
                    protected $config;
                    
                    public function __construct($config)
                    {
                        $this->config = $config;
                    }
                    
                    public function getUnreadEmails()
                    {
                        // En un sistema real, aquí se conectaría al servidor IMAP
                        // y obtendría los correos no leídos
                        return [];
                    }
                    
                    public function markAsRead($id)
                    {
                        // Marcar como leído en el servidor IMAP
                    }
                };
                
            default:
                throw new \Exception("Tipo de conexión de correo no soportado: {$config->incoming_type}");
        }
    }
    
    /**
     * Procesar un email para determinar si es un nuevo ticket o una respuesta.
     */
    private function processEmail($email, EmailConfiguration $config): void
    {
        // Verificar si es respuesta a un ticket existente
        $ticketId = $this->extractTicketIdFromSubject($email->subject ?? '');
        $inReplyTo = $email->inReplyTo ?? null;
        
        if ($ticketId) {
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $this->createReplyFromEmail($ticket, $email);
                return;
            }
        }
        
        if ($inReplyTo) {
            $reply = Reply::where('email_message_id', $inReplyTo)->first();
            if ($reply) {
                $this->createReplyFromEmail($reply->ticket, $email);
                return;
            }
            
            $ticket = Ticket::where('email_message_id', $inReplyTo)->first();
            if ($ticket) {
                $this->createReplyFromEmail($ticket, $email);
                return;
            }
        }
        
        // Si no es respuesta, crear nuevo ticket
        $this->createTicketFromEmail($email, $config);
    }
    
    /**
     * Crear un nuevo ticket a partir de un email.
     */
    private function createTicketFromEmail($email, EmailConfiguration $config): void
    {
        // Buscar o crear usuario
        $user = User::firstOrCreate(
            ['email' => $email->from],
            [
                'name' => $email->fromName ?? explode('@', $email->from)[0],
                'password' => Hash::make(Str::random(16)),
                'role' => 'client'
            ]
        );
        
        // Crear ticket
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'department_id' => $config->department_id,
            'title' => $email->subject ?? 'Sin asunto',
            'description' => $this->convertHtmlToText($email->body ?? ''),
            'status' => 'open',
            'priority' => 'medium',
            'email_subject' => $email->subject ?? 'Sin asunto',
            'email_message_id' => $email->messageId ?? null,
            'source' => 'email'
        ]);
        
        // Procesar adjuntos si existen
        if (!empty($email->attachments)) {
            $this->processAttachments($email, $ticket);
        }
        
        // Notificar al staff
        $staffUsers = User::whereIn('role', ['admin', 'super_admin', 'support'])->get();
        Notification::send($staffUsers, new NewTicketCreated($ticket));
        
        // Enviar confirmación al remitente (se implementaría en sistemas reales)
        // Mail::to($user->email)->send(new TicketCreatedConfirmation($ticket));
    }
    
    /**
     * Crear una respuesta a partir de un email.
     */
    private function createReplyFromEmail(Ticket $ticket, $email): void
    {
        // Determinar si es respuesta de cliente o staff
        $isFromStaff = User::where('email', $email->from)
            ->whereIn('role', ['admin', 'super_admin', 'support'])
            ->exists();
            
        // Buscar o crear usuario si es cliente
        $user = User::firstOrCreate(
            ['email' => $email->from],
            [
                'name' => $email->fromName ?? explode('@', $email->from)[0],
                'password' => Hash::make(Str::random(16)),
                'role' => 'client'
            ]
        );
        
        // Crear respuesta
        $reply = Reply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => $this->convertHtmlToText($email->body ?? ''),
            'is_from_admin' => $isFromStaff,
            'email_message_id' => $email->messageId ?? null,
            'source' => 'email'
        ]);
        
        // Procesar adjuntos si existen
        if (!empty($email->attachments)) {
            $this->processAttachments($email, $ticket, $reply);
        }
        
        // Si el ticket estaba cerrado, reabrirlo
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'in_progress']);
        }
        
        // Notificar según sea necesario
        if ($isFromStaff) {
            // Notificar al cliente
            $ticket->user->notify(new NewReplyAdded($ticket, $reply));
        } else {
            // Notificar al staff
            $staffUsers = User::whereIn('role', ['admin', 'super_admin', 'support'])->get();
            Notification::send($staffUsers, new ClientReplied($ticket, $reply));
        }
    }
    
    /**
     * Extraer ID de ticket del asunto.
     */
    private function extractTicketIdFromSubject($subject)
    {
        // Patrón de búsqueda para ID como [Ticket #123]
        if (preg_match('/\[Ticket #(\d+)\]/', $subject, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Procesar archivos adjuntos de un email.
     */
    private function processAttachments($email, Ticket $ticket, ?Reply $reply = null): void
    {
        foreach ($email->attachments as $attachment) {
            $path = 'ticket-attachments/' . date('Y/m/d') . '/' . Str::uuid();
            Storage::put($path, $attachment->content);
            
            if ($reply) {
                // Crear adjunto para la respuesta
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'reply_id' => $reply->id,
                    'file_name' => $attachment->name,
                    'file_path' => $path,
                    'file_size' => strlen($attachment->content),
                    'file_type' => $attachment->contentType
                ]);
            } else {
                // Crear adjunto para el ticket
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $attachment->name,
                    'file_path' => $path,
                    'file_size' => strlen($attachment->content),
                    'file_type' => $attachment->contentType
                ]);
            }
        }
    }
    
    /**
     * Convertir HTML a texto para almacenar en la base de datos.
     */
    private function convertHtmlToText($html)
    {
        // En un sistema real, aquí se utilizaría una biblioteca para
        // sanitizar y convertir el HTML a un formato seguro
        return strip_tags($html);
    }
}
