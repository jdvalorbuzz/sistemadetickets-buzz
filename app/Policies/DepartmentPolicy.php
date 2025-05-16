<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any departments.
     */
    public function viewAny(User $user): bool
    {
        // Solo administradores pueden ver la lista de departamentos
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the department.
     */
    public function view(User $user, Department $department): bool
    {
        // Solo administradores pueden ver detalles de departamentos
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create departments.
     */
    public function create(User $user): bool
    {
        // Solo administradores pueden crear departamentos
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the department.
     */
    public function update(User $user, Department $department): bool
    {
        // Solo administradores pueden actualizar departamentos
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the department.
     */
    public function delete(User $user, Department $department): bool
    {
        // Solo super administradores pueden eliminar departamentos
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the department.
     */
    public function restore(User $user, Department $department): bool
    {
        // Solo super administradores pueden restaurar departamentos
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the department.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        // Solo super administradores pueden eliminar permanentemente departamentos
        return $user->isSuperAdmin();
    }
}
