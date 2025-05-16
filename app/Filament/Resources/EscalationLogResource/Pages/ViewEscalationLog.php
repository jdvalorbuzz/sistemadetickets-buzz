<?php

namespace App\Filament\Resources\EscalationLogResource\Pages;

use App\Filament\Resources\EscalationLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewEscalationLog extends ViewRecord
{
    protected static string $resource = EscalationLogResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->role === 'super_admin'),
        ];
    }
}
