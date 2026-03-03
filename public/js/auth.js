/**
 * auth.js
 * Gestión de autenticación, tokens y cliente HTTP reutilizable.
 * Sincronizado para Laravel 12 y PHP 8.4 con Modal de Salida.
 */

const API_BASE = 'http://localhost:8000/api';

/* ═══════════════════════════════════════════════
    SISTEMA DE PERSISTENCIA Y TOKEN
   ═══════════════════════════════════════════════ */
const Auth = {
    setToken(token) {
        localStorage.setItem('jwt_token', token);
    },

    getToken() {
        return localStorage.getItem('jwt_token');
    },

    removeToken() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('agenda_user');
    },

    isAuthenticated() {
        const token = this.getToken();
        return token !== null && token !== undefined && token !== '';
    },

    /**
     * Muestra el modal de confirmación en lugar de cerrar sesión de golpe.
     */
    logout() {
        const modal = document.getElementById('modal-logout-overlay');
        if (modal) {
            modal.classList.add('active');
        } else {
            this.ejecutarCierreReal();
        }
    },

    /**
     * Proceso final de limpieza y redirección.
     */
    async ejecutarCierreReal() {
        const btnConfirmar = document.getElementById('btn-logout-confirmar');
        const btnCancelar = document.getElementById('btn-logout-cancelar');
        const mensaje = document.getElementById('logout-mensaje');

        if (btnConfirmar) {
            btnConfirmar.disabled = true;
            btnConfirmar.textContent = 'Cerrando sesión...';
        }
        if (btnCancelar) btnCancelar.style.display = 'none';
        if (mensaje) mensaje.textContent = 'Saliendo de la agenda, espera un momento...';

        try {
            await http.post('/logout', {});
        } catch (error) {
            console.warn('Sesión ya caducada en servidor o error de red.');
        } finally {
            this.removeToken();
            setTimeout(() => {
                // ACTUALIZACIÓN: Ahora redirige a /login en lugar de /
                window.location.href = '/login'; 
            }, 800);
        }
    }
};

function cerrarModalLogout() {
    const modal = document.getElementById('modal-logout-overlay');
    if (modal) modal.classList.remove('active');
}

function ejecutarLogout() {
    Auth.ejecutarCierreReal();
}

/* ═══════════════════════════════════════════════
    CLIENTE HTTP (Utilizado por todos los módulos)
   ═══════════════════════════════════════════════ */
const http = {
    async request(endpoint, options = {}) {
        const token = Auth.getToken();
        
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
            ...options.headers,
        };

        const response = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const msg = data.message || data.error || `Error ${response.status}`;
            throw new Error(msg);
        }

        return data;
    },

    get(endpoint) { return this.request(endpoint, { method: 'GET' }); },
    post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body),
        });
    },
    put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body),
        });
    },
    delete(endpoint) { return this.request(endpoint, { method: 'DELETE' }); }
};

/* ═══════════════════════════════════════════════
    FUNCIONES DE ACCESO (LOGIN Y REGISTRO)
   ═══════════════════════════════════════════════ */
async function login(email, password) {
    const data = await http.post('/login', { email, password });
    if (data.token) {
        Auth.setToken(data.token);
        if (data.user) {
            localStorage.setItem('agenda_user', JSON.stringify(data.user));
        }
    }
    return data;
}

async function register(name, email, password) {
    const data = await http.post('/register', { 
        name, 
        email, 
        password, 
        password_confirmation: password 
    });

    if (data.token) {
        Auth.setToken(data.token);
        if (data.user) {
            localStorage.setItem('agenda_user', JSON.stringify(data.user));
        }
    }
    return data;
}

/* ═══════════════════════════════════════════════
    GUARD (Protección de rutas)
   ═══════════════════════════════════════════════ */
function requireAuth() {
    if (!Auth.isAuthenticated()) {
        // ACTUALIZACIÓN: Si no hay token, manda a /login
        window.location.href = '/login'; 
    }
}