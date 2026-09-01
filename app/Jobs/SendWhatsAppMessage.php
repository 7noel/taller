<?php

namespace App\Jobs;

use App\Models\Establishment;
use App\Models\ReminderLog;
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
     * @param int|null $reminderLogId ID del reminder_log asociado (auditoría de recordatorios automáticos).
     */
    public function __construct(
        public Establishment $establishment,
        public string $phone,
        public string $message,
        public ?int $reminderLogId = null
    ) {
    }

    public function handle(WhatsAppService $whatsapp): void
    {
        $sent = $whatsapp->send($this->establishment, $this->phone, $this->message);

        // Audita el recordatorio automático (si aplica). Un log en 'failed' no
        // bloquea el reintento en la siguiente corrida del comando.
        if ($this->reminderLogId) {
            $log = ReminderLog::find($this->reminderLogId);

            if ($log) {
                $log->update([
                    'status' => $sent ? ReminderLog::STATUS_SENT : ReminderLog::STATUS_FAILED,
                    'error' => $sent ? null : 'Evolution API no confirmó el envío.',
                    'sent_at' => $sent ? now() : null,
                ]);
            }
        }

        if (! $sent) {
            throw new RuntimeException(
                'No se pudo enviar el mensaje de WhatsApp. Revisa las credenciales del establecimiento, que la instancia esté conectada y los logs de Evolution API.'
            );
        }
    }
}
