<?php

namespace App\Filament\Resources\EscalationRuleResource\Pages;

use App\Filament\Resources\EscalationRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEscalationRule extends CreateRecord
{
    protected static string $resource = EscalationRuleResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
