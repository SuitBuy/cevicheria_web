# Estudiante 1 - Frontend y Bootstrap

## Tema asignado

Desarrollo de la interfaz web de Rinconcito Marino mediante HTML, CSS, JavaScript y Bootstrap.

## Unidad del curso

Unidad 1: Frameworks de frontend.

## Objetivo de la exposición

Explicar cómo se construyó una interfaz clara, responsive y orientada al cliente, y cómo Bootstrap permitió adaptar las páginas a computadoras, tabletas y celulares.

## Contenido de su diapositiva

**Título:** Rinconcito Marino ofrece una experiencia web adaptable para sus clientes

- HTML5 organiza el contenido de las páginas.
- CSS3 define la identidad visual del restaurante.
- Bootstrap aporta componentes y diseño responsive.
- JavaScript permite interacciones en el proceso de reserva.
- Páginas principales: inicio, carta, reservas y acceso del personal.

## Cómo debe estar organizada la diapositiva

- En la parte superior: nombre del proyecto y título del tema.
- En el lado izquierdo: las tecnologías HTML, CSS, Bootstrap y JavaScript.
- En el lado derecho: indicar las páginas principales del sistema.
- En la parte inferior: una frase de resultado: `Interfaz clara, responsive y orientada al cliente`.
- Evitar definiciones extensas; la explicación completa debe darse oralmente.

## Guion principal

> Buenos días. Nuestro proyecto final se llama Rinconcito Marino y consiste en una plataforma web para presentar el restaurante, recibir reservas y administrar la atención de los clientes. Mi tema corresponde al desarrollo frontend. Utilizamos HTML para organizar el contenido, CSS para definir la identidad visual y Bootstrap para construir una interfaz responsive. Bootstrap nos proporcionó un sistema de filas y columnas, barras de navegación, botones, formularios y tablas que se adaptan a diferentes tamaños de pantalla. También utilizamos JavaScript en el proceso interactivo de reserva. El sistema cuenta con una página principal, una carta, un formulario de reservas y una página de acceso para el personal. Con esta primera unidad construimos la parte visual que posteriormente se conectó con el backend desarrollado en Spring Boot.

## Información adicional para estudiar

### ¿Qué es frontend?

Es la parte de una aplicación con la que interactúa directamente el usuario. Incluye la estructura, los estilos, los controles y el comportamiento visible en el navegador.

### ¿Qué es Bootstrap?

Es un framework de frontend que proporciona estilos y componentes reutilizables. Evita crear desde cero elementos comunes como barras de navegación, botones, tablas, alertas y formularios.

### ¿Qué significa responsive?

Significa que la interfaz cambia su distribución según el tamaño de la pantalla. Bootstrap utiliza un sistema de cuadrícula de 12 columnas y puntos de quiebre como `sm`, `md`, `lg` y `xl`.

### ¿Por qué no se utilizó solamente HTML?

HTML define la estructura, pero necesita CSS para la presentación y JavaScript para las interacciones. Bootstrap ayuda a mantener un diseño consistente y adaptable.

### Componentes utilizados en el proyecto

- Barra de navegación.
- Contenedores, filas y columnas.
- Formularios de reserva y login.
- Botones y alertas.
- Tablas administrativas.
- Diseño adaptable mediante clases responsive.

## Aplicación concreta en Rinconcito Marino

- `index.html` presenta el restaurante, platos y accesos principales.
- `reservas.html` guía al cliente durante el registro de una reserva.
- `login.html` contiene el acceso del personal.
- `admin.html` presenta tablas, filtros, estadísticas y acciones administrativas.
- `style.css` contiene los estilos personalizados del restaurante.

## Demostración recomendada

1. Abrir la página principal.
2. Mostrar la barra de navegación y los botones principales.
3. Cambiar el ancho del navegador para demostrar el comportamiento responsive.
4. Abrir el formulario de reservas.
5. Explicar que los mismos componentes mantienen su apariencia en diferentes páginas.

## Preguntas probables

### ¿Por qué eligieron Bootstrap?

Porque permite desarrollar una interfaz responsive con mayor rapidez y ofrece componentes consistentes que pueden personalizarse con CSS.

### ¿Dónde se guardan los archivos visuales?

Las plantillas están en `src/main/resources/templates`, los estilos en `static/css` y las imágenes o recursos en `static/assets`.

### ¿Bootstrap reemplaza a CSS?

No. Bootstrap proporciona una base, pero el proyecto también utiliza CSS propio para mantener la identidad visual de Rinconcito Marino.

## Archivos relacionados

- `src/main/resources/templates/index.html`
- `src/main/resources/templates/reservas.html`
- `src/main/resources/templates/login.html`
- `src/main/resources/templates/admin.html`
- `src/main/resources/static/css/style.css`

## Transición al estudiante 2

> Después de construir la interfaz, fue necesario conectarla con un backend capaz de procesar formularios y generar contenido dinámico. Esa parte se desarrolló con Spring Boot y Thymeleaf.
