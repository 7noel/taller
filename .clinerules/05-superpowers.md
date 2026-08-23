# Superpowers (Skills activos en Cline)

<EXTREMELY-IMPORTANT>
Si hay al menos 1% de probabilidad de que un skill aplique, DEBES invocarlo. NO ES OPCIONAL.
</EXTREMELY-IMPORTANT>

## La Regla

**Invoca skills relevantes ANTES de cualquier respuesta o acción** (incluye preguntas, explorar código, revisar archivos). Anuncia "Usando [skill] para [propósito]" y sigue el skill exactamente.

## Cómo cargar un skill en Cline

Cline no tiene Skill tool nativo. Para invocar un skill:

1. Busca su SKILL.md en `.clinerules/skills/<nombre>/SKILL.md`
2. Léelo completo con read_file
3. Sigue sus instrucciones exactamente

Mapeo de herramientas: `.clinerules/skills/superpowers/references/cline-tools.md`

## Skills instalados

- **superpowers/**: brainstorming, test-driven-development, systematic-debugging, writing-plans, executing-plans, using-git-worktrees, requesting-code-review, verification-before-completion, finishing-a-development-branch, dispatching-parallel-agents, writing-skills, using-superpowers
- **impeccable/**: diseño UI/UX (shape, critique, audit, polish, bolder, colorize, typeset, layout, live, etc.)
- **findskill/**: meta-skill para descubrir skills de la comunidad
- **eloquent-specialist/**: consultas Eloquent eficientes (eager loading, scopes, transacciones, evitar N+1)
- **laravel-debugger/**: depuración sistemática de errores en Laravel (logs, tinker, queries, causas comunes)
- **php-best-practices/**: calidad de código PHP (PSR-12, seguridad, estructura, tipado)

## Prioridad

- "Vamos a construir X" → brainstorming primero
- "Arregla este bug" → systematic-debugging primero
- Tarea de UI/diseño → impeccable primero
- Proceso primero, implementación después

## Red Flags (STOP)

| Pensamiento | Realidad |
|---|---|
| "Es solo una pregunta simple" | Las preguntas son tareas |
| "Necesito más contexto primero" | Revisa skills ANTES de preguntar |
| "Déjame explorar primero" | Los skills dicen CÓMO explorar |
| "Esto no necesita un skill" | Si existe, úsalo |
| "Recuerdo este skill" | Lee la versión actual |
| "Haré esto primero" | Revisa ANTES de todo |

## Nota

Las instrucciones del usuario tienen prioridad sobre los skills. Solo omite skills cuando el usuario lo diga explícitamente.