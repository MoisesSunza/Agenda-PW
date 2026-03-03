<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EventController;

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

    // 1. Gestión de Contactos (CRUD completo)
    Route::apiResource('contacts', ContactController::class);

    // 2. Gestión de Eventos (Calendario)
    Route::apiResource('events', EventController::class); 

    // 4. Notificaciones dentro de la plataforma
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']); // ¡Ruta agregada para el JS!
});