/**
 * notifications.js - Versión Final Sincronizada
 * Módulo de notificaciones para Laravel 12.
 * Requiere: auth.js (para el objeto http).
 */

/* ─────────────────────────────────────────────────
   VARIABLE GLOBAL
───────────────────────────────────────────────── */
var listaNotificaciones = [];

/* ─────────────────────────────────────────────────
   CARGAR NOTIFICACIONES
───────────────────────────────────────────────── */
async function cargarNotificaciones() {
  try {
    // Sincronizado con la ruta /api/notifications que documentamos en Swagger
    var respuesta = await http.get('/notifications');
    listaNotificaciones = respuesta.data || respuesta;

    actualizarBadge();
    renderizarDropdown();

  } catch (error) {
    console.error('Error cargando notificaciones:', error);
  }
}

/* ─────────────────────────────────────────────────
   ACTUALIZAR BADGE (CONTADOR ROJO)
───────────────────────────────────────────────── */
function actualizarBadge() {
  // Filtramos las que no han sido leídas
  var noLeidas = listaNotificaciones.filter(function(n) {
    return !n.leida && !n.read; // Soporta ambos formatos de campo
  });

  var badge = document.getElementById('notif-badge');

  if (noLeidas.length > 0) {
    badge.textContent = noLeidas.length > 9 ? '9+' : noLeidas.length;
    badge.style.display = 'flex';
  } else {
    badge.style.display = 'none';
  }
}

/* ─────────────────────────────────────────────────
   RENDERIZAR PANEL DESPLEGABLE
───────────────────────────────────────────────── */
function renderizarDropdown() {
  var lista = document.getElementById('notif-lista');

  if (listaNotificaciones.length === 0) {
    lista.innerHTML = '<p class="notif-empty">No tienes notificaciones.</p>';
    return;
  }

  var html = '';
  listaNotificaciones.forEach(function(notif) {
    var leida = notif.leida || notif.read || false;
    var mensaje = notif.mensaje || notif.message || '';
    var fecha = notif.fecha || notif.created_at || '';

    html += `
      <div class="notif-item ${leida ? 'leida' : ''}" id="notif-item-${notif.id}">
        <div class="notif-contenido">
          <p class="notif-mensaje">${escaparHTMLNotif(mensaje)}</p>
          ${fecha ? `<span class="notif-fecha">${formatearFecha(fecha)}</span>` : ''}
        </div>
        <div class="notif-acciones">
          ${!leida ? `<button class="notif-btn-leer" onclick="marcarComoLeida(${notif.id})" title="Marcar como leída">✓</button>` : ''}
          <button class="notif-btn-eliminar" onclick="eliminarNotificacion(${notif.id})" title="Eliminar">✕</button>
        </div>
      </div>
    `;
  });

  lista.innerHTML = html;
}

/* ─────────────────────────────────────────────────
   ACCIONES (LEER Y ELIMINAR)
───────────────────────────────────────────────── */
async function marcarComoLeida(id) {
  try {
    // Sincronizado: Laravel suele usar PUT para actualizaciones de estado
    await http.put('/notifications/' + id + '/read', {});

    listaNotificaciones = listaNotificaciones.map(function(n) {
      if (n.id === id) return Object.assign({}, n, { leida: true, read: true });
      return n;
    });

    actualizarBadge();
    renderizarDropdown();
  } catch (error) {
    console.error('Error al marcar notificación:', error);
  }
}

async function eliminarNotificacion(id) {
  try {
    await http.delete('/notifications/' + id);
    listaNotificaciones = listaNotificaciones.filter(function(n) { return n.id !== id; });
    actualizarBadge();
    renderizarDropdown();
  } catch (error) {
    console.error('Error al eliminar notificación:', error);
  }
}

/* ─────────────────────────────────────────────────
   UI HELPERS (DROPDOWN Y POP-UP)
───────────────────────────────────────────────── */
function toggleDropdown() {
  var panel = document.getElementById('notif-panel');
  panel.classList.toggle('active');
}

async function verificarNotificacionesLogin() {
  try {
    var respuesta = await http.get('/notifications');
    listaNotificaciones = respuesta.data || respuesta;

    var noLeidas = listaNotificaciones.filter(n => !n.leida && !n.read);
    actualizarBadge();
    renderizarDropdown();

    if (noLeidas.length > 0) {
      mostrarPopupLogin(noLeidas.length);
    }
  } catch (error) {
    console.error('Error en notificaciones de inicio:', error);
  }
}

function mostrarPopupLogin(cantidad) {
  var popup = document.getElementById('popup-notificaciones');
  var texto = document.getElementById('popup-texto');
  if (!popup || !texto) return;

  texto.textContent = `Tienes ${cantidad} notificación${cantidad > 1 ? 'es' : ''} pendiente${cantidad > 1 ? 's' : ''}.`;
  popup.classList.add('active');
  setTimeout(() => cerrarPopup(), 5000);
}

function cerrarPopup() {
  document.getElementById('popup-notificaciones').classList.remove('active');
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
  var campana = document.getElementById('btn-campana');
  var panel = document.getElementById('notif-panel');
  if (campana && panel && !campana.contains(e.target) && !panel.contains(e.target)) {
    panel.classList.remove('active');
  }
});

function escaparHTMLNotif(texto) {
  var div = document.createElement('div');
  div.textContent = texto;
  return div.innerHTML;
}

function formatearFecha(fechaISO) {
  try {
    var fecha = new Date(fechaISO);
    return fecha.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' });
  } catch { return fechaISO; }
}