<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Listar todas las notificaciones del usuario logueado.
     * Requerimiento: Mostrar alertas al iniciar sesión.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications, 200);
    }

    /**
     * Marcar una notificación específica como leída.
     * Ruta: PUT /api/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        // Buscamos la notificación asegurándonos que le pertenezca al usuario
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        $notification->update(['leido' => true]);

        return response()->json([
            'message' => 'Notificación marcada como leída',
            'notification' => $notification
        ], 200);
    }

    /**
     * Eliminar una notificación (opcional, para limpiar el historial).
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notificación eliminada'], 200);
    }
}