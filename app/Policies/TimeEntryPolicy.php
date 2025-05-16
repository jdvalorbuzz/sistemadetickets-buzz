<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeEntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any time entries.
     */
    public function viewAny(User $user)
    {
        // Solo staff puede ver registros de tiempo
        return $user->isStaff();
    }

    /**
     * Determine whether the user can view the time entry.
     */
    public function view(User $user, TimeEntry $timeEntry)
    {
        // Solo el creador o administradores pueden ver el detalle
        return $user->id === $timeEntry->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create time entries.
     */
    public function create(User $user)
    {
        // Solo staff puede crear registros de tiempo
        return $user->isStaff();
    }

    /**
     * Determine whether the user can update the time entry.
     */
    public function update(User $user, TimeEntry $timeEntry)
    {
        // Solo el creador o administradores pueden actualizar
        return $user->id === $timeEntry->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the time entry.
     */
    public function delete(User $user, TimeEntry $timeEntry)
    {
        // Solo el creador o administradores pueden eliminar
        return $user->id === $timeEntry->user_id || $user->isAdmin();
    }
}
