<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Carga las notificaciones del usuario logueado
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return response()->json(['data' => $notifications]);
    }

    // Marca como leída (Botón ✔)
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['leida' => true]);

        return response()->json(['message' => 'Notificación leída']);
    }

    // Elimina la notificación (Botón ✖)
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notificación eliminada']);
    }
}