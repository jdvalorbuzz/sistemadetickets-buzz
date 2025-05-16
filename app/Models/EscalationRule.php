<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'priority',
        'hours_until_escalation',
        'escalate_to_user_id',
        'notify_supervisor',
        'is_active'
    ];
    
    protected $casts = [
        'notify_supervisor' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    // Relaciones
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function escalateToUser()
    {
        return $this->belongsTo(User::class, 'escalate_to_user_id');
    }
    
    public function escalationLogs()
    {
        return $this->hasMany(EscalationLog::class);
    }
}
