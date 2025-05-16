<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo, // Color corporativo principal
                'secondary' => Color::Violet, // Color corporativo secundario
                'gray' => Color::Slate,
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->brandName(function () {
                if (!auth()->check()) return 'SoporteTickets';
                
                if (auth()->user()->isSuperAdmin()) return 'SoporteTickets - Super Admin';
                if (auth()->user()->isAdmin()) return 'SoporteTickets - Administrador';
                if (auth()->user()->isStaff() && !auth()->user()->isAdmin()) return 'SoporteTickets - Soporte';
                return 'Portal de Soporte Técnico';
            })
            ->favicon(asset('favicon.ico'))
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                'Tickets Management',
                'Administración',
                'Configuración',
            ])
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                    ->sort(1)
                    ->url(fn (): string => route('filament.admin.pages.dashboard'))
                    ->visible(fn (): bool => auth()->check() && auth()->user()->isStaff()),
                \Filament\Navigation\NavigationItem::make('Mis Tickets')
                    ->icon('heroicon-o-ticket')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.tickets.*'))
                    ->sort(1) // Primera opción para clientes
                    ->url(fn (): string => route('filament.admin.resources.tickets.index'))
                    ->visible(fn (): bool => auth()->check() && !auth()->user()->isAdmin()),
                    
                \Filament\Navigation\NavigationItem::make('Crear Nuevo Ticket')
                    ->icon('heroicon-o-plus-circle')
                    ->sort(2)
                    ->url(fn (): string => route('filament.admin.resources.tickets.create'))
                    ->visible(fn (): bool => auth()->check() && !auth()->user()->isAdmin()),
                \Filament\Navigation\NavigationItem::make('Ver sitio')
                    ->icon('heroicon-o-globe-alt')
                    ->sort(10)
                    ->url('/')
                    ->openUrlInNewTab(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
