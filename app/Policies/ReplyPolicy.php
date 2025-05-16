<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Auth\Access\Response;

class ReplyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tanto administradores como clientes pueden ver respuestas
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reply $reply): bool
    {
        // Administradores pueden ver cualquier respuesta
        if ($user->isAdmin()) {
            return true;
        }
        
        // Clientes solo pueden ver respuestas de sus propios tickets
        // Verificamos la relación ticket -> usuario
        $ticket = $reply->ticket;
        return $ticket && $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Todos los usuarios autenticados pueden crear respuestas
        return true;
    }
    
    /**
     * Determina si el usuario puede responder a un ticket específico.
     */
    public function replyTo(User $user, Ticket $ticket): bool
    {
        // Administradores pueden responder a cualquier ticket activo
        if ($user->isAdmin()) {
            return !in_array($ticket->status, ['archived']);
        }
        
        // Clientes solo pueden responder a sus propios tickets si no están archivados o cerrados
        return $ticket->user_id === $user->id && 
               !in_array($ticket->status, ['archived', 'closed']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reply $reply): bool
    {
        // Administradores pueden editar cualquier respuesta
        if ($user->isAdmin()) {
            return true;
        }
        
        // Usuarios solo pueden editar sus propias respuestas si el ticket está activo
        $ticket = $reply->ticket;
        return $user->id === $reply->user_id && 
               $ticket && 
               !in_array($ticket->status, ['closed', 'archived']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reply $reply): bool
    {
        // Administradores pueden eliminar cualquier respuesta
        if ($user->isAdmin()) {
            return true;
        }
        
        // Usuarios solo pueden eliminar sus propias respuestas si el ticket está activo
        $ticket = $reply->ticket;
        return $user->id === $reply->user_id && 
               $ticket && 
               !in_array($ticket->status, ['closed', 'archived']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reply $reply): bool
    {
        // Solo los administradores pueden restaurar respuestas
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reply $reply): bool
    {
        // Solo los administradores pueden eliminar permanentemente respuestas
        return $user->isAdmin();
    }
}
