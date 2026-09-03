
### Sesion: Fechas SOAT en vehiculo, fotos desde explorador y clasificacion de presupuestos con responsabilidad del taller
- **Fecha**: 02 de septiembre de 2026
- **Tarea**: Resolver 3 puntos de check-ins/presupuestos.
- **Vehiculo + SOAT**: columna `vehicles.soat_expiration` agregada en la migracion original `create_vehicles_table` (re-editada en origen, sin migracion aditiva; aplicada en BD con ALTER en dev). `MaintenanceService::syncFromCheckIn` sincroniza SOAT como la revision tecnica (valor mas reciente o si vacio). Modal "Nueva/Editar placa" y formularios de vehiculo incluyen "Vence SOAT"; `/api/vehicles/search` y quick-store/update validan y devuelven `soat_expiration`. Al seleccionar/crear un vehiculo en inventario se autocompletan `soat_expiration` y `technical_review_expiration`.
- **Fotos desde explorador**: en Chrome la FileList es "viva"; al limpiar el input antes de copiarla, las fotos elegidas no cargaban. Fix: copiar `Array.prototype.slice.call(this.files)` antes de `this.value=''` en galeria y captura nativa.
- **Presupuestos**: la seccion "Responsable del gasto / incidente" ya no se muestra para todo siniestro. Se distingue con un checkbox "Caso con responsabilidad del taller" (solo siniestro/garantia): al marcarlo `liability='workshop'` y aparecen tipo de incidente, responsable y fecha; en siniestros normales `liability='insurance'` queda oculto.
- **Verificacion**: `php -l` OK, `view:cache` OK, migracion aplicada.
### 📌 Sesión: Consolidación de migraciones #2 (nuevas ALTERs fusionadas)
- **Fecha**: 31 de agosto de 2026
- **Tarea**: Consolidar las 3 nuevas migraciones ALTER creadas para soporte de moneda, responsabilidad del taller y presupuestos hijo, fusionándolas en los `Schema::create` correspondientes.
- **Eliminadas (3)**: `09_01_000200_add_parent_estimate_id` (estimates), `09_01_000400_add_currency_to_cost_tables` (service_vouchers, third_party_orders, work_order_assignments), `09_01_000500_add_special_fields` (estimates: is_chargeable, liability, liability_user_id, warranty_of_estimate_id, incident_type, incident_reported_at).
- **Creadas nuevas que se mantienen**: `09_01_000300_create_exchange_rates`, `09_01_000600_create_work_order_internal_expenses`, `09_01_000700_create_reminder_logs` (tablas nuevas, sin cambios).
- **Resultado**: 58 migraciones, **0 `Schema::table`** y **0 `->after(`**. Sin reordenamientos (FKs self-referenciales y a users ya resueltas en el orden actual).
- **Verificación**: `php -l` OK; `migrate:fresh --seed` OK (58 migraciones, seeders completos); suite **233 tests / 717 assertions OK** (116s) — incluye los nuevos tests que ejercitan los campos fusionados (WarrantyInternalFlowTest, ProviderSettlement con USD, WhatsAppReminderTest).

### 📌 Módulo: Reportes y KPIs (Centro de Reportes)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Crear el módulo 10 de reportes para la toma de decisiones: frecuencia de vehículos (marca/modelo/año), rentabilidad de asesores, costos y utilidad por OT, seguimientos, ingresos/cobranza y repuestos utilizados.
- **`ReportService`** (nuevo, inyecta `ExchangeRateService`): 6 agregaciones normalizadas a PEN con snapshot de T.C. — `vehicleFrequency()`, `advisorProfitability()` (atribuye utilidad de OTs al asesor del primer presupuesto facturable), `workOrderProfitability()`/`workOrderProfitRows()` (ingreso = estimates facturables; costos por componente: repuestos `cost_price×qty`, mano de obra `assignments.cost`, vales `base_amount`, OC terceros `amount_without_iva`, gastos internos — una consulta por componente), `followUps()`, `revenue()` (facturado vs cobrado por invoice) y `partsUsage()` (join estimates→vehicles→brands para cruzar repuestos con marca de vehículo).
- **`ReportController`** + 13 rutas bajo `/reports` (protegidas con permiso `ver reportes`): 6 páginas + 6 endpoints `api/reports/*` (JSON con kpis/series/rows).
- **Vistas** `resources/views/reports/`: hub `index` + 6 reportes con filtros GET (desde/hasta/establecimiento + filtro propio), 4 KPIs, gráficos ApexCharts (barras/donut/área) y tabla Tabulator con paginación local, export CSV e imprimir; partial `_helpers` con utilidades JS compartidas (renderKpis, baseBar/baseDonut/baseLine, buildQuery).
- **Permisos**: `ver reportes` agregado al seeder (Administrador vía `Permission::all()` + Asesor) — seeder ejecutado. **Navegación**: grupo "Reportes" (Centro + 6 reportes) en la sidebar.
- **Verificación**: `route:list` 13 rutas OK, `view:cache` OK, smoke test tinker (6 métodos con datos reales + 7 vistas renderizadas OK), `php -l` OK. `follow_ups`/`invoices` vacíos en BD → reportes muestran 0 con placeholder.
- **Pendiente**: dashboard global de KPIs (reutiliza `ReportService`), modo oscuro en ApexCharts al integrar el theme toggle.


### 📌 Sesión: Tipo de cambio automático (SUNAT) — login + bajo demanda
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Garantizar el T.C. del día (venta SUNAT) con estrategia **BD → API → último registrado**, activada al iniciar sesión y bajo demanda al crear/editar presupuestos.
- **`ExchangeRateService::ensureRateForDate($date, $currency='USD')`** (nuevo, constructor inyecta `SunatExchangeService`): 1) busca en `exchange_rates` (date+currency); 2) si no existe, consulta `getTipoCambio()` y persiste con `updateOrCreate` (`source='SUNAT'`, `sell_rate`=venta); 3) si la API falla o no trae dato, usa el último registrado (`latestFor` ≤ fecha). `suggestRate()` sigue siendo lectura pura (el endpoint `latest` no depende de HTTP cuando ya hay dato).
- **Job `FetchExchangeRateJob`** (ShouldQueue, tries 2/backoff 15, `$date` opcional → hoy): despachado desde el evento `Login` (registrado en `AppServiceProvider::boot` con `Event::listen`) para precargar el T.C. del día.
- **Bajo demanda**: `ExchangeRateController::latest()` (`api/exchange-rates/latest`) llama `ensureRateForDate(now)` para USD antes de sugerir el T.C. — cubre el flujo de presupuestos (cambio de moneda) y el modal de cambio de moneda sin depender del worker.
- **`.env` / `.env.example`**: nueva variable `SUNAT_API_TOKEN` (antes quedaba el token default en el código).
- **Tests**: `tests/Unit/ExchangeRateServiceTest` — 5 tests / 31 assertions (BD ok sin HTTP, API ok persiste, API falla → fallback último, PEN → null, job persiste fecha). Regresión: Estimate + ProviderSettlement + ServiceVoucher + WorkOrderCost = **42 passed (133 assertions)**.
- **Nota**: `QUEUE_CONNECTION=database` → en producción ejecutar `php artisan queue:work` para que el job de login se procese; el mecanismo bajo demanda cubre el caso sin worker.

## Fecha de inicio: 17 de agosto de 2026

### 📌 Sesión: Garantías, gastos internos y siniestros por responsabilidad del taller
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Registrar los 3 casos reales de responsabilidad del taller: (1) **garantía** — el vehículo regresa por una falla del trabajo entregado; (2) **daño interno** — arañazo o repuesto malogrado durante la reparación (el taller asume el gasto); (3) **siniestro por mala maniobra** — en prueba de ruta o ingreso/salida del taller se activa el seguro del vehículo (planchado/pintura) con responsabilidad del taller.
- **Migraciones (2 aditivas)**: `2026_09_01_000500_add_special_fields_to_estimates_table` (`is_chargeable` default true, `liability` client/insurance/workshop, `liability_user_id`, `warranty_of_estimate_id` FK, `incident_type`, `incident_reported_at`) y `2026_09_01_000600_create_work_order_internal_expenses_table` (type scratch/damaged_part/other, description, amount, currency, exchange_rate, responsible_user_id, occurred_at, notes).
- **Garantía = presupuesto no facturable**: `CheckIn::SERVICE_TYPES` += `garantia`. `EstimateService::applyWarrantyInheritance()` fuerza `service_type='garantia'`, `is_chargeable=false`, `liability='workshop'`, hereda moneda/T.C. del original y nace en `in_repair` (sin gates de aprobación); validaciones (mismo vehículo, no garantía de garantía); guards en `changeStatus`/`changeStatusByClient` (no pasa por aprobación), `update()` (flags inmutables) y `InvoiceService`/selectores (`getSearchResults`/`getRelatedBillable` excluyen `is_chargeable=false`).
- **Daño interno = registro ligero en la OT** (sin presupuesto ni check-in): tabla `work_order_internal_expenses` + `WorkOrderService::addInternalExpense()/removeInternalExpense()`; `WorkOrderCostService` agrega componente **"Gastos internos (responsabilidad del taller)"** y excluye del ingreso los presupuestos no facturables (su costo sí se cuenta → utilidad real).
- **Reapertura de OT**: transición `closed → open` en `WorkOrderService::TRANSITIONS` + `reopen()` con motivo en el historial; botón "Reabrir OT" en `work-orders/show`.
- **Siniestro por maniobra (caso 3)**: se usa el flujo normal de presupuesto `siniestro` con aprobación del seguro + flags `liability='workshop'`, `liability_user_id` (chofer) e `incident_type` (road_test/maneuver/other).
- **UI**: OT show — badges "Garantía de PRE01-XXXX"/"No facturable"/"Resp. taller", botón por presupuesto "Registrar garantía", sección "Gastos internos" con alta/eliminación; estimates show — badges, botón "Registrar garantía" y sección "Garantías" con contador; estimates create — banner ámbar + inputs ocultos `warranty_of_estimate_id`/`work_order_id`; **estimates index** — badges "Garantía"/"No facturable"/"Resp. taller" en la columna Documento; **formulario de presupuesto** — sección "Responsabilidad del gasto / incidente" (liability, incident_type, responsable, fecha) visible al elegir servicio "siniestro" (toggle en `_form-scripts`).
- **Selector de facturación**: `api/estimates/search` acepta `chargeable=1` (el listado general muestra todo; el selector de `invoices/create` pasa `chargeable=1` para excluir no facturables); `getSearchResults()` expone `is_garantia`, `warranty_sn`, `is_chargeable`, `liability(_label)`.
- **Tests**: nuevo `WarrantyInternalFlowTest` (8 tests / 35 aserciones: flags forzados, mismo vehículo, garantía-de-garantía, gate de aprobación, no facturable en `InvoiceService`, costos sin ingreso de garantía + gastos internos, ciclo del gasto interno, reapertura). Verificación: **suite completa 217 tests / 675 assertions OK**, `php -l` OK, `view:cache` OK, migraciones aplicadas.


### 📌 Sesión: Costos y utilidad en Órdenes de Trabajo (moneda en costos + WorkOrderCostService)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Registrar costos de terceros (planchado/pintura y trabajos fuera del taller, p. ej. radio en Lima) en su moneda original (PEN/USD) y calcular la utilidad de la OT normalizada a soles (moneda funcional). Los vales (CST01) son el mecanismo para servicios tercerizados; la utilidad se muestra en la moneda del primer presupuesto de la OT.
- **Migración**: `2026_09_01_000400_add_currency_to_cost_tables_table` — `currency` + `exchange_rate` (snapshot, soles por 1 dólar) en `service_vouchers`, `third_party_orders` y `work_order_assignments`. (`stock_movements` ya lo tenía con `unit_cost_pen`/`total_cost_pen`.)
- **`ServiceVoucherService`**: respeta `currency`/`exchange_rate` (default PEN/1) al crear/editar; flujo "sin IGV ni detracción" (tasas 0) para maestros sin comprobante. `ServiceVoucherRequest` valida `currency` (PEN/USD) + `exchange_rate`.
- **`ProviderSettlementService`**: `computeTotals()` normaliza la suma de vales a PEN vía `sumVoucherBasePen()` — la liquidación LST01 se expresa SIEMPRE en moneda funcional (PEN).
- **`EstimateService`**: las OC de terceros heredan `currency`/`exchange_rate` del presupuesto (`syncThirdPartyOrders` + `convertCurrency`).
- **Nuevo `WorkOrderCostService`**: `summary()` agrega ingresos (Σ totales de presupuestos → PEN) y costos por componente — repuestos (ítems part `cost_price×qty`), vales (base sin IGV), mano de obra (asignaciones) y OC de terceros — normalizando a PEN con el T.C. snapshot; expone utilidad y margen en PEN y en la moneda del primer presupuesto (visualización).
- **UI**: vale con selector de moneda + T.C. snapshot + toggle "Pago directo sin IGV ni detracción" + preview con símbolo; `show` del vale con moneda; OT `show` con cards "Costos y utilidad" (KPIs + desglose por componente) y "Servicios tercerizados" (lista de vales + botón nuevo vale que preselecciona la OT); listado de vales con moneda.
- **Tests**: `ServiceVoucherFlowTest` +3 (default PEN, USD sin impuestos, update preserva snapshot), `ProviderSettlementFlowTest` +1 (liquidación normaliza USD→PEN), nuevo `WorkOrderCostTest` +2 (PEN con costos mixtos, USD). Verificación: **21 tests / 84 assertions OK** (Voucher+Settlement+Cost), **44 / 118 OK** (Estimate+WorkOrder), `php -l` OK, `view:cache` OK.

### 📌 Sesión: Moneda en presupuestos (entidad tipo de cambio + bloqueo + conversión + catálogo)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Permitir cambiar la moneda (PEN/USD) en presupuestos con reglas claras: moneda bloqueada al tener ítems, acción explícita "Cambiar moneda" (solo borrador) que convierte todos los montos, precios de catálogo convertidos a la moneda del presupuesto, y facturación multi-presupuesto con moneda uniforme (UX).
- **Convención**: `exchange_rate` = soles por 1 dólar (PEN → 1). Se guarda snapshot por presupuesto (ya existía).
- **Entidad nueva `ExchangeRate`**: migración `2026_09_01_000300_create_exchange_rates_table` (date+currency único, buy/sell/source), modelo, `ExchangeRateService::suggestRate()/convert()`, `ExchangeRatePolicy` (permiso `ver configuración`), `ExchangeRateController` (index/store/destroy + API `api/exchange-rates/latest`), vista de mantenimiento `exchange-rates/index` + menú "Tipos de Cambio" en Administración.
- **`EstimateService`**: `update()` rechaza cambiar `currency` si hay ítems/OC; `convertCurrency()` (solo draft y raíz) convierte tarifas, ítems, OC, descuento global fijo y franquicia y recalcula con `EstimateCalculationService` + activity log; `syncItems()` deriva precio de catálogo (sell/cost) convertido cuando el frontend no envía valor; `getRelatedBillable()` expone `currency`.
- **Frontend presupuestos**: en edición con ítems la moneda se renderiza bloqueada (hidden + texto) + botón "Cambiar moneda" (solo borrador) con modal `_currency-modal` (formulario independiente, preview del nuevo total y T.C. sugerido); en creación `lockCurrency()` deshabilita el select al agregar ítems; al elegir moneda se sugiere el T.C.; en el modal de ítems el precio del catálogo se convierte y se muestra nota "Catálogo: S/ X · T.C. Y → US$ Z".
- **Facturación (`invoices/create`)**: al auto-agregar relacionados se omiten presupuestos de otra moneda (con aviso) y el envío se bloquea si se mezclan PEN/USD (además del guard backend existente).
- **Tests**: `EstimateFlowTest` +5 (bloqueo de cambio con ítems, conversión PEN→USD y USD→PEN, rechazo fuera de borrador/ampliación, precio de catálogo convertido). Verificación: **EstimateFlowTest 21 OK / 47 assertions**, **InvoiceFlowTest 14 OK / 47 assertions**, `php -l` OK, `view:cache` OK, migración aplicada.

### 📌 Sesión: Ampliaciones de presupuestos (siniestro + ampliaciones = grupo)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Modelar las **ampliaciones de presupuesto** como relación padre-hijo (`parent_estimate_id` en `estimates`), con moneda heredada del siniestro, franquicia calculada a nivel de GRUPO (siniestro + ampliaciones + TODAS sus OC) y facturación multi-presupuesto con moneda uniforme.
- **Decisión**: NO se crea un tipo de servicio "ampliación" (`service_type` sigue describiendo la naturaleza del trabajo); la ampliación se identifica por `parent_estimate_id != null` (un solo nivel, el padre debe ser raíz). Badge "Ampliación de PRE01-XXXXXX" en listados/show + contador en el show del principal.
- **Migración nueva**: `2026_09_01_000200_add_parent_estimate_id_to_estimates_table` (nullable, auto-FK `nullOnDelete`, índice). Aditiva: NO requiere `migrate:fresh` (la columna se agrega sobre la BD existente).
- **Modelo `Estimate`**: `parent()` (belongsTo conTrashed), `ampliaciones()` (hasMany orderBy document_sn), accessors `is_ampliacion` y `grupo_label`; `parent_estimate_id` en fillable y LogsActivity.
- **`EstimateRequest`**: `parent_estimate_id` nullable + existe, padre no puede ser ampliación, y debe ser del mismo vehículo.
- **`EstimateService`**: `applyParentInheritance()` fuerza SIEMPRE `currency`/`exchange_rate` del padre (ignora lo enviado) y pre-rellena contexto (vehículo, cliente, aseguradora, claim, tarifas); `update()` re-fuerza moneda del padre; `delete()` propaga soft-delete a las ampliaciones del grupo; `getSearchResults()`/`getRelatedBillable()` exponen `is_ampliacion`, `parent_sn`, `currency` y agrupan por siniestro+ampliaciones+mismo vehículo/OT.
- **`EstimateCalculationService`**: franquicia por GRUPO — `applyFranchise()` (raíz calcula `calculateGroupFranchise()`, ampliación limpia sus `franchise_*` y dispara recálculo del raíz). `base = Σ taxable_base del grupo + Σ OC del grupo`; mínimo y % del presupuesto principal; `franquicia = max(mínimo_sin_IGV, base×%)` guardada en el principal.
- **`InvoiceService`**: guard de moneda uniforme en `createFromEstimates` (rechaza mezclar PEN/USD); `addFranchiseLines()`/`addInsuranceLines()` resuelven las ampliaciones a su raíz (`resolveFranchiseCarriers()`) → UNA línea de franquicia por grupo ("Franquicia … y N ampliación(es)").
- **Frontend**: botón "Ampliar" en `estimates/show` (raíz) → `estimates/create?parent_estimate_id=X`; banner ámbar, moneda y T.C. bloqueados (heredados), franquicia de solo lectura (vive en el principal); precarga de vehículo/cliente/aseguradora/claim/tarifas y resumen de franquicia del GRUPO en vivo (`_form-scripts`: `parentData.group_saved_base` excluye el presupuesto en edición). Badge "Ampl." + moneda por fila en `estimates/index`.
- **Tests**: `EstimateFlowTest` (+4: herencia de moneda/T.C., mismo vehículo obligatorio, franquicia de grupo agregada en el padre con OC, relacionados incluyen el grupo) y `InvoiceFlowTest` (+1: rechaza factura con presupuestos en monedas distintas). Verificación: **EstimateFlowTest 16 OK / 35 assertions**, **InvoiceFlowTest 14 OK / 47 assertions**, `php -l` OK, `view:cache` OK.


### 📌 Sesión: Consolidación de migraciones (solo Schema::create)
- **Fecha**: 31 de agosto de 2026
- **Tarea**: Eliminar todas las migraciones que solo modifican tablas (`Schema::table` / ALTER) fusionando sus cambios en las migraciones `create` correspondientes, de modo que solo existan migraciones que crean tablas.
- **Resultado**: 55 migraciones, **0 `Schema::table`** y **0 `->after(`** en `database/migrations`.
- **Eliminadas (17)**: `032001/032002` (activity_log), `035054` (users), `063000` (parties), `125108` (users), `08_24_000004-000007` (check_ins doc), `08_24_000101` (establishments), `08_24_000102` (parties), `08_25_000005` (estimate_items), `08_25_000006` (estimate_discounts), `08_27_000003` (work_order_id), `08_27_000400` (work_orders), `09_01_000400` (invoices/dispatches nullable), `09_01_000500` (check_ins). También se removieron los 2 bloques `Schema::table` dentro de `08_28_000300` (ahora solo crea purchase_orders/items/inventory_guides).
- **Reordenamiento (9 renombres, orden topológico)**: `users` → `2026_08_18_035054` (después de establishments), `work_orders` → `2026_08_24_000004` (antes de check_ins/estimates/stock_movements), `check_ins` → `2026_08_24_000005` + hijos checklist/results/damages/photos → `000006-000009`, `part_orders` → `2026_08_25_000008` (después de estimates), `stock_movements` → `2026_08_28_000400` (después de purchase_orders/inventory_guides). FKs inline verificadas sin ciclos.
- **Nota**: los backfills de datos (document_type DNI→1, document_number string→int, document_sn generado) desaparecen por ser irrelevantes en instalación limpia; `parties.type` desaparece del esquema (ya dropeado en vivo por `063000`). El esquema final es idéntico al esquema dev pre-consolidación.
- **Verificación**: `php -l` OK en las 55 migraciones; **`migrate:fresh --seed` OK** en dev (55 migraciones, 74 tablas, seeders completos incl. DemoDataSeeder); suite completa **185 tests / 559 assertions OK** (79s); grep `Schema::table`=0, `->after(`=0.

### 📌 Sesión: Recordatorios, mantenimiento preventivo y rol Gestor de Citas (citas y seguimiento)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Panel de recordatorios (revisión técnica, mantenimiento preventivo y presupuestos en aprobación), cálculo del próximo preventivo por kilometraje, sincronización de fechas desde el check-in al vehículo, y rol "Gestor de Citas" para el personal de agenda.
- **Migraciones modificadas (3, re-editadas en origen)**: `vehicles` (+`last_maintenance_date`, `last_maintenance_mileage`, `next_maintenance_date`, `maintenance_reminder_days` 15, `maintenance_source` calculated/manual), `company_settings` (+`maintenance_interval_km` 5000, `maintenance_default_days` 120, `maintenance_history_visits` 3), `follow_ups` (+`estimate_id` nullable → seguimiento de presupuestos).
- **Servicios**: nuevo `MaintenanceService` integrado en `CheckInService::create/update`: revisión técnica se sincroniza "solo hacia adelante o si vacío"; preventivo actualiza última visita/km y recalcula `next_maintenance_date` (0-1 visita → última + 120 días; ≥2 visitas con km → proyección `(días/km)×intervalo` con límites 30-365; `maintenance_source='manual'` no se pisa).
- **Controladores**: `ReminderController` (index + `api/reminders/search?tab=technical_review|maintenance|estimates`, protegido con `ver seguimientos`); `VehicleController::history` (ingresos y presupuestos del vehículo por `document_sn`) y `updateMaintenanceDate` (ajuste manual).
- **Frontend**: `reminders/index` (3 pestañas Tabulator, badges de plazo, contacto + teléfono, aviso de cita existente; acciones Historial / Seguimiento modal precargado / Agendar cita con prefill en `appointments/create` / Ajustar fecha), `vehicles/history`, botón "Seguimiento" en `estimates/show` (follow-up con `estimate_id`), formulario de vehículo y pestaña "Mantenimiento" en company-settings.
- **Rol nuevo "Gestor de Citas"** (seeder): ver parties/vehículos, ver/crear presupuestos, ver/crear/editar citas y seguimientos. Ítem "Recordatorios" en el grupo Citas de la navegación.
- **Tests**: `tests/Feature/ReminderMaintenanceTest` (9 tests, 32 assertions): sync de revisión+preventivo, proyección con historial (Carbon 3 `diffInDays` con signo → `abs`), fecha manual no sobrescrita, revisión nunca retrocede, endpoint de recordatorios por pestaña + render, historial, seguimiento con presupuesto y rol.
- **Verificación**: suite completa **185 tests / 558 assertions OK**; `view:cache` OK; `php -l` OK; ambas BD (dev y testing) reconstruidas con `migrate:fresh` (se editaron migraciones existentes, requiere recrear esquema).

### 📌 Sesión: Facturación Electrónica — Fase 1 y 2 (datos + proveedores + reglas de negocio)
- **Fecha**: 01 de septiembre de 2026
- **Tarea**: Construir el módulo de facturación electrónica (módulo 9) con doble proveedor (Nubefact + Factura Perú / facturadorsmart.pe), adelantos con caja completa, franquicia, aseguradora, cierre con regularización de anticipos, NC/ND y guías de remisión.
- **Decisiones de negocio (confirmadas por el usuario)**:
  - Guías de remisión con **tabla propia `dispatches`** (no toca `InventoryGuide`).
  - **Adelantos completos**: módulo de caja (cajas, métodos de pago, bancos, ingresos/salidas) + payments polimórfico.
  - Facturación **multi-presupuesto** (pivote `invoice_estimate`).
  - **Doble origen**: desde OT (agrupa sus presupuestos con checkbox) y desde presupuestos (selección múltiple) + libre.
  - Series F/B por tipo de documento (FTR1/BLT1/FTC1/BLC1/FTD1/BLD1) ya contempladas en `PREFIX_MAP`.
- **Migraciones nuevas (11)**: `payment_methods`, `banks`, `cash_registers`, `payments` (polimórfico), `cash_movements`, `invoices`, `invoice_items`, `invoice_estimate` (pivote), `invoice_discounts` (02 global / 04 por anticipos), `dispatches` + `dispatch_items`. Serie/correlativo nullable (000400). Fix pre-existente `appointment_associated` en check_ins (000500).
- **Modelos (10 nuevos)**: `Invoice`, `InvoiceItem`, `InvoiceDiscount`, `Payment`, `PaymentMethod`, `Bank`, `CashRegister`, `CashMovement`, `Dispatch`, `DispatchItem`. Relaciones nuevas: `Estimate.invoices()/payments()`, `WorkOrder.invoices()`, `Invoice.globalDiscountAmount()`.
- **Servicios** (`app/Services/Facturacion/`): `FacturadorProviderInterface`, `NubefactProvider` (anticipos con `anticipo_regularizacion`, NC/ND, guías), `FacturaPeruProvider` (`anticipos[]` + descuento código 04, `documento_afectado.external_id`), `FacturadorProviderFactory` (nubefact/propio; error claro si LOCAL), `InvoiceService` (`createFromEstimates` advance/franchise/insurance/regular/free multi-presupuesto, `createFree`, `createNote`, `emit`, `void`).
- **Reglas de negocio**: 1 adelanto = 1 factura (sunat_tx 4); cierre agrupa todos los anticipos y reduce la base (total = servicio − anticipos); con anticipos NO hay descuento global (prorrateo por ítem); franquicia→cliente, aseguradora→total−franquicia; boleta si receptor sin RUC. `igv_rate` normalizado (0.18 o 18.00).
- **Verificación**: `php artisan migrate` OK. Smoke test real: libre 224.20, adelanto 300 (sunat_tx 4), cierre 538.08−300=238.08 con línea de regularización y payload Nubefact correcto. Suite completa: **163 tests pasando** (156 + fix de 7 en CheckInServiceTest).
- **Pendiente (Fases 3-5)**: `CashService`/`DispatchService`, controladores y rutas, vistas (facturas, guías, caja), navegación, permisos y tests con `Http::fake()` para ambos proveedores.

### 📌 Sesión: Facturación Electrónica — Fases 3 a 5 (UI + controladores + tests)
- **Fecha**: 01 de septiembre de 2026
- **Servicios nuevos**: `CashService` (abrir/cerrar caja con arqueo, `registerAdvance` → Payment + factura de adelanto + movimiento de caja, `registerPayment` polimórfico, `registerExpense`, `estimatePaid`) y `DispatchService` (crear guía remitente/transportista + `emit` con numeración TR01).
- **Controladores**: `InvoiceController` (index/create con **doble origen** OT→agrupa presupuestos / presupuestos múltiples / libre, store, show, emit, void, creditNote, debitNote, search JSON, parties para Tom Select), `DispatchController`, `CashController` (open/close/movements/advance/expense), `CashCatalogController` (métodos de pago y bancos).
- **Form Request**: `InvoiceCreateRequest` (valida origen, tipo, receptor, advance_amount, ítems libres).
- **Policies** (auto-descubiertas): `InvoicePolicy`, `DispatchPolicy`, `CashRegisterPolicy`, `PaymentMethodPolicy`, `BankPolicy`.
- **Permisos nuevos (seeder)**: `ver/crear/editar facturas`, `emitir comprobantes`, `anular facturas`, `ver/crear/editar/anular guías de remisión`, `ver caja`, `abrir/cerrar caja`, `registrar movimientos de caja`, `ver/crear/eliminar métodos de pago`, `ver/crear/eliminar bancos`. Asesor: ver/crear/editar facturas + emitir + caja + métodos/bancos.
- **Rutas nuevas**: resource invoices/dispatches + acciones emit/void/NC/ND, `api/invoices/search|parties`, `api/dispatches/search`, `cash/*`, `estimates/{estimate}/advance`.
- **Navegación**: grupo "Facturación" (Comprobantes, Guías de Remisión, Caja, Métodos de Pago, Bancos).
- **Vistas**: `invoices/index` (Tabulator + badges + acciones), `invoices/create` (selector OT/Presupuestos/Libre con Tom Select + ítems dinámicos), `invoices/show` (ítems/totales/respuesta proveedor + emit/anular/NC/ND), `dispatches/index|create|show`, `cash/index` (caja, egresos, arqueo, adelanto rápido, movimientos Tabulator), `cash/catalogs`.
- **Corrección de cálculo**: factura a aseguradora convierte la franquicia (monto total con IGV) a base imponible para el descuento global.
- **Fix de `appointment_associated` definitivo**: se revirtió el booleano → **atributo transitorio** (accessor + mutator que no persiste, mantiene el objeto `Appointment` como espera `AppointmentTest`).
- **Tests**: `tests/Feature/InvoiceFlowTest` (8 tests, 35 assertions) con `Http::fake()` para Nubefact: factura libre (DNI→boleta, RUC→factura), adelanto + cierre con regularización (total = servicio − anticipos), descuento global prorrateado por ítem con anticipos, aseguradora (total − franquicia), emisión con payload verificado + respuesta persistida, NC con snapshot del documento modificado, guía TR01.
- **Verificación**: `view:cache` OK (215 vistas), `php artisan route:list` OK, permisos sembrados en dev, **suite completa 171 tests pasando** (163 + 8 InvoiceFlow; los 3 de AppointmentTest corregidos por el atributo transitorio).
- **Pendiente menor**: botón/formulario de "Registrar adelanto" embebido en `estimates/show` (hoy se hace desde la caja), seeder de métodos de pago/bancos por defecto, y jobs de cola para la emisión en segundo plano.

### 📌 Sesión: Facturación — pendientes menores resueltos (adelanto en presupuesto + catálogos + jobs)
- **Fecha**: 01 de septiembre de 2026
- **Adelanto en `estimates/show`**: nueva card "Cobros y adelantos" (tabla de pagos con comprobante enlazado, total cobrado y saldo pendiente) + formulario compacto "Registrar adelanto" (monto, medio de pago, referencia) que POST a `estimates/{id}/advance`. `EstimateController::show` ahora carga `payments.invoice/paymentMethod` y pasa `$paymentMethods` + `$paidTotal`.
- **Seeder `PaymentMethodBankSeeder`** (agregado a `DatabaseSeeder`): 7 métodos de pago (Efectivo, Tarjeta, Yape, Plin, Transferencia, Depósito, Cheque) y 8 bancos (BCP, BBVA, Interbank, Scotiabank, Bco. Nación, BanBif, MiBanco, Caja Piura). Sembrado en dev (7/8 confirmados).
- **Jobs de cola**: `EmitInvoiceJob` y `EmitDispatchJob` (ShouldQueue, reintentos 2/backoff 15s, registra error en `sunat_description` si falla). Los controladores `InvoiceController::emit` y `DispatchController::emit` ahora **despachan el job** en lugar de emitir en línea (flash "Emisión en proceso"), con guarda de estado (solo borrador/rechazada).
- **Verificación**: `view:cache` OK (215 vistas), lint OK, seeder OK. `InvoiceFlowTest` ampliado a **9 tests / 37 assertions** (nuevo test `test_emit_invoice_job_emits_via_queue` con `dispatchSync`). Suite completa: **172 tests verdes**.
- **Para producción**: ejecutar `php artisan queue:work` (cola `database`) para procesar las emisiones en segundo plano.

### 📌 Sesión: Facturación multi-presupuesto completa (multi-vehículo, multi-documento, detección de relacionados)
- **Fecha**: 01 de septiembre de 2026
- **Objetivo**: garantizar los escenarios 1 presupuesto → N facturas (ya existía) y N presupuestos → 1 factura, incluyendo siniestro + ampliaciones, flota de vehículos, facturación por OT y detección automática de presupuestos relacionados.
- **Fase A — UX del selector**: `EstimateService::getSearchResults()` ahora devuelve `text` (arregla dropdown vacío), `vehicle_id`, `vehicle_plate`, `invoiced_total` y `billable_balance` (saldo pendiente = total − facturado no anulado). `WorkOrderController::search` devuelve `text` con placa/cliente/nº presupuestos/total.
- **Fase B — Detección de relacionados**: `Estimate::BILLABLE_STATUSES` (approved_insurance/approved_client/in_repair/finalized) + `getRelatedBillable()` y endpoints `api/estimates/related?estimate_id=X` (mismo vehículo **o** misma OT) y `api/estimates/by-vehicle?vehicle_id=X`. En `invoices/create`: **auto-sugerencia** (al elegir el primer presupuesto se preseleccionan los relacionados, deseleccionables) + botón "Agregar presupuestos del mismo vehículo".
- **Fase C — Guard anti doble facturación**: `InvoiceService::guardInvoiceType()` — estados facturables obligatorios; adelanto (suma de adelantos + nuevo ≤ total), franquicia (una sola), aseguradora (una sola), cierre/regular (sin otro cierre/aseguradora).
- **Fase D — Origen "Por Vehículo"**: 4ª opción en la vista create; Tom Select de vehículo → botón "Cargar presupuestos del vehículo" → llena el multi-select con sus presupuestos facturables y cambia al origen Presupuestos.
- **Fase E — Trazabilidad multi-vehículo**: `annotateVehiclePlates()` — si la factura agrupa presupuestos de varias placas, las lista en observaciones ("Placas: ABC-123, XYZ-789").
- **Tests**: `InvoiceFlowTest` ampliado a **13 tests / 46 assertions**: multi-presupuesto de 2 vehículos en 1 factura (pivote 2, ítems 2, placas en observaciones, total 1416), rechazo de doble cierre, rechazo de adelanto que excede el total, y endpoint `related` con presupuestos del mismo vehículo. `makeEstimate` ahora numeración incremental.
- **Verificación**: `view:cache` OK (215 vistas), lint OK, **suite completa 176 tests pasando (527 assertions)**.




### 📌 Sesión 1: Configuración inicial del proyecto
- **Fecha**: 17 de agosto de 2026
- **Tarea**: Configuración inicial de Laravel, base de datos, migraciones y seeders.
- **Detalles**:
  - Proyecto creado en `C:\laragon\www\taller` con Laravel 12.
  - Base de datos `taller` configurada (MySQL, usuario root, sin contraseña).
  - Migraciones ejecutadas para tablas: `ubigeos`, `establishments`, `users` (extendida), `roles`, `permissions`, etc.
  - Seeders ejecutados: `UbigeoSeeder`, `RolePermissionSeeder`, `EstablishmentSeeder`, `UserSeeder`.
  - Usuario administrador creado: `admin@taller.com` / `password`.
  - Layout base con Tailwind, Tabulator, ApexCharts y Tom Select configurado por CDN.
  - Autenticación básica con Laravel Breeze (adaptada a CDN).
- **Próximos pasos**: Desarrollar el módulo de Parties y Vehículos con el nuevo modelo de relaciones.

### 📌 Sesión 2 (sustituida): Primer esquema de Clientes/Vehículos
- **Fecha**: 18 de agosto de 2026
- **Detalle**: El primer esquema con `clients` y `vehicle_contacts` fue **reemplazado en la Sesión 3** por el nuevo modelo de "Partes Interesadas" (Parties) con `vehicle_relationships`. Los archivos del esquema anterior se eliminaron.

### 📌 Sesión 3: Módulo de Parties y Vehículos (nuevo modelo de relaciones)
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Desarrollo del módulo de Parties (personas/empresas) y Vehículos con el nuevo modelo de "Partes Interesadas" y "Relaciones de Vehículo", reemplazando al modelo anterior de clients/vehicle_contacts.
- **Detalles**:
  - **Instalación**: `spatie/laravel-activitylog` para auditoría (versión compatible con PHP 8.2).
  - **Migraciones nuevas**: `parties` (type, document_type, document_number, first_name/last_name/business_name, ubigeo, tarifas aseguradora, receive_promotions, FK establishment/ubigeo, created_by/updated_by, SoftDeletes), `vehicle_relationships` (vehicle_id, party_id, role enum owner/driver/approver/operator/billing/insurance_company/emergency_contact/other, is_primary_commercial, notes, SoftDeletes), `party_contacts` (contactos de una party, is_primary, SoftDeletes), `activity_log` (3 migraciones del paquete).
  - **Vehículos actualizados**: sin `client_id`, con `next_technical_review_date`, `technical_review_reminder_days` (default 15), `body_type` nullable.
  - **Modelos**: `Party`, `Vehicle` (actualizado), `VehicleRelationship`, `PartyContact` — todos con SoftDeletes y trait `LogsActivity` para auditoría.
  - **Factories**: `PartyFactory` (person/company/insuranceCompany), `VehicleFactory` (actualizada), `VehicleRelationshipFactory`, `PartyContactFactory`.
  - **Seeders**: `InsuranceCompanySeeder` (Rímac, Pacífico, Mapfre, La Positiva, Protecta, Interseguro con RUC y tarifas), `PartySeeder` (10 parties de ejemplo), `VehicleSeeder` (5 vehículos), `VehicleRelationshipSeeder` (14 relaciones; 5 vehículos con contacto comercial principal = true).
  - **Permisos**: `ver/crear/editar/eliminar parties` y `ver/crear/editar/eliminar vehículos`. Administrador tiene todos; Asesor ver/crear/editar.
  - **Form Requests**: `PartyRequest` (validación condicional persona/empresa), `VehicleRequest` (placa única + validación de `relationships.*`).
  - **Policies**: `PartyPolicy`, `VehiclePolicy` registradas en `AppServiceProvider`.
  - **Services**: `PartyService`, `VehicleService` (con sincronización de `vehicle_relationships` y auditoría created_by/updated_by).
  - **Controladores**: `PartyController` (resource + search AJAX + quickStore + provincias/distritos ubigeo), `VehicleController` (resource + search incluyendo propietario).
  - **Rutas**: `Route::resource('parties')`, `Route::resource('vehicles')`, `api/parties/search`, `api/parties/quick-store`, `api/vehicles/search`, `api/ubigeo/provincias|distritos` — bajo middleware `auth` + `verified`.
  - **Vistas**: `parties/index` (Tabulator), `parties/create|edit` (formulario 2 columnas, toggle persona/empresa, cascada ubigeo, tarifas aseguradora), `parties/show`; `vehicles/index` (con Propietario vía relación), `vehicles/create|edit` (sección de relaciones dinámicas con Tom Select, modal para crear party nueva vía AJAX, checkbox único "contacto comercial principal"), `vehicles/show` (badges de rol y contacto principal).
  - **Navegación**: enlace "Parties" reemplaza a "Clientes".
  - **Pruebas**: `tests/Feature/PartyTest` (6 pruebas), `tests/Feature/VehicleTest` (4 pruebas), `tests/Unit/PartyServiceTest` (3), `tests/Unit/VehicleServiceTest` (3) — **10 pruebas del módulo pasan (27 assertions)**.
- **Decisiones**:
  - Las parties reemplazan completamente a `clients`; los vehículos se relacionan mediante `vehicle_relationships` (muchos roles posibles por vehículo).
  - `is_primary_commercial` solo puede estar marcada en una relación por vehículo (enforced en service y en JS).
  - `display_name` en `Party` retorna razón social (empresa) o nombre completo (persona).
  - Se ejecutó `migrate:fresh --seed` para aplicar el nuevo esquema.
- **Commits**: `54ae111` (eliminación esquema anterior), `4775981` (esquema parties/vehicle_relationships/party_contacts + activitylog), `2490422` (factories y seeders), `5878e73` (requests/policies/services/controllers/rutas), `51ddfb7` (vistas), `413eb65` (pruebas), `e626d9b` (bitácora).
- **Próximos pasos**: Desarrollar el módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Corrección: Vista de detalle de parte (vehículos relacionados)
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Se corrigió `parties/show.blade.php` para mostrar los vehículos relacionados de forma legible y profesional.
- **Detalles**:
  - Tabla con placa (enlace a detalle), marca, modelo, año, color, rol y si es contacto comercial principal.
  - Badges de colores según rol (owner, driver, approver, operator, otros).
  - Se usa `$vehicle->pivot->role` y `$vehicle->pivot->is_primary_commercial`.
  - `PartyController::show` carga `vehicles.vehicleModel.brand`.
  - Manejo de valores nulos.

### 📌 Sesión 9: Gestión de sesiones, perfil de usuario y renovación automática
- **Fecha**: 18 de agosto de 2026
- **Detalles**:
  - `navigation.blade.php` reescrito con dropdown vanilla JS/Tailwind (Mi Perfil, Cerrar sesión).
  - Vista `profile/edit.blade.php` unificada (nombre, email, teléfono, contraseña).
  - `ProfileController::update` valida y actualiza con `Hash::make`.
  - Ruta `GET /api/keep-alive` (auth).
  - Interceptor 419 (recarga) y keep-alive cada 5 min en `app.blade.php`.

### 📌 Sesión 8: Ajustes búsqueda DNI/RUC (ubigeo desde BD) y UI
- **Fecha**: 18 de agosto de 2026
- **Detalles**:
  - `getRuc` usa código `ubigeo` para obtener departamento/provincia/distrito desde la tabla local `ubigeos` (fallback JSON).
  - `autoFillUbigeo` JS: cascada secuencial departamento → provincia → distrito (por `ubigeo_code` exacto). Copiado a `public/js/`.
  - Vistas create/edit: select documento junto al número; tarifas aseguradora en `(USD)`.

### 📌 Sesión 7: Tipo de documento con códigos SUNAT + búsqueda API corregida
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Eliminar el campo `type` de `parties` (BD), usar códigos SUNAT en `document_type` (1=DNI, 6=RUC, 4=CEX, 7=PAS, A=Céd. Diplomática), y corregir la consulta DNI/RUC a la API `dniruc.apisperu.com`.
- **Detalles**:
  - **Migración**: elimina columna `type`; `document_type` pasa de ENUM a `string(10)` (idempotente, convierte datos existentes DNI/RUC/PAS/CEX a 1/6/7/4).
  - **`ReniecSunatService`**: URL corregida a `https://dniruc.apisperu.com/api/v1` con `?token=` (query param). DNI valida 8 dígitos (permite cero inicial); RUC 11 dígitos. DNI devuelve `last_name` (apellidos) primero y `first_name` (nombres). RUC limpia dirección quitando sufijo " depto provincia distrito".
  - **`Party`**: `display_name` = `razón social` si existe, si no `apellidos nombre`; nuevo accessor `document_type_label`.
  - **`PartyService::normalizeData`**: RUC (`document_type === '6'`) limpia `first/last_name`; otros limpian `business_name`.
  - **Vistas `create/edit`**: campo "Tipo" eliminado; select de documento con códigos SUNAT; form toggle persona/empresa según `document_type`; botón "🔍 Buscar" habilitado solo para DNI(1)/RUC(6).
  - **JS `party-helper.js`**: envía `1`/`6`; valida 8/11 dígitos; autocompleta apellidos en `last_name`; botón toggle por tipo de documento.
  - **Seeders/factory/tests**: actualizados a códigos SUNAT sin `type`.
  - **Verificación**: `migrate` OK, `db:seed` OK (9 seeders), tests **41 passed (99 assertions)**.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 6: Consulta DNI/RUC (apisperu.com) y Tipo de Cambio SUNAT
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Integración de consulta de DNI/RUC vía API `apisperu.com` con autocompletado de formularios de Parties, y servicio de tipo de cambio SUNAT.
- **Detalles**:
  - **`ReniecSunatService`**: métodos `getDni($dni)` y `getRuc($ruc)` usando `Http` facade con token de apisperu.com. Devuelve datos normalizados en mayúsculas o `null` en error.
  - **`SunatExchangeService`**: métodos `getTipoCambio($fecha)` y `getTipoCambioMes($year, $month)` usando la API de `apis.net.pe`.
  - **Endpoints**: `POST /api/party/search-by-document` y `GET /api/tipo-cambio` (protegidos por `auth` + `Gate`).
  - **`resources/js/party-helper.js`** (también copiado a `public/js/`): JS vanilla con `fetch`, autocompleta `first_name`/`last_name` (DNI) o `business_name`/`address`/ubigeo (RUC) en mayúsculas.
  - **Vistas `parties/create` y `edit`**: botón "🔍 Buscar" junto a `document_number`, consulta automática al perder el foco (blur) con 8 dígitos (DNI) o 11 (RUC).
  - Los tokens se mantienen en el backend (`env`), nunca expuestos al frontend.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 5: OCR Sunarp mejorado (Tesseract.js v5) en formularios de vehículos
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Implementación completa de OCR para capturas de Sunarp en formularios de vehículos (crear y editar).
- **Detalles**:
  - **Tesseract.js v5** cargado desde CDN en `layouts/app.blade.php` (antes era v4 local solo en create).
  - **Partial compartido** `vehicles/_sunarp_modal.blade.php` reutilizado en `create` y `edit`: previsualización de imagen, `capture="environment"` para cámara móvil, panel de resultados y enlace a `https://sede.sunarp.gob.pe/`.
  - **Endpoints API**: `POST /api/brands/find-or-create` y `POST /api/models/find-or-create` crean marca/modelo automáticamente (mayúsculas) vía `BrandService` y `VehicleModelService`.
  - **Bug corregido**: select de marca en `edit.blade.php` no tenía `name="brand_id"`.
  - **Mutadores en `Vehicle`**: placa, VIN, motor y color en mayúsculas. `VehicleService` normaliza `color`.
  - **Tests**: **41 passed (99 assertions)**.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 4: Refactorización parties/vehículos (brands + models + OCR Sunarp)
- **Fecha**: 18 de agosto de 2026
- **Detalle**:
  - `parties` y `vehicles` **sin `establishment_id`** (los establecimientos se asociarán a presupuestos/inventarios/OTs).
  - Nuevas tablas `brands` y `models`; `vehicles` ahora usa `brand_id` y `model_id`, `technical_review_date`, `review_reminder_days`.
  - Modelos `Brand`, `VehicleModel`; servicios `BrandService::findOrCreateBrand()`, `VehicleModelService::findOrCreateModel()`.
  - `BrandModelSeeder` (17 marcas / 68 modelos), seeders idempotentes, permisos y policies de marcas/modelos.
  - Vistas con marca/modelo en cascada, modal OCR **Sunarp (Tesseract.js)** que autocompleta marca, modelo, año, color, VIN y motor.
  - Se eliminó el campo `Establecimiento` de las vistas `create`/`edit` de vehículos y del `VehicleController`.
  - `db:seed` idempotente verificado; tests: **16 passed**.
- **Commits**: `00b48d5`, `51b4b50`, `83899dc`, `0181a02`.
- **Próximos pasos**: Desarrollar el módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Correcciones y mejoras: Inventario Vehicular (check-in)
- **Fecha**: 19 de agosto de 2026
- **Tarea**: Corregir error `Attempt to read property "soat_expiration" on null` en `check-ins/create` y aplicar mejoras de UX solicitadas.
- **Detalles**:
  - **Bug corregido**: en `_form.blade.php` los campos de fechas usaban `$checkIn->soat_expiration?->format(...)` que fallaba en modo creación (cuando `$checkIn` es null). Corregido a `$checkIn?->soat_expiration?->format(...)` (doble null-safe).
  - **Checklist con botones**: se reemplazó el `<select>` por 4 botones circulares (✓ verde = bueno, ▲ ámbar = regular, ✕ rojo = malo, ● negro = no aplica) con estado activo (fondo de color, símbolo blanco) e inactivo (borde + símbolo del color). Se agregó leyenda explicativa. Layout responsive: 1 columna en móvil (nombre arriba, botones a la derecha, nota debajo) y 3 columnas en PC.
  - **Categoría oculta**: se eliminó la columna "Categoría" del checklist tanto en el formulario como en el detalle (se mantiene en BD `check_in_checklist_items.category` para uso futuro).
  - **Lado del vehículo oculto en daños**: se ocultó el select "Lado" en `_damages.blade.php`, se quitaron las referencias al lado en la lista de daños del formulario, en el JS al hacer clic (se fija `side = 'front'`) y en el detalle (`show`). La columna `side` se mantiene en `check_in_damages` con default `'front'` en `CheckInService::syncDamages` (validación ya no exige side) para uso futuro cuando se soporten imágenes por lado.
  - **Mockups JPG**: los usuarios pueden colocar imágenes `{body_type}.jpg` en `public/images/mockups/` (ej. `sedan.jpg`, `suv.jpg`, `pickup.jpg`, `camioneta.jpg`, `camion.jpg`, `moto.jpg`). El sistema busca `.jpg` → `.jpeg` → `.png` → `.svg` en ese orden.
- **Verificación**: `php artisan view:cache` OK (Blade compila sin errores) y las **15 pruebas del módulo CheckIn siguen pasando (40 assertions)**.
- **Próximos pasos**: Módulo de presupuestos.

### 📌 Módulo de Inventario Vehicular
- **Fecha**: 19 de agosto de 2026
- **Tarea**: Implementación del módulo de inventario (check-in) con checklist, daños (mockup simple), fotos y flujo de estados.
- **Detalles**:
  - **Migraciones y modelos**: `check_ins`, `check_in_checklist_items`, `check_in_checklist_results`, `check_in_damages`, `check_in_photos`.
    - `CheckIn` con relaciones: Vehicle, Party (client_id / insurance_company_id), Establishment, User (created_by/updated_by), hasMany checklistResults/damages/photos. SoftDeletes + LogsActivity.
    - Catálogo de checklist con categorías (EXTERIOR, MOTOR, INTERIOR, HERRAMIENTAS/EMERGENCIA).
  - **Controladores, servicios, políticas**:
    - `CheckInController`: CRUD completo + `approve()`, `reject()`, `sendToClient()`, `search()` (API Tabulator), `contacts()` (relaciones del vehículo), `uploadPhoto()`, `destroyPhoto()`, `insuranceCompanies()`.
    - `CheckInService`: lógica transaccional (sync checklist, daños, contactos como vehicle_relationships), flujo de estados.
    - `CheckInPolicy` con permisos `ver/crear/editar/eliminar/aprobar inventarios` (Administrador: todos; Asesor: ver/crear/editar).
    - `CheckInRequest` con validaciones (vehículo no duplicado en estado abierto, aseguradora debe ser compañía de seguros, checklist/daños dinámicos).
  - **Vistas**: listado con Tabulator (filtros placa/cliente/estado/rango de fechas), formulario con secciones (vehículo/propietario con autocompletado por placa, contactos del vehículo, datos de ingreso, checklist con "ver solo regulares y malos", daños con mockup clickeable y coordenadas %, fotos con subida AJAX), detalle con pestañas (General, Checklist, Daños, Fotos) y botones de estado.
  - **Seeders**: `ChecklistItemsSeeder` lee del CSV real (`C:\Users\Noel\Downloads\checklist_details.csv`) con parser que sanea la línea partida "PALANCA DE GATA" y fallback manual; `CheckInSeeder` crea 3 inventarios de ejemplo.
  - **Mockups**: SVGs placeholder en `public/images/mockups/` (sedan, suv, pickup, camioneta, camion, moto). El sistema busca `{body_type}.jpg` luego `.svg` y si no existe muestra mensaje con dropdowns.
  - **Almacenamiento**: `php artisan storage:link` creado para fotos en `storage/app/public/check-in-photos/`.
  - **Rutas**: `Route::resource('check-ins')` + POST approve/reject/send-to-client + API search/contacts/insurance-companies/photos.
  - **Navegación**: enlace "Inventario" agregado al menú principal.
  - **Pruebas**: `tests/Feature/CheckInTest` (9 pruebas), `tests/Unit/CheckInServiceTest` (6) — **15 pruebas del módulo pasan (40 assertions)**. Suite completa: 55 passed, 1 fail preexistente de `ProfileTest` (Laravel Breeze estándar, sin relación con el módulo).
- **Próximos pasos**: Módulo de presupuestos.

### 📌 Auditoría y estandarización de autocompletados (selección única)
- **Fecha**: 22 de agosto de 2026
- **Tarea**: Auditoría integral de todos los componentes de autocompletado (Tom Select) del frontend y corrección de la confusión visual de "selección múltiple simultánea" en el modal "Buscar / Registrar contacto" y demás dropdowns de búsqueda.
- **Diagnóstico (causa raíz)**:
  1. **CSS**: en `resources/views/layouts/app.blade.php`, los estados `:hover` y `.active` de las opciones del dropdown de Tom Select usaban exactamente el mismo estilo (`bg-blue-50 text-blue-800`). Al pasar el cursor sobre una opción mientras otra estaba activa (cursor/teclado), ambas se veían igual → apariencia de selección múltiple simultánea.
  2. **JS**: ningún Tom Select definía `closeAfterSelect: true` ni `maxItems: 1`. El dropdown permanecía abierto tras seleccionar y el contenedor no forzaba el modo "single" → reforzaba la confusión visual.
- **Archivos modificados**:
  - `resources/views/layouts/app.blade.php`: estados separados — `:hover` = gris claro sutil (`bg-gray-100`), `.active` = azul claro (`bg-blue-50`), `.selected` = azul sólido (`bg-blue-600`) con check SVG y subopciones en texto azul claro.
  - `resources/views/vehicles/_relationships.blade.php`: TomSelect del campo "Contacto" (`#rel-party-id`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
  - `resources/views/check-ins/_form-scripts.blade.php`: TomSelect de vehículo (`#vehicle_id`) y aseguradora (`#insurance_company_id`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
  - `resources/views/check-ins/index.blade.php`: TomSelect de filtro de cliente (`#f-client`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
- **Comportamiento nuevo de los autocompletados**:
  - Al hacer clic en una opción: el menú se cierra automáticamente (`closeAfterSelect`).
  - Solo se puede seleccionar UNA opción a la vez (`maxItems: 1`).
  - La opción seleccionada muestra fondo azul sólido con check; el hover sobre otras opciones es solo un resaltado gris temporal.
  - El CSS garantiza que nunca se marquen múltiples elementos simultáneamente.
- **Verificación**: los 4 componentes Tom Select del proyecto (cliente, vehículo, aseguradora, contacto/party) fueron estandarizados. No se alteró la funcionalidad de los formularios (`rel-party-id` sigue alimentando `party_id`; el filtro de cliente sigue refrescando Tabulator).
- **Corrección adicional (single estricto)**: campo "Contacto" se expandía en dos líneas con cursor parpadeante. CSS: altura fija 2.5rem, item plano, input oculto tras seleccionar. JS: blur()+close() en item_add (4 TomSelect) y clear() en type (Contacto).
- **Corrección adicional (cursor same-line al re-buscar)**: `.ts-control` ahora `flex`; input oculto con `visibility` + reaparece en la misma línea; `dropdown_open` con `setTextValue('')` + cursor al inicio en los 4 TomSelect. Estándar en `.clinerules/03-frontend.md`.
- **Próximos pasos**: continuar con el módulo de presupuestos.

### 📌 Sistema de diseño frontend (rediseño de listados con skill impeccable)
- **Fecha**: 23 de agosto de 2026
- **Tarea**: Rediseño profesional y moderno de los listados de **Usuarios, Vehículos, Contactos e Inventario**, y creación de un **sistema de diseño obligatorio** para todas las vistas futuras, aplicando el skill **impeccable** (modo Operate, dirección Restrained).
- **Diagnóstico (causas del aspecto deficiente)**:
  1. Botones del header (acciones primarias) sin jerarquía clara, con estilos ad-hoc en cada vista.
  2. Tablas Tabulator con tema por defecto: cabeceras sin contraste, filas sin hover suave, celdas apretadas, paginación sin color primario.
  3. Botones de acción en columnas con texto plano (Ver/Editar/Eliminar) en lugar de iconos.
  4. Búsqueda sin icono de lupa y con estilos inconsistentes.
  5. Mensajes flash con estilos dispares.
- **Nuevo sistema de diseño** (definido en `resources/views/layouts/app.blade.php` como Tailwind CDN `@layer components`):
  - **Botones**: `.btn` (base), `.btn-primary`, `.btn-secondary`, `.btn-danger` — `inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold`, transición 150ms `ease-out`, `focus-visible:ring-2`, hover/active, disabled.
  - **Botones de icono en tablas**: `.btn-icon` + `.btn-icon-blue` (ver), `.btn-icon-amber` (editar), `.btn-icon-red` (eliminar) — `h-8 w-8`, fondo `*-50`, color `*-600`, hover `*-100`, siempre con `title`.
  - **Cards**: `.card` (`bg-white rounded-lg border border-gray-200 shadow-sm`), reemplaza `bg-white shadow-sm sm:rounded-lg`.
  - **Búsqueda**: `.search-input` con icono de lupa SVG absoluto a la izquierda (`relative max-w-md`).
  - **Tablas Tabulator**: tema global — cabecera `#f8fafc` uppercase 0.75rem, filas con hover `#eff6ff`, bordes `#f1f5f9`, celdas `padding .625rem .75rem`, placeholder `#94a3b8`, paginación con página activa en `#2563eb`.
  - **Micro-interacciones**: hover en filas y botones (150ms), superficies del navegador themeadas (`::selection` = `#bfdbfe`/`#1e3a8a`).
- **Vistas rediseñadas** (botones con iconos, card, búsqueda con lupa, acciones con `.btn-icon`):
  - `resources/views/users/index.blade.php`
  - `resources/views/vehicles/index.blade.php`
  - `resources/views/parties/index.blade.php`
  - `resources/views/check-ins/index.blade.php`
- **Reglas permanentes**: creado **`.clinerules/07-diseno-sistema.md`** con paleta exacta, tipografía, radios de borde, sombras, patrones de componentes (botones, inputs, tablas), tono/UX (estados de carga, vacíos y errores) y checklist de verificación obligatorio para toda vista nueva o existente.
- **Verificación**: `php artisan view:cache` exitoso (todas las vistas compilan); funcionalidad de negocio y JavaScript sin cambios.
- **Próximos pasos**: aplicar el sistema de diseño a vistas de formulario (create/edit) si el usuario lo solicita.

### 📌 Seguridad y UX en formularios (CSRF refresh + Anti-doble envío)
- **Fecha**: 23 de agosto de 2026
- **Tarea**: Corrección del error de expiración del token CSRF en Login y prevención de duplicidad de datos por doble clic en todos los formularios del proyecto.
- **Detalles**:
  - **Endpoint público** `GET /api/csrf-token` en `routes/web.php`: devuelve un token CSRF fresco (`csrf_token`). Al tocarlo, Laravel renueva la actividad de la sesión (o crea una nueva si expiró), habilitando que cualquier formulario se pueda enviar aunque la página haya estado inactiva mucho tiempo.
  - **Partial global** `resources/views/partials/form-guard.blade.php` (nuevo):
    - **Regla CSRF**: intercepta el `submit` de cualquier formulario (event delegation en `document` con capture), llama a `GET /api/csrf-token` y actualiza el meta `csrf-token` y los inputs ocultos `_token` justo antes de `form.submit()` programático. Cubre también los formularios generados dinámicamente por Tabulator.
    - **Regla Anti-duplicados**: marca `data-submitting="1"` (flag) antes del fetch para bloquear doble clic ráfaga; deshabilita `button[type="submit"]` y muestra spinner + "Guardando..." (personalizable con `data-loading-text`). Respeta los `onsubmit` inline que cancelan (confirm/prompt rechazado) y permite reintentar tras error de validación.
  - **Layouts protegidos**: el partial se incluyó en `layouts/guest.blade.php` (login, register, reset/forgot/confirm password, verify-email) y en `layouts/app.blade.php` (todos los formularios autenticados: parties, vehicles, check-ins, users, profile, acciones de estado y deletes).
  - **Modales AJAX protegidos** con flag booleano `saving` (bloquea reentrada, re-habilita en `finally`): `partials/contact-modal.blade.php` (quick-store/quick-update de parties) y `check-ins/_vehicle_modal.blade.php` (quick-store/quick-update de vehículos). En ambos, el botón se deshabilita y muestra "Guardando..." durante el fetch.
  - **Verificación**: `php artisan view:cache` OK (todas las vistas compilan); las confirmaciones de delete/estado y el prompt de rechazo siguen funcionando (el guard respeta `e.defaultPrevented`).
  - **Reglas del proyecto**: creado `.clinerules/08-seguridad-forms.md` con las dos reglas obligatorias (CSRF refresh antes del envío + Anti-duplicados con flag booleano y botón deshabilitado), incluida la implementación estándar para formularios tradicionales y modales AJAX, para que todo desarrollo futuro las cumpla sin duplicar lógica.
- **Próximos pasos**: continuar con el módulo de presupuestos.

### 📌 Rediseño del Login (pantalla dividida)
- **Fecha**: 23 de agosto de 2026
- **Tarea**: Rediseño completo de la pantalla de login del Taller Mecánico con el skill **impeccable**, reemplazando el diseño por defecto de Laravel Breeze.
- **Detalles**:
  - **Nuevo partial** `resources/views/partials/auth-split-side.blade.php`: panel izquierdo reutilizable con temática de taller mecánico (gradiente azul petróleo/gris acero, engranajes decorativos, logo del taller, etapas del flujo del vehículo y footer). En móvil (`< lg`) se oculta.
  - **Layout guest con variantes**: `app/View/Components/GuestLayout.php` ahora acepta `variant` (`centered` por defecto | `split`), y `resources/views/layouts/guest.blade.php` renderiza la pantalla dividida cuando la variante es `split`. El resto de vistas guest (forgot-password, reset, verify-email, register) conservan la variante `centered`.
  - **Login rediseñado** `resources/views/auth/login.blade.php`: usa `<x-guest-layout variant="split">`, formulario centrado sobre fondo claro con iconos en inputs (usuario/candado), botón mostrar/ocultar contraseña, enlace "¿Olvidaste tu contraseña?" alineado, checkbox "Recordarme", botón "Iniciar sesión" full-width y logo compacto en móvil. Labels/textos traducidos a español.
  - **Seguridad intacta**: se mantuvieron `name` de inputs (`email`, `password`, `_token`), `action="{{ route('login') }}"`, `@csrf`, y el guard global `partials/form-guard.blade.php` (renovación CSRF pre-envío + anti-doble clic).
  - **Regla permanente**: actualizado `.clinerules/07-diseno-sistema.md` con la sección **"Login (pantalla dividida — obligatorio)"**: todos los logins deben usar split screen con temática de taller mecánico; prohibido el diseño por defecto de Laravel Breeze para el login principal.
  - **Verificación**: `php artisan view:cache` exitoso (todas las vistas compilan, incluido el nuevo partial y la variante split).
- **Próximos pasos**: continuar con el módulo de presupuestos.

### 📌 Sistema de numeración por series, configuración de empresa y establecimientos
- **Fecha**: 24 de agosto de 2026
- **Tarea**: Implementación completa del sistema de numeración por series (DocumentSeriesService con lock pesimista), configuración global de empresa (company_settings) y módulo de establecimientos con sus series editables. Integración del servicio en el flujo de creación de `check_ins` (IV01-XXXXX).
- **Migraciones nuevas** (`2026_08_24_*`):
  - `company_settings`: RUC (unique nullable), razón social, nombre comercial, dirección, ubigeo (depto/provincia/distrito), teléfono, celular, email, logo_path, favicon_path, `detraccion_account`, `default_number_source` (enum LOCAL/API, default LOCAL), `facturador_provider` (enum local/nubefact/propio), facturador_api_url/key/secret, whatsapp_api_url/token, timestamps. Una sola fila global.
  - `document_types`: code (unique), name, is_electronic (bool), is_active (bool).
  - `document_series`: establishment_id (FK), document_type_id (FK), prefix_serie, current_number (int default 0), number_source (enum LOCAL/API), status (bool), unique(establishment_id, document_type_id).
  - `check_ins` ampliada: `document_series_id` (FK nullable) y `document_number` (string nullable).
  - `establishments` ampliada: `celular`, `ubigeo_departamento`, `ubigeo_provincia`, `ubigeo_distrito` (datos editables que se copian desde company_settings).
- **Modelos**: `CompanySetting` (with accessors logo_url/favicon_url + `CompanySetting::get()`), `DocumentType`, `DocumentSeries` (with `formatted_number` accessor), `Establishment` actualizado con relaciones `documentSeries` y `checkIns`, `CheckIn` con `document_series_id`/`document_number` en fillable y relación `documentSeries`.
- **Servicio `DocumentSeriesService`**:
  - `generateSeriesForEstablishment($establishmentId)`: consulta `DocumentType::where('is_active', true)`, crea una fila en `document_series` por tipo con `prefix_serie` = código del documento, `current_number = 0` y `number_source` heredado de `company_settings->default_number_source`.
  - `getNextNumber($establishmentId, $documentTypeCode)`: `DB::transaction()` + `lockForUpdate()` sobre la fila de `document_series`. Si `number_source` es LOCAL, incrementa y devuelve `PREFIJO-00001`; si es API, devuelve `null`. Lanza RuntimeException si la serie no está activa.
  - `updateSeries()`: actualización manual de `prefix_serie`, `current_number`, `number_source` y `status`.
- **Seeders**:
  - `DocumentTypeSeeder`: 16 documentos (FTR1, BLT1, FTC1, BLC1, FTD1, BLD1, TR01 electrónicos; NV01, NIA1, NSA1, NTA1, PRE01, OT01, IV01, CST01, LST01 internos).
  - `CompanySettingSeeder`: fila global con valores por defecto (RUC/Razón Social vacíos, source LOCAL, provider local).
  - `DocumentSeriesSeeder`: ejecuta `generateSeriesForEstablishment()` para todos los establecimientos.
  - `DatabaseSeeder` actualizado (orden: Ubigeo → Roles → CompanySetting → DocumentType → Establishment → DocumentSeries → ...).
  - `RolePermissionSeeder`: nuevos permisos `ver/editar configuración`, `ver/crear/editar/eliminar establecimientos`, `ver/editar series`.
- **Controladores**: `CompanySettingController` (edit/update con subida de logo/favicon), `EstablishmentController` (CRUD + `copyFromCompany()` que copia solo datos editables y **nunca RUC/Razón Social/Nombre Comercial** + `regenerateSeries()`), `DocumentSeriesController` (index/update de series por establecimiento).
- **Vistas (estilo Vehículos)**: `company-settings/edit` con 4 pestañas (Datos Fiscales y Ubigeo, Branding con previsualización en vivo, Detracción y Contacto, Integraciones con selector LOCAL/API); `establishments/index` con Tabulator y columna Acciones con `.btn-icon`; `create`/`edit` con formulario tipo Vehículos, botón "Copiar datos editables de la empresa" y "Regenerar series"; `establishments/series` con edición inline de prefijo, número actual, origen y estado de cada serie.
- **Integración en check-ins**: `CheckInService::create()` asigna `document_series_id` y `document_number` (IV01-XXXXX) vía `DocumentSeriesService::getNextNumber()` antes de crear. El formulario de check-ins muestra la sección "Documento" con el número generado (formateado en mono) y la serie.
- **Navegación**: enlaces "Establecimientos" y "Configuración" (protegidos con `@can`) en menú desktop y móvil.
- **Reglas permanentes**: creado `.clinerules/10-numeracion-configuracion.md` con las 3 reglas obligatorias (Configuración, Numeración, Estilo).
- **Pruebas** (`tests/Feature/CheckInTest.php` y `tests/Unit/CheckInServiceTest.php`): se actualizaron los `setUp()` para crear el `DocumentType` IV01 y su `DocumentSeries` (LOCAL) antes de usar el servicio. **19 tests del módulo CheckIn pasan (54 assertions)** — la integración de numeración IV01 funciona correctamente.
- **Verificación**: `php artisan migrate:fresh --seed` **OK**. Base resultado: 16 tipos de documento, 1 company_settings, 1 establecimiento, **16 series generadas** (una por documento), 3 check-ins, 38 permisos. Prueba directa de `getNextNumber('IV01')` devolvió `IV01-00001` e incrementó `current_number` a 1 (lock pesimista funcionando). Archivos temporales de prueba eliminados.
- **Corrección posterior (ubigeo con ubigeo_code — patrón Parties)**: En `company_settings` y `establishments` se reemplazaron los 3 campos de texto (`ubigeo_departamento`, `ubigeo_provincia`, `ubigeo_distrito`) por un único `ubigeo_code` (string 6, FK a `ubigeos.code`). Se actualizaron modelos (`CompanySetting`, `Establishment` con relación `ubigeo()`), controladores (`CompanySettingController`, `EstablishmentController` con `$departamentos` a la vista, validación `exists:ubigeos,code` y `copyFromCompany()` copiando `ubigeo_code`), y vistas (`company-settings/edit` Pestaña 1 y `establishments/_form`) con 3 selects en cascada idénticos al módulo de Parties. Se creó el partial reutilizable `resources/views/partials/ubigeo-cascade.blade.php` con pre-carga de valores guardados vía `api/ubigeo/resolve`. Seeders completados con datos en todos los campos: `CompanySettingSeeder` (RUC, razón social, dirección, teléfono, celular, email, detracción, integraciones, `ubigeo_code`), `EstablishmentSeeder` (`ubigeo_code` + datos), `PartySeeder` (10 parties todas con `ubigeo_code`), `InsuranceCompanySeeder` (6 aseguradoras con dirección y `ubigeo_code`). Orden de seeders confirmado: Ubigeo → Roles → Configuración → Establecimientos → DocumentTypes → Series → ... → Inventario. **`migrate:fresh --seed` OK** (28 migraciones, todas Ran; `CompanySetting.ubigeo_code=150101`, `Establishment.ubigeo_code=150101`, 16/16 parties con ubigeo, 16 series). **19 tests del módulo CheckIn pasan (54 assertions)**.
- **Corrección posterior (códigos SUNAT del facturador + correlativos en seeders)**: Se alineó el esquema de documentos con el dump del facturador (`cat_document_types` y `series`):
  - **`document_types`**: ahora **14 tipos** donde `code` es el **código SUNAT** ('01' Factura, '03' Boleta, '07' NC, '08' ND, '09' Guía Remisión, '80' Nota Venta, 'U2'/'U3'/'U4' Guías, 'PRE', 'OT', 'IV', 'CST', 'LST'), coincidiendo con `cat_document_types.id` del facturador.
  - **`document_series`**: unique cambiado a `(establishment_id, document_type_id, prefix_serie)` para permitir **2 series por tipo** para NC ('07' → FTC1/BLC1 factura/boleta) y ND ('08' → FTD1/BLD1), igual que el facturador.
  - **`DocumentSeriesService`**: nuevo `PREFIX_MAP` que genera los prefijos por código (ej. '07' → ['FTC1','BLC1']); `getNextNumber($establishmentId, $code, $prefix = null)` acepta prefijo opcional (si es null usa la primera serie activa).
  - **Integración**: `CheckInService::assignDocumentNumber()` usa código `'IV'` (serie IV01); **`CheckInSeeder` ahora llama a `getNextNumber('IV')`** para asignar `document_series_id` + `document_number` a cada inventario (resultado: IV01-00001, IV01-00002, IV01-00003 y `current_number=3`).
  - **Tests**: `CheckInServiceTest` y `CheckInTest` actualizados a código 'IV'; **nuevo `DocumentSeriesServiceTest`** con 3 pruebas (NC/ND generan 2 series c/u, `getNextNumber` por prefijo, primer activo sin prefijo).
  - **Verificación**: **`migrate:fresh --seed` OK** → 14 tipos, **16 series** (NC=FTC1,BLC1; ND=FTD1,BLD1), 3 check-ins con números IV01-00001..00003 y `current_number=3`. **22 tests pasan (67 assertions)** (19 CheckIn + 3 DocumentSeriesService).
- **Próximos pasos**: continuar con el módulo de presupuestos (Estimate) usando el mismo servicio de numeración para PRE01.

### 📌 Identidad de documento completa en check_ins (document_type_code + serie + número + sn)
- **Fecha**: 24 de agosto de 2026
- **Tarea**: Almacenar en cada `check_ins` el snapshot completo de la identidad del documento emitido para permitir búsqueda por serie, número o string completo, y estandarizar la visualización de documentos internos y electrónicos (Serie + Código SUNAT).
- **Cambios**:
  - **Nueva migración** `2026_08_24_000007_add_document_identity_to_check_ins_table.php`: agrega `document_type_code` (código SUNAT/catálogo), `document_serie` (prefijo), `document_sn` (`SSSS-XXXXXX`) + backfill desde `document_series`/`document_types` + índices en `document_sn`, `document_serie`, `document_number`.
  - `DocumentSeriesService::getNextNumber()` ahora devuelve `{series, number (int), sn ("IV01-000001"), document_type_code ("IV")}`.
  - `CheckInService::assignDocumentNumber()` y `CheckInSeeder` rellenan el snapshot completo (type_code, serie, number, sn).
  - `CheckIn` (modelo): nueva columnas en `fillable`, accessor `formatted_document_number` = `document_sn`.
  - `CheckInController::search()`: filtro `q` ahora incluye `document_sn`, `document_serie`, `document_type_code` y `document_number` (por número correlativo) para buscar por serie, número o completo; expone `document_type_code`, `document_serie`, `document_number`, `document_sn`, `document_type_name`, `is_electronic`.
  - Listado de inventario: 1ª columna **Documento** (`sn`, enlace), nueva columna compacta **Serie · SUNAT** (`IV01 · IV` con badge E/I) y columna **N°** (correlativo).
  - Form y detalle de check-in: sección Documento con Tipo (SUNAT), Serie, N° correlativo y Documento completo (`SSSS-XXXXXX`).
  - **Regla permanente**: `.clinerules/10-numeracion-configuracion.md` con "Regla de Identidad de Documento (snapshot en el documento emitido)".
- **Verificación**: `php artisan migrate:fresh --seed` OK → 3 check-ins con `IV | IV01 | 1..3 | IV01-000001..000003`, serie IV01 `current_number=3`; `php artisan view:cache` OK; tests del módulo de numeración/check-ins **28 passed (90 assertions)**.
- **Próximos pasos**: continuar con el módulo de presupuestos (Estimate) usando el mismo servicio de numeración para PRE01.

### 📌 Modal global de confirmación de eliminación (corrige bug "Cancelar borra")
- **Fecha**: 24 de agosto de 2026
- **Tarea**: Corregir el bug en el que al presionar "Cancelar" en la confirmación de eliminación igual se borraba el registro (probado en contactos), y estandarizar la confirmación con un modal propio del sistema.
- **Causa raíz**: `form-guard.blade.php` registra el listener de `submit` con `useCapture: true`, por lo que **se ejecuta antes** que el `onsubmit="return confirm(...)"` inline. Al cancelar el `confirm()`, form-guard ya había hecho `preventDefault()` y encolado `form.submit()` programático (que no vuelve a pasar por el `onsubmit`), por lo que el registro se eliminaba igual. Afectaba a todos los formularios con `confirm()`.
- **Cambios**:
  - **Nuevo `resources/views/partials/confirm-modal.blade.php`**: modal global con estilos del sistema (card, `.btn-secondary` Cancelar + `.btn-danger` Eliminar, overlay, cierre por clic fuera/Escape). Expone `window.ConfirmModal.open(form)`; al confirmar marca `data-confirmed="1"` y llama `form.requestSubmit()`, que vuelve a pasar por form-guard (refresh CSRF + anti-doble envío).
  - `resources/views/partials/form-guard.blade.php`: nueva compuerta al inicio del handler `submit` — si el form tiene `data-confirm` y no `data-confirmed`, hace `preventDefault()+stopImmediatePropagation()` y abre el modal. **Cancelar ya no borra**; confirmar sí envía.
  - `resources/views/layouts/app.blade.php`: incluye `partials.confirm-modal` antes que `form-guard`.
  - Reemplazado `onsubmit="return confirm('...')"` por `data-confirm="..."` en 8 vistas: `parties/index`, `vehicles/index`, `users/index`, `check-ins/index`, `establishments/index`, `parties/show`, `vehicles/show` y `establishments/edit` (copiar datos + regenerar series).
  - **Regla permanente**: actualizado `.clinerules/08-seguridad-forms.md` con "Regla de Confirmación de Eliminación": toda acción destructiva se confirma con el modal global (`data-confirm`), **prohibido `onsubmit="return confirm(...)"`**.
- **Verificación**: `php artisan view:cache` OK; búsqueda confirma que no queda ningún `onsubmit="return confirm(...)"` activo (solo mención en comentario del partial). Comportamiento: Cancelar cierra el modal sin enviar; Confirmar envía el DELETE con CSRF renovado.
- **Pendiente (fuera de alcance)**: `check-ins/show` usa `prompt()` en "Rechazar" con el mismo patrón de bug; conviene migrarlo a un modal de motivo en otra iteración.
- **Próximos pasos**: continuar con el módulo de presupuestos (Estimate) usando el mismo servicio de numeración para PRE01.

### 📌 Ajustes de numeración y tablas compactas (correlativo separado + estilo Vehículos)
- **Fecha**: 24 de agosto de 2026
- **Tarea**: Corregir el formato de serie (sin "00000" confuso), separar serie y número correlativo en `check_ins`, reemplazar el menú `⋮` de series por la columna de acciones `.btn-icon` del módulo de Vehículos, compactar/centrar las tablas, y rediseñar el formulario de establecimiento (acciones rápidas).
- **Cambios**:
  - `app/Models/DocumentSeries.php`: `formatted_number` ahora devuelve solo el prefijo (`BLT1`) cuando `current_number = 0` (serie sin uso) y `PREFIJO-000001` (6 dígitos) cuando ya tiene correlativo.
  - **Nueva migración** `2026_08_24_000006_convert_document_number_to_integer_in_check_ins_table.php`: convierte `check_ins.document_number` de string `"IV01-00001"` a **entero correlativo** (`1`) con backfill del sufijo; down() restaura string.
  - `app/Models/CheckIn.php`: cast `document_number` a `integer` + accessors `document_serie` (prefijo desde la serie) y `formatted_document_number` (`IV01-000001`).
  - `app/Services/DocumentSeriesService.php`: `getNextNumber()` ahora devuelve el correlativo **entero** (lock pesimista intacto). `CheckInService::assignDocumentNumber()` y `CheckInSeeder` lo usan tal cual.
  - `app/Http/Controllers/CheckInController.php`: `search()` carga `documentSeries` y expone `document_number`, `document_serie`, `formatted_document_number`; `show()` carga `documentSeries`.
  - `resources/views/establishments/series.blade.php`: columna "Acciones" con `.btn-icon` editar/eliminar (reemplaza el menú `⋮`), `headerHozAlign: center`, y la columna "Serie Actual" ya no muestra `-00000`.
  - `resources/views/check-ins/index.blade.php`: **correlativo como 1ª columna** ("Documento", mono, enlace a `show`); placa como texto normal; título "Acciones" centrado; eliminada la columna "Fecha" (`created_at`).
  - `resources/views/check-ins/_form.blade.php`: sección Documento con 3 campos readonly (Serie, Número correlativo, Documento completo).
  - `resources/views/check-ins/show.blade.php`: header muestra `Inventario IV01-000001 — PLACA` (mono) en lugar del id interno.
  - `resources/views/layouts/app.blade.php`: `.btn-icon` normalizado a `h-8 w-8` (regla `.clinerules/07`).
  - `resources/views/establishments/create.blade.php` y `edit.blade.php`: card `.card`, botones `.btn-primary`/`.btn-secondary`, "Acciones rápidas" con etiquetas cortas + iconos `shrink-0` que ya no desbordan; añadido flash de error rojo.
- **Pruebas**: actualizados `tests/Unit/DocumentSeriesServiceTest.php` y `tests/Feature/DocumentSeriesTest.php` al correlativo entero. **28 tests del módulo de numeración/check-ins pasan (85 assertions)**.
- **Verificación**: `php artisan migrate:fresh --seed` **OK** (3 check-ins seedeados con `document_number` entero 1/2/3 → `IV01-000001..000003`, serie IV01 `current_number=3`); `php artisan view:cache` OK.
- **Próximos pasos**: continuar con el módulo de presupuestos (Estimate) usando el mismo servicio de numeración para PRE01.

### 📌 CRUD completo de Series + rediseño compacto (estilo Vehículos + Impeccable)
- **Fecha**: 24 de agosto de 2026
- **Tarea**: Implementar crear/editar/eliminar de series de documentos por establecimiento y rediseñar el listado de Series para que sea compacto, escaneable y consistente con el sistema de diseño del módulo de Vehículos, aplicando los criterios de auditoría del skill **impeccable** (modo Operate).
- **Diagnóstico (causas del aspecto deficiente de la vista de series)**:
  1. La tabla tenía 8 columnas (Serie, Código, Documento, Electrónico, Prefijo, N. Actual, Origen, Estado) más una columna Acciones de 220px que incrustaba un formulario de edición inline (input prefijo + input número + select origen + checkbox estado + botón) dentro de cada fila → filas altas y saturadas, difícil de escanear.
  2. No existían permisos `crear series` ni `eliminar series`, ni rutas `store`/`destroy`.
  3. No había validación de documentos asociados al eliminar una serie (la FK `document_series_id` de `check_ins` fallaría con error crudo de BD).
  4. No había mensaje flash de error en `establishments/index`.
- **Backend**:
  - `database/seeders/RolePermissionSeeder.php`: nuevos permisos `crear series` y `eliminar series` (Administrador los recibe vía `Permission::all()`).
  - `routes/web.php`: `POST establishments/{establishment}/series` (store) y `DELETE establishments/{establishment}/series/{series}` (destroy).
  - **Nuevo `app/Http/Requests/SeriesRequest.php`**: validación de `document_type_id` (required en post, exists activo), `prefix_serie` (required, max 10, unique compuesto por `establishment_id + document_type_id + prefix`, ignore en update), `current_number` (integer min 0), `number_source` (in LOCAL/API), `status` (boolean) + mensajes en español.
  - `app/Services/DocumentSeriesService.php`: `createSeries()` (normaliza prefijo a mayúsculas, respeta numeración con `getNextNumber`), `hasAssociatedDocuments()` (verifica `check_ins.document_series_id`) y `destroy()`.
  - `app/Http/Controllers/DocumentSeriesController.php`: `store()` (Gate `crear series`), `destroy()` (Gate `eliminar series` + validación de documentos asociados → mensaje de error claro) e `index()` (ahora pasa `$documentTypes` activos para el modal de creación).
- **Frontend (estilo Vehículos / Impeccable)**:
  - `resources/views/establishments/series.blade.php` rediseñada:
    - Header con botón "Nueva Serie" (`.btn-primary`) y "Volver" (`.btn-secondary`).
    - Tabla Tabulator compacta de **5 columnas** (Documento con badge Electrónico/Interno + código mono; "Serie Actual" en una sola columna `PREFIJO-00015`; Origen pill LOCAL/API; Estado pill Activa/Inactiva; menú de acciones `⋮` de 56px).
    - Menú de acciones flotante (three-dots) con Editar/Eliminar, cierre por clic fuera/Escape/scroll, posicionamiento anti-desborde.
    - Modal crear/editar con flag `saving` anti-doble envío, botón "Guardando...", errores 422 inline por campo, CSRF leído del meta al momento del envío (regla `.clinerules/08`), tipo de documento readonly en edición.
    - Modal de confirmación de eliminación con `@method('DELETE')`; si hay documentos asociados el servidor redirige con `error` y la vista muestra flash rojo.
  - `resources/views/establishments/index.blade.php`: agregado `session('error')` (flash rojo) para consistencia.
- **Regla permanente**: creado `.clinerules/11-tablas-compactas.md` con la regla obligatoria: "Todos los listados y tablas deben ser compactos, usando badges para estados y menús de acción agrupados. Prohibido mostrar timestamps en tablas de administración", más el patrón de menú `⋮` y verificación.
- **Pruebas**: **nuevo `tests/Feature/DocumentSeriesTest.php`** con 6 pruebas (crear serie normalizando prefijo, duplicado rechazado, editar serie, no eliminar serie con check-ins asociados, eliminar serie sin documentos, usuario sin permiso rechazado → 403). **6 passed (18 assertions)**.
- **Verificación**: `php artisan route:list` muestra las 5 rutas de series (index/store/update/destroy + regenerate); `php artisan db:seed --class=RolePermissionSeeder` OK (permisos nuevos); `php artisan view:cache` OK; suite completa: **82 passed, 3 failed preexistentes** (ProfileTest del Breeze + ExampleTest redirect de `/`, sin relación con el módulo, ya documentados en sesiones anteriores).
- **Próximos pasos**: continuar con el módulo de presupuestos (Estimate) usando el mismo servicio de numeración para PRE01.

---

A partir de ahora, esta bitácora se actualizará automáticamente por el asistente (DeepSeek) en cada hito importante del desarrollo. Los registros incluirán fecha, tarea realizada, decisiones tomadas y próximos pasos.

---

### 📌 Módulo de Órdenes de Trabajo (OT) — agrupación de presupuestos aprobados
- **Fecha**: 27 de agosto de 2026
- **Tarea**: Implementar el módulo de Órdenes de Trabajo con el diseño acordado: **una OT agrupa todos los presupuestos aprobados de una visita** (y puede recibir presupuestos de reingresos), mediante FK nullable `estimates.work_order_id` (muchos-a-uno, **sin tabla intermedia**).
- **Modelo de datos**:
  - **3 migraciones nuevas**: `work_orders` (con snapshot de identidad `document_type_code`='OT', `document_serie`, `document_number`, `document_sn` + series `OT01` vía `DocumentSeriesService`), `work_order_substages` (catálogo global) + `work_order_assignments` (técnico, horas, costo, status), y `add_work_order_id_to_estimates_and_check_ins` (FK nullable en `estimates` y `check_ins`).
  - `CheckIn.work_order_id` vincula el ingreso original y los **reingresos** (retorno por repuesto pendiente: misma OT, sin nuevo presupuesto).
- **Lógica de negocio** (`app/Services/WorkOrderService.php`):
  - `createFromEstimates()`: valida presupuestos aprobados sin OT y del mismo vehículo → crea OT con numeración OT01 (lock pesimista), marca presupuestos `in_repair` + `EstimateStatusHistory`, vincula check-ins.
  - `attachEstimate()`: anexa un adicional aprobado a la OT existente (daño nuevo en reingreso).
  - `detachEstimate()` / `delete()`: revierten el presupuesto a estado aprobado previo (usa el historial) y desvinculan check-ins.
  - `changeStatus()` con transiciones validadas; nuevo estado **`delivered_pending`** (Entregado con pendientes — backorder: el vehículo sale pero la OT no se cierra).
  - Asignaciones: `addAssignment` / `updateAssignmentStatus` / `deleteAssignment`.
- **Controlador/rutas**: `WorkOrderController` (index/show/store/destroy + attach/detach/transition/assignments + `search` JSON con `withCount`/`withSum` de presupuestos). 11 rutas nuevas `work-orders.*` + `api.work-orders.search`.
- **Permisos**: `ver/crear/editar/eliminar órdenes de trabajo` (Admin todas, Asesor ver/crear/editar, Técnico ver) + `WorkOrderPolicy` registrada.
- **Vistas**: `work-orders/index` (Tabulator estilo Vehículos, badge de estado, acciones `.btn-icon`, sin timestamps) y `work-orders/show` (info + presupuestos vinculados + anexar presupuesto + subetapas/técnicos + visitas). Botón **"Generar OT"** agregado en `check-ins/show` (agrupa presupuestos aprobados del ingreso) y en `estimates/show` (reemplaza "Iniciar reparación" cuando hay OT pendiente de generar; muestra "Ver OT" si ya existe).
- **Subetapas**: `WorkOrderSubstageSeeder` con 6 subetapas por defecto (Recepción/diagnóstico, Desabolladura/pintura, Mecánica, Electricidad, Instalación de repuestos, Control de calidad).
- **Pruebas**: prueba funcional round-trip ejecutada sobre la BD real: creación `OT01-000001`, presupuesto → `in_repair`, check-in vinculado, transición `open→in_progress`, asignación + cambio de estado, y eliminación que revierte el presupuesto a `approved_client`. `php artisan view:cache` OK. `php artisan migrate` OK (3 migraciones aplicadas).

### 📌 Tests automatizados del módulo OT + suite completa en verde
- **Fecha**: 28 de agosto de 2026
- **Tarea**: Cerrar lo pendiente del módulo OT: tests automatizados profesionales y suite PHPUnit completa en verde.
- **Cambios**:
  - **Nuevo `tests/Feature/WorkOrderFlowTest.php`** (patrón `EstimateFlowTest`, `RefreshDatabase` + series PRE01/OT01 en setUp): **20 tests / 62 assertions** que cubren servicio y rutas:
    - Creación de OT desde presupuesto aprobado (sn `OT01-000001`, presupuesto → `in_repair`, historial).
    - **Agrupación de múltiples presupuestos aprobados del mismo check-in en una sola OT** + check-in vinculado.
    - Rechazos: presupuesto no aprobado, ya vinculado, de otro vehículo, lista vacía.
    - `attachEstimate` / `detachEstimate` (adicional aprobado → misma OT; revert a aprobado).
    - Transiciones válidas `open→...→closed`, transición inválida lanza, y **`delivered_pending` no es final** (backorder: se retoma la misma OT).
    - Eliminar OT revierte presupuestos y desvincula check-ins (`assertSoftDeleted`).
    - Flujo de asignaciones `pending→in_progress→done`.
    - Rutas HTTP: `store` desde check-in y desde estimate, `attach-estimate`, `transition`, `destroy` (con permisos) y **403 sin permiso**.
  - **Tests preexistentes corregidos** (no relacionados con el módulo, dejaban la suite en rojo): `tests/Feature/ExampleTest.php` (el `/` redirige → `assertRedirect` + smoke test de `/login`), `tests/Feature/ProfileTest.php` (el taller no invalida `email_verified_at` al cambiar correo → `assertNotNull`; eliminación de cuenta usa SoftDeletes → `assertSoftDeleted`).
- **Verificación**: la BD de pruebas `taller_testing` ya existía (MySQL 8.4 Laragon). PHPUnit se ejecutó en proceso separado (`Start-Process -WindowStyle Hidden`) porque el shell corta procesos largos. **Suite completa: OK (135 tests, 380 assertions)**. `php artisan view:cache` OK.
- **Próximos pasos**: fase futura — Kanban del flujo con la OT como tarjeta (`.clinerules/01`); módulos siguientes (servicios tercerizados, almacén/kardex, citas, facturación).

### 📌 Módulo de Servicios Tercerizados (CST01 + LST01) — con detracción 12%
- **Fecha**: 28 de agosto de 2026
- **Tarea**: Implementar comprobantes de servicio tercerizado (CST01) y liquidaciones (LST01). El usuario registra montos **SIN IGV**; el sistema calcula base, IGV, total con IGV, detracción y total a pagar.
- **Detracción**: confirmada vía Anexo 2 del SPOT (R.S. 183-2004/SUNAT): **"Mantenimiento y reparación de bienes muebles" (código 020) = 12%** (también "Demás servicios gravados" 037 = 12%). Configurable en Empresa (`company_settings.detraccion_rate`, default 0.1200).
- **Migraciones**: `company_settings` consolidada en **una sola migración** (create + igv_rate + detraccion_rate + qc_require_assignments_completed; se eliminaron las 2 migraciones ALTER). Nuevas: `provider_settlements` y `service_vouchers` (identidad CST/LST con `document_sn`, montos sin IGV, `detraction_rate` snapshot, estados).
- **Servicios**: `ServiceVoucherService` (emitir CST01 con numeración atómica, completar, editar/eliminar solo si no liquidado) y `ProviderSettlementService` (LST01, syncVouchers diff/upsert, aprobar → pagar marca vales `liquidated`).
- **Controladores/Policies**: `ServiceVoucherController`, `ProviderSettlementController`, policies + permisos (`ver/crear/editar/eliminar vales de servicio` y `liquidaciones de servicios`; Admin todas, Asesor ver/crear/editar).
- **Vistas**: listados Tabulator estilo Vehículos (documento enlazable, badges de estado, acciones `.btn-icon`), formularios con Tom Select estricto (proveedores `is_supplier` + OT) y **preview en vivo** del desglose IGV/detracción, `show` imprimible con cuenta BN de detracción.
- **Correcciones colaterales**: tests `PortalTest` y `WorkOrderFlowTest` actualizados de `estimate_status_history` → `status_histories` (polimórfica); `detraccion_rate` nullable en validación de configuración.
- **Pruebas**: **15 tests nuevos (50 assertions)** en `ServiceVoucherFlowTest` + `ProviderSettlementFlowTest`. **Suite completa: OK (150 tests, 430 assertions)**. `php artisan view:cache` OK; `migrate:fresh --seed` OK.
- **Próximos pasos**: completar almacén/compras/kardex (módulo 7), citas y seguimiento (8), facturación y pagos (9).

### 📌 Corrección: ParseError por `@json()` con comas en vistas de formularios
- **Fecha**: 28 de agosto de 2026
- **Tarea**: Corregir `ParseError: Unclosed '[' on line X does not match ')'` en `purchase-orders/_form.blade.php:72` (GET /purchase-orders/create, HTTP 500).
- **Causa raíz**: el directivo `@json()` de Blade compila con `explode(',', $this->stripParentheses($expression))` (`Illuminate\View\Compilers\Concerns\CompilesJson`). Cualquier coma dentro de la expresión trunca el PHP generado → ParseError. `view:cache` NO lo detecta (no ejecuta el PHP compilado); solo explota al renderizar la vista.
- **Cambios** (4 instancias con el mismo patrón, corregidas con el patrón `@php` + variable):
  - `resources/views/purchase-orders/_form.blade.php`: `$poItemsData` en el `@php` existente y `const poItems = @json($poItemsData);`.
  - `resources/views/estimates/_form-scripts.blade.php`: `$serviceCategoriesData` / `$partCategoriesData` en el `@php` y `@json($variable)`.
  - `resources/views/form-templates/edit.blade.php`: `$sectionsData` en un `@php` y `@json($sectionsData)`.
- **Regla permanente**: nueva sección en `.clinerules/03-frontend.md` — "Regla `@json` en Blade (PROHIBIDO expresiones con comas)".
- **Verificación**: `php artisan view:clear` + `view:cache` OK; `php -l` en todos los compilados de `storage/framework/views/` sin errores de sintaxis.

### 🌙 Modo oscuro global (capa de overrides CSS + toggle sol/luna)
- **Fecha**: 30 de agosto de 2026
- **Tarea**: Poner todo el proyecto en modo oscuro sin editar las 140 vistas (retrofit por capa global).
- **Estrategia**: clase `dark` en `<html>` (persistida en `localStorage('theme')` + `prefers-color-scheme` como default) + hoja de overrides CSS scoped a `html.dark` con `!important`, envuelta en `@media screen` para que **la impresión de documentos SIEMPRE salga en claro** (incluye `check-ins/pdf.blade.php`).
- **Archivos**:
  - **Nuevo** `resources/views/partials/theme-dark.blade.php`: script before-paint (mismo patrón que `sidebar-collapsed`), mapa de overrides (superficies slate-900/800/700, texto slate-100..500, bordes, estados semánticos `*-50/100` → tintados oscuros + texto `*-300/400`, `.card`, `.btn-secondary`, `.btn-icon-*`, inputs base, checkbox/radio con `accent-color`, `.search-input`, `.user-dropdown`, Tom Select completo, Tabulator completo, ring-offset oscuro) y wiring del toggle `[data-theme-toggle]` con `.icon-sun`/`.icon-moon`.
  - `layouts/app.blade.php` y `layouts/guest.blade.php`: `@include('partials.theme-dark')` tras `design-base` (script antes del paint, sin FOUC).
  - `layouts/navigation.blade.php`: botón toggle en la cabecera de la sidebar (desktop) y en la topbar móvil; oculto con sidebar colapsada (regla CSS).
- **Decisiones**: el login split ya era oscuro (panel izquierdo slate-900) — solo se oscureció la columna derecha; se respeta el sistema de diseño Restrained (acentos `#2563eb` intactos, badges tintados en vez de invertidos); el login detecta el tema por sistema/localStorage (sin toggle propio).
- **Verificación**: `php artisan view:cache` OK (201 vistas), `php -l` sin errores en el compilado del theme-dark; inventario previo de clases de color en `resources/views` para dimensionar el mapa (grises + indigo/orange/purple/teal/yellow 50/100 + `*-500/600` sólidos que se mantienen).
- **Pendiente (fase futura)**: al integrar ApexCharts, configurar `theme: { mode: 'dark' }` condicional; revisar visualmente en navegador (listados, formularios con Tom Select, kanban, modales, móvil, impresión) si algún componente exótico escapa al mapa.

### 📌 Módulo de Citas y Seguimiento (appointments + follow_ups)
- **Fecha**: 31 de agosto de 2026
- **Tarea**: Implementación del módulo de citas (agenda) y seguimientos con regla de asociación cita ↔ check-in.
- **Migraciones nuevas** (`2026_08_31_000001`/`000002`): `appointments` (vehicle_id/party_id/advisor_id/contact_name/phone/email snapshot, scheduled_at, service_type, reason, status scheduled|confirmed|cancelled|completed, `check_in_id` unique nullable, soft deletes) y `follow_ups` (party_id/vehicle_id, date, type call|whatsapp|email|visit, notes, next_action_date, done/done_at).
- **Backend**: `Appointment`/`FollowUp` (SoftDeletes + LogsActivity), `AppointmentService` (create/update/confirm/cancel/unlink/`associateForCheckIn`/`vehicleInfo`), `FollowUpService`, `AppointmentRequest`/`FollowUpRequest`, Policies + permisos spatie `ver|crear|editar|eliminar citas` y `... seguimientos` (Asesor: ver/crear/editar). Rutas resource + `api/appointments/search`, `api/appointments/vehicle-info/{vehicle}`, `api/follow-ups/search`, `appointments/{appointment}/confirm|cancel|unlink`, `follow-ups/{followUp}/done`.
- **Regla de negocio**: al crear un check-in (`CheckInService::create`), `associateForCheckIn()` asocia la cita del vehículo que caiga **el mismo día calendario** con estado scheduled/confirmed y sin ingreso → la cita pasa a `completed` con `check_in_id`. Otro día → NO se asocia (indicador ámbar en el formulario de ingreso vía `vehicleInfo`); cita hoy → banner azul "se asociará".
- **Frontend**: `appointments/index|create|edit|show|_form` (Tabulator + badges + Tom Select vehículo con modal de creación rápida reutilizado + contacto del vehículo), `follow-ups/index` (Tabulator + modal de creación), grupo "Citas" en la navegación.
- **Tests**: `tests/Feature/AppointmentTest` (8 tests: asociación misma fecha, no-asociación otro día, cita completed no se reofrece, unlink → confirmed, vehicleInfo today/others, CRUD citas y seguimientos, render de vistas).
- **Nota entorno Windows**: `DatabaseTruncation` con `RefreshDatabaseState::$migrated = true` en `setUpBeforeClass` (evita `migrate:fresh` lento); DB `taller_testing` se migra por lotes (`--path`).
