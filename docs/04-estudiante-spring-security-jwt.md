# Estudiante 4 - Spring Security, JWT e integración final

## Tema asignado

Autenticación, autorización y protección del panel administrativo mediante Spring Security, BCrypt, JWT y roles.

## Unidad del curso

Unidad 4: Spring Security e integración de frontend y backend.

## Objetivo de la exposición

Explicar cómo se identifica al usuario, cómo se protegen las contraseñas y cómo se restringen las operaciones administrativas según el rol.

## Contenido de su diapositiva

**Título:** JWT y los roles protegen el panel administrativo

- BCrypt protege las contraseñas almacenadas.
- JWT identifica al usuario en cada solicitud.
- Spring Security protege rutas y operaciones.
- Existen roles ADMIN, EMPLEADO y LECTOR.
- El proyecto integra frontend, backend, base de datos, validación y seguridad.

**Flujo principal:**

`Login → BCrypt → JWT → Filtro → SecurityContext → Recurso protegido`

## Cómo debe estar organizada la diapositiva

- En la parte superior: título del tema.
- En el centro: flujo completo del login y validación del token.
- En la parte inferior: tabla corta de permisos por rol.
- En un lateral: rutas públicas y protegidas.
- Terminar con la frase: `Aplicación dinámica, validada y segura`.

## Matriz de permisos

| Operación | ADMIN | EMPLEADO | LECTOR |
|---|:---:|:---:|:---:|
| Consultar panel | Sí | Sí | Sí |
| Modificar reservas | Sí | Sí | No |
| Moderar opiniones | Sí | Sí | No |
| Eliminar registros | Sí | No | No |
| Gestionar usuarios | Sí | No | No |

## Guion principal

> Mi tema corresponde a la seguridad y la integración final. Para proteger el panel implementamos Spring Security. El usuario primero ingresa sus credenciales y AuthenticationService busca su cuenta en la base de datos. La contraseña se compara mediante BCrypt, por lo que las claves no se guardan como texto visible. Si las credenciales son correctas, JwtUtil genera un token JWT con el nombre del usuario, su rol y una fecha de vencimiento. Ese token se guarda en una cookie HttpOnly y también puede utilizarse como Bearer Token para la API. En cada solicitud, JwtAuthenticationFilter valida el token y registra al usuario en el SecurityContext. Finalmente, SecurityConfig verifica la ruta, el método HTTP y el rol. El lector solo consulta, el empleado puede gestionar reservas y opiniones, y el administrador también elimina registros y administra usuarios. De esta forma integramos Bootstrap y Thymeleaf, Spring Boot, JPA, MySQL, validaciones y seguridad en una sola aplicación.

## Información adicional para estudiar

### Diferencia entre autenticación y autorización

- **Autenticación:** comprueba quién es el usuario.
- **Autorización:** determina qué acciones puede realizar ese usuario.

### ¿Qué es Spring Security?

Es el componente de Spring encargado de autenticación, autorización, filtros de seguridad, protección de rutas y administración del contexto del usuario.

### ¿Qué es BCrypt?

Es un algoritmo diseñado para almacenar contraseñas mediante hashes. No permite recuperar la contraseña original; durante el login se compara la clave ingresada con el hash almacenado.

### ¿Qué es JWT?

JWT significa JSON Web Token. Es un token firmado que puede incluir información o claims. En este proyecto contiene:

- Emisor del token.
- Nombre del usuario.
- Rol.
- Fecha de creación.
- Fecha de vencimiento.

### Partes conceptuales de un JWT

- **Header:** algoritmo y tipo de token.
- **Payload:** claims o información del usuario.
- **Signature:** firma que permite detectar modificaciones.

El contenido del JWT puede leerse, pero no debe poder modificarse sin invalidar la firma. Por eso no deben guardarse contraseñas ni datos secretos dentro del payload.

### Flujo de autenticación del proyecto

1. El usuario envía su nombre y contraseña.
2. `AuthenticationService` busca el usuario.
3. BCrypt compara la contraseña.
4. `JwtUtil` genera el token.
5. El navegador recibe una cookie `RM_TOKEN`.
6. `JwtAuthenticationFilter` valida el token en cada solicitud.
7. Spring Security aplica los permisos del rol.

### Rutas públicas y protegidas

**Públicas:**

- `/`
- `/reservas`
- `/login`
- `/carta`
- Recursos CSS e imágenes.

**Protegidas:**

- `/admin/**`
- Administración de usuarios.
- Modificación y eliminación de información.
- Endpoints administrativos de reservas y opiniones.

## Aplicación concreta en Rinconcito Marino

- `SecurityConfig` define permisos por ruta y método HTTP.
- `AuthenticationService` valida usuario y contraseña.
- `AdminUserDetailsService` carga el usuario y sus autoridades.
- `JwtUtil` genera y verifica tokens.
- `JwtAuthenticationFilter` procesa cookies y encabezados Bearer.
- `AuthController` administra login y logout.
- Thymeleaf utiliza `sec:authorize` para mostrar u ocultar acciones según el rol.

## Demostración recomendada

1. Intentar abrir `/admin` sin iniciar sesión.
2. Mostrar la redirección al login.
3. Probar una contraseña incorrecta.
4. Iniciar sesión correctamente.
5. Mostrar que el nombre del usuario aparece en el panel.
6. Comparar las acciones disponibles para LECTOR y ADMIN.
7. Cerrar sesión e intentar volver a `/admin`.

## Preguntas probables

### ¿Por qué utilizar JWT?

Porque permite identificar al usuario mediante un token firmado y validar cada solicitud sin mantener una sesión tradicional en el servidor.

### ¿Dónde se almacena el JWT?

En la web se almacena en una cookie `HttpOnly` llamada `RM_TOKEN`. La API también acepta el encabezado `Authorization: Bearer`.

### ¿Qué significa HttpOnly?

Impide que el token sea leído directamente mediante JavaScript del navegador, reduciendo la exposición ante determinados ataques.

### ¿Qué pasa cuando vence el token?

La validación falla, el contexto de seguridad se limpia y el usuario debe iniciar sesión nuevamente.

### ¿Cómo demuestra el proyecto la autorización?

Cada rol tiene permisos diferentes. Un lector no puede modificar datos y un empleado no puede administrar usuarios ni eliminar registros.

### ¿Cuál es la integración final del curso?

Bootstrap y Thymeleaf forman la interfaz, Spring Boot procesa las solicitudes, JPA e Hibernate trabajan con MySQL, Jakarta Validation controla los datos y Spring Security protege el acceso.

## Archivos relacionados

- `src/main/java/com/rinconcitomarino/config/SecurityConfig.java`
- `src/main/java/com/rinconcitomarino/security/AuthenticationService.java`
- `src/main/java/com/rinconcitomarino/security/AdminUserDetailsService.java`
- `src/main/java/com/rinconcitomarino/security/JwtUtil.java`
- `src/main/java/com/rinconcitomarino/security/JwtAuthenticationFilter.java`
- `src/main/java/com/rinconcitomarino/controller/AuthController.java`
- `src/main/java/com/rinconcitomarino/model/RolUsuario.java`
- `src/main/resources/templates/login.html`
- `src/main/resources/templates/admin.html`

## Cierre final

> En conclusión, Rinconcito Marino aplica los temas principales del curso en una solución funcional: una interfaz responsive, un backend organizado, persistencia y validación de datos, y seguridad basada en JWT y roles. El resultado es un sistema que informa al cliente y mejora la gestión interna del restaurante.
