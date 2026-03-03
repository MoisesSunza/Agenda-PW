/**
 * contacts.js - Versión Final Sincronizada
 * CRUD de contactos para Laravel 12.
 * Requiere: auth.js (que ya contiene el objeto http y API_BASE).
 */

/* ─────────────────────────────────────────────────
   VARIABLE GLOBAL
───────────────────────────────────────────────── */
var listaContactos = [];

/* ─────────────────────────────────────────────────
   CARGAR CONTACTOS (GET /api/contacts)
───────────────────────────────────────────────── */
async function cargarContactos() {
  var tbody = document.getElementById('tabla-contactos');
  var alerta = document.getElementById('alert-contactos');

  // Estado de carga
  tbody.innerHTML = '<tr><td colspan="5"><div class="spinner-wrap"><div class="spinner"></div></div></td></tr>';
  alerta.classList.remove('visible');

  try {
    // El objeto 'http' viene de auth.js y ya incluye el Token Bearer
    var respuesta = await http.get('/contacts');

    // Sincronización: Laravel suele envolver colecciones en una llave 'data'
    listaContactos = respuesta.data || respuesta; 

    renderizarTabla(listaContactos);

  } catch (error) {
    alerta.textContent = error.message || 'No se pudieron cargar los contactos.';
    alerta.classList.add('visible');
    tbody.innerHTML = '<tr><td colspan="5">Error al cargar datos.</td></tr>';
  }
}

/* ─────────────────────────────────────────────────
   RENDERIZAR TABLA
───────────────────────────────────────────────── */
function renderizarTabla(contactos) {
  var tbody = document.getElementById('tabla-contactos');

  if (!contactos || contactos.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><p>No hay contactos todavía.</p></div></td></tr>';
    return;
  }

  // Generamos las filas mapeando el arreglo de contactos
  tbody.innerHTML = contactos.map(function(c) {
    return `
      <tr>
        <td>${escaparHTML(c.nombre || c.name || '')}</td>
        <td>${escaparHTML(c.correo || c.email || '')}</td>
        <td>${escaparHTML(c.telefono || c.phone || '—')}</td>
        <td>${escaparHTML(c.notas || c.notes || '—')}</td>
        <td>
          <div class="actions">
            <button class="btn-secondary" onclick="abrirModalEditar(${c.id})">Editar</button>
            <button class="btn-danger" onclick="eliminarContacto(${c.id})">Eliminar</button>
          </div>
        </td>
      </tr>
    `;
  }).join('');
}

/* ─────────────────────────────────────────────────
   ABRIR MODAL (CREAR / EDITAR)
───────────────────────────────────────────────── */
function abrirModalCrear() {
  document.getElementById('form-contacto').reset();
  document.getElementById('contacto-id').value = '';
  document.getElementById('modal-titulo').textContent = 'Nuevo contacto';
  document.getElementById('btn-guardar').textContent = 'Guardar';
  
  ocultarAlertaModal();
  document.getElementById('modal-overlay').classList.add('active');
}

function abrirModalEditar(id) {
  var contacto = listaContactos.find(function(c) { return c.id === id; });
  if (!contacto) return;

  document.getElementById('contacto-id').value = contacto.id;
  document.getElementById('contacto-nombre').value = contacto.nombre || contacto.name || '';
  document.getElementById('contacto-correo').value = contacto.correo || contacto.email || '';
  document.getElementById('contacto-telefono').value = contacto.telefono || contacto.phone || '';
  document.getElementById('contacto-notas').value = contacto.notas || contacto.notes || '';

  document.getElementById('modal-titulo').textContent = 'Editar contacto';
  document.getElementById('btn-guardar').textContent = 'Actualizar';

  ocultarAlertaModal();
  document.getElementById('modal-overlay').classList.add('active');
}

function cerrarModal() {
  document.getElementById('modal-overlay').classList.remove('active');
}

/* ─────────────────────────────────────────────────
   GUARDAR (POST o PUT)
───────────────────────────────────────────────── */
document.getElementById('form-contacto').addEventListener('submit', async function(e) {
  e.preventDefault();
  ocultarAlertaModal();

  var id = document.getElementById('contacto-id').value;
  var datos = {
    nombre:   document.getElementById('contacto-nombre').value.trim(),
    correo:   document.getElementById('contacto-correo').value.trim(),
    telefono: document.getElementById('contacto-telefono').value.trim(),
    notas:    document.getElementById('contacto-notas').value.trim(),
  };

  if (!datos.nombre || !datos.correo) {
    mostrarAlertaModal('Nombre y Correo son obligatorios.');
    return;
  }

  var btn = document.getElementById('btn-guardar');
  btn.disabled = true;
  btn.textContent = 'Procesando...';

  try {
    if (id) {
      await http.put('/contacts/' + id, datos);
    } else {
      await http.post('/contacts', datos);
    }
    cerrarModal();
    cargarContactos();
  } catch (error) {
    mostrarAlertaModal(error.message);
  } finally {
    btn.disabled = false;
    btn.textContent = id ? 'Actualizar' : 'Guardar';
  }
});

/* ─────────────────────────────────────────────────
   ELIMINAR
───────────────────────────────────────────────── */
async function eliminarContacto(id) {
  if (!confirm('¿Eliminar este contacto?')) return;

  try {
    await http.delete('/contacts/' + id);
    cargarContactos();
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

/* ─────────────────────────────────────────────────
   BÚSQUEDA LOCAL Y HELPERS
───────────────────────────────────────────────── */
function filtrarContactos(textoBusqueda) {
  var txt = textoBusqueda.toLowerCase();
  var filtrados = listaContactos.filter(function(c) {
    return (c.nombre || c.name || '').toLowerCase().includes(txt) || 
           (c.correo || c.email || '').toLowerCase().includes(txt);
  });
  renderizarTabla(filtrados);
}

function mostrarAlertaModal(msg) {
  var el = document.getElementById('modal-alert');
  el.textContent = msg;
  el.className = 'alert error visible';
}

function ocultarAlertaModal() {
  document.getElementById('modal-alert').className = 'alert';
}

function escaparHTML(texto) {
  var div = document.createElement('div');
  div.textContent = texto;
  return div.innerHTML;
}

// Cerrar al clickear fuera
document.getElementById('modal-overlay').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});