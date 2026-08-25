{{-- Modal pequeño para crear marca/categoría desde un formulario (patrón "nueva placa") --}}
<div id="catalogQuickModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/50" data-catalog-quick-close></div>
    <div class="relative w-full max-w-sm rounded-lg bg-white shadow-xl p-5">
        <h3 id="catalogQuickTitle" class="text-lg font-semibold text-gray-800 mb-1">Nueva marca</h3>
        <p class="text-sm text-gray-500 mb-3">Se creará y se seleccionará automáticamente.</p>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
        <input type="text" id="catalogQuickName" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" data-catalog-quick-close class="btn btn-secondary">Cancelar</button>
            <button type="button" id="catalogQuickSave" class="btn btn-primary">Crear y seleccionar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('catalogQuickModal');
    const nameInput = document.getElementById('catalogQuickName');
    const titleEl = document.getElementById('catalogQuickTitle');
    const saveBtn = document.getElementById('catalogQuickSave');
    let current = null;

    if (!modal || !nameInput || !saveBtn) return;

    function openQuickCreate(btn) {
        const catalog = btn.dataset.catalogCreate;
        const target = btn.dataset.target;
        const label = btn.dataset.label || 'registro';
        const instance = (window.catalogInstances && window.catalogInstances[target]) || null;

        // Si el usuario escribió algo en el autocompletado, usarlo como nombre sugerido
        let typed = '';
        if (instance && instance.input) typed = instance.input.value.trim();

        current = { catalog, target, instance, label };
        titleEl.textContent = 'Nueva ' + label.toLowerCase();
        nameInput.value = typed;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        nameInput.focus();
        nameInput.select();
    }

    function closeQuick() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        current = null;
    }

    document.querySelectorAll('[data-catalog-create]').forEach(btn => {
        btn.addEventListener('click', () => openQuickCreate(btn));
    });

    modal.querySelectorAll('[data-catalog-quick-close]').forEach(el => el.addEventListener('click', closeQuick));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQuick(); });

    let saving = false; // flag anti-doble envío
    saveBtn.addEventListener('click', async function () {
        if (saving || !current) return;
        const name = nameInput.value.trim();
        if (!name) { nameInput.focus(); return; }

        saving = true;
        this.disabled = true;
        this.textContent = 'Creando...';

        try {
            const res = await fetch(`/api/${current.catalog}/quick-store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { alert(data.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo crear el registro.'); return; }

            const ts = current.instance;
            if (ts && data.id) {
                ts.addOption({ id: data.id, name: data.name });
                ts.addItem(data.id);
            }
            closeQuick();
        } catch (e) {
            alert('Error al crear el registro.');
        } finally {
            saving = false;
            this.disabled = false;
            this.textContent = 'Crear y seleccionar';
        }
    });
});
</script>
@endpush