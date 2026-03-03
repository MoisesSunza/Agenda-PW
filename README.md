# MiAgenda — Ecosistema de Gestión de Productividad y Notificaciones

**Proyecto Académico de Programación Web | Universidad Autónoma de Campeche (UAC) | Facultad de Ingeniería (FDI)**

[![Laravel](https://img.shields.io/badge/Framework-Laravel%2012-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)](https://www.php.net/)
[![JS](https://img.shields.io/badge/Frontend-Vanilla%20JS-F7DF1E?logo=javascript)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![MySQL](https://img.shields.io/badge/DB-MySQL-4479A1?logo=mysql)](https://www.mysql.com/)

---

## Autores
* **Moisés Abraham Sunza Vázquez**
* **Fernando Adriano Sabido Quijano**

---

## Maestro
* **Juan Antonio Chuc Méndez

---

## Resumen del Proyecto
**MiAgenda** es una solución integral diseñada para la administración centralizada de contactos y la planificación estratégica de eventos. El sistema implementa una arquitectura robusta basada en **Laravel 12**, enfocándose en la automatización de procesos mediante un motor de notificaciones dual que opera tanto en el cliente (navegador) como en el servidor (tareas programadas).

El proyecto destaca por su interfaz de alto contraste y su enfoque en la **Experiencia de Usuario (UX)**, utilizando patrones de diseño modernos como **Optimistic UI** para garantizar una respuesta instantánea a las interacciones del usuario.

---

## Funcionalidades Principales

### 1. Gestión Avanzada de Contactos
* **CRUD Completo:** Administración total de registros con validación de datos en el servidor (Server-side validation).
* **Confirmación de Acciones:** Despacho de correos electrónicos profesionales al registrar, actualizar o eliminar contactos.

### 2. Planificación de Eventos y Calendario
* **FullCalendar Integration:** Interfaz dinámica para la gestión de agendas temporales.
* **Lógica de Continuidad:** Soporte nativo para eventos que cruzan la medianoche, asegurando una visualización coherente en la rejilla mensual.

### 3. Sistema de Notificaciones de Proximidad (10 min)
* **Vigilante en Frontend:** Script de monitoreo constante que dispara popups visuales 10 minutos antes del inicio de una actividad.
* **Automatización Artisan:** Comando de consola personalizado (`app:send-event-reminders`) que busca eventos próximos y envía recordatorios vía SMTP de forma autónoma.

---

## Stack Tecnológico

| Capa | Tecnología | Función |
| :--- | :--- | :--- |
| **Backend** | Laravel 12 (PHP 8.4) | Motor de lógica, API RESTful y Task Scheduling. |
| **Frontend** | JavaScript ES6+ / CSS3 | Reactividad del DOM y gestión de peticiones asíncronas. |
| **Base de Datos** | MySQL | Almacenamiento persistente y relaciones de integridad. |
| **Infraestructura** | Laravel Herd / PHP Artisan | Entorno de ejecución y automatización de tareas. |
| **Diseño** | Beyond Gardens Theme | Estética personalizada en modo oscuro y dorado metálico. |

---

## Arquitectura Técnica

### Arquitectura de Datos (Eloquent ORM)
El sistema utiliza un esquema de base de datos relacional donde la entidad `User` posee relaciones de tipo **One-to-Many (`HasMany`)** con `Contact`, `Event` y `Notification`. Esto garantiza un aislamiento total de los datos por sesión de usuario.



### Flujo de Notificaciones Automáticas
El sistema de recordatorios de 10 minutos opera mediante el **Task Scheduler** de Laravel. Un comando programado realiza consultas de proximidad temporal cada 60 segundos, disparando eventos de correo electrónico mediante Mailables personalizados.



---

## Identidad Visual y UX
Bajo el concepto de diseño **"Beyond Gardens"**, se ha priorizado la legibilidad y la elegancia técnica:
* **Paleta:** Fondos en `#0d0d0f` con tipografía y acentos en `#c8a96e`.
* **Optimistic UI:** Las notificaciones se marcan como leídas o se eliminan visualmente en el instante en que el usuario hace clic, sincronizándose con el servidor en segundo plano para eliminar la percepción de latencia.



---

## Instalación y Configuración

1.  **Clonación del repositorio:**
    ```bash
    git clone [https://github.com/moises-sunza/mi-agenda.git](https://github.com/moises-sunza/mi-agenda.git)
    cd mi-agenda
    ```
2.  **Instalación de dependencias:**
    ```bash
    composer install
    ```
3.  **Variables de Entorno:**
    * Configurar el archivo `.env` con las credenciales de base de datos y SMTP.
    * Importante: Configurar `TIMEZONE=America/Merida` para la precisión de los recordatorios.
4.  **Migración de esquemas:**
    ```bash
    php artisan migrate
    ```
5.  **Ejecución del Programador:**
    ```bash
    php artisan schedule:work
    ```

---

## Créditos y Autoría
Proyecto desarrollado como parte de la formación académica en la **Facultad de Ingeniería de la Universidad Autónoma de Campeche (UAC)**.

* **Desarrollo Backend & Backend Logic:** Moisés Abraham Sunza Vázquez.
* **UI/UX Design & Mailing Architecture:** Fernando Adriano Sabido Quijano.

---
**© 2026 MiAgenda Team — Todos los derechos reservados.**
