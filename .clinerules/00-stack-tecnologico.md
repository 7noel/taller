# Stack tecnológico y estándares generales

## Backend
- **Framework**: Laravel 12 (PHP 8.2+).
- **Base de datos**: MySQL.
- **Estructura**: Modelos, Controladores (con Form Requests), Servicios (lógica de negocio), Jobs (colas), Policies (autorización).
- **Paquetes**:
  - `spatie/laravel-permission` para roles y permisos.
  - `spatie/laravel-activitylog` para auditoría (opcional, pero recomendado).

## Frontend
- **CSS**: Tailwind CSS (cargado por CDN).
- **Tablas**: Tabulator (CDN).
- **Gráficos**: ApexCharts (CDN).
- **Autocompletado**: Tom Select (CDN).
- **JavaScript**: Vanilla JS (sin jQuery, salvo que sea estrictamente necesario).
- **Nota**: No se usará Vite, Laravel Mix ni compilación en servidor. Todo por CDN.

## Estándares de código
- PHP: PSR-12.
- Nombres de tablas: plural, snake_case (ej. `clients`, `vehicles`).
- Nombres de modelos: singular, CamelCase (ej. `Client`, `Vehicle`).
- Controladores: usar Form Requests para validación.
- Lógica de negocio: en Services (app/Services).
- Tareas largas: Jobs (colas) para envío de WhatsApp, generación de PDF, etc.
- Base de datos en inglés (todas las tablas y columnas).
- SoftDeletes en modelos principales (clientes, vehículos, presupuestos, etc.).