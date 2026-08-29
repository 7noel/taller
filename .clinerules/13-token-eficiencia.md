# Uso eficiente de tokens (regla estricta y permanente)

Esta regla es **obligatoria SIEMPRE**, en toda tarea y mensaje, complementaria a `09-optimizacion-contexto.md`. El objetivo es minimizar consumo de tokens sin sacrificar precisión.

## Reglas

1. **Tarea ambigua → preguntar antes de explorar.** Si no está claro qué se pide o por dónde empezar, hacer **máximo 1 pregunta** con opciones (`ask_question`), antes de leer archivos o ejecutar búsquedas.
2. **Prohibido leer archivos completos por defecto.** Usar rangos de líneas (`start_line`/`end_line`), búsquedas dirigidas (`search_codebase`) y leer solo lo necesario para la tarea. Leer un archivo completo solo cuando sea estrictamente indispensable.
3. **Búsquedas antes que lecturas.** Para localizar dónde cambiar algo, buscar por clase, método, ruta o texto concreto antes de abrir archivos grandes.
4. **No repetir contenido ya analizado.** No volcar de nuevo en la conversación código o texto que ya se revisó; referenciarlo por ruta y línea.
5. **Respuestas concisas.** Resumen final de **máx. 10 líneas**: qué cambió, archivos modificados, qué queda pendiente. Sin incluir bloques grandes de código salvo que sea necesario.
6. **Una tarea por vez.** No arrastrar contexto de tareas anteriores ya cerradas; partir del estado actual del código como fuente de verdad.
7. **Contexto grande → recomendar nueva conversación.** Si la conversación se vuelve extensa, detenerse y sugerir iniciar una nueva tarea con un resumen corto del estado.
8. **No exploración general.** Cuando la tarea es específica, no hacer recorridos del proyecto; solo inspeccionar los archivos implicados.
9. **Logs y salidas largas:** no enviar logs completos ni respuestas de herramientas innecesarias; filtrar y enviar solo lo relevante.

## Recordatorio al usuario (para reducir consumo)

- Dar una tarea clara por mensaje e indicar qué NO tocar.
- En bugs, adjuntar el error de consola del navegador (F12 → Console/Network).
- No pegar archivos completos en el chat; indicar ruta y sección.
- Cerrar conversaciones al terminar cada tarea.
- Usar modo Plan para análisis y modo Act para implementar, sin alternar a mitad de tarea.
