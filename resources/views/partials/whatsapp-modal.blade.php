{{-- Modal de envío del enlace por WhatsApp (check-ins / estimates / vehículos).
     El JS que lo puebla vive en partials/whatsapp-modal-scripts.blade.php. --}}
<div id="whatsapp-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500/75" data-whatsapp-close></div>
        <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-semibold text-gray-800">Enviar por WhatsApp</h3>
                <button type="button" data-whatsapp-close class="text-gray-400 hover:text-gray-600" aria-label="Cerrar">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="whatsapp-form" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="recipient_name" id="whatsapp-recipient-name">
                {{-- send_method se setea por JS (hidden): form-guard usa form.submit() y los
                     botones submit programáticos no envían su name/value. --}}
                <input type="hidden" name="send_method" id="whatsapp-send-method" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Destinatario</label>
                    <select name="phone" id="whatsapp-phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar destinatario...</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        "Abrir WhatsApp" abre WhatsApp Web con el mensaje listo para enviar; "Enviar por API" usa Evolution API del establecimiento.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mensaje</label>
                    <textarea name="message" id="whatsapp-message" rows="7" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex flex-wrap gap-2 justify-end">
                    <button type="button" data-whatsapp-close class="btn btn-secondary">Cancelar</button>
                    <button type="button" data-whatsapp-wa class="btn btn-secondary">
                        <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.6-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1.1-1.4-1.1-2.7s.7-1.9.9-2.1c.2-.3.5-.3.7-.3h.5c.2 0 .4-.1.6.4.2.6.7 2 .8 2.1.1.1.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.5.2.3.8 1.3 1.7 2.1 1.2 1.1 2.1 1.4 2.4 1.5.3.1.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>
                        Abrir WhatsApp
                    </button>
                    <button type="submit" data-whatsapp-api class="btn btn-primary">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Enviar por API
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
