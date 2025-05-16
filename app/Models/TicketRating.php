<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRating extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'rating',
        'feedback',
    ];
    
    /**
     * Get the ticket that was rated.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Get the user who submitted the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
