<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Ticket::with(['user', 'closedBy'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Título',
            'Estado',
            'Prioridad',
            'Fecha de creación',
            'Cerrado por',
            'Fecha de cierre',
            'Tiempo de resolución (horas)',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($ticket): array
    {
        // Calcular tiempo de resolución
        $resolutionTime = null;
        if ($ticket->closed_at) {
            $created = new \DateTime($ticket->created_at);
            $closed = new \DateTime($ticket->closed_at);
            $interval = $created->diff($closed);
            $resolutionTime = ($interval->days * 24) + $interval->h + ($interval->i / 60);
        }

        return [
            $ticket->id,
            $ticket->user->name ?? 'N/A',
            $ticket->title,
            $this->formatStatus($ticket->status),
            $this->formatPriority($ticket->priority),
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->closedBy->name ?? 'N/A',
            $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : 'N/A',
            $resolutionTime ? round($resolutionTime, 2) : 'N/A',
        ];
    }
    
    /**
     * Format status for readability
     */
    private function formatStatus($status): string
    {
        return match($status) {
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'closed' => 'Cerrado',
            'archived' => 'Archivado',
            default => $status,
        };
    }
    
    /**
     * Format priority for readability
     */
    private function formatPriority($priority): string
    {
        return match($priority) {
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => $priority,
        };
    }
}
