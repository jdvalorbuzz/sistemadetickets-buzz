<?php

namespace App\Policies;

use App\Models\Mention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MentionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the mention.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mention  $mention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Mention $mention)
    {
        // Solo el usuario mencionado puede ver sus menciones
        return $user->id === $mention->user_id;
    }

    /**
     * Determine whether the user can mark the mention as read.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mention  $mention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsRead(User $user, Mention $mention)
    {
        // Solo el usuario mencionado puede marcar como leída
        return $user->id === $mention->user_id;
    }

    /**
     * Determine whether the user can delete the mention.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mention  $mention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mention $mention)
    {
        // Solo el usuario mencionado o un administrador puede eliminar la mención
        return $user->id === $mention->user_id || $user->role === 'super_admin';
    }
}
