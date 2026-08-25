# Reglas de Configuración, Numeración y Estilo (obligatorias)

## Regla de Configuración
Al crear un establecimiento, se copian los datos editables (dirección, teléfono, celular, email, ubigeo, etc.) desde `company_settings`, pero **NUNCA** se copian el RUC, Razón Social ni Nombre Comercial (deben quedar vacíos para edición manual). Ver `EstablishmentController::copyFromCompany()` y `store()`.

## Regla de Numeración
Todo documento (incluyendo `check_ins` / IV01) debe usar `DocumentSeriesService::getNextNumber()` con `lockForUpdate()` dentro de `DB::transaction()`. **PROHIBIDO usar `MAX()+1`** o cualquier numeración manual. El prefijo de serie se copia desde el código del tipo de documento y `current_number` se incrementa atómicamente.

## Regla de Estilo
Todos los nuevos módulos deben copiar el estilo visual del módulo de Vehículos: `<x-app-layout>` con `<x-slot name="header">`, botones `.btn*` / `.btn-icon*`, tablas Tabulator con tema del layout, búsqueda `.search-input`, mensajes flash `green-50`, y botones de acción siempre con `formatter` en JavaScript.

## Regla de Identidad de Documento (snapshot en el documento emitido)

Todo documento emitido por la empresa (interno o electrónico) guarda en el registro del documento un **snapshot inmutable** de su identidad, para búsqueda, impresión y auditoría, además de la FK `document_series_id`:

- `document_type_code` (varchar): código del tipo de documento (`document_types.code`, p. ej. `IV`, `01`, `07`).
- `document_serie` (varchar): serie/prefijo del documento correspondiente a la emisión (p. ej. `IV01`).
- `document_number` (int): correlativo **numérico** (p. ej. `1`). Se genera con `DocumentSeriesService::getNextNumber()` (lock pesimista, **prohibido MAX+1**).
- `document_sn` (varchar): serie completa con formato **SSSS-XXXXXX** (p. ej. `IV01-000001`). Es la clave de búsqueda (por serie, número o string completo) y la que se muestra en listados, vistas e impresiones.

Los listados de documentos deben mostrar la **Serie y el Código SUNAT** agrupados (ej. `IV01 · IV`) en una sola celda compacta, con el documento completo (`sn`) como primera columna enlazable, para estandarizar documentos internos y electrónicos.
