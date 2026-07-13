# Estudiante 2 - Estados de reservas

## Tema

Estados mas completos para representar el ciclo real de una reserva.

## Aporte principal

Se ampliaron los estados de reserva para que el sistema pueda representar mejor lo que ocurre en un restaurante.

## Puntos tecnicos para exponer

- Los estados se manejan mediante el enum `EstadoReserva`.
- Cada estado tiene etiqueta, orden y clase visual para el badge.
- El panel permite cambiar estados desde la tabla de reservas.
- Los cambios se guardan en base de datos mediante JPA.

## Estados actuales

- `PENDIENTE`: reserva creada y aun no confirmada.
- `CONFIRMADO`: reserva aceptada.
- `ATENDIDO`: cliente atendido.
- `NO_ASISTIO`: cliente no llego.
- `CANCELADO_CLIENTE`: cliente cancelo.
- `RECHAZADO`: reserva no aceptada.
- `EXPIRADO`: reserva pendiente vencida.

## Guion breve

"Mi aporte fue mejorar el flujo de reservas. Antes los estados eran basicos, pero ahora representan situaciones reales: confirmado, atendido, no asistio, cancelado por cliente, rechazado y expirado. Esto se implemento en el enum `EstadoReserva`, que permite controlar el texto, el orden de visualizacion y el color del estado en el panel. Con esto el sistema deja de ser solo un formulario y se comporta mas como una herramienta de gestion."

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/model/EstadoReserva.java`
- `src/main/java/com/rinconcitomarino/model/Reserva.java`
- `src/main/resources/templates/admin.html`
