# Estudiante 3 - Historial y auditoria

## Tema

Registro de acciones importantes realizadas por usuarios del panel.

## Aporte principal

Se agrego un historial para saber quien cambio o elimino una reserva, cuando lo hizo y que accion realizo.

## Puntos tecnicos para exponer

- Se creo la entidad `ReservaHistorial`.
- Se creo el repositorio `ReservaHistorialRepository`.
- El servicio `ReservaService` registra eventos al cambiar estado o eliminar.
- El dashboard muestra el historial reciente.
- El usuario se toma desde la sesion autenticada.

## Datos guardados

- `reservaId`
- `usuario`
- `accion`
- `detalle`
- `fecha`

## Guion breve

"Mi parte fue agregar auditoria basica. En un sistema real no basta con cambiar datos; tambien es importante saber quien hizo cada cambio. Por eso se creo la tabla `reservas_historial`, donde se registra el usuario, la accion, el detalle y la fecha. Cada vez que alguien cambia el estado de una reserva o la elimina, el sistema guarda ese movimiento. Esto mejora la trazabilidad y permite defender el proyecto como una aplicacion administrativa mas seria."

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/model/ReservaHistorial.java`
- `src/main/java/com/rinconcitomarino/repository/ReservaHistorialRepository.java`
- `src/main/java/com/rinconcitomarino/service/ReservaService.java`
