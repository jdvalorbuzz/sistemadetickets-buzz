<?php

use App\Providers\FilamentPanelProvider;
use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Panels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the behavior of your Filament panels.
    |
    */

    'panels' => [
        'admin' => [
            'path' => 'admin',
            'profile' => true,
            'auth' => [
                'guard' => 'web',
            ],
            'middleware' => [
                'web',
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                Authenticate::class,
            ],
            // Cambiamos el color primario a nuestro color de marca
            'colors' => [
                'primary' => Color::hex('#fa4619'),
            ],
            // Definimos colores específicos para componentes
            'theme' => [
                'button' => '#fa4619',
                'sidebar' => '#f8fafc',
                'navbar' => '#ffffff',
                'text' => '#1a202c',
                'link' => '#fa4619',
                'link_hover' => '#e13d12',
            ],
        ],
    ],
];
