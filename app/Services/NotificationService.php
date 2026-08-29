<?php

namespace App\Services;

use InvalidArgumentException;

class NotificationService
{
    /**
     * Arma el mensaje de WhatsApp para un evento del flujo del taller.
     *
     * Todos los mensajes apuntan al MISMO enlace del portal del vehículo; el
     * contenido del portal se adapta a la etapa actual del vehículo.
     */
    public function buildMessage(string $event, array $data): string
    {
        return match ($event) {
            'checkin_ready' => sprintf(
                "Hola %s, tu vehículo %s ingresó a nuestro taller.\nRevisa el inventario (checklist, daños y fotos) y confírmanos si estás de acuerdo:\n\n%s",
                $data['recipient'],
                $data['plate'],
                $data['link']
            ),
            'estimate_ready' => sprintf(
                "Hola %s, el presupuesto %s de tu vehículo %s está listo por %s.\nRevisa el detalle y confírmalo:\n\n%s",
                $data['recipient'],
                $data['sn'] ?? '',
                $data['plate'],
                $data['total'] ?? '',
                $data['link']
            ),
            'ready_for_pickup' => sprintf(
                "Hola %s, tu vehículo %s ya está listo para que lo recojas en nuestro taller.\n\n%s",
                $data['recipient'],
                $data['plate'],
                $data['link']
            ),
            'work_order_ready' => sprintf(
                "Hola %s, tu vehículo %s está listo para recoger en nuestro taller. La orden de trabajo %s ya pasó el control de calidad.\nRevisa el detalle de todos los trabajos realizados:\n\n%s",
                $data['recipient'],
                $data['plate'],
                $data['sn'] ?? '',
                $data['link']
            ),
            'work_order_survey' => sprintf(
                "Hola %s, ya puedes recoger tu vehículo %s de nuestro taller. Para seguir mejorando, te pedimos 2 minutos y respondas nuestra breve encuesta de satisfacción:\n\n%s\n\n¡Gracias por preferirnos!",
                $data['recipient'],
                $data['plate'],
                $data['link']
            ),
            'survey' => sprintf(
                "Hola %s, queremos conocer tu experiencia con el servicio de tu vehículo %s.\nDéjanos tu opinión:\n\n%s",
                $data['recipient'],
                $data['plate'],
                $data['link']
            ),
            default => throw new InvalidArgumentException("Evento de notificación desconocido: {$event}"),
        };
    }
}
