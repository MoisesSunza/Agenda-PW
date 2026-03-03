/**
 * contacts.js - Versión Final con Modal de Confirmación
 */

var listaContactos = [];
let idContactoAEliminar = null; // Variable puente para el modal

/* ─────────────────────────────────────────────────
   1. CARGAR Y RENDERIZAR CONTACTOS
───────────────────────────────────────────────── */
async function cargarContactos() {
  try {
    const respuesta = await http.get('/contacts');
    let datos = respuesta.data ? respuesta.data : respuesta;
    if (datos && datos.data) datos = datos.data;

    if (!Array.isArray(datos)) {
        listaContactos = [];
    } else {
        listaContactos = datos;
    }
    renderizarTablaContactos();
  } catch (error) {
    console.error('❌ Error al cargar contactos:', error);
  }
}

function renderizarTablaContactos() {
  const tbody = document.getElementById('tabla-contactos'); 
  if (!tbody) return;

  tbody.innerHTML = '';

  if (listaContactos.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">No tienes contactos guardados aún.</td></tr>';
    return;
  }

  listaContactos.forEach(function(contacto) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${contacto.nombre || ''}</td>
      <td>${contacto.correo || ''}</td>
      <td>${contacto.telefono || '-'}</td>
      <td>${contacto.notas || '-'}</td>
      <td class="actions">
        <button class="btn-secondary" onclick="prepararEdicionContacto(${contacto.id})">Editar</button>
        <button class="btn-danger" onclick="eliminarContacto(${contacto.id})">Borrar</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

/* ─────────────────────────────────────────────────
   2. MODALES (CREAR / EDITAR)
───────────────────────────────────────────────── */
function abrirModalCrear() {
  document.getElementById('form-contacto').reset();
  document.getElementById('contacto-id').value = '';
  document.getElementById('modal-titulo').textContent = 'Nuevo contacto';
  document.getElementById('btn-guardar').textContent = 'Guardar';
  ocultarAlertaContacto();
  document.getElementById('modal-overlay').classList.add('active');
}

function prepararEdicionContacto(id) {
  const contacto = listaContactos.find(c => String(c.id) === String(id));
  if (contacto) {
    abrirModalEditarContacto(contacto);
  }
}

function abrirModalEditarContacto(contacto) {
  document.getElementById('contacto-id').value = contacto.id;
  document.getElementById('contacto-nombre').value = contacto.nombre || '';
  document.getElementById('contacto-correo').value = contacto.correo || '';
  document.getElementById('contacto-telefono').value = contacto.telefono || '';
  document.getElementById('contacto-notas').value = contacto.notas || '';
  document.getElementById('modal-titulo').textContent = 'Editar contacto';
  document.getElementById('btn-guardar').textContent = 'Actualizar';
  ocultarAlertaContacto();
  document.getElementById('modal-overlay').classList.add('active');
}

function cerrarModal() {
  document.getElementById('modal-overlay').classList.remove('active');
}

/* ─────────────────────────────────────────────────
   3. ELIMINAR (CON MODAL PERSONALIZADO)
───────────────────────────────────────────────── */
function eliminarContacto(id) {
    idContactoAEliminar = id;
    document.getElementById('modal-confirmacion-overlay').classList.add('active');
}

function cerrarModalConfirmacion() {
    idContactoAEliminar = null;
    document.getElementById('modal-confirmacion-overlay').classList.remove('active');
}

/* ─────────────────────────────────────────────────
   4. INICIALIZACIÓN Y EVENTOS
───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('form-contacto');
  if(form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      ocultarAlertaContacto();
      var id = document.getElementById('contacto-id').value;
      var datos = {
        nombre: document.getElementById('contacto-nombre').value.trim(),
        correo: document.getElementById('contacto-correo').value.trim(),
        telefono: document.getElementById('contacto-telefono').value.trim(),
        notas: document.getElementById('contacto-notas').value.trim()
      };

      if (!datos.nombre || !datos.correo) {
        mostrarAlertaContacto('El nombre y el correo son obligatorios.');
        return;
      }

      try {
        if (id) { await http.put('/contacts/' + id, datos); }
        else { await http.post('/contacts', datos); }
        cerrarModal();
        await cargarContactos();
        if (typeof cargarNotificaciones === 'function') cargarNotificaciones();
      } catch (error) {
        mostrarAlertaContacto(error.message);
      }
    });
  }

  // Lógica del botón de confirmación en el nuevo modal
  const btnConfirmar = document.getElementById('btn-confirmar-eliminar');
  if (btnConfirmar) {
    btnConfirmar.addEventListener('click', async function() {
      if (!idContactoAEliminar) return;
      try {
        await http.delete('/contacts/' + idContactoAEliminar);
        cerrarModalConfirmacion();
        await cargarContactos();
        if (typeof cargarNotificaciones === 'function') cargarNotificaciones();
      } catch (error) {
        console.error('Error al eliminar:', error);
        cerrarModalConfirmacion();
      }
    });
  }
});

/* ─────────────────────────────────────────────────
   5. HELPERS
───────────────────────────────────────────────── */
function mostrarAlertaContacto(msg) {
  var el = document.getElementById('modal-alert');
  if(el) {
    el.textContent = msg;
    el.className = 'alert error visible';
  }
}

function ocultarAlertaContacto() {
  var el = document.getElementById('modal-alert');
  if(el) { el.className = 'alert'; }
}