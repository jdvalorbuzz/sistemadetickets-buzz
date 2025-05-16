<div>
    <div class="filament-section bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 flex justify-between items-center">
            <h2 class="text-xl font-bold tracking-tight">
                Archivos adjuntos
            </h2>
            
            <div class="flex items-center gap-3">
                <button 
                    type="button"
                    wire:click="toggleRepliesAttachments"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-500 flex items-center gap-1"
                >
                    @if($showRepliesAttachments)
                        @svg('heroicon-o-eye-slash', 'w-4 h-4')
                        Ocultar adjuntos de respuestas
                    @else
                        @svg('heroicon-o-eye', 'w-4 h-4')
                        Mostrar adjuntos de respuestas
                    @endif
                </button>
            </div>
        </div>
        
        <div class="p-4">
            @if($allAttachments->count() > 0)
                <div class="space-y-4">
                    @if($ticketAttachments->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Adjuntos del ticket ({{ $ticketAttachments->count() }})
                            </h3>
                            <ul class="space-y-2">
                                @foreach($ticketAttachments as $attachment)
                                    <li class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                        @php
                                            $fileType = $attachment->file_type ?? 'application/octet-stream';
                                            $icon = match(true) {
                                                str_starts_with($fileType, 'image/') => 'heroicon-o-photo',
                                                str_contains($fileType, 'pdf') => 'heroicon-o-document-text',
                                                str_contains($fileType, 'word') || str_contains($fileType, 'doc') => 'heroicon-o-document',
                                                str_contains($fileType, 'excel') || str_contains($fileType, 'xls') => 'heroicon-o-table-cells',
                                                str_contains($fileType, 'text') => 'heroicon-o-document',
                                                default => 'heroicon-o-paper-clip'
                                            };
                                        @endphp
                                        
                                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-600 rounded">
                                            @svg($icon, 'w-5 h-5 text-gray-500 dark:text-gray-400')
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <a 
                                                href="{{ asset('storage/' . $attachment->file_path) }}" 
                                                target="_blank" 
                                                class="text-sm font-medium text-primary-600 dark:text-primary-500 hover:underline truncate block"
                                            >
                                                {{ $attachment->file_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attachment->formatted_file_size }} • 
                                                <span title="{{ $attachment->created_at }}">{{ $attachment->created_at->diffForHumans() }}</span>
                                                por {{ $attachment->user->name }}
                                            </p>
                                        </div>
                                        
                                        <a 
                                            href="{{ asset('storage/' . $attachment->file_path) }}" 
                                            download="{{ $attachment->file_name }}"
                                            class="flex-shrink-0 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-500"
                                            title="Descargar"
                                        >
                                            @svg('heroicon-o-arrow-down-tray', 'w-5 h-5')
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($showRepliesAttachments && $replyAttachments->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Adjuntos de respuestas ({{ $replyAttachments->count() }})
                            </h3>
                            <ul class="space-y-2">
                                @foreach($replyAttachments as $attachment)
                                    <li class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                        @php
                                            $fileType = $attachment->file_type ?? 'application/octet-stream';
                                            $icon = match(true) {
                                                str_starts_with($fileType, 'image/') => 'heroicon-o-photo',
                                                str_contains($fileType, 'pdf') => 'heroicon-o-document-text',
                                                str_contains($fileType, 'word') || str_contains($fileType, 'doc') => 'heroicon-o-document',
                                                str_contains($fileType, 'excel') || str_contains($fileType, 'xls') => 'heroicon-o-table-cells',
                                                str_contains($fileType, 'text') => 'heroicon-o-document',
                                                default => 'heroicon-o-paper-clip'
                                            };
                                        @endphp
                                        
                                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-600 rounded">
                                            @svg($icon, 'w-5 h-5 text-gray-500 dark:text-gray-400')
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <a 
                                                href="{{ asset('storage/' . $attachment->file_path) }}" 
                                                target="_blank" 
                                                class="text-sm font-medium text-primary-600 dark:text-primary-500 hover:underline truncate block"
                                            >
                                                {{ $attachment->file_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attachment->formatted_file_size }} • 
                                                <span title="{{ $attachment->created_at }}">{{ $attachment->created_at->diffForHumans() }}</span>
                                                por {{ $attachment->user->name }} 
                                                <span class="ml-1 text-xs inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                                    En respuesta
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <a 
                                            href="{{ asset('storage/' . $attachment->file_path) }}" 
                                            download="{{ $attachment->file_name }}"
                                            class="flex-shrink-0 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-500"
                                            title="Descargar"
                                        >
                                            @svg('heroicon-o-arrow-down-tray', 'w-5 h-5')
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 mb-4">
                        @svg('heroicon-o-paper-clip', 'w-8 h-8')
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                        Sin archivos adjuntos
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No hay archivos adjuntos asociados a este ticket.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
{{-- Because she competes with no one, no one can compete with her. --}}
