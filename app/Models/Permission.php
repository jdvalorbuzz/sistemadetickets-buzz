<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
    ];

    /**
     * Obtener los roles que tienen este permiso.
     */
    public function roles()
    {
        return $this->belongsToMany(
            'App\Models\User',
            'role_permissions',
            'permission_id',
            'role',
            'id',
            'role'
        );
    }
}
