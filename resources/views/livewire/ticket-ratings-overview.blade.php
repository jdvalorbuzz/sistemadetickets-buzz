<div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
    <header class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            Resumen de calificaciones
        </h2>
        
        <div class="inline-flex rounded-md shadow-sm">
            <button 
                wire:click="setPeriod('week')" 
                type="button" 
                class="px-3 py-1.5 text-xs font-medium {{ $period === 'week' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600' }} border border-gray-300 dark:border-gray-600 rounded-l-lg"
            >
                Semana
            </button>
            <button 
                wire:click="setPeriod('month')" 
                type="button" 
                class="px-3 py-1.5 text-xs font-medium {{ $period === 'month' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600' }} border-t border-b border-r border-gray-300 dark:border-gray-600"
            >
                Mes
            </button>
            <button 
                wire:click="setPeriod('quarter')" 
                type="button" 
                class="px-3 py-1.5 text-xs font-medium {{ $period === 'quarter' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600' }} border-t border-b border-r border-gray-300 dark:border-gray-600"
            >
                Trimestre
            </button>
            <button 
                wire:click="setPeriod('year')" 
                type="button" 
                class="px-3 py-1.5 text-xs font-medium {{ $period === 'year' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600' }} border-t border-b border-r border-gray-300 dark:border-gray-600"
            >
                Año
            </button>
            <button 
                wire:click="setPeriod('all')" 
                type="button" 
                class="px-3 py-1.5 text-xs font-medium {{ $period === 'all' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600' }} border-t border-b border-r border-gray-300 dark:border-gray-600 rounded-r-lg"
            >
                Todo
            </button>
        </div>
    </header>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Promedio de calificaciones -->
        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Calificación Promedio</h3>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($chartData['stats']['average_rating'], 1) }}
                        </p>
                        <p class="ml-1 text-sm text-gray-500 dark:text-gray-400">/ 5</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1 text-yellow-400">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($chartData['stats']['average_rating']))
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        
        <!-- Total de calificaciones -->
        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Calificaciones</h3>
            <div class="mt-1 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $chartData['stats']['total_ratings'] }}
                </p>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $chartData['stats']['rated_tickets_percent'] }}% de tickets cerrados
            </p>
        </div>
        
        <!-- Distribución de calificaciones -->
        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Distribución de Calificaciones</h3>
            <div class="mt-2 space-y-2">
                @for ($i = 5; $i >= 1; $i--)
                    @php
                        $count = $chartData['stats']['rating_distribution'][$i] ?? 0;
                        $total = array_sum($chartData['stats']['rating_distribution']);
                        $percent = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <div class="flex items-center w-12">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $i }}</span>
                            <svg class="w-4 h-4 ml-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="w-full h-2 mx-2 bg-gray-200 rounded dark:bg-gray-600">
                            <div class="h-2 bg-yellow-400 rounded" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 w-9 text-right">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    
    @if(count($chartData['labels']) > 0)
    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-700 shadow-sm">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Tendencia de Calificaciones</h3>
        
        <div class="chart-container" style="position: relative; height:200px;">
            <canvas id="ratingsChart"></canvas>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const labels = @json($chartData['labels']);
            const averages = @json($chartData['averages']);
            const counts = @json($chartData['counts']);
            
            const ctx = document.getElementById('ratingsChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Calificación Promedio',
                        data: averages,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y',
                    }, {
                        label: 'Número de Calificaciones',
                        data: counts,
                        borderColor: 'rgb(249, 115, 22)',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y1',
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Calificación Promedio'
                            },
                            min: 0,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Número de Calificaciones'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
            
            Livewire.hook('message.processed', (message, component) => {
                if (component.fingerprint.name === 'ticket-ratings-overview') {
                    chart.data.labels = @json($chartData['labels']);
                    chart.data.datasets[0].data = @json($chartData['averages']);
                    chart.data.datasets[1].data = @json($chartData['counts']);
                    chart.update();
                }
            });
        });
    </script>
    @else
    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-700 shadow-sm text-center">
        <div class="flex justify-center mb-4">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay datos suficientes</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            No hay calificaciones disponibles para este período. Cuando los usuarios califiquen tickets, podrás ver las estadísticas aquí.
        </p>
    </div>
    @endif
</div>
