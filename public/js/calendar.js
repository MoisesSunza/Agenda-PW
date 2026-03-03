/**
 * calendar.js - Versión Final Sincronizada
 * Módulo de calendario usando FullCalendar 6.
 * Requiere: auth.js (para el objeto http).
 */

var calendarioInstancia = null;
var listaEventos = [];

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

    // CARGA DE EVENTOS DESDE LARAVEL
    events: function(fetchInfo, successCallback, failureCallback) {
      http.get('/events')
        .then(function(respuesta) {
          // Laravel suele envolver en 'data'
          var eventos = respuesta.data || respuesta;
          listaEventos = eventos;

          // Mapeo de campos: Backend -> FullCalendar
          var eventosFormateados = eventos.map(function(e) {
            return {
              id:    e.id,
              title: e.titulo || e.title, // Sincronizado con tu migración
              start: e.fecha_inicio || e.start,
              description: e.descripcion || e.description || '',
              extendedProps: {
                hora: e.hora || ''
              }
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
      abrirModalEvento(info.dateStr);
    },

    eventClick: function(info) {
      var evento = listaEventos.find(function(e) {
        return String(e.id) === String(info.event.id);
      });
      if (evento) abrirModalEditarEvento(evento);
    },

    eventColor: '#c8a96e',
    eventTextColor: '#0d0d0f'
  });

  calendarioInstancia.render();

  setTimeout(function() {
    calendarioInstancia.updateSize();
  }, 100);
}

function cargarEventos() {
  if (calendarioInstancia) calendarioInstancia.refetchEvents();
}

/* ─────────────────────────────────────────────────
   MODALES (CREAR / EDITAR)
───────────────────────────────────────────────── */
function abrirModalEvento(fecha) {
  document.getElementById('form-evento').reset();
  document.getElementById('evento-id').value = '';
  document.getElementById('evento-fecha').value = fecha || '';
  
  document.getElementById('modal-evento-titulo').textContent = 'Nuevo evento';
  document.getElementById('btn-guardar-evento').textContent = 'Guardar';
  document.getElementById('btn-eliminar-evento').style.display = 'none';

  ocultarAlertaEvento();
  document.getElementById('modal-evento-overlay').classList.add('active');
}

function abrirModalEditarEvento(evento) {
  document.getElementById('evento-id').value = evento.id;
  document.getElementById('evento-titulo').value = evento.titulo || evento.title || '';
  // Extraemos solo YYYY-MM-DD para el input date
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

/* ─────────────────────────────────────────────────
   GUARDAR / ELIMINAR
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
      if (id) {
        await http.put('/events/' + id, datos);
      } else {
        await http.post('/events', datos);
      }
      cerrarModalEvento();
      cargarEventos();
    } catch (error) {
      mostrarAlertaEvento(error.message);
    }
  });

  document.getElementById('btn-eliminar-evento').addEventListener('click', async function() {
    var id = document.getElementById('evento-id').value;
    if (id && confirm('¿Eliminar este evento?')) {
      try {
        await http.delete('/events/' + id);
        cerrarModalEvento();
        cargarEventos();
      } catch (error) {
        mostrarAlertaEvento(error.message);
      }
    }
  });
});

/* ─────────────────────────────────────────────────
   HELPERS
───────────────────────────────────────────────── */
function mostrarAlertaEvento(msg) {
  var el = document.getElementById('modal-evento-alert');
  el.textContent = msg;
  el.className = 'alert error visible';
}

function ocultarAlertaEvento() {
  document.getElementById('modal-evento-alert').className = 'alert';
}

function filtrarPorFecha(fecha) {
  if (calendarioInstancia && fecha) calendarioInstancia.gotoDate(fecha);
}