<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Mail\EventoCreadoMail;
use App\Mail\EventoActualizadoMail; // Nuevo import
use App\Mail\EventoEliminadoMail;   // Nuevo import
use Illuminate\Support\Facades\Mail;

/**
 * @OA\Tag(name="Eventos", description="Gestión de agenda y recordatorios")
 */
class EventController extends Controller
{
    /**
     * GET /api/events -> Cargar solo eventos del usuario logueado.
     */

    /**
     * @OA\Get(
     * path="/events",
     * summary="Listar eventos del calendario",
     * tags={"Eventos"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Eventos recuperados")
     * )
     */
    public function index()
    {
        $events = auth()->user()->events; 
        return response()->json(['data' => $events], 200);
    }

    /**
     * POST /api/events -> Guardar nuevo evento y disparar alertas.
     */
    /**
     * @OA\Post(
     * path="/events",
     * summary="Crear un evento y programar recordatorio",
     * tags={"Eventos"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"titulo", "fecha_inicio"},
     * @OA\Property(property="titulo", type="string", example="Proyecto de Ingeniería"),
     * @OA\Property(property="fecha_inicio", type="string", format="date", example="2026-03-03"),
     * @OA\Property(property="hora", type="string", example="10:00"),
     * @OA\Property(property="descripcion", type="string", example="Entrega final con Fernando")
     * )
     * ),
     * @OA\Response(response=201, description="Evento programado")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
        ]);

        $event = auth()->user()->events()->create($request->all());

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "📅 Has programado un nuevo evento: {$event->titulo}",
            'leida' => false
        ]);

        try {
            Mail::to(auth()->user()->email)->send(new EventoCreadoMail($event));
        } catch (\Exception $e) {
            \Log::error("Error de correo en creación: " . $e->getMessage());
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

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "✏️ Evento actualizado: {$event->titulo}",
            'leida' => false
        ]);

        // ENVIAR CORREO DE ACTUALIZACIÓN
        try {
            Mail::to(auth()->user()->email)->send(new EventoActualizadoMail($event));
        } catch (\Exception $e) {
            \Log::error("Error de correo en actualización: " . $e->getMessage());
        }

        return response()->json(['message' => 'Evento actualizado', 'data' => $event], 200);
    }

    /**
     * DELETE /api/events/{id} -> Eliminar y notificar salida.
     */
    /**
     * @OA\Delete(
     * path="/events/{id}",
     * summary="Cancelar un evento",
     * tags={"Eventos"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Evento eliminado")
     * )
     */
    public function destroy($id)
    {
        $event = auth()->user()->events()->find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $titulo = $event->titulo;
        $event->delete();

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "🗑️ Evento eliminado: {$titulo}",
            'leida' => false
        ]);

        // ENVIAR CORREO DE ELIMINACIÓN
        try {
            Mail::to(auth()->user()->email)->send(new EventoEliminadoMail($titulo));
        } catch (\Exception $e) {
            \Log::error("Error de correo en eliminación: " . $e->getMessage());
        }

        return response()->json(['message' => 'Evento eliminado correctamente'], 200);
    }
}