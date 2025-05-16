<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketAttachment;

class TicketAttachments extends Component
{
    public $ticketId;
    public $showRepliesAttachments = true;
    
    public function mount($ticketId = null)
    {
        $this->ticketId = $ticketId;
    }
    
    public function render()
    {
        if (!$this->ticketId) {
            return view('livewire.ticket-attachments', [
                'ticket' => null,
                'ticketAttachments' => collect([]),
                'replyAttachments' => collect([]),
                'allAttachments' => collect([]),
            ]);
        }
        
        $ticket = Ticket::with(['ticketAttachments.user'])->find($this->ticketId);
        
        if (!$ticket) {
            return view('livewire.ticket-attachments', [
                'ticket' => null,
                'ticketAttachments' => collect([]),
                'replyAttachments' => collect([]),
                'allAttachments' => collect([]),
            ]);
        }
        
        $ticketAttachments = $ticket->ticketAttachments;
        
        $replyAttachments = collect([]);
        if ($this->showRepliesAttachments) {
            $replyAttachments = TicketAttachment::with(['user'])
                ->where('ticket_id', $this->ticketId)
                ->where('context', 'reply')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $allAttachments = $ticketAttachments->concat($replyAttachments);
        
        return view('livewire.ticket-attachments', [
            'ticket' => $ticket,
            'ticketAttachments' => $ticketAttachments,
            'replyAttachments' => $replyAttachments,
            'allAttachments' => $allAttachments,
        ]);
    }
    
    public function toggleRepliesAttachments()
    {
        $this->showRepliesAttachments = !$this->showRepliesAttachments;
    }
}
