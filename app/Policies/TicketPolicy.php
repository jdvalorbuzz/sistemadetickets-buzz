<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Todo el personal y clientes pueden ver tickets
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Administradores y soporte pueden ver cualquier ticket
        if ($user->isStaff()) {
            return true;
        }
        
        // Clientes solo pueden ver sus propios tickets
        return $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tanto administradores, soporte como clientes pueden crear tickets
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Solo los administradores pueden editar tickets (información base)
        // El personal de soporte no puede editar, solo responder
        if ($user->isAdmin()) {
            return true;
        }
        
        // Clientes solo pueden actualizar sus propios tickets si están abiertos o en progreso
        return $user->id === $ticket->user_id && 
               !in_array($ticket->status, ['closed', 'archived']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Solo los administradores pueden eliminar tickets
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        // Solo los administradores pueden restaurar tickets
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        // Solo los administradores pueden eliminar permanentemente tickets
        return $user->isAdmin();
    }
}
