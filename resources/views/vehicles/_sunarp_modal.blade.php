{{-- Modal OCR Sunarp --}}
<div id="modalSunarp" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Cargar captura de Sunarp</h3>

        {{-- Botones de carga --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <label for="sunarpImage" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-2">
                📷 Seleccionar imagen
                <input type="file" id="sunarpImage" accept="image/*" capture="environment" class="hidden">
            </label>
            <button id="btnPasteImage" type="button" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-2">
                📋 Pegar imagen
            </button>
        </div>

        {{-- Área de previsualización --}}
        <div id="preview" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center min-h-[100px] flex items-center justify-center text-gray-400">
            <span>No hay imagen cargada</span>
        </div>

        {{-- Botón procesar --}}
        <button id="btnProcessOCR" type="button" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg w-full hover:bg-green-700 transition" disabled>
            Procesar OCR
        </button>

        {{-- Loading --}}
        <div id="ocrLoading" class="hidden mt-4 text-sm text-gray-600 text-center">
            ⏳ Procesando imagen... (puede tardar unos segundos)
        </div>

        {{-- Resultados --}}
        <div id="ocrResults" class="mt-4 text-sm text-gray-700 hidden space-y-1">
            <p class="font-semibold text-green-700 mb-1">✅ Datos detectados:</p>
            <p><strong>Marca:</strong> <span id="ocrBrand" class="font-mono"></span></p>
            <p><strong>Modelo:</strong> <span id="ocrModel" class="font-mono"></span></p>
            <p><strong>Año:</strong> <span id="ocrYear" class="font-mono"></span></p>
            <p><strong>Color:</strong> <span id="ocrColor" class="font-mono"></span></p>
            <p><strong>VIN:</strong> <span id="ocrVin" class="font-mono"></span></p>
            <p><strong>Motor:</strong> <span id="ocrEngine" class="font-mono"></span></p>
            <p><strong>Placa:</strong> <span id="ocrPlate" class="font-mono"></span></p>
        </div>

        {{-- Enlace a Sunarp --}}
        <div class="text-center text-sm mt-4">
            <a href="https://consultavehicular.sunarp.gob.pe/consulta-vehicular/inicio" target="_blank" rel="noopener" class="text-blue-600 underline hover:text-blue-800">
                🔗 Ir a la página de Sunarp para consultar
            </a>
        </div>

        {{-- Cerrar --}}
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
    const btnPaste = document.getElementById('btnPasteImage');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    let currentImageFile = null;

    // Abrir modal
    btnOpen?.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        resetModal();
    });

    // Cerrar modal
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        resetModal();
    }

    btnClose?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Resetear modal
    function resetModal() {
        fileInput.value = '';
        currentImageFile = null;
        preview.innerHTML = '<span class="text-gray-400">No hay imagen cargada</span>';
        btnProcess.disabled = true;
        btnProcess.textContent = 'Procesar OCR';
        loadingDiv.classList.add('hidden');
        resultsDiv.classList.add('hidden');
        document.querySelectorAll('#ocrResults span').forEach(el => el.textContent = '');
    }

    // Cargar imagen desde archivo
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        loadImage(file);
    });

    // Pegar imagen desde portapapeles
    btnPaste?.addEventListener('click', async function () {
        try {
            if (!navigator.clipboard || !navigator.clipboard.read) {
                alert('Tu navegador no soporta pegar imágenes. Usa la opción "Seleccionar imagen".');
                return;
            }
            const items = await navigator.clipboard.read();
            let imageFound = false;
            for (const item of items) {
                if (item.type.startsWith('image/')) {
                    const blob = await item.getType(item.type);
                    const file = new File([blob], 'clipboard-image.png', { type: blob.type });
                    loadImage(file);
                    imageFound = true;
                    break;
                }
            }
            if (!imageFound) {
                alert('No se encontró ninguna imagen en el portapapeles.');
            }
        } catch (error) {
            console.error('Error al pegar imagen:', error);
            alert('No se pudo leer el portapapeles. Asegúrate de haber copiado una imagen.');
        }
    });

    // Función para cargar y mostrar imagen
    function loadImage(file) {
        if (!file.type.startsWith('image/')) {
            alert('El archivo no es una imagen válida.');
            return;
        }
        currentImageFile = file;
        const reader = new FileReader();
        reader.onload = function (event) {
            preview.innerHTML = `<img src="${event.target.result}" class="max-h-48 mx-auto rounded border">`;
            btnProcess.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // Función segura para extraer campos con múltiples patrones
    function extractField(text, patterns) {
        if (!patterns || patterns.length === 0) return null;
        
        // Normalizar texto: eliminar caracteres especiales, convertir a mayúsculas
        const normalizedText = text.toUpperCase();
        
        for (const pattern of patterns) {
            try {
                // Si es una expresión regular
                if (pattern instanceof RegExp) {
                    const match = normalizedText.match(pattern);
                    if (match && match[1] && match[1].trim() !== '') {
                        return match[1].trim();
                    }
                }
                // Si es un string (búsqueda simple)
                else if (typeof pattern === 'string') {
                    const index = normalizedText.indexOf(pattern.toUpperCase());
                    if (index !== -1) {
                        // Extraer hasta el siguiente salto de línea
                        const start = index + pattern.length;
                        const end = normalizedText.indexOf('\n', start);
                        const value = end !== -1 ? normalizedText.substring(start, end) : normalizedText.substring(start);
                        return value.trim() || null;
                    }
                }
            } catch (e) {
                console.warn('Error en extractField con patrón:', pattern, e);
            }
        }
        return null;
    }

    // Procesar OCR
    btnProcess.addEventListener('click', async function () {
        if (!currentImageFile) return;

        btnProcess.disabled = true;
        btnProcess.textContent = 'Procesando...';
        loadingDiv.classList.remove('hidden');
        resultsDiv.classList.add('hidden');

        try {
            const { data: { text } } = await Tesseract.recognize(currentImageFile, 'spa', {
                logger: m => console.log(m)
            });

            console.log('Texto extraído:', text);
            const textUpper = text.toUpperCase();

            // Extraer datos con múltiples patrones (flexibles)
            const brand = extractField(textUpper, [
                /MARCA[:\s]*([A-ZÁÉÍÓÚÑ0-9\s]+)(?:\n|$)/i,
                /MARCA\/MODELO[:\s]*([A-ZÁÉÍÓÚÑ0-9\s]+)(?:\n|$)/i,
                /(TOYOTA|HYUNDAI|KIA|CHEVROLET|FORD|NISSAN|HONDA|VOLKSWAGEN|MITSUBISHI|MAZDA|SUBARU|RENAULT|PEUGEOT|CITROEN|FIAT|JEEP|SUZUKI)/i
            ]);

            const model = extractField(textUpper, [
                /MODELO[:\s]*([A-ZÁÉÍÓÚÑ0-9\s]+)(?:\n|$)/i
            ]);

            const year = extractField(textUpper, [
                /A[ÑN]O(?:\s*DE\s*MODELO)?[:\s]*(\d{4})/i,
                /(\d{4})/  // Si no encuentra el patrón específico, busca cualquier año de 4 dígitos
            ]);

            const color = extractField(textUpper, [
                /COLOR[:\s]*([A-ZÁÉÍÓÚÑ0-9\s]+)(?:\n|$)/i
            ]);

            // Mejorando la extracción de VIN (más flexible)
            const vin = extractField(textUpper, [
                /(?:N[º°]?|NUM\.?|NRO\.?)\s*VIN[:\s]*([A-Z0-9]{17})/i,
                /VIN[:\s]*([A-Z0-9]{17})/i,
                /SERIE[:\s]*([A-Z0-9]{17})/i,
                /[A-HJ-NPR-Z0-9]{17}/  // Busca cualquier secuencia de 17 caracteres alfanuméricos (excluyendo I, O, Q)
            ]);

            // Mejorando la extracción de Motor (más flexible)
            const engine = extractField(textUpper, [
                /(?:N[º°]?|NUM\.?|NRO\.?)\s*MOTOR[:\s]*([A-Z0-9\-]{4,20})/i,
                /MOTOR[:\s]*([A-Z0-9\-]{4,20})/i,
                /[A-Z0-9]{8,20}/  // Busca secuencias largas de caracteres alfanuméricos
            ]);

            // Mejorando la extracción de Placa (más flexible)
            const plate = extractField(textUpper, [
                /PLACA[:\s]*([A-Z0-9]{3,8})/i,
                /([A-Z]{3}[0-9]{3})/,  // Patrón común de placa peruana
                /([A-Z]{3}[0-9]{4})/,  // Patrón alternativo
                /([A-Z0-9]{6,7})/      // Último recurso: cualquier secuencia alfanumérica de 6-7 caracteres
            ]);

            // Mostrar resultados
            document.getElementById('ocrBrand').textContent = brand || 'No detectado';
            document.getElementById('ocrModel').textContent = model || 'No detectado';
            document.getElementById('ocrYear').textContent = year || 'No detectado';
            document.getElementById('ocrColor').textContent = color || 'No detectado';
            document.getElementById('ocrVin').textContent = vin || 'No detectado';
            document.getElementById('ocrEngine').textContent = engine || 'No detectado';
            document.getElementById('ocrPlate').textContent = plate || 'No detectado';
            resultsDiv.classList.remove('hidden');

            // Autocompletar formulario
            const brandSelect = document.querySelector('select[name="brand_id"]');
            const modelSelect = document.querySelector('select[name="model_id"]');

            // Placa
            if (plate) {
                const plateInput = document.querySelector('input[name="plate"]');
                if (plateInput) plateInput.value = plate;
            }

            // Marca
            if (brand && brandSelect) {
                let brandFound = [...brandSelect.options].find(o => o.text.toUpperCase() === brand);
                if (brandFound) {
                    brandSelect.value = brandFound.value;
                    brandSelect.dispatchEvent(new Event('change'));
                } else {
                    try {
                        const newBrand = await findOrCreateBrand(brand);
                        const opt = document.createElement('option');
                        opt.value = newBrand.id;
                        opt.text = newBrand.name;
                        brandSelect.add(opt);
                        brandSelect.value = newBrand.id;
                        brandSelect.dispatchEvent(new Event('change'));
                    } catch (e) {
                        console.error('Error al crear marca:', e);
                    }
                }
            }

            // Modelo (esperar a que se cargue la lista de modelos después de seleccionar marca)
            if (brand && model && modelSelect) {
                await new Promise(r => setTimeout(r, 500));
                let modelFound = [...modelSelect.options].find(o => o.text.toUpperCase() === model);
                if (modelFound) {
                    modelSelect.value = modelFound.value;
                } else {
                    try {
                        const brandId = brandSelect?.value;
                        if (brandId) {
                            const newModel = await findOrCreateModel(brandId, model);
                            const mOpt = document.createElement('option');
                            mOpt.value = newModel.id;
                            mOpt.text = newModel.name;
                            modelSelect.add(mOpt);
                            modelSelect.value = newModel.id;
                        }
                    } catch (e) {
                        console.error('Error al crear modelo:', e);
                    }
                }
            }

            // Año
            if (year) {
                const yearInput = document.querySelector('input[name="year"]');
                if (yearInput) yearInput.value = year;
            }

            // Color
            if (color) {
                const colorInput = document.querySelector('input[name="color"]');
                if (colorInput) colorInput.value = color;
            }

            // VIN
            if (vin) {
                const vinInput = document.querySelector('input[name="vin"]');
                if (vinInput) vinInput.value = vin;
            }

            // Motor
            if (engine) {
                const engineInput = document.querySelector('input[name="engine_number"]');
                if (engineInput) engineInput.value = engine;
            }

        } catch (error) {
            console.error('Error en OCR:', error);
            alert('Error al procesar la imagen. Intenta de nuevo.');
        } finally {
            btnProcess.textContent = 'Procesar OCR';
            btnProcess.disabled = false;
            loadingDiv.classList.add('hidden');
        }
    });

    // Funciones API
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
});
</script>
@endpush