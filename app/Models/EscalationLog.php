<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'previous_user_id',
        'escalated_to_user_id',
        'reason',
        'escalation_rule_id'
    ];
    
    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    public function previousUser()
    {
        return $this->belongsTo(User::class, 'previous_user_id');
    }
    
    public function escalatedToUser()
    {
        return $this->belongsTo(User::class, 'escalated_to_user_id');
    }
    
    public function escalationRule()
    {
        return $this->belongsTo(EscalationRule::class);
    }
}
