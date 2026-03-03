<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EventController;

/*
|--------------------------------------------------------------------------
| API Routes - Proyecto MiAgenda (Moisés & Fernando)
|--------------------------------------------------------------------------
| Base URL: http://localhost:8000/api [cite: 195]
*/

// --- RUTAS PÚBLICAS ---
// Registro e inicio de sesión [cite: 197, 208]
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (Requieren Token Bearer) [cite: 196] ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Perfil del usuario autenticado 
    Route::get('/user', [AuthController::class, 'me']); 

    // Cerrar sesión [cite: 224]
    Route::post('/logout', [AuthController::class, 'logout']);

    // 1. Gestión de Contactos (CRUD: index, store, show, update, destroy) [cite: 241, 249]
    Route::apiResource('contacts', ContactController::class);

    // 2. Gestión de Eventos (Calendario y Recordatorios) [cite: 271, 284]
    Route::apiResource('events', EventController::class); 

    // 3. Centro de Notificaciones (Logica de Optimistic UI)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});