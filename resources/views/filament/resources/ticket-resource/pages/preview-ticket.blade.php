<x-filament::page>
    <!-- Información principal del ticket -->
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">
                Ticket #{{ $record->id }}: {{ $record->title }}
            </h2>
            
            @if($record->status !== 'closed')
                <div>
                    @if ($record->assigned_to === null)
                        <span class="inline-flex rounded-md shadow-sm">
                            <x-filament::button
                                color="success"
                                icon="heroicon-o-hand-raised"
                                wire:click="$set('record.assigned_to', {{ auth()->id() }})"
                            >
                                Tomar Ticket
                            </x-filament::button>
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <span class="mr-1">Asignado a:</span>
                            {{ optional($record->assignedTo)->name ?? 'Sin asignar' }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <dl class="grid grid-cols-3 gap-1">
                    <dt class="text-sm font-medium text-gray-500">Cliente:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">{{ $record->user->name }}</dd>
                    
                    <dt class="text-sm font-medium text-gray-500">Departamento:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">{{ $record->department->name }}</dd>
                    
                    <dt class="text-sm font-medium text-gray-500">Prioridad:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">
                        <span @class([
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            'bg-red-100 text-red-800' => $record->priority === 'high',
                            'bg-yellow-100 text-yellow-800' => $record->priority === 'medium',
                            'bg-green-100 text-green-800' => $record->priority === 'low',
                        ])>
                            {{ ucfirst($record->priority) }}
                        </span>
                    </dd>
                </dl>
            </div>
            <div>
                <dl class="grid grid-cols-3 gap-1">
                    <dt class="text-sm font-medium text-gray-500">Estado:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">
                        <span @class([
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            'bg-green-100 text-green-800' => $record->status === 'open',
                            'bg-blue-100 text-blue-800' => $record->status === 'in_progress',
                            'bg-gray-100 text-gray-800' => $record->status === 'closed',
                        ])>
                            {{ $record->status === 'open' ? 'Abierto' : ($record->status === 'in_progress' ? 'En Progreso' : 'Cerrado') }}
                        </span>
                    </dd>
                    
                    <dt class="text-sm font-medium text-gray-500">Creado:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">{{ $record->created_at->format('d/m/Y H:i') }}</dd>
                    
                    <dt class="text-sm font-medium text-gray-500">Última actualización:</dt>
                    <dd class="col-span-2 text-sm text-gray-900">{{ $record->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
        
        <!-- Descripción del ticket -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Descripción del Ticket</h3>
            <div class="prose prose-sm max-w-none bg-white p-4 rounded-lg border border-gray-200">
                {!! $record->description !!}
            </div>
        </div>
        
        <!-- Archivos adjuntos del ticket -->
        @if($record->attachments && $record->attachments->count() > 0)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Archivos Adjuntos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($record->attachments as $attachment)
                        <a href="{{ asset('storage/' . $attachment->path) }}" 
                           class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50"
                           target="_blank">
                            <x-filament::icon
                                alias="heroicon-o-document"
                                class="w-5 h-5 mr-2 text-primary-500"
                            />
                            <span class="text-sm font-medium text-gray-700 truncate">
                                {{ $attachment->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>

    <!-- Historial de respuestas -->
    <x-filament::section heading="Historial de Respuestas" class="mt-6">
        @if($record->replies->count() > 0)
            <div class="space-y-6">
                @foreach($record->replies as $reply)
                    <div @class([
                        'p-4 rounded-lg shadow-sm',
                        'bg-primary-50' => $reply->user_id === $record->user_id,
                        'bg-gray-50' => $reply->user_id !== $record->user_id,
                        'border border-gray-200' => !$reply->is_system,
                        'border border-gray-300 bg-gray-100' => $reply->is_system,
                    ])>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <span class="font-medium">{{ $reply->user->name }}</span>
                                @if($reply->is_system)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-300 text-gray-800">
                                        Sistema
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="prose prose-sm max-w-none">
                            {!! $reply->content !!}
                        </div>
                        
                        <!-- Archivos adjuntos de la respuesta -->
                        @if($reply->attachments && $reply->attachments->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Archivos adjuntos:</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($reply->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->path) }}"
                                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200"
                                           target="_blank">
                                            <x-filament::icon
                                                alias="heroicon-o-paper-clip"
                                                class="w-4 h-4 mr-1 text-gray-500"
                                            />
                                            {{ $attachment->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-filament::icon
                            alias="heroicon-o-information-circle"
                            class="w-5 h-5 text-blue-400"
                        />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            No hay respuestas aún para este ticket.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
    
    <!-- Formulario de respuesta -->
    @if($record->status !== 'closed')
        <x-filament::section heading="Responder" class="mt-6">
            <form wire:submit.prevent="submitReply">
                <div class="space-y-4">
                    {{ $this->getReplyForm() }}
                    
                    <div class="mt-4 flex justify-end space-x-2">
                        <x-filament::button
                            type="submit"
                        >
                            Enviar Respuesta
                        </x-filament::button>
                        
                        <x-filament::button
                            type="button"
                            color="success"
                            wire:click="submitReplyAndClose"
                        >
                            Responder y Cerrar
                        </x-filament::button>
                    </div>
                </div>
            </form>
            
            <div class="mt-2 text-xs text-gray-500">
                Para mencionar a un usuario, utiliza @ seguido del nombre (ej: @soporte).
            </div>
        </x-filament::section>
    @else
        <x-filament::section class="mt-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-filament::icon
                            alias="heroicon-o-exclamation-circle"
                            class="w-5 h-5 text-yellow-400"
                        />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Este ticket está cerrado. No se pueden añadir más respuestas.
                        </p>
                        <div class="mt-2">
                            <x-filament::button
                                type="button"
                                color="warning"
                                icon="heroicon-o-arrow-path"
                                wire:click="$set('record.status', 'in_progress')"
                            >
                                Reabrir Ticket
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament::page>
