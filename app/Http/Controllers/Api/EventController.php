<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event; // Asegúrate de tener tu modelo Event creado

class EventController extends Controller
{
    // GET /api/events -> Cargar eventos en el calendario
    public function index()
    {
        // Traemos todos los eventos (idealmente aquí filtrarías por el usuario logueado)
        $events = Event::all(); 
        return response()->json(['data' => $events], 200);
    }

    // POST /api/events -> Guardar nuevo evento
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            // hora y descripcion son opcionales según tu JS
        ]);

        $event = Event::create($request->all());

        return response()->json(['message' => 'Evento creado con éxito', 'data' => $event], 201);
    }

    // PUT /api/events/{id} -> Actualizar evento existente
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->update($request->all());

        return response()->json(['message' => 'Evento actualizado', 'data' => $event], 200);
    }

    // DELETE /api/events/{id} -> Eliminar evento
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Evento eliminado correctamente'], 200);
    }
}