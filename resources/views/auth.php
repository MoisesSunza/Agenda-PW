<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/css/auth.css">
  <title>Agenda — Acceso</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet" />
  
</head>
<body>

  <!-- ── Left decorative panel ── -->
  <aside class="panel-left">
    <div class="grid-lines"></div>
    <div class="brand">
      <p class="brand-tag">Programación Web · 2026</p>
      <h1 class="brand-name">Mi<em>Agenda</em></h1>
    </div>
    <div class="features">
      <div class="feature">
        <div class="feature-icon">📇</div>
        <div class="feature-text">
          <strong>Gestión de Contactos</strong>
          <span>Crea, edita y organiza tu libreta de contactos</span>
        </div>
      </div>
      <div class="feature">
        <div class="feature-icon">🗓</div>
        <div class="feature-text">
          <strong>Calendario Interactivo</strong>
          <span>Visualiza y administra eventos y recordatorios</span>
        </div>
      </div>
      <div class="feature">
        <div class="feature-icon">🔔</div>
        <div class="feature-text">
          <strong>Alertas Automáticas</strong>
          <span>Recordatorios por correo y notificaciones en app</span>
        </div>
      </div>
    </div>
    <p class="panel-footer">Universidad Autónoma de Campeche · ITS</p>
  </aside>

  <!-- ── Right form panel ── -->
  <main class="panel-right">
    <div class="form-container">

      <div class="tabs" role="tablist">
        <button class="tab-btn active" role="tab" data-tab="login">Iniciar sesión</button>
        <button class="tab-btn"        role="tab" data-tab="register">Registrarse</button>
      </div>

      <!-- LOGIN -->
      <div class="form-panel active" id="panel-login">
        <h2 class="form-title">Bienvenido de vuelta</h2>
        <p class="form-subtitle">Ingresa tus credenciales para continuar.</p>
        <div class="alert" id="login-alert"></div>
        <form id="form-login" novalidate>
          <div class="field">
            <label for="login-email">Correo electrónico</label>
            <div class="input-wrap">
              <input type="email" id="login-email" placeholder="usuario@correo.com" autocomplete="email" />
            </div>
            <span class="field-error" id="err-login-email">Ingresa un correo válido.</span>
          </div>
          <div class="field">
            <label for="login-password">Contraseña</label>
            <div class="input-wrap">
              <input type="password" id="login-password" placeholder="••••••••" autocomplete="current-password" />
              <button type="button" class="toggle-pw" data-target="login-password" aria-label="Mostrar contraseña">👁</button>
            </div>
            <span class="field-error" id="err-login-password">La contraseña es requerida.</span>
          </div>
          <button type="submit" class="btn-submit" id="btn-login">
            <span class="btn-text">Entrar</span>
            <span class="spinner"></span>
          </button>
        </form>
        <div class="token-box" id="token-display">
          <strong>JWT almacenado</strong>
          <span id="token-preview"></span>
        </div>
      </div>

      <!-- REGISTER -->
      <div class="form-panel" id="panel-register">
        <h2 class="form-title">Crear cuenta</h2>
        <p class="form-subtitle">Completa el formulario para comenzar.</p>
        <div class="alert" id="register-alert"></div>
        <form id="form-register" novalidate>
          <div class="field">
            <label for="reg-name">Nombre completo</label>
            <div class="input-wrap">
              <input type="text" id="reg-name" placeholder="Juan Pérez" autocomplete="name" />
            </div>
            <span class="field-error" id="err-reg-name">El nombre es requerido.</span>
          </div>
          <div class="field">
            <label for="reg-email">Correo electrónico</label>
            <div class="input-wrap">
              <input type="email" id="reg-email" placeholder="usuario@correo.com" autocomplete="email" />
            </div>
            <span class="field-error" id="err-reg-email">Ingresa un correo válido.</span>
          </div>
          <div class="field">
            <label for="reg-password">Contraseña</label>
            <div class="input-wrap">
              <input type="password" id="reg-password" placeholder="Mín. 8 caracteres" autocomplete="new-password" />
              <button type="button" class="toggle-pw" data-target="reg-password" aria-label="Mostrar contraseña">👁</button>
            </div>
            <span class="field-error" id="err-reg-password">Mínimo 8 caracteres.</span>
          </div>
          <div class="field">
            <label for="reg-password2">Confirmar contraseña</label>
            <div class="input-wrap">
              <input type="password" id="reg-password2" placeholder="Repite tu contraseña" autocomplete="new-password" />
              <button type="button" class="toggle-pw" data-target="reg-password2" aria-label="Mostrar contraseña">👁</button>
            </div>
            <span class="field-error" id="err-reg-password2">Las contraseñas no coinciden.</span>
          </div>
          <button type="submit" class="btn-submit" id="btn-register">
            <span class="btn-text">Crear cuenta</span>
            <span class="spinner"></span>
          </button>
        </form>
      </div>

    </div>
  </main>

  <!-- ── Scripts: config primero, auth segundo, lógica de página al final ── -->
  <script src="/js/auth.js"></script>
  <script>
    /* ═══════════════════════════════════════════════
       Redirige si ya hay sesión activa
    ═══════════════════════════════════════════════ */
    if (Auth.isAuthenticated()) {
      window.location.href = 'dashboard';
    }

    /* ═══════════════════════════════════════════════
       UI HELPERS  (solo para auth.html)
    ═══════════════════════════════════════════════ */
    function showAlert(id, msg, type = 'error') {
      const el = document.getElementById(id);
      el.textContent = msg;
      el.className = `alert ${type} visible`;
    }

    function hideAlert(id) {
      document.getElementById(id).className = 'alert';
    }

    function setLoading(btnId, loading) {
      const btn = document.getElementById(btnId);
      btn.disabled = loading;
      btn.classList.toggle('loading', loading);
    }

    function setInputError(inputEl, errId, show) {
      inputEl.classList.toggle('error', show);
      document.getElementById(errId).classList.toggle('visible', show);
    }

    /* Mostrar / ocultar contraseña */
    document.querySelectorAll('.toggle-pw').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.textContent = input.type === 'password' ? '👁' : '🙈';
      });
    });

    /* Cambio de tabs */
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(`panel-${btn.dataset.tab}`).classList.add('active');
      });
    });

    /* ═══════════════════════════════════════════════
       FORM: LOGIN
    ═══════════════════════════════════════════════ */
    document.getElementById('form-login').addEventListener('submit', async e => {
      e.preventDefault();
      hideAlert('login-alert');

      const emailInput = document.getElementById('login-email');
      const passInput  = document.getElementById('login-password');
      let valid = true;

      const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
      setInputError(emailInput, 'err-login-email', !emailOk);
      if (!emailOk) valid = false;

      const passOk = passInput.value.length > 0;
      setInputError(passInput, 'err-login-password', !passOk);
      if (!passOk) valid = false;

      if (!valid) return;

      setLoading('btn-login', true);
      try {
        const res = await login(emailInput.value.trim(), passInput.value);

        document.getElementById('token-preview').textContent = res.token.substring(0, 40) + '…';
        document.getElementById('token-display').classList.add('visible');
        showAlert('login-alert', '¡Bienvenido! Redirigiendo…', 'success');

        setTimeout(() => window.location.href = 'dashboard', 1200);

      } catch (err) {
        showAlert('login-alert', err.message || 'Credenciales incorrectas.');
      } finally {
        setLoading('btn-login', false);
      }
    });

    /* ═══════════════════════════════════════════════
       FORM: REGISTER
    ═══════════════════════════════════════════════ */
    document.getElementById('form-register').addEventListener('submit', async e => {
      e.preventDefault();
      hideAlert('register-alert');

      const nameInput  = document.getElementById('reg-name');
      const emailInput = document.getElementById('reg-email');
      const passInput  = document.getElementById('reg-password');
      const pass2Input = document.getElementById('reg-password2');
      let valid = true;

      const nameOk = nameInput.value.trim().length >= 2;
      setInputError(nameInput, 'err-reg-name', !nameOk);
      if (!nameOk) valid = false;

      const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
      setInputError(emailInput, 'err-reg-email', !emailOk);
      if (!emailOk) valid = false;

      const passOk = passInput.value.length >= 8;
      setInputError(passInput, 'err-reg-password', !passOk);
      if (!passOk) valid = false;

      const pass2Ok = pass2Input.value === passInput.value;
      setInputError(pass2Input, 'err-reg-password2', !pass2Ok);
      if (!pass2Ok) valid = false;

      if (!valid) return;

      setLoading('btn-register', true);
      try {
        await register(nameInput.value.trim(), emailInput.value.trim(), passInput.value);
        showAlert('register-alert', 'Cuenta creada. ¡Bienvenido!', 'success');
        setTimeout(() => window.location.href = 'dashboard', 1200);
      } catch (err) {
        showAlert('register-alert', err.message || 'No se pudo crear la cuenta.');
      } finally {
        setLoading('btn-register', false);
      }
    });
  </script>
</body>
</html>
