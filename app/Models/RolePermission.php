<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * Obtener el permiso asociado.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
