# Contexto tecnico del proyecto Rinconcito Marino

Wiki rapida para que otra IA o desarrollador entienda este proyecto sin depender del historial de chat.

## Resumen

`cevicheria_web` es una aplicacion web para el restaurante/cevicheria **Rinconcito Marino**. Esta hecha con Spring Boot, Thymeleaf, Spring Security, JPA y MySQL. Tiene sitio publico, flujo de reservas, opiniones de clientes, panel administrativo y endpoints REST.

Ruta real del proyecto:

```text
C:\Users\USUARIO\Desktop\cevicheria\cevicheria_web
```

## Stack

- Backend: Java 21 + Spring Boot 3.5.14.
- Vistas server-side: Thymeleaf.
- Seguridad: Spring Security + JWT en cookie.
- Persistencia: JPA/Hibernate + MySQL.
- Validacion: Jakarta Validation.
- Estilos: Bootstrap 5 + CSS propio.
- Iconos: Font Awesome.
- Build: Maven.

Comandos utiles:

```bash
mvn spring-boot:run
mvn test
mvn clean package
```

Scripts locales:

```text
run-local.cmd
run-local.ps1
stop-local.cmd
stop-local.ps1
```

## Jerarquia principal

```text
cevicheria_web/
  pom.xml
  Dockerfile
  railway.json
  README.md
  context.md
  src/
    main/
      java/com/rinconcitomarino/
        RinconcitoMarinoApplication.java
        config/
          AdminBootstrapConfig.java
          SecurityConfig.java
          StaticResourceConfig.java
        controller/
          WebController.java
          ReservaRestController.java
          OpinionRestController.java
          AuthController.java
        dto/
          AuthTokenResponse.java
          EstadoUpdateRequest.java
          LoginRequest.java
          ReservaStats.java
        model/
          Reserva.java
          Opinion.java
          UsuarioAdmin.java
          EstadoReserva.java
          EstadoReservaConverter.java
          RolUsuario.java
          RolUsuarioConverter.java
        repository/
          ReservaRepository.java
          OpinionRepository.java
          UsuarioAdminRepository.java
        security/
          AdminUserDetailsService.java
          AuthenticationService.java
          JwtAuthenticationFilter.java
          JwtUtil.java
        service/
          ReservaService.java
          OpinionService.java
          OpinionNotificationService.java
      resources/
        application.properties
        application-prod.properties
        templates/
          index.html
          reservas.html
          login.html
          admin.html
        static/
          css/style.css
          assets/
            logo.png
            logo2.png
            main.mp4
            ceviche.jpg
            chicharron.jpg
            causa.jpg
            carta.pdf
    test/
      java/com/rinconcitomarino/
        RinconcitoMarinoApplicationTests.java
```

## Arquitectura general

La aplicacion combina paginas Thymeleaf con endpoints REST.

- `WebController`: controla rutas web renderizadas con templates.
- `ReservaRestController`: API REST de reservas.
- `OpinionRestController`: API REST de opiniones.
- `AuthController`: login REST/autenticacion.
- `SecurityConfig`: reglas de seguridad.
- `JwtAuthenticationFilter`: lee y valida JWT.
- `ReservaService` y `OpinionService`: reglas de negocio.
- Repositories: acceso a base de datos.

## Rutas web

```text
GET  /                  Pagina publica principal
POST /opiniones         Guarda opinion desde el sitio publico
GET  /reservas          Formulario tipo wizard para reservar
POST /reservas          Crea reserva desde formulario web
GET  /login             Login admin
GET  /admin             Dashboard principal
GET  /admin/reservas    Panel admin de reservas
GET  /admin/opiniones   Panel admin de opiniones
GET  /admin/usuarios    Panel admin de usuarios
GET  /carta             Redirige a /assets/carta.pdf
```

Acciones admin por formulario:

```text
POST /admin/reservas/{id}/estado
POST /admin/reservas/{id}/eliminar
POST /admin/opiniones/{id}/eliminar
POST /admin/usuarios
POST /admin/usuarios/{id}/eliminar
```

## Endpoints REST

Reservas:

```text
GET    /api/reservas?q=
POST   /api/reservas
PATCH  /api/reservas/{id}/estado
DELETE /api/reservas/{id}
```

Opiniones:

```text
GET    /api/opiniones?q=
POST   /api/opiniones
DELETE /api/opiniones/{id}
```

Autenticacion:

```text
POST /api/auth/login
```

## Modelos importantes

### Reserva

Entidad central del sistema. Representa una reserva de mesa.

Datos esperados:

- `nombreCompleto`
- `dni`
- `edad`
- `telefono`
- `email`
- `personas`
- `fecha`
- `hora`
- `estado`

El admin cambia estados y elimina reservas desde el panel.

### EstadoReserva

Enum de estado de reserva. Se usa para mostrar badges y controlar acciones.

Estados esperados por la UI:

- `PENDIENTE`
- `CONFIRMADO`
- `RECHAZADO`
- Otros si existen en el enum.

Cada estado expone informacion visual como `etiqueta` y `badgeClass`.

### Opinion

Representa comentarios enviados desde la pagina publica.

Datos principales:

- `nombres`
- `correo`
- `comentario`
- `fechaRegistro`

El admin puede listar, buscar y eliminar opiniones.

### UsuarioAdmin

Usuario interno del panel.

Datos:

- `usuario`
- `password`
- `rol`

La password se guarda codificada con `PasswordEncoder`.

### RolUsuario

Enum de permisos internos:

- `ADMIN`
- otros roles si existen en el enum.

El acceso a `/admin/usuarios` esta restringido a `ADMIN`.

## Seguridad

La seguridad se maneja con Spring Security.

Piezas clave:

- `SecurityConfig`: define rutas publicas, rutas protegidas, login/logout y filtros.
- `JwtUtil`: genera y valida tokens.
- `JwtAuthenticationFilter`: recupera token desde cookie y autentica la request.
- `AuthenticationService`: valida credenciales.
- `AdminUserDetailsService`: adapta `UsuarioAdmin` a Spring Security.
- `AdminBootstrapConfig`: crea usuario inicial si esta habilitado.

Variables importantes en `application.properties`:

```properties
app.jwt.secret
app.jwt.expiration-minutes
app.security.cookie-name
app.security.cookie-secure
app.admin.bootstrap.enabled
app.admin.bootstrap.user
app.admin.bootstrap.password
app.admin.bootstrap.role
```

## Base de datos

La app esta preparada para MySQL.

Configuracion principal:

```properties
spring.datasource.url=${DB_URL:jdbc:mysql://localhost:3306/rinconcito_marino?createDatabaseIfNotExist=true&serverTimezone=America/Lima}
spring.datasource.username=${DB_USER:root}
spring.datasource.password=${DB_PASSWORD:}
spring.jpa.hibernate.ddl-auto=${DDL_AUTO:update}
```

Para produccion se usan variables de entorno. Para pruebas existe dependencia H2 en scope `test`.

## Templates

### `index.html`

Pagina publica principal.

Responsabilidades:

- Hero con video `main.mp4`.
- Presentacion del restaurante.
- Platos destacados.
- Formulario de opiniones.
- Enlaces a reserva, carta y contacto.

Datos cargados por `WebController.cargarInicio()`:

- `opinion`
- `opinionOk`
- `opinionError`
- `platos`

### `reservas.html`

Formulario de reserva tipo wizard.

Usa clases clave:

- `.reservation-page`
- `.reservation-wizard`
- `.progress-container`
- `.step-indicator`
- `.step-panel`
- `.people-grid`
- `.btn-circle`
- `.time-grid`
- `.btn-time`
- `.final-layout`
- `.reservation-summary-card`

Es la pantalla donde el cliente completa cantidad de personas, fecha, hora y datos personales.

### `login.html`

Login del panel administrativo.

Usa:

- `.login-screen`
- Bootstrap cards/forms

Recibe:

- `loginRequest`
- `loginError`
- `sessionExpired`

### `admin.html`

Panel administrativo unificado. Cambia su contenido segun la variable `view`.

Valores de `view`:

- `dashboard`
- `reservas`
- `opiniones`
- `usuarios`

Secciones:

- Navbar admin.
- Dashboard operativo.
- Tabla de reservas.
- Tabla de opiniones.
- CRUD basico de usuarios.

Importante: el bloque de metricas debe funcionar como dashboard, no como contenido duplicado de cada modulo. Si se percibe duplicacion visual, revisar el `th:if` de la fila de KPIs.

## Panel admin

Menu superior:

- Dashboard: `/admin`
- Reservas: `/admin/reservas`
- Opiniones: `/admin/opiniones`
- Usuarios: `/admin/usuarios` solo `ADMIN`
- Usuario actual
- Salir

### Dashboard

Muestra:

- Reservas pendientes.
- Reservas confirmadas hoy.
- Personas esperadas hoy.
- Horario mas reservado.
- Reservas rechazadas y expiradas.
- Reservas del mes.
- Opiniones totales.
- Agenda de reservas de hoy.
- Pendientes urgentes para hoy/manana.
- Opiniones recientes.
- Accesos rapidos.

### Reservas

Muestra:

- Cliente.
- DNI y edad.
- Telefono y correo.
- Numero de personas.
- Fecha y hora.
- Estado.
- Acciones.

Acciones:

- Abrir WhatsApp del cliente.
- Confirmar.
- Rechazar.
- Eliminar.

Filtros:

- Busqueda por cliente, DNI, telefono o correo.
- Estado.
- Fecha.
- Limpiar filtros.

### Opiniones

Muestra:

- Nombre.
- Correo.
- Comentario.
- Fecha.
- Eliminar.

### Usuarios

Permite:

- Crear usuario.
- Elegir rol.
- Eliminar usuario.
- Evita eliminar la cuenta actual.

## Servicios

### ReservaService

Responsabilidades:

- Crear reservas.
- Listar reservas con busqueda.
- Cambiar estado.
- Eliminar.
- Calcular estadisticas (`ReservaStats`).
- Expirar pendientes vencidas.
- Generar URL de WhatsApp.

Es el servicio mas importante del negocio.

### OpinionService

Responsabilidades:

- Guardar opiniones.
- Listar opiniones con busqueda.
- Eliminar opiniones.
- Coordinar notificaciones si corresponde.

### OpinionNotificationService

Responsable de enviar notificaciones por correo cuando se registran opiniones, si esta habilitado por configuracion.

Variables:

```properties
app.notifications.opinions.enabled
app.notifications.opinions.to
app.notifications.from
```

## Clases CSS importantes

Archivo:

```text
src/main/resources/static/css/style.css
```

### Variables

```css
--rm-primary
--rm-accent
--rm-ink
--rm-header-dark
```

Estas variables definen la identidad visual. Para cambios de branding, empezar por ahi.

### Layout publico

- `.site-navbar`: navbar del sitio.
- `.home-navbar`: navbar transparente en home.
- `.home-navbar.navbar-scrolled`: version con fondo al hacer scroll.
- `.hero`: hero full viewport.
- `.hero > video`: video de fondo.
- `.hero::after`: overlay de contraste.
- `.hero-content`: contenido visible sobre el video.
- `.hero-logo`: logo grande del hero.
- `.scroll-cue`: indicador de scroll.
- `.section-pad`: padding vertical de secciones.
- `.brand-panel`: panel blanco reusable.
- `.index-footer`: footer publico.
- `.floating-buttons`: botones flotantes.

### Botones

- `.btn-rm`: boton principal azul.
- `.btn-outline-rm`: boton bordeado.
- `.btn-float`: boton flotante.
- `.btn-scroll-top`: boton subir.

### Reservas

- `.reservation-page`: fondo de la pagina de reserva.
- `.reservation-wizard`: contenedor principal del wizard.
- `.progress-container`: barra de pasos.
- `.step-indicator`: paso del wizard.
- `.step-indicator.active`: paso actual.
- `.step-panel`: panel oculto por defecto.
- `.step-panel.active`: panel visible.
- `.step-title`: titulo de paso.
- `.btn-back`: volver.
- `.people-grid`: opciones de cantidad.
- `.btn-circle`: boton circular de cantidad.
- `.date-picker-container`: selector de fecha.
- `.time-grid`: grilla de horarios.
- `.btn-time`: boton de hora.
- `.final-layout`: layout formulario + resumen.
- `.form-column`: columna de datos.
- `.summary-column`: columna de resumen.
- `.reservation-input-group`: grupo label/input.
- `.reservation-summary-card`: resumen oscuro.
- `.btn-confirm`: boton final.

### Admin

- `.admin-layout`: fondo general admin.
- `.table td`, `.table th`: alineacion de tablas.
- Bootstrap controla gran parte del admin: `.navbar`, `.card`, `.table`, `.badge`, `.btn`.

Si se quiere personalizar mas el admin, agregar clases propias en `admin.html` y no sobreescribir Bootstrap globalmente sin necesidad.

## Datos importantes para defender el proyecto

- El proyecto no usa JSON como base de datos: usa MySQL mediante JPA.
- Tiene arquitectura MVC: Controller, Service, Repository, Model.
- Tiene rutas web Thymeleaf y endpoints REST.
- Tiene autenticacion con Spring Security.
- Tiene JWT para mantener sesion mediante cookie.
- Tiene roles internos para proteger administracion de usuarios.
- Tiene validaciones de formularios con Jakarta Validation.
- Tiene CRUD parcial/completo para reservas, opiniones y usuarios.
- Usa variables de entorno para despliegue.

## Riesgos y limites actuales

- El admin de usuarios crea y elimina, pero no edita usuarios existentes.
- Opiniones no tienen estado de moderacion; se eliminan directamente.
- La carta es PDF estatico.
- Los platos destacados estan definidos en `WebController.cargarInicio()`, no en base de datos.
- El contenido publico no tiene CMS/admin editable.
- La UI admin depende mucho de Bootstrap; si se busca un panel mas avanzado, conviene crear layout propio.

## Recomendaciones para siguientes avances

1. Crear CRUD de platos/carta desde admin.
2. Agregar estados de opinion: pendiente, aprobado, oculto.
3. Agregar dashboard real con reservas por dia, ocupacion y horarios mas pedidos.
4. Agregar modulo de disponibilidad de horarios.
5. Agregar edicion de contenido publico desde admin.
6. Agregar subida de imagenes para platos.
7. Agregar paginacion en tablas admin.
8. Agregar auditoria basica: quien confirma/rechaza reservas.
9. Agregar tests de servicios y controladores.
10. Mejorar UI admin con tarjetas por modulo y filtros por estado/fecha.

## Flujo de prueba recomendado

1. Ejecutar la app.
2. Entrar a `/`.
3. Crear opinion.
4. Entrar a `/reservas`.
5. Crear reserva.
6. Entrar a `/login`.
7. Iniciar sesion admin.
8. Revisar `/admin`.
9. Confirmar o rechazar reserva.
10. Eliminar opinion de prueba en `/admin/opiniones`.
11. Crear usuario en `/admin/usuarios`.

## Archivos que otra IA debe revisar antes de cambiar algo

- `src/main/java/com/rinconcitomarino/controller/WebController.java`
- `src/main/java/com/rinconcitomarino/service/ReservaService.java`
- `src/main/java/com/rinconcitomarino/config/SecurityConfig.java`
- `src/main/resources/templates/admin.html`
- `src/main/resources/templates/reservas.html`
- `src/main/resources/static/css/style.css`
- `src/main/resources/application.properties`
