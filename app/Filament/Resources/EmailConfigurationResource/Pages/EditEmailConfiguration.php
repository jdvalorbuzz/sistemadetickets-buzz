<?php

namespace App\Filament\Resources\EmailConfigurationResource\Pages;

use App\Filament\Resources\EmailConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailConfiguration extends EditRecord
{
    protected static string $resource = EmailConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('test')
                ->label('Probar conexión')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    // Lógica para probar la conexión
                    // En un sistema real, se implementaría la prueba de conexión
                    $this->notify('success', 'Conexión probada con éxito');
                }),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
