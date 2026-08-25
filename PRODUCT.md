# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Asesores de servicio del taller** (usuario primario): gestionan el flujo completo de un vehículo — inventario → presupuesto → aprobación → reparación → control de calidad → entrega — de forma rápida y sin errores.
- **Técnicos y jefes de taller**: consumen órdenes de trabajo, subetapas y asignaciones (roles confirmados en el modelo de permisos).
- **Administración/gerencia**: control, reportes y KPIs del negocio.
- **Clientes del taller**: acceso público futuro (enlace único, firma digital, encuestas de satisfacción) — aún no implementado.

## Product Purpose

Sistema integral de gestión para un taller mecánico que registra y controla cada vehículo desde su ingreso hasta la entrega: inventario con checklist y daños, presupuestos y aprobaciones (seguro/cliente), órdenes de trabajo por subetapas, servicios tercerizados, almacén/kardex, citas, facturación y pagos. El éxito se mide en que el asesor complete el flujo sin perder información, sin duplicar datos y con trazabilidad total.

## Positioning

Un solo lugar de verdad para todo el ciclo de reparación del vehículo, donde cada etapa alimenta la siguiente (el presupuesto aprobado genera la OT; los repuestos pedidos actualizan stock; los vales se liquidan al proveedor) y el estado del vehículo es siempre visible en un tablero Kanban. No es un ERP genérico: la secuencia de etapas y sus estados están modelados como reglas de negocio del taller.

## Operating Context

- Trabajo diario en el mostrador del taller: el asesor atiende vehículos, asegura y clientes, con interrupciones frecuentes; la rapidez y la prevención de errores son críticas.
- Flujo de seis etapas con estados explícitos por entidad (Inventario, Presupuesto, Aprobación, Reparación, Control de Calidad, Entrega), reflejado en un tablero Kanban.
- Multi-establecimiento: cada `Establishment` tiene su código correlativo; los usuarios pertenecen a un establecimiento.
- Deuda técnica aceptada del proyecto: frontend por CDN (Tailwind, Tabulator, ApexCharts, Tom Select) sin Vite/Mix; idioma de UI en español (entidades ya registradas del dominio, ej. "check-in", "OT", "vale"), base de datos en inglés.
- Reglas obligatorias de frontend y seguridad ya establecidas en `.clinerules/` (patrón `<x-app-layout>`, sistema de diseño Restrained, sincronización diff/upsert, guard anti-duplicados y renovación CSRF) — son vinculantes para todo trabajo nuevo.

## Capabilities and Constraints

- **Módulos implementados (orden de prioridad)**: Configuración/usuarios/roles, Clientes (parties) y vehículos, Inventario (check-in con checklist, daños, fotos y aprobación). Pendientes: presupuestos, OT, servicios tercerizados, almacén, citas, facturación, reportes/KPIs, notificaciones y acceso público.
- **Stack confirmado**: Laravel 12 (PHP 8.2+), MySQL, Tailwind CSS por CDN, Tabulator (tablas), ApexCharts (gráficos), Tom Select (autocompletado de selección única estricta), Vanilla JS. Sin compilación en servidor.
- **Modelo de datos**: tablas en inglés, plural snake_case; SoftDeletes en modelos principales.
- **Sincronización de colecciones hijo**: prohibido "delete-all + reinsertar"; usar diff/upsert (ver `CheckInService` como referencia).
- **Estado del frontend actual**: el sistema de diseño "Restrained" (modo Operate) definido en `.clinerules/07-diseno-sistema.md` ya es la autoridad visual vinculante; `layouts/app.blade.php` contiene los tokens y componentes (`.btn*`, `.card`, `.search-input`, tema Tabulator).
- **Seguridad/UX de formularios**: renovación CSRF pre-envío y flag anti-doble-clic en `partials/form-guard.blade.php`; modales AJAX con flag `saving` (obligatorio en cualquier modal nuevo).

## Brand Commitments

No existe marca definida. El frontend debe usar una identidad **neutra** y no asumir nombre comercial, logo ni colores de marca: el acento azul del sistema de diseño es funcional (acciones primarias, selección, estados), no de marca.

## Evidence on Hand

- Mockups SVG de siluetas de vehículos para el registro de daños: `public/images/mockups/`.
- Seeders reales de checklist de inventario, establecimientos, usuarios, roles/permisos, parties, vehículos y compañías de seguros.
- Reglas de negocio y diseño documentadas en `.clinerules/` (flujo del taller, entidades, frontend, sistema de diseño, seguridad).
- No hay testimonios, casos de estudio, precios ni datos de producción disponibles; no fabricar estos contenidos.

## Product Principles

1. **El flujo manda**: cada pantalla deja claro en qué etapa está el vehículo y qué sigue, sin pasos ocultos.
2. **Rápido y sin errores**: el asesor completa tareas con la menor cantidad de clics; los formularios previenen duplicados y validan al instante.
3. **Un solo lugar de verdad**: los datos se capturan una vez y fluyen entre etapas, sin reescrituras (diff/upsert, no delete-all).
4. **Trazabilidad total**: quién hizo qué y cuándo está registrado (actividad, soft deletes, estados).
5. **Coherencia sobre sorpresa**: interfaz familiar, consistente y sobria (modo Operate); el azul se reserva para acción y estado.