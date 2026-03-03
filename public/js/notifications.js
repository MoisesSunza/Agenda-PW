/**
 * notifications.js - Versión Final con Recordatorios de 10 min
 */

let listaNotificaciones = [];
let recordatoriosVistos = new Set(); // Evita repetir el mismo aviso

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('jwt_token')) {
        cargarNotificaciones();
    }

    const btnCampana = document.getElementById('btn-campana'); 
    const panelNotif = document.getElementById('notif-panel'); 

    document.addEventListener('click', function(event) {
        if (panelNotif && btnCampana && !panelNotif.contains(event.target) && !btnCampana.contains(event.target)) {
            panelNotif.classList.remove('active');
        }
    });

    // INICIAR VIGILANTE DE RECORDATORIOS
    setInterval(verificarRecordatorios, 30000); 
});

function toggleDropdown() {
    const panelNotif = document.getElementById('notif-panel');
    if (panelNotif) panelNotif.classList.toggle('active');
}

/* ─────────────────────────────────────────────────
   SISTEMA DE RECORDATORIOS (10 MIN ANTES)
───────────────────────────────────────────────── */
function verificarRecordatorios() {
    if (!window.listaEventos || window.listaEventos.length === 0) return;

    const ahora = new Date();

    window.listaEventos.forEach(evento => {
        if (!evento.hora || recordatoriosVistos.has(evento.id)) return;

        const fechaEvento = new Date(`${evento.fecha_inicio}T${evento.hora}`);
        const diferenciaMs = fechaEvento - ahora;
        const diferenciaMinutos = Math.floor(diferenciaMs / 1000 / 60);

        // Si faltan exactamente 10 minutos (margen entre 9 y 10)
        if (diferenciaMinutos >= 9 && diferenciaMinutos <= 10) {
            lanzarPopupRecordatorio(evento);
            recordatoriosVistos.add(evento.id);
        }
    });
}

function lanzarPopupRecordatorio(evento) {
    const popup = document.getElementById('popup-notificaciones');
    const texto = document.getElementById('popup-texto');

    if (popup && texto) {
        texto.innerHTML = `<strong>Próximo evento:</strong><br>${evento.titulo} a las ${evento.hora}`;
        popup.classList.add('active');
        
        // Auto-cerrar tras 10 segundos
        setTimeout(() => {
            popup.classList.remove('active');
        }, 10000);
    }
}

function cerrarPopup() {
    document.getElementById('popup-notificaciones').classList.remove('active');
}

/* ─────────────────────────────────────────────────
   GESTIÓN DE NOTIFICACIONES (CRUD)
───────────────────────────────────────────────── */
async function cargarNotificaciones() {
    try {
        const respuesta = await http.get('/notifications');
        let datos = respuesta.data ? respuesta.data : respuesta;
        if (datos && datos.data) datos = datos.data;
        listaNotificaciones = datos || [];
        renderizarNotificaciones();
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

function renderizarNotificaciones() {
    const contenedor = document.getElementById('notif-lista');
    const badge = document.getElementById('notif-badge');
    if (!contenedor) return;

    contenedor.innerHTML = '';
    let noLeidas = 0;

    if (!listaNotificaciones || listaNotificaciones.length === 0) {
        contenedor.innerHTML = '<p class="notif-empty" style="padding:15px; text-align:center;">No hay notificaciones.</p>';
        if (badge) badge.style.display = 'none';
        return;
    }

    listaNotificaciones.forEach(notif => {
        if (!notif.leida) noLeidas++;

        const div = document.createElement('div');
        div.id = `notif-${notif.id}`; 
        div.className = `notif-item ${notif.leida ? 'leida' : ''}`;
        
        div.innerHTML = `
            <div class="notif-contenido">
                <p class="notif-mensaje">${notif.mensaje}</p>
                <span class="notif-fecha">${formatearFecha(notif.created_at)}</span>
            </div>
            <div class="notif-acciones">
                ${!notif.leida ? `<button class="btn-secondary" onclick="marcarComoLeida(${notif.id})" style="padding:4px 8px;">✔</button>` : ''}
                <button class="btn-danger" onclick="eliminarNotificacion(${notif.id})" style="padding:4px 8px;">✖</button>
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

function marcarComoLeida(id) {
    const elemento = document.getElementById(`notif-${id}`);
    if (elemento) {
        elemento.classList.remove('unread');
        elemento.style.opacity = '0.7';
        restarCampanita();
    }
    http.put('/notifications/' + id + '/read').catch(e => console.error(e));
}

function eliminarNotificacion(id) {
    const elemento = document.getElementById(`notif-${id}`);
    if (elemento) {
        if (!elemento.classList.contains('leida')) restarCampanita();
        elemento.style.transform = "translateX(20px)";
        elemento.style.opacity = "0";
        setTimeout(() => { elemento.remove(); verificarListaVacia(); }, 200);
    }
    http.delete('/notifications/' + id).catch(e => console.error(e));
}

function restarCampanita() {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    let total = parseInt(badge.textContent) || 0;
    if (total <= 1) { badge.style.display = 'none'; } 
    else { badge.textContent = total - 1; }
}

function verificarListaVacia() {
    const contenedor = document.getElementById('notif-lista');
    if (contenedor && contenedor.children.length === 0) {
        contenedor.innerHTML = '<p class="notif-empty" style="padding:15px; text-align:center;">No hay notificaciones.</p>';
    }
}

function formatearFecha(fechaString) {
    if (!fechaString) return '';
    const f = new Date(fechaString);
    return f.toLocaleDateString('es-ES', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

setInterval(function() {
    if (localStorage.getItem('jwt_token')) cargarNotificaciones();
}, 15000);