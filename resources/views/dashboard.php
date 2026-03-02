<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/css/dashboard.css">
  <title>MiAgenda — Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet" />
  <!-- FullCalendar — librería para el calendario interactivo -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css" rel="stylesheet" />
  
</head>
<body>

  <!-- ══════════════════════════════════════════
       SIDEBAR — menú lateral
  ══════════════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <span>Programación Web</span>
      <h1>Mi<em>Agenda</em></h1>
    </div>

    <ul class="nav">
      <li class="nav-item">
        <button class="nav-btn active" data-section="inicio">
          <span class="nav-icon">🏠</span>
          <span>Inicio</span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-btn" data-section="contactos">
          <span class="nav-icon">📇</span>
          <span>Contactos</span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-btn" data-section="calendario">
          <span class="nav-icon">🗓</span>
          <span>Calendario</span>
        </button>
      </li>
      <li class="nav-item">
        <!-- Campana con badge — no cambia sección, abre dropdown -->
        <div class="notif-wrap">
          <span class="notif-badge" id="notif-badge"></span>
          <button class="nav-btn" id="btn-campana" onclick="toggleDropdown()">
            <span class="nav-icon">🔔</span>
            <span>Notificaciones</span>
          </button>
        </div>

        <!-- Panel desplegable -->
        <div class="notif-panel" id="notif-panel">
          <div class="notif-panel-header">
            <h4>Notificaciones</h4>
            <span id="notif-contador"></span>
          </div>
          <div class="notif-lista" id="notif-lista">
            <p class="notif-empty">Cargando…</p>
          </div>
        </div>
      </li>
    </ul>

    <div class="sidebar-footer">
      <!-- Llama a Auth.logout() que está en auth.js -->
      <button class="nav-btn" onclick="Auth.logout()">
        <span class="nav-icon">🚪</span>
        <span>Cerrar sesión</span>
      </button>
    </div>
  </aside>

  <!-- ══════════════════════════════════════════
       ÁREA PRINCIPAL
  ══════════════════════════════════════════ -->
  <main class="main">

    <!-- SECCIÓN: INICIO -->
    <section class="section active" id="section-inicio">
      <div class="section-header">
        <h2>Inicio</h2>
      </div>
      <div class="welcome-card">
        <h3>¡Bienvenido! 👋</h3>
        <p>Usa el menú de la izquierda para navegar entre las secciones de tu agenda electrónica.</p>
      </div>
    </section>

    <!-- SECCIÓN: CONTACTOS -->
    <section class="section" id="section-contactos">
      <div class="section-header">
        <h2>Contactos</h2>
        <!-- Al hacer clic abre el modal para crear un nuevo contacto -->
        <button class="btn-primary" onclick="abrirModalCrear()">+ Nuevo contacto</button>
      </div>

      <!-- Barra de búsqueda -->
      <div class="search-bar">
        <input
          type="text"
          id="input-busqueda"
          placeholder="Buscar por nombre, correo o teléfono…"
          oninput="filtrarContactos(this.value)"
        />
      </div>

      <!-- Mensaje de error si la API falla -->
      <div class="alert error" id="alert-contactos"></div>

      <!-- Tabla donde se listan los contactos -->
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Teléfono</th>
              <th>Notas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tabla-contactos">
            <!-- Las filas se generan dinámicamente con JavaScript -->
            <tr>
              <td colspan="5">
                <div class="spinner-wrap"><div class="spinner"></div></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- SECCIÓN: CALENDARIO -->
    <section class="section" id="section-calendario">
      <div class="section-header">
        <h2>Calendario</h2>
        <!-- Botón para crear evento manualmente (sin hacer clic en un día) -->
        <button class="btn-primary" onclick="abrirModalEvento('')">+ Nuevo evento</button>
      </div>

      <!-- Filtro de fecha: navega el calendario a una fecha específica -->
      <div class="calendar-toolbar">
        <label for="filtro-fecha">Ir a fecha:</label>
        <input type="date" id="filtro-fecha" onchange="filtrarPorFecha(this.value)" />
      </div>

      <!-- FullCalendar se renderiza aquí -->
      <div id="fullcalendar"></div>
    </section>

  </main>

  <!-- ══════════════════════════════════════════
       POP-UP de bienvenida (notificaciones pendientes)
  ══════════════════════════════════════════ -->
  <div class="popup-login" id="popup-notificaciones">
    <span class="popup-icon">🔔</span>
    <div class="popup-cuerpo">
      <strong>Tienes notificaciones</strong>
      <p id="popup-texto"></p>
    </div>
    <button class="popup-cerrar" onclick="cerrarPopup()">✕</button>
  </div>

  <!-- ══════════════════════════════════════════
       MODAL — Crear / Editar contacto
       Está fuera del <main> para que se centre
       correctamente sobre toda la pantalla.
  ══════════════════════════════════════════ -->
  <div class="modal-overlay" id="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <!-- El título cambia según si estamos creando o editando -->
        <h3 id="modal-titulo">Nuevo contacto</h3>
        <button class="modal-close" onclick="cerrarModal()">✕</button>
      </div>

      <div class="alert" id="modal-alert"></div>

      <form id="form-contacto" novalidate>
        <!-- Campo oculto para guardar el ID cuando editamos -->
        <input type="hidden" id="contacto-id" />

        <div class="field">
          <label for="contacto-nombre">Nombre *</label>
          <input type="text" id="contacto-nombre" placeholder="Juan Pérez" />
        </div>

        <div class="field">
          <label for="contacto-correo">Correo electrónico *</label>
          <input type="email" id="contacto-correo" placeholder="juan@correo.com" />
        </div>

        <div class="field">
          <label for="contacto-telefono">Teléfono</label>
          <input type="tel" id="contacto-telefono" placeholder="999 123 4567" />
        </div>

        <div class="field">
          <label for="contacto-notas">Notas</label>
          <textarea id="contacto-notas" placeholder="Información adicional…"></textarea>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
          <button type="submit" class="btn-primary" id="btn-guardar">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════
       MODAL — Crear / Editar evento
  ══════════════════════════════════════════ -->
  <div class="modal-overlay" id="modal-evento-overlay">
    <div class="modal">
      <div class="modal-header">
        <h3 id="modal-evento-titulo">Nuevo evento</h3>
        <button class="modal-close" onclick="cerrarModalEvento()">✕</button>
      </div>

      <div class="alert" id="modal-evento-alert"></div>

      <form id="form-evento" novalidate>
        <input type="hidden" id="evento-id" />

        <div class="field">
          <label for="evento-titulo">Título *</label>
          <input type="text" id="evento-titulo" placeholder="Ej. Reunión de equipo" />
        </div>

        <div class="field">
          <label for="evento-fecha">Fecha *</label>
          <input type="date" id="evento-fecha" />
        </div>

        <div class="field">
          <label for="evento-hora">Hora</label>
          <input type="time" id="evento-hora" />
        </div>

        <div class="field">
          <label for="evento-descripcion">Descripción</label>
          <textarea id="evento-descripcion" placeholder="Detalles del evento…"></textarea>
        </div>

        <div class="modal-footer">
          <!-- Botón eliminar (solo visible al editar) -->
          <button type="button" class="btn-danger" id="btn-eliminar-evento" style="display:none; margin-right:auto">
            Eliminar
          </button>
          <button type="button" class="btn-secondary" onclick="cerrarModalEvento()">Cancelar</button>
          <button type="submit" class="btn-primary" id="btn-guardar-evento">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════
       SCRIPTS — el orden importa:
       1. config.js  → define API_BASE
       2. auth.js    → define Auth, http, requireAuth
       3. contacts.js → lógica de contactos
       4. calendar.js → lógica de calendario
       5. FullCalendar → librería externa
       6. dashboard.js → navegación (va al final para
          que todo lo anterior ya esté disponible)
  ══════════════════════════════════════════ -->
  <script src="js/config.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/contacts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>
  <script src="js/calendar.js"></script>
  <script src="js/notifications.js"></script>
  <script src="js/dashboard.js"></script>

</body>
</html>
