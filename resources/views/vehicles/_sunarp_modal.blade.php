{{-- Modal OCR Sunarp --}}
<div id="modalSunarp" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Cargar captura de Sunarp</h3>

        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
            <input type="file" id="sunarpImage" accept="image/*" capture="environment" class="hidden">
            <label for="sunarpImage" class="cursor-pointer text-blue-600 hover:underline text-lg inline-flex items-center gap-2">
                📷 Seleccionar imagen o tomar foto
            </label>
            <div id="preview" class="mt-4"></div>
        </div>

        <button id="btnProcessOCR" type="button" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg w-full hover:bg-green-700 transition" disabled>
            Procesar OCR
        </button>

        <div id="ocrLoading" class="hidden mt-4 text-sm text-gray-600 text-center">
            ⏳ Procesando imagen... (puede tardar unos segundos)
        </div>

        <div id="ocrResults" class="mt-4 text-sm text-gray-700 hidden space-y-1">
            <p class="font-semibold text-green-700 mb-1">✅ Datos detectados:</p>
            <p><strong>Marca:</strong> <span id="ocrBrand" class="font-mono"></span></p>
            <p><strong>Modelo:</strong> <span id="ocrModel" class="font-mono"></span></p>
            <p><strong>Año:</strong> <span id="ocrYear" class="font-mono"></span></p>
            <p><strong>Color:</strong> <span id="ocrColor" class="font-mono"></span></p>
            <p><strong>VIN:</strong> <span id="ocrVin" class="font-mono"></span></p>
            <p><strong>Motor:</strong> <span id="ocrEngine" class="font-mono"></span></p>
        </div>

        <div class="text-center text-sm mt-4">
            <a href="https://sede.sunarp.gob.pe/" target="_blank" rel="noopener" class="text-blue-600 underline hover:text-blue-800">
                🔗 Ir a la página de Sunarp para consultar
            </a>
        </div>

        <button id="closeModal" type="button" class="mt-4 bg-gray-300 px-4 py-2 rounded-lg w-full hover:bg-gray-400 transition">
            Cerrar
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalSunarp');
    const btnOpen = document.getElementById('btnSunarp');
    const btnClose = document.getElementById('closeModal');
    const fileInput = document.getElementById('sunarpImage');
    const preview = document.getElementById('preview');
    const btnProcess = document.getElementById('btnProcessOCR');
    const loadingDiv = document.getElementById('ocrLoading');
    const resultsDiv = document.getElementById('ocrResults');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    btnOpen?.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    function resetModal() {
        fileInput.value = '';
        preview.innerHTML = '';
        btnProcess.disabled = true;
        btnProcess.textContent = 'Procesar OCR';
        loadingDiv.classList.add('hidden');
        resultsDiv.classList.add('hidden');
    }

    btnClose?.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        resetModal();
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            resetModal();
        }
    });

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'max-h-48 mx-auto rounded border';
            preview.innerHTML = '';
            preview.appendChild(img);
            btnProcess.disabled = false;
        };
        reader.readAsDataURL(file);
    });

    function extractField(text, regex) {
        const match = text.match(regex);
        return match ? match[1].trim().toUpperCase() : null;
    }

    async function findOrCreateBrand(name) {
        const res = await fetch('/api/brands/find-or-create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name }),
        });
        if (!res.ok) throw new Error('Error al crear marca');
        return res.json();
    }

    async function findOrCreateModel(brandId, name) {
        const res = await fetch('/api/models/find-or-create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ brand_id: brandId, name }),
        });
        if (!res.ok) throw new Error('Error al crear modelo');
        return res.json();
    }

    function selectBrand(brandSelect, brandId) {
        brandSelect.value = brandId;
        brandSelect.dispatchEvent(new Event('change'));
        return new Promise(r => setTimeout(r, 80));
    }

    btnProcess.addEventListener('click', async function () {
        const file = fileInput.files[0];
        if (!file) return;
        btnProcess.disabled = true;
        btnProcess.textContent = 'Procesando...';
        loadingDiv.classList.remove('hidden');
        try {
            const { data } = await Tesseract.recognize(file, 'spa', { logger: m => console.log(m) });
            const text = data.text.toUpperCase();
            const brand = extractField(text, /MARCA:\s*(.+)/) || extractField(text, /MARCA\/MODELO:\s*(.+)/) || extractField(text, /(TOYOTA|HYUNDAI|KIA|CHEVROLET|FORD|NISSAN|HONDA|VOLKSWAGEN|MITSUBISHI|MAZDA|SUBARU|RENAULT|PEUGEOT|CITROEN|FIAT|JEEP|SUZUKI)/);
            const model = extractField(text, /MODELO:\s*(.+)/);
            const year = extractField(text, /A[ÑN]O(?:\s*DE\s*MODELO)?[:\s]*(\d{4})/);
            const color = extractField(text, /COLOR:\s*(.+)/);
            const vin = extractField(text, /(?:N[º°]?|NUM\.?|NRO\.?)\s*VIN[:\s]*([A-Z0-9]{17})/) || extractField(text, /[A-HJ-NPR-Z0-9]{17}/);
            const engine = extractField(text, /(?:N[º°]?|NUM\.?|NRO\.?)\s*MOTOR[:\s]*([A-Z0-9\-]{4,20})/);
            document.getElementById('ocrBrand').textContent = brand || 'No detectado';
            document.getElementById('ocrModel').textContent = model || 'No detectado';
            document.getElementById('ocrYear').textContent = year || 'No detectado';
            document.getElementById('ocrColor').textContent = color || 'No detectado';
            document.getElementById('ocrVin').textContent = vin || 'No detectado';
            document.getElementById('ocrEngine').textContent = engine || 'No detectado';
            resultsDiv.classList.remove('hidden');
            const brandSelect = document.querySelector('select[name="brand_id"]');
            const modelSelect = document.querySelector('select[name="model_id"]');
            if (brand && brandSelect) {
                let brandFound = [...brandSelect.options].find(o => o.text.toUpperCase() === brand);
                if (brandFound) {
                    await selectBrand(brandSelect, brandFound.value);
                } else {
                    const newBrand = await findOrCreateBrand(brand);
                    const opt = document.createElement('option');
                    opt.value = newBrand.id; opt.text = newBrand.name;
                    brandSelect.add(opt);
                    await selectBrand(brandSelect, newBrand.id);
                }
                if (model && modelSelect) {
                    let modelFound = [...modelSelect.options].find(o => o.text.toUpperCase() === model);
                    if (modelFound) { modelSelect.value = modelFound.value; }
                    else {
                        const newModel = await findOrCreateModel(brandSelect.value, model);
                        const mOpt = document.createElement('option');
                        mOpt.value = newModel.id; mOpt.text = newModel.name;
                        modelSelect.add(mOpt); modelSelect.value = newModel.id;
                    }
                }
            }
            if (year) document.querySelector('input[name="year"]').value = year;
            if (color) document.querySelector('input[name="color"]').value = color;
            if (vin) document.querySelector('input[name="vin"]').value = vin;
            if (engine) document.querySelector('input[name="engine_number"]').value = engine;
        } catch (error) {
            console.error(error);
            alert('Error al procesar la imagen. Intenta de nuevo.');
        } finally {
            btnProcess.textContent = 'Procesar OCR';
            btnProcess.disabled = false;
            loadingDiv.classList.add('hidden');
        }
    });
});
</script>
@endpush
