<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRolePermissions extends ListRecords
{
    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No permitimos crear permisos desde la interfaz, solo editar los existentes
        ];
    }
}
