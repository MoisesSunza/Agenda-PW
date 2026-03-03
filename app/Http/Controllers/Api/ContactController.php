<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoRegistradoMail;
use App\Mail\ContactoActualizadoMail;
use App\Mail\ContactoEliminadoMail;

/**
* @OA\Tag(name="Contactos", description="Operaciones para la libreta de direcciones")
*/
class ContactController extends Controller
{
    /**
     * Listar contactos del usuario logueado.
     */

    /**
     * @OA\Get(
     * path="/contacts",
     * summary="Listar contactos del usuario",
     * tags={"Contactos"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Lista de contactos obtenida")
     * )
     */
    public function index()
    {
        $contacts = auth()->user()->contacts()->get();
        return response()->json(['data' => $contacts], 200);
    }

    /**
     * REGISTRAR CONTACTO: Crea el registro y envía el correo de bienvenida.
     */
    /**
     * @OA\Post(
     * path="/contacts",
     * summary="Registrar un nuevo contacto",
     * tags={"Contactos"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"nombre", "correo"},
     * @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     * @OA\Property(property="correo", type="string", example="juan@example.com"),
     * @OA\Property(property="telefono", type="string", example="9811234567"),
     * @OA\Property(property="notas", type="string", example="Compañero de la UAC")
     * )
     * ),
     * @OA\Response(response=201, description="Contacto creado y correo enviado")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'notas'    => 'nullable|string'
        ]);

        $contact = auth()->user()->contacts()->create($request->all());

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "👤 Has registrado un nuevo contacto: {$contact->nombre}",
            'leida'   => false
        ]);

        try {
            Mail::to(auth()->user()->email)->send(new ContactoRegistradoMail($contact));
        } catch (\Exception $e) {
            \Log::error("Error al enviar correo de contacto: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Contacto creado con éxito y correo enviado', 
            'data'    => $contact
        ], 201);
    }

    /**
     * EDITAR CONTACTO: Actualiza y notifica el cambio al usuario.
     */
    /**
     * @OA\Put(
     * path="/contacts/{id}",
     * summary="Actualizar un contacto existente",
     * tags={"Contactos"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="nombre", type="string"),
     * @OA\Property(property="correo", type="string")
     * )
     * ),
     * @OA\Response(response=200, description="Contacto actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $contact = auth()->user()->contacts()->findOrFail($id);

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'notas' => 'nullable|string'
        ]);

        $contact->update($request->all());

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "✏️ Actualizaste los datos de: {$contact->nombre}",
            'leida' => false
        ]);

        try {
            // Cambio de Mail::raw a Mailable profesional
            Mail::to(auth()->user()->email)->send(new ContactoActualizadoMail($contact));
        } catch (\Exception $e) {
            \Log::error("Error de correo en actualización: " . $e->getMessage());
        }

        return response()->json(['message' => 'Contacto actualizado', 'data' => $contact], 200);
    }

    /**
     * ELIMINAR CONTACTO: Borra el registro y confirma la acción por correo.
     */
    /**
     * @OA\Delete(
     * path="/contacts/{id}",
     * summary="Eliminar un contacto",
     * tags={"Contactos"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Contacto eliminado")
     * )
     */
    public function destroy($id)
    {
        $contact = auth()->user()->contacts()->findOrFail($id);
        $nombreContacto = $contact->nombre; 
        
        $contact->delete();

        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "🗑️ Eliminaste al contacto: {$nombreContacto}",
            'leida' => false
        ]);

        try {
            // Cambio de Mail::raw a Mailable profesional
            Mail::to(auth()->user()->email)->send(new ContactoEliminadoMail($nombreContacto));
        } catch (\Exception $e) {
            \Log::error("Error de correo en eliminación: " . $e->getMessage());
        }

        return response()->json(['message' => 'Contacto eliminado correctamente'], 200);
    }
}