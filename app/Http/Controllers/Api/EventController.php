<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Mail\EventoCreadoMail;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    /**
     * GET /api/events -> Cargar solo eventos del usuario logueado.
     */
    public function index()
    {
        // Importante: Solo traemos eventos del usuario autenticado
        $events = auth()->user()->events; 
        return response()->json(['data' => $events], 200);
    }

    /**
     * POST /api/events -> Guardar nuevo evento y disparar alertas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
        ]);

        // Creamos el evento asociado al usuario
        $event = auth()->user()->events()->create($request->all());

        // 1. Notificación interna para el frontend
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "📅 Has programado un nuevo evento: {$event->titulo}",
            'leida' => false
        ]);

        // 2. Notificación por correo con vista profesional (Blade)
        try {
            Mail::to(auth()->user()->email)->send(new EventoCreadoMail($event));
        } catch (\Exception $e) {
            \Log::error("Error de correo en calendario: " . $e->getMessage());
        }

        return response()->json(['message' => 'Evento creado con éxito', 'data' => $event], 201);
    }

    /**
     * PUT /api/events/{id} -> Actualizar y notificar cambio.
     */
    public function update(Request $request, $id)
    {
        $event = auth()->user()->events()->find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->update($request->all());

        // Notificamos la actualización
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "✏️ Evento actualizado: {$event->titulo}",
            'leida' => false
        ]);

        return response()->json(['message' => 'Evento actualizado', 'data' => $event], 200);
    }

    /**
     * DELETE /api/events/{id} -> Eliminar y notificar salida.
     */
    public function destroy($id)
    {
        $event = auth()->user()->events()->find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $titulo = $event->titulo;
        $event->delete();

        // Notificación de eliminación
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "🗑️ Evento eliminado: {$titulo}",
            'leida' => false
        ]);

        return response()->json(['message' => 'Evento eliminado correctamente'], 200);
    }
}