<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los permisos del sistema agrupados por categoría
        $permissionsByCategory = [
            'Tickets' => [
                ['name' => 'view_tickets', 'display_name' => 'Ver Tickets'],
                ['name' => 'create_tickets', 'display_name' => 'Crear Tickets'],
                ['name' => 'update_tickets', 'display_name' => 'Actualizar Tickets'],
                ['name' => 'delete_tickets', 'display_name' => 'Eliminar Tickets'],
                ['name' => 'reply_to_tickets', 'display_name' => 'Responder Tickets'],
                ['name' => 'close_tickets', 'display_name' => 'Cerrar Tickets'],
                ['name' => 'reopen_tickets', 'display_name' => 'Reabrir Tickets'],
                ['name' => 'assign_tickets', 'display_name' => 'Asignar Tickets'],
            ],
            'Departamentos' => [
                ['name' => 'view_departments', 'display_name' => 'Ver Departamentos'],
                ['name' => 'create_departments', 'display_name' => 'Crear Departamentos'],
                ['name' => 'update_departments', 'display_name' => 'Actualizar Departamentos'],
                ['name' => 'delete_departments', 'display_name' => 'Eliminar Departamentos'],
            ],
            'Escalamientos' => [
                ['name' => 'view_escalation_rules', 'display_name' => 'Ver Reglas de Escalamiento'],
                ['name' => 'create_escalation_rules', 'display_name' => 'Crear Reglas de Escalamiento'],
                ['name' => 'update_escalation_rules', 'display_name' => 'Actualizar Reglas de Escalamiento'],
                ['name' => 'delete_escalation_rules', 'display_name' => 'Eliminar Reglas de Escalamiento'],
                ['name' => 'view_escalation_logs', 'display_name' => 'Ver Registros de Escalamiento'],
            ],
            'Email' => [
                ['name' => 'view_email_configs', 'display_name' => 'Ver Configuraciones de Email'],
                ['name' => 'create_email_configs', 'display_name' => 'Crear Configuraciones de Email'],
                ['name' => 'update_email_configs', 'display_name' => 'Actualizar Configuraciones de Email'],
                ['name' => 'delete_email_configs', 'display_name' => 'Eliminar Configuraciones de Email'],
            ],
            'Kanban' => [
                ['name' => 'view_kanban', 'display_name' => 'Ver Tablero Kanban'],
                ['name' => 'update_kanban_status', 'display_name' => 'Actualizar Estado Kanban'],
                ['name' => 'manage_kanban_statuses', 'display_name' => 'Gestionar Estados Kanban'],
            ],
            'Tiempo' => [
                ['name' => 'track_time', 'display_name' => 'Registrar Tiempo'],
                ['name' => 'view_time_entries', 'display_name' => 'Ver Registros de Tiempo'],
                ['name' => 'edit_time_entries', 'display_name' => 'Editar Registros de Tiempo'],
                ['name' => 'delete_time_entries', 'display_name' => 'Eliminar Registros de Tiempo'],
            ],
            'Reportes' => [
                ['name' => 'view_reports', 'display_name' => 'Ver Reportes'],
                ['name' => 'export_reports', 'display_name' => 'Exportar Reportes'],
            ],
            'Usuarios' => [
                ['name' => 'view_users', 'display_name' => 'Ver Usuarios'],
                ['name' => 'create_users', 'display_name' => 'Crear Usuarios'],
                ['name' => 'update_users', 'display_name' => 'Actualizar Usuarios'],
                ['name' => 'delete_users', 'display_name' => 'Eliminar Usuarios'],
            ],
        ];
        
        // Crear todos los permisos
        foreach ($permissionsByCategory as $category => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'] ?? null,
                    'category' => $category,
                ]);
            }
        }
        
        // Asignar permisos predeterminados para cada rol
        
        // Permisos para administradores (todos excepto gestión de permisos)
        $adminPermissions = Permission::whereNotIn('name', ['manage_permissions'])->pluck('id');
        foreach ($adminPermissions as $permissionId) {
            RolePermission::create([
                'role' => 'admin',
                'permission_id' => $permissionId,
            ]);
        }
        
        // Permisos para soporte (limitados)
        $supportPermissions = [
            'view_tickets', 'create_tickets', 'update_tickets', 'reply_to_tickets', 
            'close_tickets', 'reopen_tickets', 'view_kanban', 'update_kanban_status', 
            'track_time', 'view_time_entries', 'edit_time_entries',
            'view_reports'
        ];
        
        $supportPermissionIds = Permission::whereIn('name', $supportPermissions)->pluck('id');
        foreach ($supportPermissionIds as $permissionId) {
            RolePermission::create([
                'role' => 'support',
                'permission_id' => $permissionId,
            ]);
        }
        
        // Permisos para clientes (muy limitados)
        $clientPermissions = [
            'view_tickets', 'create_tickets', 'reply_to_tickets', 
            'view_kanban'
        ];
        
        $clientPermissionIds = Permission::whereIn('name', $clientPermissions)->pluck('id');
        foreach ($clientPermissionIds as $permissionId) {
            RolePermission::create([
                'role' => 'client',
                'permission_id' => $permissionId,
            ]);
        }
    }
}
