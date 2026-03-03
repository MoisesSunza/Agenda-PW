/**
 * auth.js
 * Gestión de autenticación, tokens y cliente HTTP reutilizable.
 * Sincronizado para Laravel 12 y PHP 8.4.
 */

// 1. Configuración de la URL base de tu API
const API_BASE = 'http://localhost:8000/api';

/* ═══════════════════════════════════════════════
    SISTEMA DE PERSISTENCIA Y TOKEN
   ═══════════════════════════════════════════════ */
const Auth = {
    /**
     * Guarda el token en localStorage.
     */
    setToken(token) {
        localStorage.setItem('jwt_token', token);
    },

    /**
     * Recupera el token almacenado.
     */
    getToken() {
        return localStorage.getItem('jwt_token');
    },

    /**
     * Elimina el token (Logout).
     */
    removeToken() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('agenda_user');
    },

    /**
     * Verifica si hay una sesión activa.
     */
    isAuthenticated() {
        const token = this.getToken();
        return token !== null && token !== undefined && token !== '';
    },

    /**
     * Cierra la sesión en el servidor y limpia el navegador.
     */
    /* Reemplaza la función logout en tu auth.js con esta versión blindada */
    async logout() {
        try {
            // Intentamos avisar al servidor (opcional)
            await http.post('/logout', {});
        } catch (error) {
            // Si el servidor dice "Unauthenticated" (401), no importa
            // ya que igual queremos salir de la app localmente.
            console.warn('Sesión ya caducada en servidor o error de red.');
        } finally {
            // LO IMPORTANTE: Limpiar el navegador pase lo que pase
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('agenda_user');
            
            // Redirigimos a la raíz (donde está tu vista de auth)
            window.location.href = '/'; 
        }
    }
};

/* ═══════════════════════════════════════════════
    CLIENTE HTTP (Utilizado por todos los módulos)
   ═══════════════════════════════════════════════ */
const http = {
    /**
     * Método base para peticiones Fetch.
     * Adjunta el token Bearer automáticamente si existe.
     */
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

        // Intentamos parsear a JSON, si falla devolvemos objeto vacío
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            // Captura el mensaje de error de Laravel si existe
            const msg = data.message || data.error || `Error ${response.status}`;
            throw new Error(msg);
        }

        return data;
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

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

    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

/* ═══════════════════════════════════════════════
    FUNCIONES DE ACCESO (LOGIN Y REGISTRO)
   ═══════════════════════════════════════════════ */

/**
 * Inicia sesión contra /api/login
 */
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

/**
 * Registra usuario contra /api/register
 */
async function register(name, email, password) {
    const data = await http.post('/register', { 
        name, 
        email, 
        password, 
        password_confirmation: password // Requerido por validación de Laravel
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

/**
 * Llama a esta función al inicio de dashboard.html
 */
function requireAuth() {
    if (!Auth.isAuthenticated()) {
        window.location.href = '/'; // Si no hay token, manda al login
    }
}