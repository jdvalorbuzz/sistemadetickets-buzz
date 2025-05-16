<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_type',  // IMAP, API, etc.
        'incoming_server',
        'incoming_port',
        'incoming_encryption',
        'incoming_username',
        'incoming_password',
        'outgoing_type',  // SMTP, API, etc.
        'outgoing_server',
        'outgoing_port',
        'outgoing_encryption',
        'outgoing_username',
        'outgoing_password',
        'from_email',
        'from_name',
        'department_id',
        'polling_interval'  // en minutos
    ];
    
    protected $casts = [
        'incoming_password' => 'encrypted',
        'outgoing_password' => 'encrypted',
        'polling_interval' => 'integer',
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
