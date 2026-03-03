/**
 * dashboard.js - Versión Final Sincronizada
 * Controla la navegación entre secciones y la seguridad inicial.
 * Requiere: auth.js, contacts.js, calendar.js y notifications.js.
 */

/* ─────────────────────────────────────────────────
   1. PROTECCIÓN DE RUTA
   Se ejecuta de inmediato para evitar flashes de contenido privado.
───────────────────────────────────────────────── */
requireAuth(); // Función definida en auth.js

/* ─────────────────────────────────────────────────
   2. INICIALIZACIÓN AL CARGAR EL DOM
───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
  
  // Verificamos notificaciones pendientes y mostramos pop-up si existen
  if (typeof verificarNotificacionesLogin === 'function') {
    verificarNotificacionesLogin();
  }

  // Configuramos los botones de la barra lateral (Sidebar)
  const botonesNav = document.querySelectorAll('.nav-btn[data-section]');

  botonesNav.forEach(function(boton) {
    boton.addEventListener('click', function() {
      const seccion = boton.getAttribute('data-section');
      navegarA(seccion);
    });
  });

  // Opcional: Mostrar el nombre del usuario logueado en la interfaz
  const userData = localStorage.getItem('agenda_user');
  if (userData) {
    const user = JSON.parse(userData);
    const welcomeTitle = document.querySelector('.welcome-card h3');
    if (welcomeTitle) {
      welcomeTitle.textContent = `¡Bienvenido, ${user.name || 'Usuario'}! 👋`;
    }
  }
});

/* ─────────────────────────────────────────────────
   3. LÓGICA DE NAVEGACIÓN (SPA Style)
───────────────────────────────────────────────── */
/**
 * Cambia la sección visible sin recargar la página.
 * @param {string} nombreSeccion - ID de la sección (inicio, contactos, calendario)
 */
function navegarA(nombreSeccion) {
  // 1. Actualizar estado visual de los botones
  const botonesNav = document.querySelectorAll('.nav-btn[data-section]');
  botonesNav.forEach(b => b.classList.remove('active'));
  
  const botonActivo = document.querySelector(`.nav-btn[data-section="${nombreSeccion}"]`);
  if (botonActivo) botonActivo.classList.add('active');

  // 2. Cambiar visibilidad de las secciones
  const todasLasSecciones = document.querySelectorAll('.section');
  todasLasSecciones.forEach(s => s.classList.remove('active'));

  const seccionActiva = document.getElementById('section-' + nombreSeccion);
  if (seccionActiva) {
    seccionActiva.classList.add('active');
  }

  // 3. Carga de datos bajo demanda (Lazy Loading)
  if (nombreSeccion === 'contactos') {
    // Solo carga contactos si la función existe (definida en contacts.js)
    if (typeof cargarContactos === 'function') {
      cargarContactos();
    }
  }

  if (nombreSeccion === 'calendario') {
    // Inicializa o refresca FullCalendar (definida en calendar.js)
    if (typeof inicializarCalendario === 'function') {
      inicializarCalendario();
    }
  }
}

/* ─────────────────────────────────────────────────
   4. MANEJO DE CIERRE DE SESIÓN
   Vinculado al botón "Cerrar sesión" del sidebar
───────────────────────────────────────────────── */
// Nota: La función Auth.logout() ya está disponible globalmente desde auth.js