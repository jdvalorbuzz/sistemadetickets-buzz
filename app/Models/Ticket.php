<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'closed_at',
        'closed_by',
        'department_id', // Añadido campo para departamento
        'assigned_to',   // Campo para usuario asignado
        'email_subject', // Para integración con email
        'email_message_id', // ID del mensaje de correo relacionado
        'source',        // web, email, api
        'kanban_status_id', // Para sistema Kanban
        'kanban_order',     // Orden en la columna Kanban
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that closed the ticket.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Get the department associated with the ticket.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the replies associated with the ticket.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
    
    /**
     * Get the tags associated with the ticket.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    
    /**
     * Get the ratings for this ticket.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(TicketRating::class);
    }
    
    /**
     * Get the average rating for this ticket.
     * 
     * @return float|null
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating');
    }
    
    /**
     * Get the attachments for this ticket.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }
    
    /**
     * Get the user assigned to this ticket.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the escalation logs for this ticket.
     */
    public function escalationLogs(): HasMany
    {
        return $this->hasMany(EscalationLog::class);
    }
    
    /**
     * Get the last activity (reply) for this ticket.
     */
    public function lastActivity()
    {
        return $this->hasOne(Reply::class)->latest();
    }
    
    /**
     * Check if ticket is eligible for escalation.
     *
     * @return bool
     */
    public function isEligibleForEscalation(): bool
    {
        // Verificar elegibilidad para escalar
        return !in_array($this->status, ['closed', 'archived']);
    }
    
    /**
     * Obtener el último ID de mensaje de email relacionado con este ticket
     * 
     * @return string|null
     */
    public function getLastEmailMessageId()
    {
        return $this->replies()
            ->whereNotNull('email_message_id')
            ->latest()
            ->value('email_message_id');
    }
    
    /**
     * Get the kanban status associated with the ticket.
     */
    public function kanbanStatus()
    {
        return $this->belongsTo(KanbanStatus::class);
    }
    
    /**
     * Get the time entries for this ticket.
     */
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
    
    /**
     * Get the total time in minutes spent on this ticket.
     * 
     * @return int
     */
    public function getTotalTimeAttribute()
    {
        return $this->timeEntries->sum('minutes');
    }
    
    /**
     * Get the total billable time in minutes spent on this ticket.
     * 
     * @return int
     */
    public function getTotalBillableTimeAttribute()
    {
        return $this->timeEntries
            ->where('is_billable', true)
            ->sum('minutes');
    }
    
    /**
     * Get the attachments that belong directly to the ticket, not to a reply.
     */
    public function ticketAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->where('context', 'ticket');
    }

    /**
     * Scope a query to only include open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include in progress tickets.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include archived tickets.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
    
    /**
     * Scope a query to filter tickets by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
