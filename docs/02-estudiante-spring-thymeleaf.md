# Estudiante 2 - Spring Boot, Spring Web y Thymeleaf

## Tema asignado

Construcción del backend y generación de páginas dinámicas mediante Spring Boot, Spring Web y Thymeleaf.

## Unidad del curso

Unidad 2: Introducción a Spring.

## Objetivo de la exposición

Explicar cómo Spring Boot recibe las solicitudes del navegador, ejecuta la lógica del restaurante y devuelve páginas con información dinámica mediante Thymeleaf.

## Contenido de su diapositiva

**Título:** Spring Boot transforma la interfaz en una aplicación web dinámica

- Spring Web recibe solicitudes HTTP.
- Los controladores atienden rutas y formularios.
- Los servicios ejecutan las reglas del negocio.
- Thymeleaf conecta los datos de Java con las páginas HTML.
- El proyecto está organizado mediante capas.

**Flujo principal:**

`Usuario → Controller → Service → Thymeleaf → Respuesta`

## Cómo debe estar organizada la diapositiva

- En la parte superior: título del tema.
- En el centro: flujo horizontal con Usuario, Controller, Service, Thymeleaf y Respuesta.
- Debajo del flujo: ejemplos reales `WebController`, `ReservaService` y `reservas.html`.
- En un lateral: tecnologías Java 21, Spring Boot, Spring Web y Thymeleaf.
- Evitar colocar fragmentos grandes de código.

## Guion principal

> Mi tema corresponde al backend desarrollado con Spring Boot. Spring Boot permite crear y configurar una aplicación Java de manera más rápida, porque integra el servidor web y administra las dependencias principales. Cuando un usuario abre una página o envía un formulario, Spring Web recibe la solicitud y la dirige hacia un controlador. En nuestro proyecto, WebController administra rutas como inicio, reservas, login y panel administrativo. El controlador delega las operaciones a servicios como ReservaService y OpinionService, donde se encuentran las reglas del negocio. Finalmente, Thymeleaf permite enviar datos desde Java hacia las páginas HTML. Gracias a esta integración podemos mostrar reservas, opiniones, estadísticas y mensajes de validación. La separación entre controlador, servicio y vista hace que el proyecto sea más ordenado y fácil de mantener.

## Información adicional para estudiar

### ¿Qué es backend?

Es la parte de la aplicación que se ejecuta en el servidor. Procesa solicitudes, aplica reglas, accede a la base de datos y construye respuestas.

### ¿Qué es Spring Boot?

Es una herramienta basada en Spring que facilita la creación de aplicaciones Java. Proporciona configuración automática, dependencias iniciales y un servidor incorporado.

### ¿Qué es Spring Web?

Es el componente utilizado para crear controladores, atender solicitudes HTTP, procesar formularios y construir servicios REST.

### ¿Qué es Thymeleaf?

Es un motor de plantillas del lado del servidor. Procesa archivos HTML y reemplaza expresiones con datos enviados desde Java.

### Anotaciones importantes

- `@Controller`: identifica un controlador de vistas.
- `@RestController`: identifica un controlador que devuelve datos, normalmente JSON.
- `@GetMapping`: atiende solicitudes de consulta.
- `@PostMapping`: procesa formularios o creación de información.
- `@Service`: identifica la capa de lógica del negocio.
- `@RequestParam`: recibe parámetros de la URL o formulario.
- `@PathVariable`: recibe valores incluidos en una ruta.
- `@ModelAttribute`: enlaza un formulario con un objeto Java.

### Expresiones de Thymeleaf usadas

- `th:text`: muestra texto dinámico.
- `th:each`: recorre listas.
- `th:if`: muestra elementos según una condición.
- `th:action`: genera la dirección de un formulario.
- `th:field`: enlaza un campo HTML con una propiedad Java.
- `th:href`: genera enlaces internos.

## Aplicación concreta en Rinconcito Marino

- `WebController` administra las vistas públicas y administrativas.
- `ReservaRestController` expone operaciones REST de reservas.
- `OpinionRestController` administra opiniones mediante API.
- `ReservaService` contiene reglas para registrar, filtrar y actualizar reservas.
- `OpinionService` guarda y modera opiniones.
- Thymeleaf genera el dashboard y las tablas administrativas.

## Demostración recomendada

1. Abrir el formulario de reservas.
2. Completar datos válidos.
3. Enviar el formulario.
4. Mostrar el mensaje de confirmación.
5. Explicar que la petición `POST` pasó por Controller, Service y finalmente generó una respuesta.

## Preguntas probables

### ¿Qué ventaja ofrece la arquitectura por capas?

Separa responsabilidades. El controlador recibe solicitudes, el servicio aplica reglas y el repositorio trabaja con los datos. Esto facilita pruebas y mantenimiento.

### ¿Cuál es la diferencia entre Controller y RestController?

`@Controller` normalmente devuelve el nombre de una plantilla HTML. `@RestController` devuelve directamente datos como JSON.

### ¿Thymeleaf se ejecuta en el navegador?

No. Se procesa en el servidor. El navegador recibe el HTML final ya generado.

### ¿Qué versiones utiliza el proyecto?

El proyecto está configurado con Java 21 y Spring Boot 3.5.14.

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/controller/WebController.java`
- `src/main/java/com/rinconcitomarino/controller/ReservaRestController.java`
- `src/main/java/com/rinconcitomarino/controller/OpinionRestController.java`
- `src/main/java/com/rinconcitomarino/service/ReservaService.java`
- `src/main/java/com/rinconcitomarino/service/OpinionService.java`
- `src/main/resources/templates/reservas.html`
- `src/main/resources/templates/admin.html`

## Transición al estudiante 3

> Una vez procesada la información, el sistema necesita validarla y almacenarla de forma permanente. Para eso se utilizaron Jakarta Validation, JPA, Hibernate y MySQL.
