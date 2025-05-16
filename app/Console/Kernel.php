<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ProcessTicketEscalations;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar verificación de escalamientos cada 30 minutos
        $schedule->job(new ProcessTicketEscalations())->everyThirtyMinutes();
        
        // Procesar emails según intervalo configurado en cada configuración
        $emailConfigs = \App\Models\EmailConfiguration::all();
        
        foreach ($emailConfigs as $config) {
            $interval = max(1, $config->polling_interval); // Mínimo 1 minuto
            $schedule->job(new \App\Jobs\ProcessIncomingEmails())->cron("*/{$interval} * * * *");
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
