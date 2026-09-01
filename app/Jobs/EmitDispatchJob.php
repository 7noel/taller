<?php

namespace App\Jobs;

use App\Models\Dispatch;
use App\Services\Facturacion\DispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EmitDispatchJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 15;

    public function __construct(public Dispatch $dispatch)
    {
    }

    public function handle(DispatchService $service): void
    {
        try {
            $service->emit($this->dispatch);
        } catch (\Throwable $e) {
            Log::error("Falló la emisión de la guía {$this->dispatch->document_sn}: {$e->getMessage()}");

            $this->dispatch->refresh();
            $this->dispatch->sunat_description = 'Error de emisión: ' . mb_substr($e->getMessage(), 0, 240);
            $this->dispatch->save();

            throw $e;
        }
    }
}
