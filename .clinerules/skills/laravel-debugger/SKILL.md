---
name: laravel-debugger
description: Use when debugging errors, exceptions, unexpected behavior, or performance issues in a Laravel application. Covers reading stack traces, using tinker, debugging queries, checking logs, Mailpit/Telescope, debugging HTTP requests, middleware issues, and resolving common Laravel errors.
---

# Laravel Debugger

Enfoque sistematico para depurar problemas en Laravel.

## Proceso sistematico de depuracion

1. **Lee el mensaje de error completo** — nunca ignores el stack trace; identifica archivo, linea y tipo de excepcion.
2. **Revisa los logs** — `storage/logs/laravel.log`, usa `--tail` o colas.
3. **Aisla el problema** — reproduce con tinker o un test minimo.
4. **Verifica la causa raiz** — no trates el sintoma.
5. **Confirma la correccion** — con un test que falle antes de la correccion.

## Herramientas de depuracion

### Tinker (consola interactiva)
```bash
php artisan tinker
```

### Depurar queries SQL
```php
DB::enableQueryLog();
// ... queries ...
dump(DB::getQueryLog());

// O en el modelo/controller temporalmente
\DB::listen(function ($query) {
    dump($query->sql, $query->bindings);
});
```

### Ver rutas registradas
```bash
php artisan route:list
```

### Cache limpia (problemas frecuentes)
```bash
php artisan optimize:clear   # cache, config, route, view
```

### Causal de errores comunes
| Error | Causa probable | Solucion |
|---|---|---|
| 419 Page Expired | Falta CSRF token | Agregar `@csrf` al form |
| 500 en produccion | Cache de config | `php artisan config:clear` |
| Class not found | Composer autoload | `composer dump-autoload` |
| Table not found | Migraciones pendientes | `php artisan migrate` |
| N+1 queries | Falta eager loading | Ver skill eloquent-specialist |
| Mixed content | HTTP/HTTPS | `\URL::forceScheme('https')` |

## Nota
Usa junto con el skill `eloquent-specialist` para problemas de queries, y `php-best-practices` para calidad de codigo.
