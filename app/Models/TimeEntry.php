<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'description',
        'minutes',
        'started_at',
        'ended_at',
        'is_billable'
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_billable' => 'boolean'
    ];
    
    /**
     * Get the ticket that owns the time entry.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Get the user that created the time entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the duration of the time entry in minutes.
     */
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->ended_at) {
            return $this->started_at->diffInMinutes($this->ended_at);
        }
        
        return $this->minutes;
    }
}
