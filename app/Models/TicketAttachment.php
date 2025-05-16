<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'context',
        'reply_id',
    ];
    
    /**
     * Get the ticket that owns the attachment.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Get the user that uploaded the attachment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the reply that owns the attachment, if applicable.
     */
    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }
    
    /**
     * Get the file URL for this attachment.
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
    
    /**
     * Get the formatted file size (KB, MB, etc).
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' bytes';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        } else {
            return round($bytes / 1073741824, 2) . ' GB';
        }
    }
}
