<x-filament-panels::page>
    <div class="space-y-6">
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm overflow-hidden p-6 dark:bg-gray-800">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Califica tu experiencia
            </h2>
            
            <div class="mb-6">
                <p class="text-gray-500 dark:text-gray-400">
                    Tu opinión es importante para nosotros. Ayúdanos a mejorar nuestro servicio calificando tu experiencia con el ticket <strong>#{{ $ticket->id }}</strong>: {{ $ticket->title }}
                </p>
            </div>
            
            <form wire:submit="submit">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button
                        type="submit"
                        color="primary"
                    >
                        Enviar calificación
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
