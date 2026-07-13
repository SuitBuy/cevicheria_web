# Estudiante 3 - JPA, Hibernate, MySQL y validaciones

## Tema asignado

Persistencia, operaciones CRUD y validación de información mediante Spring Data JPA, Hibernate, MySQL y Jakarta Validation.

## Unidad del curso

Unidad 3: Spring con bases de datos.

## Objetivo de la exposición

Explicar cómo los objetos Java se almacenan en MySQL y cómo el sistema impide guardar información incompleta o incorrecta.

## Contenido de su diapositiva

**Título:** Los datos se validan antes de almacenarse en MySQL

- JPA define la persistencia de objetos Java.
- Hibernate implementa el mapeo objeto-relacional.
- Spring Data proporciona repositorios y operaciones CRUD.
- Jakarta Validation controla campos y formatos.
- Entidades: reservas, opiniones, usuarios e historial.

**Flujo principal:**

`Formulario → @Valid → Service → Repository → MySQL`

## Cómo debe estar organizada la diapositiva

- En la parte superior: título del tema.
- En el centro: flujo desde el formulario hasta MySQL.
- En la parte inferior: nombres de las cuatro entidades principales.
- En un lateral: ejemplos de validación como DNI, correo, edad, teléfono y fecha.
- Utilizar flechas para dejar claro que solo los datos válidos llegan a la base de datos.

## Guion principal

> Mi tema corresponde a la persistencia y validación de datos. El proyecto utiliza MySQL como base de datos y Spring Data JPA con Hibernate para relacionar los objetos Java con las tablas. Cada entidad representa información del negocio, como una reserva, una opinión, un usuario administrativo o un registro del historial. Los repositorios proporcionan operaciones CRUD para crear, consultar, actualizar y eliminar información. Antes de guardar una reserva, Jakarta Validation comprueba que los datos sean correctos. Se validan campos obligatorios, formato del correo, DNI, edad, teléfono, cantidad de personas y fecha. Si existe un error, el controlador evita guardar la información y Thymeleaf muestra el mensaje correspondiente. Cuando los datos son válidos, el servicio utiliza el repositorio para almacenarlos. Esto mantiene la información organizada, consistente y confiable.

## Información adicional para estudiar

### ¿Qué es ORM?

ORM significa mapeo objeto-relacional. Permite representar tablas mediante clases Java y registros mediante objetos.

### Diferencia entre JPA e Hibernate

JPA es la especificación que define cómo debe realizarse la persistencia en Java. Hibernate es una implementación de esa especificación.

### ¿Qué es Spring Data JPA?

Es un componente que simplifica la creación de repositorios. Al extender `JpaRepository`, el proyecto obtiene operaciones CRUD sin implementar manualmente cada consulta.

### Operaciones CRUD

- **Create:** registrar una reserva, opinión o usuario.
- **Read:** listar reservas y consultar información.
- **Update:** cambiar el estado o moderación.
- **Delete:** eliminar registros con permisos administrativos.

### Anotaciones de persistencia

- `@Entity`: identifica una clase que se almacenará.
- `@Table`: establece el nombre de la tabla.
- `@Id`: identifica la clave primaria.
- `@GeneratedValue`: genera el identificador automáticamente.
- `@Column`: configura una columna y sus restricciones.
- `@Enumerated` o convertidores: permiten guardar valores enumerados.

### Validaciones utilizadas

- `@NotBlank`: impide textos vacíos.
- `@NotNull`: impide valores nulos.
- `@Size`: controla la longitud.
- `@Email`: comprueba el formato del correo.
- `@Min` y `@Max`: controlan valores numéricos.
- Validaciones de fecha: evitan registrar reservas en fechas inválidas.

## Aplicación concreta en Rinconcito Marino

- `Reserva` almacena cliente, contacto, personas, fecha, hora y estado.
- `Opinion` almacena información y opciones de moderación.
- `UsuarioAdmin` almacena usuario, contraseña cifrada y rol.
- `ReservaHistorial` mantiene la trazabilidad de cambios.
- Los repositorios conectan estas entidades con la base de datos.
- El servicio permite filtrar reservas y exportar resultados a CSV.

## Demostración recomendada

1. Completar una reserva con DNI corto o correo inválido.
2. Enviar el formulario y mostrar los errores.
3. Corregir los campos.
4. Guardar la reserva correctamente.
5. Mostrarla desde la tabla del panel administrativo.

## Preguntas probables

### ¿Qué pasa si el usuario escribe datos incorrectos?

`@Valid` activa las restricciones. Si aparecen errores, `BindingResult` los recibe, el controlador no guarda la entidad y devuelve el formulario.

### ¿Por qué utilizar JPA en lugar de SQL directo?

JPA reduce código repetitivo, permite trabajar con objetos Java y facilita operaciones CRUD y consultas mediante repositorios.

### ¿Dónde se configura la conexión?

La conexión y credenciales se configuran en `application.properties` o mediante variables de entorno en producción.

### ¿Qué aporta el historial?

Registra quién realizó una acción, qué cambio hizo y cuándo ocurrió, proporcionando trazabilidad administrativa.

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/model/Reserva.java`
- `src/main/java/com/rinconcitomarino/model/Opinion.java`
- `src/main/java/com/rinconcitomarino/model/UsuarioAdmin.java`
- `src/main/java/com/rinconcitomarino/model/ReservaHistorial.java`
- `src/main/java/com/rinconcitomarino/repository/ReservaRepository.java`
- `src/main/java/com/rinconcitomarino/repository/OpinionRepository.java`
- `src/main/java/com/rinconcitomarino/service/ReservaService.java`
- `src/main/resources/application.properties`

## Transición al estudiante 4

> Como el sistema almacena información de clientes y permite modificar reservas, fue necesario proteger el panel mediante autenticación, JWT y permisos según el rol.
