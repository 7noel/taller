<?php

namespace App\Jobs;

use App\Models\Establishment;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    /**
     * @param Establishment $establishment Establecimiento dueño del envío (sus credenciales Evolution API).
     * @param string $phone Teléfono del destinatario (formato local o internacional).
     * @param string $message Texto del mensaje (con el enlace del portal).
     */
    public function __construct(
        public Establishment $establishment,
        public string $phone,
        public string $message
    ) {
    }

    public function handle(WhatsAppService $whatsapp): void
    {
        $sent = $whatsapp->send($this->establishment, $this->phone, $this->message);

        if (! $sent) {
            throw new RuntimeException(
                'No se pudo enviar el mensaje de WhatsApp. Revisa las credenciales del establecimiento, que la instancia esté conectada y los logs de Evolution API.'
            );
        }
    }
}
