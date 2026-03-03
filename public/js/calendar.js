/**
 * calendar.js - Versión Final Sincronizada con Notificaciones
 */

var calendarioInstancia = null;
window.listaEventos = []; // Global para el sistema de recordatorios
let idEventoAEliminar = null; 

/* ─────────────────────────────────────────────────
   INICIALIZAR CALENDARIO
───────────────────────────────────────────────── */
function inicializarCalendario() {
  var contenedor = document.getElementById('fullcalendar');

  if (calendarioInstancia) {
    cargarEventos();
    return;
  }

  calendarioInstancia = new FullCalendar.Calendar(contenedor, {
    locale: 'es',
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listMonth'
    },
    height: 'auto',
    firstDay: 0,

    events: function(fetchInfo, successCallback, failureCallback) {
      http.get('/events')
        .then(function(respuesta) {
          // Extracción segura de datos para Laravel API
          let datos = respuesta.data ? respuesta.data : respuesta;
          if (datos && datos.data) datos = datos.data;
          
          const eventosArr = Array.isArray(datos) ? datos : [];
          window.listaEventos = eventosArr; // Compartir con notifications.js

          var eventosFormateados = eventosArr.map(function(e) {
            let fechaInicioExacta = e.fecha_inicio || e.start;
            if (e.hora) {
                fechaInicioExacta = `${fechaInicioExacta}T${e.hora}`;
            }

            return {
              id:    e.id,
              title: e.titulo || e.title,
              start: fechaInicioExacta,
              description: e.descripcion || e.description || '',
              allDay: e.hora ? false : true,
              extendedProps: { hora: e.hora || '' }
            };
          });

          successCallback(eventosFormateados);
        })
        .catch(function(error) {
          console.error('Error en calendario:', error);
          failureCallback(error);
        });
    },

    dateClick: function(info) {
      let fechaSeleccionada = info.allDay ? info.dateStr : info.dateStr.split('T')[0];
      let horaSeleccionada = info.allDay ? '' : info.dateStr.split('T')[1].substring(0, 5);
      abrirModalEvento(fechaSeleccionada, horaSeleccionada);
    },

    eventClick: function(info) {
      var evento = window.listaEventos.find(e => String(e.id) === String(info.event.id));
      if (evento) abrirModalEditarEvento(evento);
    },

    eventColor: '#c8a96e',
    eventTextColor: '#0d0d0f'
  });

  calendarioInstancia.render();

  setTimeout(() => calendarioInstancia.updateSize(), 100);
}

function cargarEventos() {
  if (calendarioInstancia) calendarioInstancia.refetchEvents();
}

/* ─────────────────────────────────────────────────
   MODALES (CREAR / EDITAR)
───────────────────────────────────────────────── */
function abrirModalEvento(fecha, hora = '') {
  document.getElementById('form-evento').reset();
  document.getElementById('evento-id').value = '';
  document.getElementById('evento-fecha').value = fecha || '';
  document.getElementById('evento-hora').value = hora || ''; 
  document.getElementById('modal-evento-titulo').textContent = 'Nuevo evento';
  document.getElementById('btn-guardar-evento').textContent = 'Guardar';
  document.getElementById('btn-eliminar-evento').style.display = 'none';
  ocultarAlertaEvento();
  document.getElementById('modal-evento-overlay').classList.add('active');
}

function abrirModalEditarEvento(evento) {
  document.getElementById('evento-id').value = evento.id;
  document.getElementById('evento-titulo').value = evento.titulo || evento.title || '';
  document.getElementById('evento-fecha').value = (evento.fecha_inicio || evento.start || '').substring(0, 10);
  document.getElementById('evento-hora').value = evento.hora || '';
  document.getElementById('evento-descripcion').value = evento.descripcion || evento.description || '';
  document.getElementById('modal-evento-titulo').textContent = 'Editar evento';
  document.getElementById('btn-guardar-evento').textContent = 'Actualizar';
  document.getElementById('btn-eliminar-evento').style.display = 'inline-block';
  ocultarAlertaEvento();
  document.getElementById('modal-evento-overlay').classList.add('active');
}

function cerrarModalEvento() {
  document.getElementById('modal-evento-overlay').classList.remove('active');
}

function cerrarModalConfirmarEvento() {
    idEventoAEliminar = null;
    document.getElementById('modal-confirmar-evento-overlay').classList.remove('active');
}

/* ─────────────────────────────────────────────────
   GUARDAR / ELIMINAR (CON DISPARADOR DE NOTIF)
───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('form-evento');
  if(!form) return;

  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    ocultarAlertaEvento();

    var id = document.getElementById('evento-id').value;
    var datos = {
      titulo: document.getElementById('evento-titulo').value.trim(),
      fecha_inicio: document.getElementById('evento-fecha').value,
      hora: document.getElementById('evento-hora').value,
      descripcion: document.getElementById('evento-descripcion').value.trim()
    };

    if (!datos.titulo || !datos.fecha_inicio) {
      mostrarAlertaEvento('Título y Fecha son obligatorios.');
      return;
    }

    try {
      if (id) { await http.put('/events/' + id, datos); } 
      else { await http.post('/events', datos); }
      
      cerrarModalEvento();
      cargarEventos();

      // ACTUALIZACIÓN: Refrescar campana inmediatamente
      if (typeof cargarNotificaciones === 'function') {
          cargarNotificaciones();
      }
    } catch (error) {
      mostrarAlertaEvento(error.message);
    }
  });

  document.getElementById('btn-eliminar-evento').addEventListener('click', function() {
    idEventoAEliminar = document.getElementById('evento-id').value;
    if (idEventoAEliminar) {
        document.getElementById('modal-confirmar-evento-overlay').classList.add('active');
    }
  });

  const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar-evento');
  if (btnConfirmarEliminar) {
    btnConfirmarEliminar.addEventListener('click', async function() {
        if (!idEventoAEliminar) return;
        try {
            await http.delete('/events/' + idEventoAEliminar);
            cerrarModalConfirmarEvento();
            cerrarModalEvento();
            cargarEventos();
            // ACTUALIZACIÓN: Refrescar campana tras borrar
            if (typeof cargarNotificaciones === 'function') {
                cargarNotificaciones();
            }
        } catch (error) {
            mostrarAlertaEvento(error.message);
            cerrarModalConfirmarEvento();
        }
    });
  }
});

function mostrarAlertaEvento(msg) {
  var el = document.getElementById('modal-evento-alert');
  if(el) {
    el.textContent = msg;
    el.className = 'alert error visible';
  }
}

function ocultarAlertaEvento() {
  var el = document.getElementById('modal-evento-alert');
  if(el) el.className = 'alert';
}

function filtrarPorFecha(fecha) {
  if (calendarioInstancia && fecha) calendarioInstancia.gotoDate(fecha);
}