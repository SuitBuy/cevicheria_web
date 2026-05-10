# Rinconcito Marino - Spring Boot

Aplicacion web del restaurante Rinconcito Marino migrada a Spring Boot, Thymeleaf, Spring Security JWT, JPA/Hibernate y MySQL.

## Produccion en Railway

Arquitectura acordada:

- GitHub: `https://github.com/SuitBuy/cevicheria_web`
- Runtime: Railway
- Base de datos: Railway MySQL limpia
- Dominio: `suuit.dev`
- DNS: Cloudflare / Namecheap
- Vercel: no se usa para esta version

## Variables Railway

Configura estas variables en el servicio Spring Boot:

```env
SPRING_PROFILES_ACTIVE=prod
JWT_SECRET=un-secreto-largo-y-aleatorio
JWT_EXPIRATION_MINUTES=480
JWT_COOKIE_SECURE=true
ADMIN_BOOTSTRAP_ENABLED=true
ADMIN_USER=admin
ADMIN_PASSWORD=una-contrasena-segura
ADMIN_ROLE=ADMIN
WHATSAPP_NUMBER=51910872665
RESERVATION_FEE=20.00
DDL_AUTO=update
```

Para recibir por correo cada comentario enviado desde "Queremos tu opinion", agrega tambien:

```env
OPINION_EMAIL_ENABLED=true
OPINION_EMAIL_TO=restaurante@suuit.dev
MAIL_FROM="Rinconcito Marino <no-reply@suuit.dev>"
SPRING_MAIL_HOST=smtp.example.com
SPRING_MAIL_PORT=587
SPRING_MAIL_USERNAME=usuario-smtp
SPRING_MAIL_PASSWORD=password-smtp
SPRING_MAIL_PROPERTIES_MAIL_SMTP_AUTH=true
SPRING_MAIL_PROPERTIES_MAIL_SMTP_STARTTLS_ENABLE=true
```

El proveedor SMTP puede ser Gmail, Brevo, Mailgun, Resend SMTP u otro. Si `OPINION_EMAIL_ENABLED=false`, las opiniones se guardan sin enviar correo.

Si agregas el plugin MySQL de Railway al mismo proyecto, Railway expone estas variables automaticamente al servicio:

```env
MYSQLHOST
MYSQLPORT
MYSQLDATABASE
MYSQLUSER
MYSQLPASSWORD
```

## Primer despliegue

1. Conecta Railway al repositorio GitHub.
2. Crea/agrega una base MySQL en Railway.
3. En el servicio web, agrega las variables anteriores.
4. Despliega desde la rama configurada en Railway.
5. Entra a `/login` con `ADMIN_USER` y `ADMIN_PASSWORD`.
6. Despues del primer login, cambia la clave desde base de datos o rota `ADMIN_PASSWORD` y elimina el usuario si necesitas recrearlo.

## Dominio

En Railway, agrega el dominio `suuit.dev` al servicio web.

En Cloudflare o Namecheap, apunta el dominio segun indique Railway:

- Si Railway entrega `CNAME`, crea `CNAME` para `www` o el subdominio elegido.
- Si usas apex/root `suuit.dev`, configura el registro recomendado por Railway o usa Cloudflare CNAME flattening.

Activa HTTPS desde Railway y manten `JWT_COOKIE_SECURE=true`.

## Desarrollo local

En Windows:

```powershell
.\run-local.cmd
```

Esto levanta la app con H2 en memoria y abre `http://localhost:8081`.

Para detenerla:

```powershell
.\stop-local.cmd
```

Para correr pruebas:

```bash
mvn test
```
