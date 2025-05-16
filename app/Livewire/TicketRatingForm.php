<?php

namespace App\Livewire;

use Livewire\Component;

class TicketRatingForm extends Component
{
    public ?int $ticketId = null;
    public int $rating = 5;
    public string $feedback = '';
    
    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'feedback' => 'nullable|string|max:1000',
    ];
    
    public function mount($ticketId = null)
    {
        $this->ticketId = $ticketId;
    }
    
    public function saveRating()
    {
        $this->validate();
        
        if (!$this->ticketId) {
            return;
        }
        
        $ticket = \App\Models\Ticket::find($this->ticketId);
        
        if (!$ticket) {
            return;
        }
        
        // Verificar que el usuario sea el propietario del ticket
        if (auth()->id() !== $ticket->user_id) {
            return;
        }
        
        // Crear o actualizar la calificación
        \App\Models\TicketRating::updateOrCreate(
            [
                'ticket_id' => $this->ticketId,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $this->rating,
                'feedback' => $this->feedback,
            ]
        );
        
        // Mostrar mensaje de éxito
        $this->dispatch('notify', [
            'message' => '¡Gracias por tu calificación!',
            'type' => 'success',
        ]);
        
        // Resetear el formulario
        $this->reset(['rating', 'feedback']);
        
        // Cerrar el modal
        $this->dispatch('close-modal');
    }
    
    public function render()
    {
        return view('livewire.ticket-rating-form');
    }
}
