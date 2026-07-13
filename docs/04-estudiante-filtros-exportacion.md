# Estudiante 4 - Filtros y exportacion

## Tema

Busqueda avanzada de reservas y exportacion de datos.

## Aporte principal

Se mejoro la gestion de reservas agregando filtros mas completos y exportacion CSV.

## Puntos tecnicos para exponer

- Los filtros se reciben como parametros `GET` en `/admin/reservas`.
- La logica de filtrado esta en `ReservaService.listarReservas`.
- La exportacion esta en `/admin/reservas/exportar`.
- El archivo se devuelve con `Content-Disposition` para descarga.
- El CSV respeta los filtros actuales.

## Filtros disponibles

- Nombre, DNI, telefono o correo.
- Estado.
- Fecha desde.
- Fecha hasta.
- Cantidad de personas.
- Hora.

## Guion breve

"Mi aporte fue mejorar la busqueda y los reportes. Antes el administrador veia una lista simple, pero ahora puede filtrar reservas por cliente, estado, rango de fechas, cantidad de personas y hora. Tambien se agrego un boton para exportar los resultados a CSV. Esto es importante porque un restaurante puede necesitar reportes diarios o mensuales. Tecnicamente, los filtros llegan al controlador, se procesan en `ReservaService` y el CSV se genera desde una ruta especifica."

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/controller/WebController.java`
- `src/main/java/com/rinconcitomarino/service/ReservaService.java`
- `src/main/resources/templates/admin.html`
