---
name: php-best-practices
description: Use when writing or reviewing PHP code in Laravel applications. Covers PSR-12, code organization, SOLID principles, security best practices, input validation, type declarations, defensive programming, error handling, and code quality standards.
---

# PHP Best Practices

Escribes codigo PHP limpio, seguro y mantenible siguiendo los estandares de la industria.

## Estandares

- **PSR-12**: Estilo de codigo obligatorio.
- **Tipos declarados**: Usa strict_types en archivos nuevos y declares type hints en parametros y retornos.
- **Nombres**: `camelCase` para metodos/propiedades, `snake_case` para tablas/columnas, `PascalCase` para clases.

## Seguridad

1. Nunca confies en input del usuario: valida siempre con Form Requests.
2. Usa preparacion de consultas (Eloquent lo hace automaticamente).
3. Escapa la salida con `{{ }}` en Blade (nunca `{!! !!}` con datos no confiables).
4. Usa autorizacion con Policies para acciones sensibles.
5. No registres datos sensibles (passwords, tokens, tarjetas).

## Estructura del codigo

- **Controladores delgados**: logica de negocio en Services.
- **Validacion en Form Requests**, no en controladores.
- **Modelos**: relaciones, scopes, accessors/mutators, casts. Nada mas.
- **Servicios**: logica de negocio reutilizable.
- **Jobs**: tareas pesadas (WhatsApp, PDF) en cola.

## Ejemplos

### Form Request con validacion
```php
public function rules(): array
{
    return [
        'document_number' => ['required', 'string', 'max:11'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:15'],
    ];
}
```

### Servicio con tipado
```php
class PartyService
{
    public function create(array $data): Party
    {
        return DB::transaction(fn () => Party::create($data));
    }
}
```

### Never exponer datos internos
```php
// MAL
return response()->json($user);

// BIEN
return response()->json($user->only(['id', 'name', 'email']));
```

## Checklist de calidad
- [ ] `declare(strict_types=1)` al inicio de archivos nuevos
- [ ] Type hints en parametros y retorno
- [ ] Validacion en Form Requests
- [ ] Logica de negocio en Services
- [ ] Sin dd()/dump() en codigo de produccion
- [ ] Sin consultas N+1
- [ ] PSR-12 aplicado
- [ ] Tests para la funcionalidad
