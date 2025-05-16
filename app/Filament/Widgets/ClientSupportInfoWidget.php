<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ClientSupportInfoWidget extends Widget
{
    protected static ?int $sort = 5;
    
    // Mostrar en pantalla completa
    protected int | string | array $columnSpan = 'full';
    
    // Usar vista personalizada
    protected static string $view = 'filament.widgets.client-support-info-widget';
    
    /**
     * Solo visible para clientes
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }
}
