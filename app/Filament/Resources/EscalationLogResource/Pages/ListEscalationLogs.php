<?php

namespace App\Filament\Resources\EscalationLogResource\Pages;

use App\Filament\Resources\EscalationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEscalationLogs extends ListRecords
{
    protected static string $resource = EscalationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No hay acciones de creación ya que los logs son generados por el sistema
        ];
    }
}
