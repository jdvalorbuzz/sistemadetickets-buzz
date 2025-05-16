<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
        'description',
    ];
    
    /**
     * Los tickets que tienen esta etiqueta
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }
}
