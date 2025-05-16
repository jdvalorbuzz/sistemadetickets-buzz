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
