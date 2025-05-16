<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RateTicket extends Page
{
    protected static string $resource = TicketResource::class;
    
    protected static string $view = 'filament.resources.ticket-resource.pages.rate-ticket';
    
    public ?Ticket $ticket = null;
    
    public ?array $data = [];
    
    public function mount(int | string $record): void
    {
        $this->ticket = Ticket::find($record);
        
        if (! $this->ticket) {
            abort(404);
        }
        
        // Verificar que el usuario sea el propietario del ticket
        if (Auth::id() !== $this->ticket->user_id) {
            abort(403);
        }
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Radio::make('rating')
                            ->label('¿Cómo calificarías el servicio recibido?')
                            ->options([
                                1 => '1 - Muy insatisfecho',
                                2 => '2 - Insatisfecho',
                                3 => '3 - Neutral',
                                4 => '4 - Satisfecho',
                                5 => '5 - Muy satisfecho',
                            ])
                            ->columns(5)
                            ->required()
                            ->default(5),
                        
                        Forms\Components\Textarea::make('feedback')
                            ->label('Comentarios adicionales (opcional)')
                            ->placeholder('¿Qué te gustó? ¿Qué podríamos mejorar?')
                            ->maxLength(1000),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }
    
    public function submit(): void
    {
        $data = $this->form->getState();
        
        \App\Models\TicketRating::updateOrCreate(
            [
                'ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $data['rating'],
                'feedback' => $data['feedback'] ?? null,
            ]
        );
        
        Notification::make()
            ->title('¡Gracias por tu calificación!')
            ->success()
            ->send();
        
        $this->redirect(TicketResource::getUrl('index'));
    }
}
