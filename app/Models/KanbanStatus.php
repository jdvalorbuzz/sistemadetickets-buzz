<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'order',
        'is_default',
        'department_id'
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    /**
     * Get the department that owns the kanban status.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get the tickets associated with this kanban status.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'kanban_status_id');
    }
}
