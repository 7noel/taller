# Optimización de uso de contexto y tokens (obligatorio)

Reglas obligatorias para todas las tareas futuras de este proyecto.

## Reglas

1. NO cargar ni analizar archivos completos si no son necesarios para la tarea actual.
2. Antes de leer un archivo, determinar si realmente es necesario para resolver la tarea.
3. Leer únicamente las secciones relevantes de los archivos cuando sea posible.
4. No repetir en la conversación contenido de archivos ya analizados.
5. No incluir grandes bloques de código o archivos completos en las respuestas salvo que sea estrictamente necesario.
6. Mantener el contexto de trabajo enfocado exclusivamente en la tarea actual.
7. Si una tarea anterior ya fue completada, considerarla cerrada y no arrastrar su contexto.
8. Para tareas nuevas, partir del estado actual del proyecto e inspeccionar solamente los archivos necesarios.
9. Para recordar decisiones arquitectónicas anteriores, consultar el archivo de contexto/checkpoint del proyecto (bitacora.md, CONTEXTO_LARAVEL.txt) en lugar de depender del historial de conversación.
10. Al terminar una tarea, proporcionar un resumen MUY breve de: qué cambió, archivos modificados, qué queda pendiente. Sin copiar código completo.
11. Evitar enviar a la API contenido innecesario: archivos grandes, logs completos, respuestas completas de herramientas, código duplicado, contenido no relacionado, historial innecesario.
12. Si el contexto de la conversación se hace demasiado grande, detener el trabajo y recomendar iniciar una nueva tarea/conversación con un resumen corto del estado actual.
13. Procedimiento para cada nueva tarea:
    A. Entender exactamente qué se solicita.
    B. Identificar los archivos probablemente involucrados.
    C. Inspeccionar únicamente esos archivos.
    D. Realizar los cambios.
    E. Verificar los cambios.
    F. Resumir brevemente el resultado.
14. NO hacer exploración general del proyecto cuando la tarea sea específica.
15. Si se necesita explorar para encontrar dónde realizar un cambio, hacer primero búsquedas específicas por nombre de clase, método, modelo, ruta, componente o texto relacionado, en lugar de leer grandes cantidades de archivos.

## Gestión de conversaciones

- Cuando una tarea haya sido completada y verificada, considerarla cerrada.
- No utilizar conversaciones anteriores como fuente principal de contexto para nuevas tareas.
- Para una nueva tarea:
  - utilizar el estado actual del código como fuente de verdad;
  - consultar `PROJECT_CONTEXT.md` si se necesitan decisiones arquitectónicas;
  - inspeccionar únicamente los archivos relacionados;
  - no reconstruir el contexto completo del proyecto.
- Si la conversación actual se vuelve extensa, crear o actualizar un resumen de estado y recomendar comenzar una nueva conversación.

## Objetivo principal

Minimizar el uso de contexto y tokens sin perder la información necesaria para realizar correctamente la tarea. No sacrificar precisión por ahorrar contexto: la regla es evitar contexto innecesario, no evitar contexto necesario.