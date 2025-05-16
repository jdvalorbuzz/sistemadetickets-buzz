<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mentionable_id',
        'mentionable_type',
        'mentioned_by',
        'read_at'
    ];
    
    protected $casts = [
        'read_at' => 'datetime'
    ];
    
    /**
     * Get the model that was mencionado.
     */
    public function mentionable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get the user that was mentioned.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user that mentioned.
     */
    public function mentionedBy()
    {
        return $this->belongsTo(User::class, 'mentioned_by');
    }
    
    /**
     * Scope for unread mentions.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
