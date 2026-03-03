<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification; // Importamos el modelo de la campanita
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Importamos el enviador de correos
use App\Mail\ContactoRegistradoMail;

class ContactController extends Controller
{
    // Listar contactos del usuario logueado
    public function index()
    {
        // Asumiendo que un usuario tiene muchos contactos (relación en User.php)
        $contacts = auth()->user()->contacts()->get();
        return response()->json(['data' => $contacts], 200);
    }

    // REGISTRAR CONTACTO
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'notas'    => 'nullable|string'
        ]);

        // Creamos el contacto asociado al usuario autenticado
        $contact = auth()->user()->contacts()->create($request->all());

        // 1. Notificación interna para la "campanita" en el frontend
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "Has registrado un nuevo contacto: {$contact->nombre}",
            'leida'   => false
        ]);

        // 2. Notificación por correo con vista profesional
        try {
            // Reemplazamos Mail::raw por nuestro Mailable personalizado
            Mail::to(auth()->user()->email)->send(new ContactoRegistradoMail($contact));
        } catch (\Exception $e) {
            // Registramos el error en logs pero permitimos que la app responda al usuario
            \Log::error("Error al enviar correo de contacto: " . $e->getMessage());
        }

        // Respuesta para el frontend (contacts.js)
        return response()->json([
            'message' => 'Contacto creado con éxito y correo enviado', 
            'data'    => $contact
        ], 201);
    }

    // EDITAR CONTACTO
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

        // 1. Notificación en la aplicación
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "Actualizaste los datos de: {$contact->nombre}",
            'leida' => false
        ]);

        // 2. Notificación por correo
        try {
            Mail::raw("Hola,\n\nLos datos de tu contacto {$contact->nombre} han sido actualizados correctamente en tu agenda.\n\nSaludos.", function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('Contacto actualizado - MiAgenda');
            });
        } catch (\Exception $e) {
            \Log::error("Error de correo: " . $e->getMessage());
        }

        return response()->json(['message' => 'Contacto actualizado', 'data' => $contact], 200);
    }

    // ELIMINAR CONTACTO
    public function destroy($id)
    {
        $contact = auth()->user()->contacts()->findOrFail($id);
        $nombreContacto = $contact->nombre; // Guardamos el nombre antes de borrarlo
        
        $contact->delete();

        // 1. Notificación en la aplicación
        Notification::create([
            'user_id' => auth()->id(),
            'mensaje' => "Eliminaste al contacto: {$nombreContacto}",
            'leida' => false
        ]);

        // 2. Notificación por correo
        try {
            Mail::raw("Hola,\n\nTe confirmamos que el contacto {$nombreContacto} ha sido eliminado permanentemente de tu libreta de direcciones.\n\nSaludos.", function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('Contacto eliminado - MiAgenda');
            });
        } catch (\Exception $e) {
            \Log::error("Error de correo: " . $e->getMessage());
        }

        return response()->json(['message' => 'Contacto eliminado correctamente'], 200);
    }
}