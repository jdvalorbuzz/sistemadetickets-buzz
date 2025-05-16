<x-filament-panels::page>
    <div class="space-y-6">
        <div class="filament-stats-overview-widget p-2 bg-white rounded-xl shadow dark:bg-gray-800">
            <div class="p-4 flex items-center justify-between space-x-4">
                <h2 class="text-lg font-bold tracking-tight">Reportes y Análisis de Tickets</h2>
                
                <div class="flex items-center space-x-2">
                    <x-filament::button color="gray" icon="heroicon-o-document-arrow-down">
                        Exportar a Excel
                    </x-filament::button>
                    
                    <x-filament::button color="gray" icon="heroicon-o-document-text">
                        Exportar a PDF
                    </x-filament::button>
                    
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="period">
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 3 meses</option>
                            <option value="365">Último año</option>
                            <option value="custom">Personalizado</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </div>
        
        <div class="filament-widgets-container grid grid-cols-1 lg:grid-cols-3 gap-4">
            @if(count($this->getHeaderWidgets()))
                @foreach($this->getHeaderWidgets() as $widget)
                    @livewire($widget)
                @endforeach
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow dark:bg-gray-800 p-6">
                <h3 class="text-base font-semibold mb-4">Tiempo Promedio de Resolución (horas)</h3>
                <div class="flex flex-col space-y-2">
                    <!-- Esta sección se llenará dinámicamente por los componentes -->
                    <div class="flex justify-between items-center">
                        <span>Tickets de prioridad baja:</span>
                        <span class="font-semibold">24.5</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Tickets de prioridad media:</span>
                        <span class="font-semibold">12.3</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Tickets de prioridad alta:</span>
                        <span class="font-semibold">6.8</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Tickets urgentes:</span>
                        <span class="font-semibold">2.4</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow dark:bg-gray-800 p-6">
                <h3 class="text-base font-semibold mb-4">Satisfacción del Cliente</h3>
                <div class="flex items-center justify-center h-40">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-primary-600 dark:text-primary-500">94%</div>
                        <div class="text-sm mt-2">Nivel de satisfacción general</div>
                        <div class="flex items-center justify-center mt-3 space-x-1">
                            <x-heroicon-s-star class="w-5 h-5 text-yellow-500"/>
                            <x-heroicon-s-star class="w-5 h-5 text-yellow-500"/>
                            <x-heroicon-s-star class="w-5 h-5 text-yellow-500"/>
                            <x-heroicon-s-star class="w-5 h-5 text-yellow-500"/>
                            <x-heroicon-o-star class="w-5 h-5 text-yellow-500"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección de Análisis de Satisfacción del Cliente -->
        <div class="bg-white rounded-xl shadow dark:bg-gray-800 p-4 mt-6">
            <h2 class="text-lg font-bold tracking-tight mb-4">Análisis Detallado de Satisfacción</h2>
            
            <!-- Componente Livewire para mostrar el resumen de calificaciones -->
            @livewire('ticket-ratings-overview')
        </div>
        
        <div class="filament-widgets-container grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
            @if(count($this->getFooterWidgets()))
                @foreach($this->getFooterWidgets() as $widget)
                    @livewire($widget)
                @endforeach
            @endif
        </div>
    </div>
</x-filament-panels::page>
