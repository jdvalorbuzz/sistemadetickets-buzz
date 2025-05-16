<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    /**
     * Mostrar la página de gestión de permisos.
     */
    public function index()
    {
        // Solo super admin puede gestionar permisos
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'No tienes permiso para gestionar permisos del sistema');
        }
        
        // Obtener todos los permisos agrupados por categoría
        $permissionsByCategory = Permission::orderBy('category')->orderBy('display_name')->get()
            ->groupBy('category');
            
        // Obtener los roles disponibles en el sistema
        $roles = ['admin', 'support', 'client'];
        
        // Obtener permisos asignados por rol
        $rolePermissions = [];
        foreach ($roles as $role) {
            $permissions = RolePermission::where('role', $role)->pluck('permission_id')->toArray();
            $rolePermissions[$role] = $permissions;
        }
        
        return view('admin.permissions.index', compact('permissionsByCategory', 'roles', 'rolePermissions'));
    }
    
    /**
     * Actualizar los permisos de un rol.
     */
    public function updateRolePermissions(Request $request)
    {
        // Solo super admin puede gestionar permisos
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'No tienes permiso para gestionar permisos del sistema');
        }
        
        $validated = $request->validate([
            'role' => 'required|string|in:admin,support,client',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role = $validated['role'];
        $permissionIds = $validated['permissions'] ?? [];
        
        // Eliminar permisos actuales para este rol
        RolePermission::where('role', $role)->delete();
        
        // Asignar nuevos permisos
        foreach ($permissionIds as $permissionId) {
            RolePermission::create([
                'role' => $role,
                'permission_id' => $permissionId,
            ]);
        }
        
        return redirect()->back()->with('success', "Permisos actualizados para el rol '{$role}'");
    }
    
    /**
     * Crear un nuevo permiso.
     */
    public function store(Request $request)
    {
        // Solo super admin puede gestionar permisos
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'No tienes permiso para gestionar permisos del sistema');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
        ]);
        
        Permission::create($validated);
        
        return redirect()->back()->with('success', 'Permiso creado correctamente');
    }
    
    /**
     * Actualizar un permiso existente.
     */
    public function update(Request $request, Permission $permission)
    {
        // Solo super admin puede gestionar permisos
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'No tienes permiso para gestionar permisos del sistema');
        }
        
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
        ]);
        
        $permission->update($validated);
        
        return redirect()->back()->with('success', 'Permiso actualizado correctamente');
    }
    
    /**
     * Eliminar un permiso.
     */
    public function destroy(Permission $permission)
    {
        // Solo super admin puede gestionar permisos
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'No tienes permiso para gestionar permisos del sistema');
        }
        
        // Eliminar las asignaciones de rol para este permiso
        RolePermission::where('permission_id', $permission->id)->delete();
        
        // Eliminar el permiso
        $permission->delete();
        
        return redirect()->back()->with('success', 'Permiso eliminado correctamente');
    }
}
