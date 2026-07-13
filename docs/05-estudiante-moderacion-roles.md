# Estudiante 5 - Moderacion y roles

## Tema

Control de opiniones y permisos segun tipo de usuario.

## Aporte principal

Se agrego moderacion de opiniones y se mejoro la separacion de permisos entre administrador, empleado y solo lectura.

## Puntos tecnicos para exponer

- La entidad `Opinion` ahora tiene `visible` y `destacado`.
- El servicio `OpinionService` permite cambiar la moderacion.
- La vista admin permite ocultar, mostrar o destacar opiniones.
- Los roles se controlan desde `RolUsuario` y `SecurityConfig`.
- Se agrego el rol `LECTOR`.

## Roles actuales

- `ADMIN`: control total, usuarios y eliminaciones.
- `EMPLEADO`: gestion operativa de reservas y opiniones.
- `LECTOR`: solo puede revisar informacion.

## Guion breve

"Mi parte fue mejorar el control administrativo. En opiniones ya no es necesario borrar todo directamente; ahora una opinion puede estar visible, oculta o destacada. Tambien se mejoraron los roles: el administrador puede gestionar usuarios y eliminar datos, el empleado puede operar el panel y el lector solo puede ver informacion. Esto se configuro en `SecurityConfig`, usando reglas por ruta y metodo HTTP. Con esto el sistema queda mas seguro y mejor organizado."

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/model/Opinion.java`
- `src/main/java/com/rinconcitomarino/service/OpinionService.java`
- `src/main/java/com/rinconcitomarino/model/RolUsuario.java`
- `src/main/java/com/rinconcitomarino/config/SecurityConfig.java`
