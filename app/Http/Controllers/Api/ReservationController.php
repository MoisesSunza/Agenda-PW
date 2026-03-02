<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Notification;
use App\Mail\ReservationConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * Listar todas las reservaciones del usuario logueado.
     * Incluye la relación con 'space' para mostrar el nombre del lugar.
     */
    public function index()
    {
        $reservations = auth()->user()->reservations()->with('space')->get();
        return response()->json($reservations, 200);
    }

    /**
     * Crear una nueva reservación con validación de conflictos y transacciones.
     */
    public function store(Request $request)
    {
        $request->validate([
            'space_id'    => 'required|exists:spaces,id',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora'        => 'required',
        ]);

        // Iniciamos la transacción para asegurar la integridad (FAQ 9)
        return DB::transaction(function () use ($request) {
            
            // Verificamos si ya existe una reserva en ese espacio, fecha y hora
            // Usamos lockForUpdate para evitar que dos usuarios reserven lo mismo al milisegundo
            $conflicto = Reservation::where('space_id', $request->space_id)
                ->where('fecha', $request->fecha)
                ->where('hora', $request->hora)
                ->where('status', 'activa')
                ->lockForUpdate()
                ->exists();

            if ($conflicto) {
                return response()->json([
                    'error' => 'El espacio seleccionado ya está ocupado en esa fecha y hora.'
                ], 409);
            }

            // 1. Creamos la reserva amarrada al usuario autenticado
            $reservation = auth()->user()->reservations()->create([
                'space_id'    => $request->space_id,
                'titulo'      => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha'       => $request->fecha,
                'hora'        => $request->hora,
                'status'      => 'activa'
            ]);

            // 2. Creamos la notificación interna para el Requerimiento 3
            Notification::create([
                'user_id' => auth()->id(),
                'mensaje' => "Has programado con éxito: " . $request->titulo,
            ]);

            // 3. Enviamos el correo de confirmación (Mailtrap)
            // Nota: Asegúrate de haber creado el Mailable 'ReservationConfirmed'
            try {
                Mail::to(auth()->user()->email)->send(new ReservationConfirmed($reservation));
            } catch (\Exception $e) {
                // Si el correo falla, no detenemos la reserva pero lo registramos
                \Log::error("Error enviando correo: " . $e->getMessage());
            }

            return response()->json([
                'message' => 'Reserva creada y notificada con éxito',
                'data' => $reservation->load('space')
            ], 201);
        });
    }

    /**
     * Mostrar detalles de una reserva específica.
     */
    public function show($id)
    {
        $reservation = auth()->user()->reservations()->with('space')->findOrFail($id);
        return response()->json($reservation, 200);
    }

    /**
     * Actualizar una reserva existente.
     */
    public function update(Request $request, $id)
    {
        $reservation = auth()->user()->reservations()->findOrFail($id);
        
        $reservation->update($request->only([
            'titulo', 'descripcion', 'fecha', 'hora', 'status'
        ]));

        return response()->json($reservation, 200);
    }

    /**
     * Cancelar una reserva (Requerimiento 7).
     */
    public function destroy($id)
    {
        $reservation = auth()->user()->reservations()->findOrFail($id);
        
        // En lugar de borrarla, podemos marcarla como cancelada
        $reservation->update(['status' => 'cancelada']);

        return response()->json(['message' => 'La reserva ha sido cancelada.'], 200);
    }
}