<?php

namespace App\Jobs;

use App\Services\ExchangeRateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchExchangeRateJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 15;

    public function __construct(public ?string $date = null)
    {
    }

    /**
     * Precarga el tipo de cambio del día (BD → API SUNAT → último registrado).
     * Se despacha al iniciar sesión para tener el T.C. listo durante la sesión.
     */
    public function handle(ExchangeRateService $service): void
    {
        $service->ensureRateForDate($this->date ?? now()->toDateString());
    }
}
