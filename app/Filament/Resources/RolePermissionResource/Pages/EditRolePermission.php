<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditRolePermission extends EditRecord
{
    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Obtener los roles actuales que tienen este permiso
        $roles = DB::table('role_permissions')
            ->where('permission_id', $this->record->id)
            ->pluck('role')
            ->toArray();
            
        // Preparar los datos para el formulario
        $data['roles'] = [
            'admin' => in_array('admin', $roles),
            'support' => in_array('support', $roles),
            'client' => in_array('client', $roles),
        ];
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Obtener los roles del formulario
        $roles = $this->data['roles'] ?? [];
        
        // Eliminar asignaciones actuales para este permiso
        DB::table('role_permissions')
            ->where('permission_id', $this->record->id)
            ->delete();
            
        // Crear nuevas asignaciones
        foreach ($roles as $role => $hasPermission) {
            if ($hasPermission) {
                DB::table('role_permissions')->insert([
                    'permission_id' => $this->record->id,
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
