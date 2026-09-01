<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class RemindersProcess extends Command
{
    protected $signature = 'reminders:process {--dry-run : Muestra qué recordatorios se enviarían hoy sin registrar ni despachar.}';

    protected $description = 'Procesa los recordatorios automáticos por WhatsApp vencidos hoy';

    public function handle(ReminderService $service): int
    {
        $result = $service->process((bool) $this->option('dry-run'));

        if ($result['dry_run']) {
            $this->info('Modo dry-run: no se registró ni se despachó ningún envío.');
        }

        $this->line(sprintf('Recordatorios despachados: %d', $result['sent']));

        if ($result['disabled']) {
            $this->warn('Tipos desactivados en configuración: ' . implode(', ', $result['disabled']));
        }

        return self::SUCCESS;
    }
}
