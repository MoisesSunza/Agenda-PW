/**
 * notifications.js - Gestión de Alertas y Vigilante de Proximidad
 * Versión Final Corregida (Global Scope)
 */

let listaNotificaciones = [];
let recordatoriosVistos = new Set(); // Evita duplicar alertas en la misma sesión

document.addEventListener('DOMContentLoaded', function() {
    // Carga inicial si el usuario está logueado
    if (localStorage.getItem('jwt_token')) {
        cargarNotificaciones();
    }

    // Lógica para cerrar el panel de notificaciones al hacer clic fuera
    const btnCampana = document.getElementById('btn-campana'); 
    const panelNotif = document.getElementById('notif-panel'); 

    document.addEventListener('click', function(event) {
        if (panelNotif && btnCampana && !panelNotif.contains(event.target) && !btnCampana.contains(event.target)) {
            panelNotif.classList.remove('active');
        }
    });

    // VIGILANTE DE PROXIMIDAD: Revisa cada 30 segundos
    setInterval(verificarRecordatoriosProximos, 30000); 

    // REFRESCO AUTOMÁTICO DE CAMPANA: Cada 15 segundos
    setInterval(function() {
        if (localStorage.getItem('jwt_token')) cargarNotificaciones();
    }, 15000);
});

/* ─────────────────────────────────────────────────
   INTERFAZ DE USUARIO
───────────────────────────────────────────────── */
function toggleDropdown() {
    const panelNotif = document.getElementById('notif-panel');
    if (panelNotif) panelNotif.classList.toggle('active');
}

function cerrarPopup() {
    const popup = document.getElementById('popup-notificaciones');
    if (popup) popup.classList.remove('active');
}

/* ─────────────────────────────────────────────────
   SISTEMA DE RECORDATORIOS (10 MINUTOS ANTES)
───────────────────────────────────────────────── */
function verificarRecordatoriosProximos() {
    // Usamos la variable global poblada por calendar.js
    if (!window.listaEventos || window.listaEventos.length === 0) return;

    const ahora = new Date();

    window.listaEventos.forEach(evento => {
        // Solo verificamos eventos con hora que no hayamos avisado ya
        if (!evento.hora || recordatoriosVistos.has(evento.id)) return;

        // Construir fecha objeto para comparar (YYYY-MM-DDTHH:mm)
        const fechaEvento = new Date(`${evento.fecha_inicio}T${evento.hora}`);
        const diferenciaMs = fechaEvento - ahora;
        const minutosRestantes = Math.floor(diferenciaMs / 1000 / 60);

        // Si faltan exactamente 10 minutos (margen de 9 a 10)
        if (minutosRestantes >= 9 && minutosRestantes <= 10) {
            lanzarPopupAviso(evento);
            recordatoriosVistos.add(evento.id);
        }
    });
}

function lanzarPopupAviso(evento) {
    const popup = document.getElementById('popup-notificaciones');
    const texto = document.getElementById('popup-texto');

    if (popup && texto) {
        texto.innerHTML = `<strong>¡Evento Próximo!</strong><br>${evento.titulo} comienza en 10 minutos (${evento.hora}).`;
        popup.classList.add('active');
        
        // Auto-cerrar el popup tras 10 segundos
        setTimeout(() => popup.classList.remove('active'), 10000);
    }
}

/* ─────────────────────────────────────────────────
   GESTIÓN DE LA CAMPANA (CRUD)
───────────────────────────────────────────────── */
async function cargarNotificaciones() {
    try {
        const respuesta = await http.get('/notifications');
        
        // Extracción segura para Laravel API
        let datos = respuesta.data ? respuesta.data : respuesta;
        if (datos && datos.data) datos = datos.data;
        
        listaNotificaciones = Array.isArray(datos) ? datos : [];
        renderizarNotificaciones();
    } catch (error) {
        console.error('Error al sincronizar notificaciones:', error);
    }
}

function renderizarNotificaciones() {
    const contenedor = document.getElementById('notif-lista');
    const badge = document.getElementById('notif-badge');
    if (!contenedor) return;

    contenedor.innerHTML = '';
    let noLeidas = 0;

    if (listaNotificaciones.length === 0) {
        contenedor.innerHTML = '<p style="padding:15px; text-align:center; color:var(--muted);">Sin alertas nuevas.</p>';
        if (badge) badge.style.display = 'none';
        return;
    }

    listaNotificaciones.forEach(notif => {
        if (!notif.leida) noLeidas++;

        const div = document.createElement('div');
        div.id = `notif-${notif.id}`; 
        div.className = `notif-item ${notif.leida ? 'leida' : 'unread'}`;
        
        div.innerHTML = `
            <div class="notif-contenido">
                <p class="notif-mensaje">${notif.mensaje}</p>
                <span class="notif-fecha">${formatearFecha(notif.created_at)}</span>
            </div>
            <div class="notif-acciones">
                ${!notif.leida ? `<button class="btn-icon" onclick="marcarComoLeida(${notif.id})" title="Leída">✔</button>` : ''}
                <button class="btn-icon danger" onclick="eliminarNotificacion(${notif.id})" title="Eliminar">✖</button>
            </div>
        `;
        contenedor.appendChild(div);
    });

    if (badge) {
        if (noLeidas > 0) {
            badge.textContent = noLeidas;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

async function marcarComoLeida(id) {
    try {
        await http.put(`/notifications/${id}/read`);
        // Actualización optimista: marcamos localmente y renderizamos
        const index = listaNotificaciones.findIndex(n => n.id === id);
        if (index !== -1) listaNotificaciones[index].leida = true;
        renderizarNotificaciones();
    } catch (error) {
        console.error('Error al marcar como leída:', error);
    }
}

async function eliminarNotificacion(id) {
    try {
        await http.delete(`/notifications/${id}`);
        // Eliminación visual inmediata
        listaNotificaciones = listaNotificaciones.filter(n => n.id !== id);
        renderizarNotificaciones();
    } catch (error) {
        console.error('Error al eliminar notificación:', error);
    }
}

function formatearFecha(fechaString) {
    if (!fechaString) return '';
    const f = new Date(fechaString);
    return f.toLocaleDateString('es-ES', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

/* ─────────────────────────────────────────────────
   PUENTE AL OBJETO WINDOW (SOLUCIÓN REFERENCE ERROR)
───────────────────────────────────────────────── */
window.marcarComoLeida = marcarComoLeida;
window.eliminarNotificacion = eliminarNotificacion;
window.toggleDropdown = toggleDropdown;
window.cerrarPopup = cerrarPopup;