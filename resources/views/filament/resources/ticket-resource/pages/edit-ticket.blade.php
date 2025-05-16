<x-filament-panels::page
    :class="\\Illuminate\\Support\\Arr::toCssClasses([
        'filament-resources-edit-record-page',
        'filament-resources-' . str_replace('/', '-', config('filament.path')) . '-edit-record-page',
    ])"
>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <!-- Sección de archivos adjuntos -->
    <div class="mt-6">
        <h2 class="text-xl font-bold tracking-tight mb-4">Archivos adjuntos</h2>
        @livewire('ticket-attachments', ['ticketId' => $this->record->id])
    </div>

    <x-filament::section>
        {{ $this->relationManagers }}
    </x-filament::section>
</x-filament-panels::page>
