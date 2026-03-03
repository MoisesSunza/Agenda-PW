<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Notificaciones", description="Centro de alertas del sistema")
 */
class NotificationController extends Controller
{
    // Carga las notificaciones del usuario logueado
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return response()->json(['data' => $notifications]);
    }

    // Marca como leída (Botón ✔)
    /**
     * @OA\Put(
     * path="/notifications/{id}/read",
     * summary="Marcar notificación como leída",
     * tags={"Notificaciones"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Estado actualizado")
     * )
     */
    public function markAsRead($id)
    {
        // Esto busca: WHERE id = $id AND user_id = [tu_id]
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        $notification->update(['leida' => true]);

        return response()->json(['success' => true]);
    }

    // Elimina la notificación (Botón ✖)
    /**
     * @OA\Delete(
     * path="/notifications/{id}",
     * summary="Eliminar notificación de la lista",
     * tags={"Notificaciones"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Notificación borrada")
     * )
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notificación eliminada']);
    }
}