# MiAgenda — Ecosistema de Gestión de Productividad y Notificaciones
**Documentación Técnica de Ingeniería de Software | Facultad de Ingeniería (FDI)**
**Universidad Autónoma de Campeche (UAC)**

---

## Información del Proyecto
* **Autores:** * Moisés Abraham Sunza Vázquez
    * Fernando Adriano Sabido Quijano
* **Catedrático:** Juan Antonio Chuc Méndez
* **Institución:** Facultad de Ingeniería, UAC.
* **Semestre:** Sexto Semestre.
* **Fecha:** Marzo 2026.

---

## 1. Resumen Ejecutivo
**MiAgenda** es una solución integral diseñada para la administración centralizada de contactos y la planificación estratégica de eventos académicos y personales. El sistema resuelve la problemática de la desorganización mediante una arquitectura de software moderna que separa la lógica de presentación de la lógica de negocio. 

El núcleo del proyecto reside en su motor de automatización, el cual procesa eventos en tiempo real para disparar recordatorios críticos tanto en la interfaz del navegador como a través de servicios de mensajería SMTP.

---

## 2. Arquitectura del Sistema
Para este proyecto se optó por una arquitectura **Desacoplada de Aplicación Web Stateless**. Esta decisión técnica permite que el sistema sea escalable y ligero.

### 2.1 Modelo Cliente-Servidor
* **Frontend (Vanilla Stack):** Construido exclusivamente con HTML5, CSS3 y JavaScript ES6+ nativo. Esto garantiza un renderizado instantáneo y una manipulación directa del DOM para la lógica de **Optimistic UI**.
* **Backend (Laravel 12 API):** El servidor actúa como una API RESTful que procesa las peticiones, gestiona la persistencia de datos y ejecuta las tareas programadas de fondo.
* **Comunicación:** El intercambio de información se realiza mediante objetos **JSON** sobre protocolo HTTP, asegurando la interoperabilidad total entre las capas.



### 2.2 Seguridad y Autenticación (Laravel Sanctum)
La protección de los datos se gestiona mediante un sistema de **Tokens de Acceso Personal**.
* **Persistencia de Sesión:** Tras la validación de credenciales, el token emitido por Sanctum se almacena en el `localStorage` del cliente.
* **Validación en cada Request:** El frontend inyecta automáticamente el encabezado `Authorization: Bearer {token}` en todas las peticiones privadas.
* **Aislamiento de Recursos:** El sistema identifica al usuario a través del token, filtrando las consultas para que cada alumno visualice exclusivamente sus propios registros, garantizando la privacidad institucional.

---

## 3. Stack Tecnológico
La selección tecnológica refleja el uso de las versiones más estables y potentes disponibles para el desarrollo de software actual.

| Capa | Tecnología | Versión / Detalle |
| :--- | :--- | :--- |
| **Backend** | Laravel 12 | Framework PHP de última generación para lógica de servidor. |
| **Lenguaje** | PHP 8.4 | Motor de ejecución optimizado para alto rendimiento y tipado fuerte. |
| **Frontend** | Vanilla JS | JavaScript nativo para gestión de eventos y reactividad de interfaz. |
| **Estilos** | TailwindCSS | Framework de diseño basado en utilidades para maquetación rápida. |
| **Base de Datos** | MySQL | Motor relacional para asegurar la integridad referencial. |
| **Task Scheduling**| Artisan Scheduler | Motor de cronjobs para el envío de recordatorios automáticos. |

---

## 4. Modelos de Datos y Estructura
El diseño de la base de datos se basa en una estructura relacional que garantiza la consistencia de la información mediante el uso de llaves foráneas y restricciones de cascada.

### 4.1 Diagrama Entidad-Relación (ERD)
El diagrama muestra una jerarquía donde la entidad `User` es el punto de origen de todos los recursos del sistema.



### 4.2 Diccionario de Datos: Tabla `events`
Esta tabla es el componente crítico para la lógica de los recordatorios.

| Campo | Tipo | Función |
| :--- | :--- | :--- |
| `id` | BIGINT | Identificador único (Primary Key). |
| `user_id` | BIGINT | Relación de propiedad con el usuario (Foreign Key). |
| `fecha_inicio` | DATE | Fecha exacta del evento programado. |
| `hora` | TIME | Tiempo exacto para el cálculo de la alerta de 10 minutos. |
| `notificado` | TINYINT(1) | Flag de control que evita envíos duplicados de correo. |

### 4.3 Diccionario de Datos: Tabla `notifications`
Gestiona el estado visual de las alertas en la interfaz de usuario.

| Campo | Tipo | Función |
| :--- | :--- | :--- |
| `id` | BIGINT | Identificador de la notificación. |
| `mensaje` | VARCHAR(255) | Contenido textual de la alerta disparada. |
| `leido` | TINYINT(1) | Estado binario para la lógica de **Optimistic UI**. |
## 5. Descripción de Funcionalidades y Lógica de Control

En esta sección se detalla el comportamiento operativo del sistema, describiendo la interacción entre el **Vanilla Stack** del frontend y los servicios de **Laravel 12**.

### 5.1 Gestión de Autenticación Stateless
El flujo de acceso se diseñó para ser totalmente independiente de sesiones en el servidor, utilizando la **Fetch API** de JavaScript nativo:
* **Captura de Credenciales:** El frontend intercepta el evento de envío del formulario, serializa los datos en formato JSON y los despacha al endpoint `/api/login`.
* **Persistencia Local:** Ante una respuesta exitosa, el token de **Sanctum** se almacena en el `localStorage`, permitiendo que el estado de "conectado" persista incluso tras recargar la página.
* **Cierre de Sesión:** El método de logout invalida el token en la base de datos y limpia el almacenamiento local, garantizando que el acceso sea revocado de forma inmediata.

### 5.2 Módulo de Contactos y Automatización de Correo
La administración de la libreta de direcciones integra procesos automáticos de comunicación:
* **Integración SMTP:** Al registrar un nuevo contacto mediante una petición `POST`, el controlador de Laravel dispara un evento de correo electrónico.
* **Mensajería de Bienvenida:** El sistema utiliza **Mailables** personalizados para enviar un mensaje formal al correo electrónico del contacto registrado, notificándole su adición a la agenda del usuario.

### 5.3 Calendario Dinámico
La visualización de eventos se gestiona mediante la integración de la librería **FullCalendar**, alimentada por peticiones asíncronas:
* **Mapeo de Datos:** JavaScript recupera la colección de eventos del endpoint `/api/events` y transforma los campos `fecha_inicio` y `hora` en objetos de calendario renderizables.
* **Soporte de Continuidad:** El sistema está programado para manejar eventos que superan la medianoche, asegurando que la representación gráfica en la interfaz sea coherente con la realidad temporal.

---

## 6. Reglas de Negocio y Algoritmo de Notificación

La inteligencia del sistema reside en su capacidad para anticiparse a los compromisos del usuario mediante una lógica de proximidad temporal.

### 6.1 Motor de Recordatorios de 10 Minutos
Se implementó un comando de consola (`app:send-event-reminders`) que actúa como un vigilante autónomo del sistema.

**Especificaciones del Algoritmo:**
1. **Ejecución:** El **Artisan Scheduler** invoca el comando cada 60 segundos.
2. **Cálculo de Proximidad ($\Delta t$):** El sistema busca registros donde la diferencia entre la hora del evento y la hora actual sea exactamente de 10 minutos.
   $$\Delta t = t_{evento} - t_{actual} = 10 \text{ min}$$
3. **Filtro de Unicidad:** Solo se procesan eventos cuyo campo `notificado` sea igual a `0`.
4. **Despacho Dual:** Se genera una notificación interna en la base de datos y se envía un correo electrónico de alerta vía SMTP.
5. **Cierre de Ciclo:** Tras el envío, el campo `notificado` se actualiza a `1`, bloqueando envíos redundantes.



### 6.2 Sincronización Localizada
Para garantizar que la regla de los 10 minutos funcione con precisión en la región de la **UAC**, se forzó la zona horaria `America/Merida` en la configuración global de Laravel (`config/app.php`). Esto asegura que el servidor y el cliente hablen el mismo idioma temporal.

---

## 7. Documentación de la API (Endpoints)

La comunicación entre capas se rige por un contrato de API RESTful estandarizado.

| Recurso | Método | Endpoint | Descripción |
| :--- | :--- | :--- | :--- |
| **Sesión** | POST | `/api/login` | Validación de credenciales y emisión de token. |
| **Contactos**| GET | `/api/contacts` | Recupera la lista de contactos del usuario. |
| **Contactos**| POST | `/api/contacts` | Almacena un contacto y envía correo de bienvenida. |
| **Eventos** | PUT | `/api/events/{id}`| Actualiza título, fecha o descripción de un evento. |
| **Alertas** | PUT | `/api/notifications/{id}/read` | Marca una notificación como leída (Optimistic UI). |

---

## 8. Estrategia de UX: Identidad "Beyond Gardens"

El diseño visual busca un equilibrio entre la elegancia técnica y la funcionalidad moderna.

### 8.1 Paleta de Colores y Estética
Se utilizó **TailwindCSS** para implementar un modo oscuro profundo (`#0d0d0f`) con acentos en dorado metálico (`#c8a96e`), priorizando la legibilidad en entornos de estudio prolongado.

### 8.2 Optimistic UI (Respuesta Instantánea)
Para eliminar la percepción de latencia en la red, se implementó una lógica de "Actualización Optimista" mediante JavaScript puro:
* **Acción Local:** Cuando el usuario marca una notificación como leída, el script modifica el DOM instantáneamente para reflejar el cambio visual.
* **Validación Diferida:** La petición `PUT` se envía al servidor en segundo plano. Si la API retorna un error (ej. 500), el JavaScript revierte el cambio en la interfaz y muestra un mensaje de advertencia.



---

## 9. Manejo de Errores y Respuestas HTTP

El sistema utiliza códigos de estado estandarizados para guiar al frontend en la resolución de conflictos.

* **201 Created:** Confirmación de registro exitoso de contactos/eventos.
* **401 Unauthorized:** Token inválido o expirado; el frontend redirige al login.
* **403 Forbidden:** El usuario intentó acceder a un recurso (evento/contacto) que no le pertenece.
* **422 Unprocessable Entity:** Fallo en las reglas de validación (ej. formato de email incorrecto).
