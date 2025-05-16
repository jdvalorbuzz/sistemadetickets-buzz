<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'is_from_admin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_from_admin' => 'boolean',
    ];

    /**
     * Get the ticket that owns the reply.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the reply.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the attachments for this reply.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class)->where('context', 'reply');
    }
}
