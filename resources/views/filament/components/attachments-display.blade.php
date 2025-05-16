@php
    $attachments = $attachments ?? [];
@endphp

@if(count($attachments) > 0)
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 bg-gray-50 dark:bg-gray-800">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            Archivos adjuntos ({{ count($attachments) }})
        </h3>
        <ul class="space-y-2">
            @foreach($attachments as $attachment)
                <li class="flex items-center gap-3">
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
                    
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded">
                        @svg($icon, 'w-5 h-5 text-gray-500 dark:text-gray-400')
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <a 
                            href="{{ $attachment->file_url }}" 
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
                        href="{{ $attachment->file_url }}" 
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
