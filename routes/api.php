<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\SpaceController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes - Proyecto Agenda Electrónica
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS ---
// Registro e inicio de sesión para obtener el token
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- RUTAS PROTEGIDAS (Requieren inicio de sesión) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // 1. Gestión de Contactos (CRUD completo: index, store, show, update, destroy)
    Route::apiResource('contacts', ContactController::class);

    // 2. Gestión de Eventos y Reservas
    // Incluye visualización en calendario y validación de horarios
    Route::apiResource('reservations', ReservationController::class);

    // 3. Notificaciones dentro de la plataforma
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // --- RUTAS DE ADMINISTRADOR (Middleware de roles) ---
    Route::middleware('admin')->group(function () {
        // Listar y gestionar espacios disponibles
        Route::apiResource('spaces', SpaceController::class);
    });
});