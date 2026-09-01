<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\Facturacion\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EmitInvoiceJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 15;

    public function __construct(public Invoice $invoice)
    {
    }

    public function handle(InvoiceService $service): void
    {
        try {
            $service->emit($this->invoice);
        } catch (\Throwable $e) {
            Log::error("Falló la emisión del comprobante {$this->invoice->document_sn}: {$e->getMessage()}");

            $this->invoice->refresh();
            $this->invoice->sunat_description = 'Error de emisión: ' . mb_substr($e->getMessage(), 0, 240);
            $this->invoice->save();

            throw $e;
        }
    }
}
