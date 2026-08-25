<script>
// Selects en cascada de ubigeo (patrón estándar del proyecto)
(function () {
    function setupUbigeoCascade(prefix, savedUbigeoCode) {
        const departamentoEl = document.getElementById(prefix + '-departamento');
        const provinciaEl = document.getElementById(prefix + '-provincia');
        const distritoEl = document.getElementById(prefix + '-distrito');
        const distritoName = distritoEl.getAttribute('name');

        if (!departamentoEl || !provinciaEl || !distritoEl) return;

        async function loadProvincias(departamento) {
            provinciaEl.disabled = !departamento;
            provinciaEl.innerHTML = '<option value="">Seleccionar...</option>';
            distritoEl.disabled = true;
            distritoEl.innerHTML = '<option value="">Seleccionar...</option>';
            if (!departamento) return;

            const res = await fetch(`{{ route('api.ubigeo.provincias') }}?departamento=${encodeURIComponent(departamento)}`);
            const data = await res.json();
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                provinciaEl.appendChild(opt);
            });
            provinciaEl.disabled = false;
        }

        async function loadDistritos(departamento, provincia) {
            distritoEl.disabled = !provincia || !departamento;
            distritoEl.innerHTML = '<option value="">Seleccionar...</option>';
            if (!provincia || !departamento) return;

            const res = await fetch(`{{ route('api.ubigeo.distritos') }}?departamento=${encodeURIComponent(departamento)}&provincia=${encodeURIComponent(provincia)}`);
            const data = await res.json();
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.code;
                opt.textContent = d.distrito;
                distritoEl.appendChild(opt);
            });
            distritoEl.disabled = false;
        }

        // Pre-cargar valores guardados (edición)
        async function preselect() {
            if (!savedUbigeoCode) return;

            const res = await fetch(`{{ route('api.ubigeo.resolve') }}?code=${encodeURIComponent(savedUbigeoCode)}`);
            const data = await res.json();
            if (!data || !data.departamento) return;

            departamentoEl.value = data.departamento;
            await loadProvincias(data.departamento);
            provinciaEl.value = data.provincia;
            await loadDistritos(data.departamento, data.provincia);
            distritoEl.value = savedUbigeoCode;
        }

        departamentoEl.addEventListener('change', function () {
            loadProvincias(this.value);
        });

        provinciaEl.addEventListener('change', function () {
            loadDistritos(departamentoEl.value, this.value);
        });

        // Forzar name="ubigeo_code" en el select de distrito (si viene con prefijo)
        if (distritoName && distritoName !== 'ubigeo_code' && distritoName.endsWith('-ubigeo_code')) {
            distritoEl.setAttribute('name', 'ubigeo_code');
        }

        preselect();
    }

    window.setupUbigeoCascade = setupUbigeoCascade;
})();
</script>