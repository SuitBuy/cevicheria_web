// CRITERIO 1: Introducción a JavaScript (const)
const headerAnimado = document.querySelector('.header-animado');
const menuToggle = document.getElementById('menu-toggle');
const navMenu = document.getElementById('nav-menu');
const opinionForm = document.getElementById('opinionForm');
const formMessageContainer = document.getElementById('form-message');
const modal = document.getElementById('promotional-modal');
const closeModalBtn = document.getElementById('close-modal-btn');
const openCartaBtn = document.getElementById('open-carta-btn');

// CRITERIO 1 & 3: Evento 1 - Header Animation (para index.html)
window.addEventListener('scroll', () => {
    // Uso de console.log para depuración (Criterio 1)
    console.log('Scroll Y:', window.scrollY); 
    
    // Solo aplica el efecto de scroll si la cabecera es la animada (index.html)
    if (headerAnimado) { 
        headerAnimado.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// CRITERIO 1, 3 & 4: Evento 2 - Menu Toggle (Menú Responsivo - para ambos HTML)
if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', () => {
        // Segundo uso de addEventListener (Criterio 3)
        navMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
    });
}


// CRITERIO 2 & 3: Evento 3 - Form Submission (Estructuras de Control y DOM - solo index.html)
if (opinionForm && formMessageContainer) {
    opinionForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // CRITERIO 2: Estructuras de control (for loop, if/else) y Arreglos
        const inputs = opinionForm.querySelectorAll('input[required], textarea[required]');
        let isValid = true;
        const requiredFields = []; // Uso de Arreglos (Criterio 2)

        for (let i = 0; i < inputs.length; i++) { // For loop (Criterio 2)
            if (!inputs[i].value.trim()) { // if statement (Criterio 2)
                isValid = false;
                requiredFields.push(inputs[i].id); 
            }
        }

        if (isValid) {
            // Métodos de E/S (alert) (Criterio 2)
            alert('¡Gracias por tu opinión, ' + document.getElementById('nombres').value + '! Tu mensaje ha sido enviado.'); 
            
            // CRITERIO 3: Manipulación del DOM (Creación dinámica de elementos)
            opinionForm.style.display = 'none';
            formMessageContainer.innerHTML = ''; // Limpiar contenedor

            const successMsg = document.createElement('p'); // Crear elemento (Criterio 3)
            successMsg.textContent = '✅ ¡Recibimos tu mensaje! Un miembro de nuestro equipo se pondrá en contacto pronto.';
            successMsg.style.cssText = 'color: #0077b6; font-weight: bold; font-size: 1.2rem; text-align: center; margin-top: 20px;';
            
            formMessageContainer.appendChild(successMsg); // Añadir elemento (Criterio 3)

        } else {
            // CRITERIO 2: Estructuras de control/E/S
            alert('Por favor, completa todos los campos requeridos: ' + requiredFields.join(', '));
        }
    });
}

// CRITERIO 4: Lógica del Modal/Pop-up (solo index.html)
if (modal && closeModalBtn && openCartaBtn) {
    // Abrir Modal después de un breve retardo para demostración (Criterio 4)
    setTimeout(() => {
        modal.style.display = 'block';
    }, 2000);

    // Evento para cerrar Modal con el botón (Criterio 4)
    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Evento para cerrar Modal al hacer clic fuera
    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Evento para navegar a la carta desde el modal
    openCartaBtn.addEventListener('click', () => {
        window.location.href = 'carta.html';
    });
}