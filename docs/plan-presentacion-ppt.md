# Plan de presentación final - 4 diapositivas / 4 integrantes

## Objetivo de la presentación

Resumir las cuatro unidades del curso **Marcos de Desarrollo Web** mediante su aplicación real en el proyecto **Rinconcito Marino**. Cada integrante presenta una diapositiva y demuestra una parte funcional del sistema.

La presentación completa debe durar entre **6 y 8 minutos**. Cada integrante dispone de aproximadamente **1 minuto y 30 segundos**, incluyendo una demostración breve.

## Reglas generales de diseño

- Formato panorámico 16:9.
- Una sola idea principal por diapositiva.
- Usar los colores del proyecto: azul marino, blanco y un acento celeste o turquesa.
- Colocar el logo de Rinconcito Marino en una esquina sin quitar espacio al contenido.
- Títulos entre 32 y 40 puntos; texto entre 18 y 24 puntos.
- Máximo cinco textos breves visibles por diapositiva.
- Priorizar capturas reales del proyecto sobre párrafos extensos.
- No colocar código completo; mostrar únicamente fragmentos de 5 a 10 líneas cuando sea necesario.
- Mantener el mismo pie de página: `Marcos de Desarrollo Web | Rinconcito Marino | Proyecto final`.
- La portada se integra en la primera diapositiva para conservar el límite de cuatro diapositivas.

---

## Diapositiva 1 - Del diseño estático a una interfaz web responsive

**Responsable:** Integrante 1  
**Unidad resumida:** Frameworks de frontend  
**Duración sugerida:** 1 minuto y 30 segundos

### Título visible

**Rinconcito Marino: una experiencia web adaptable para clientes**

### Texto visible en la diapositiva

- HTML5 y CSS3 para la estructura y estilo.
- Bootstrap para navegación, formularios, tablas y diseño responsive.
- JavaScript y componentes interactivos para mejorar la experiencia.
- Vistas principales: inicio, carta, reservas y acceso del personal.

En la parte inferior deben aparecer los nombres de los cuatro integrantes, el curso y el nombre del docente.

### Cómo debe verse

- **Lado izquierdo (40 %):** título, subtítulo `Proyecto final de Marcos de Desarrollo Web`, integrantes y cuatro tecnologías: HTML, CSS, Bootstrap y JavaScript.
- **Lado derecho (60 %):** una captura grande de la página principal dentro de un marco de computadora y una captura pequeña de la versión móvil superpuesta.
- Usar como prueba visual la barra de navegación, los platos destacados y el botón de reserva.
- No colocar definiciones largas de HTML o Bootstrap.

### Capturas recomendadas

1. Página principal `/` en escritorio.
2. Página `/reservas` en vista móvil.

### Guion del integrante 1

> Buenos días. Nuestro proyecto final se llama Rinconcito Marino y consiste en una plataforma web para presentar el restaurante, recibir reservas y administrar la atención de los clientes. En la primera etapa aplicamos los temas de frontend del curso. Utilizamos HTML para organizar el contenido, CSS para personalizar la identidad visual y Bootstrap para crear una interfaz responsive que se adapta a computadoras y celulares. También empleamos sus componentes para la barra de navegación, formularios, botones y tablas. Como resultado, el cliente puede revisar la carta, conocer los platos y registrar una reserva desde distintos dispositivos. Esta interfaz posteriormente se integró con Spring Boot para convertirla en una aplicación dinámica.

### Demostración

- Abrir la página principal.
- Reducir el ancho del navegador para mostrar el comportamiento responsive.
- Entrar al formulario de reservas.

### Datos extra para responder preguntas

- Bootstrap está cargado mediante CDN.
- Thymeleaf genera las rutas de recursos con expresiones como `th:href` y `th:src`.
- El proyecto tiene hojas de estilo propias en `src/main/resources/static/css/style.css`.
- Las vistas están en `src/main/resources/templates`.
- Los recursos visuales están en `src/main/resources/static/assets`.

### Enlace con el siguiente expositor

> La interfaz por sí sola no procesa información; por eso el siguiente paso fue conectarla con un backend desarrollado con Spring Boot.

---

## Diapositiva 2 - Spring Boot convierte la interfaz en una aplicación dinámica

**Responsable:** Integrante 2  
**Unidad resumida:** Introducción a Spring  
**Duración sugerida:** 1 minuto y 30 segundos

### Título visible

**Spring Boot conecta las acciones del usuario con la lógica del restaurante**

### Texto visible en la diapositiva

- Spring Web recibe y responde solicitudes HTTP.
- Los controladores gestionan páginas y formularios.
- Los servicios concentran las reglas del negocio.
- Thymeleaf muestra datos dinámicos en las vistas HTML.

### Cómo debe verse

- Colocar al centro un flujo horizontal grande:

  `Cliente → Controller → Service → Thymeleaf → Respuesta`

- Debajo de cada etapa colocar un ejemplo real:
  - Cliente: formulario de reserva.
  - Controller: `WebController`.
  - Service: `ReservaService`.
  - Thymeleaf: `reservas.html` y `admin.html`.
- En el lado derecho usar una captura pequeña del mensaje de reserva registrada.
- Las flechas deben indicar claramente la dirección de la solicitud y respuesta.

### Guion del integrante 2

> Para transformar el frontend en una aplicación web dinámica utilizamos Spring Boot. Spring Web recibe las solicitudes del navegador y las dirige hacia los controladores. En nuestro proyecto, WebController atiende páginas como inicio, reservas, login y panel administrativo. Luego delega las operaciones a servicios como ReservaService y OpinionService, donde se encuentran las reglas del negocio. Thymeleaf permite enviar los datos obtenidos desde Java hacia las páginas HTML; por ejemplo, muestra las reservas, estadísticas y mensajes de validación. Esta separación en controladores, servicios y vistas hace que el proyecto sea más ordenado y mantenible. En la demostración registraremos una reserva para observar cómo el formulario del cliente es procesado por Spring.

### Demostración

- Completar una reserva válida.
- Enviar el formulario.
- Mostrar el mensaje de confirmación y explicar que Spring procesó la solicitud `POST`.

### Datos extra para responder preguntas

- El proyecto utiliza Java 21 y Spring Boot 3.5.14.
- `@Controller` administra vistas web.
- `@GetMapping` atiende consultas y páginas.
- `@PostMapping` procesa formularios y creación de información.
- `@Service` separa la lógica de negocio del controlador.
- Thymeleaf utiliza atributos como `th:text`, `th:each`, `th:if` y `th:action`.
- También existen controladores REST para reservas, opiniones y autenticación.

### Enlace con el siguiente expositor

> Después de procesar la información, necesitábamos almacenarla de manera permanente y evitar que se registraran datos incorrectos.

---

## Diapositiva 3 - MySQL, JPA y las validaciones aseguran datos confiables

**Responsable:** Integrante 3  
**Unidad resumida:** Spring con bases de datos  
**Duración sugerida:** 1 minuto y 30 segundos

### Título visible

**La información se valida antes de almacenarse en MySQL**

### Texto visible en la diapositiva

- JPA e Hibernate relacionan objetos Java con tablas MySQL.
- Repositorios permiten crear, consultar, actualizar y eliminar datos.
- Jakarta Validation controla campos obligatorios y formatos.
- Entidades principales: reservas, opiniones, usuarios e historial.

### Cómo debe verse

- **Lado izquierdo:** diagrama vertical del flujo de datos:

  `Formulario → @Valid → Service → Repository → MySQL`

- **Lado derecho:** dos pruebas visuales:
  1. Captura de un formulario mostrando errores de validación.
  2. Captura de la tabla administrativa con registros guardados.
- En una franja inferior colocar cuatro etiquetas: `Reserva`, `Opinion`, `UsuarioAdmin` y `ReservaHistorial`.
- Evitar mostrar una captura completa de la base de datos con texto demasiado pequeño.

### Guion del integrante 3

> Para almacenar la información utilizamos MySQL junto con Spring Data JPA e Hibernate. Cada entidad Java representa información que se guarda en una tabla, como las reservas, opiniones, usuarios administrativos y el historial de cambios. Los repositorios permiten realizar operaciones CRUD sin escribir manualmente todas las consultas SQL. Antes de guardar una reserva, Jakarta Validation comprueba los datos ingresados. El sistema valida campos obligatorios, formato del correo, DNI, edad, teléfono, cantidad de personas y fecha. Si existe un error, Thymeleaf vuelve a mostrar el formulario con el mensaje correspondiente y la información no se almacena. Si los datos son correctos, la reserva se guarda y aparece en el panel administrativo. Así aseguramos integridad y confiabilidad en la base de datos.

### Demostración

- Intentar registrar una reserva con DNI o correo inválido.
- Mostrar los mensajes de validación.
- Corregir los datos, registrar la reserva y localizarla en el panel.

### Datos extra para responder preguntas

- `@Entity` identifica una clase persistente.
- `@Id` y `@GeneratedValue` administran la clave primaria.
- `JpaRepository` aporta las operaciones CRUD básicas.
- `@Valid` activa las restricciones antes de ejecutar el método del controlador.
- Se utilizan restricciones como `@NotBlank`, `@Email`, `@Size`, `@Min` y validaciones de fecha.
- `ReservaHistorial` registra usuario, acción, detalle y fecha para mantener trazabilidad.
- El sistema permite filtrar reservas y exportarlas en CSV.

### Enlace con el siguiente expositor

> Finalmente, como el panel contiene información administrativa, fue necesario protegerlo y controlar qué puede hacer cada usuario.

---

## Diapositiva 4 - Spring Security protege la operación completa

**Responsable:** Integrante 4  
**Unidad resumida:** Spring Security e integración final  
**Duración sugerida:** 1 minuto y 30 segundos

### Título visible

**Autenticación JWT y roles protegen el panel administrativo**

### Texto visible en la diapositiva

- BCrypt protege las contraseñas almacenadas.
- JWT identifica al usuario en cada solicitud.
- Spring Security protege rutas y operaciones.
- Roles: administrador, empleado y solo lectura.
- Integración completa: frontend, backend, validación, base de datos y seguridad.

### Cómo debe verse

- Usar un flujo de seguridad en la mitad superior:

  `Login → BCrypt → JWT → Filtro de seguridad → Recurso protegido`

- En la mitad inferior colocar una matriz pequeña de permisos:

| Operación | Administrador | Empleado | Lector |
|---|:---:|:---:|:---:|
| Consultar panel | Sí | Sí | Sí |
| Modificar reservas | Sí | Sí | No |
| Eliminar registros | Sí | No | No |
| Gestionar usuarios | Sí | No | No |

- Añadir una captura del login o del panel con el nombre del usuario autenticado.
- Cerrar la diapositiva con la frase: `Una solución web dinámica, validada y segura`.

### Guion del integrante 4

> Para proteger el sistema implementamos Spring Security. Primero, el usuario ingresa sus credenciales y la aplicación compara la contraseña utilizando BCrypt, por lo que las claves no se almacenan como texto visible. Cuando las credenciales son correctas, se genera un JWT con el nombre del usuario, su rol y una fecha de vencimiento. El filtro JWT valida ese token en cada solicitud y Spring Security decide si el usuario puede acceder al recurso. Implementamos tres roles: el lector solo consulta, el empleado puede gestionar reservas y opiniones, y el administrador también elimina registros y administra usuarios. Si una persona intenta entrar a la ruta administrativa sin autenticarse, el sistema la envía al login. Con esta seguridad completamos la integración de Bootstrap y Thymeleaf en el frontend, Spring Boot en el backend, JPA con MySQL, validaciones y control de acceso.

### Demostración

- Abrir `/admin` sin autenticación y mostrar la redirección al login.
- Iniciar sesión correctamente.
- Mostrar la cookie `RM_TOKEN` desde las herramientas del navegador si hay tiempo.
- Comparar brevemente las operaciones disponibles para un lector y un administrador.
- Cerrar sesión y comprobar que el panel vuelve a estar protegido.

### Datos extra para responder preguntas

- `SecurityConfig` define las rutas públicas y protegidas.
- `AuthenticationService` valida las credenciales.
- `JwtUtil` genera y verifica los tokens.
- `JwtAuthenticationFilter` autentica cada solicitud protegida.
- El JWT se acepta desde una cookie `HttpOnly` o desde `Authorization: Bearer`.
- La sesión está configurada como `STATELESS`.
- La cookie se llama `RM_TOKEN` y tiene política `SameSite=Strict`.
- Los recursos públicos incluyen inicio, reservas, carta y login.
- Las rutas `/admin/**` y los endpoints administrativos requieren autenticación y roles.

### Cierre del integrante 4

> En conclusión, Rinconcito Marino aplica todos los temas principales del curso en una solución funcional: una interfaz responsive, un backend organizado, persistencia y validación de datos, y seguridad basada en JWT y roles. El resultado es un sistema que no solo informa al cliente, sino que también mejora la gestión interna del restaurante.

---

## Orden de la demostración completa

Para evitar interrupciones, la demostración debe seguir este orden:

1. Integrante 1 muestra la página principal y su adaptación móvil.
2. Integrante 2 abre el formulario y registra una reserva válida.
3. Integrante 3 primero provoca una validación y luego muestra la reserva almacenada.
4. Integrante 4 demuestra login, protección de `/admin`, roles y cierre de sesión.

## Preparación antes de exponer

- Tener la aplicación ejecutándose antes de iniciar.
- Preparar usuarios de prueba para `ADMIN`, `EMPLEADO` y `LECTOR`.
- Tener varias reservas y opiniones registradas para que el panel no esté vacío.
- Mantener abiertas las pestañas necesarias y cerrar información personal.
- Guardar capturas de respaldo por si falla Internet o la aplicación.
- Probar la presentación en la computadora y proyector que se utilizarán.
- No depender del despliegue remoto: tener disponible la ejecución local.
- Ensayar las transiciones para que ningún integrante repita el tema anterior.

## Preguntas probables del docente

### ¿Por qué utilizar Spring Boot?

Porque simplifica la configuración del proyecto, integra Spring Web, JPA, validaciones y seguridad, y permite organizar el backend por capas.

### ¿Cuál es la diferencia entre JPA e Hibernate?

JPA es la especificación que define cómo persistir objetos Java; Hibernate es la implementación utilizada para ejecutar ese proceso.

### ¿Cuál es la diferencia entre autenticación y autorización?

La autenticación verifica la identidad del usuario; la autorización determina qué recursos y operaciones puede utilizar según su rol.

### ¿Por qué utilizar JWT?

Porque permite identificar al usuario mediante un token firmado y validar cada solicitud sin mantener una sesión tradicional en el servidor.

### ¿Cómo se protegen las contraseñas?

Se almacenan utilizando BCrypt. Durante el login se compara la contraseña ingresada con el hash guardado, sin recuperar la contraseña original.

### ¿Qué ocurre cuando una reserva tiene datos incorrectos?

Las restricciones de Jakarta Validation generan errores, el controlador evita guardar la entidad y Thymeleaf muestra los mensajes en el formulario.

### ¿Qué aporta el proyecto al restaurante?

Centraliza reservas y opiniones, permite controlar estados, consultar indicadores, aplicar filtros, exportar información y limitar las operaciones según la responsabilidad del personal.
