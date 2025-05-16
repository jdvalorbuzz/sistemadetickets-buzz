<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\TimeEntry;
use App\Models\Mention;
use App\Models\Department;
use App\Policies\TicketPolicy;
use App\Policies\TimeEntryPolicy;
use App\Policies\MentionPolicy;
use App\Policies\DepartmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ticket::class => TicketPolicy::class,
        TimeEntry::class => TimeEntryPolicy::class,
        Mention::class => MentionPolicy::class,
        Department::class => DepartmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir Gate para gestionar el Kanban (basado en permisos)
        Gate::define('manage_kanban', function ($user) {
            return $user->hasPermission('manage_kanban_statuses');
        });
        
        // Definir helper para comprobar si un usuario es staff (admin, super_admin o support)
        Gate::define('is_staff', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'support']);
        });
        
        // Registrar todos los permisos como Gates para facilitar su uso en el sistema
        Gate::before(function ($user, $ability) {
            // Super Admin siempre tiene acceso a todo
            if ($user->isSuperAdmin()) {
                return true;
            }
            
            // Comprobar si el ability es un permiso definido
            if (strpos($ability, 'permission:') === 0) {
                $permissionName = substr($ability, 11);
                return $user->hasPermission($permissionName);
            }
            
            return null; // continuar con el siguiente Gate
        });
    }
}
