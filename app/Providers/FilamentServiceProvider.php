<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar activos CSS personalizados
        FilamentAsset::register([
            Css::make('theme', __DIR__ . '/../../resources/css/filament/admin/theme.css'),
        ]);
    }
}
