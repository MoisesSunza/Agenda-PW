<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Listar solo los contactos del usuario logueado
    public function index() {
        return response()->json(auth()->user()->contacts, 200);
    }

    // Guardar un nuevo contacto (Requerimiento 1)
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string',
            'correo' => 'required|email',
            'telefono' => 'required|string',
            'notas' => 'nullable|string'
        ]);

        $contact = auth()->user()->contacts()->create($data);
        return response()->json($contact, 201);
    }

    // Mostrar un contacto específico
    public function show($id) {
        $contact = auth()->user()->contacts()->findOrFail($id);
        return response()->json($contact, 200);
    }

    // Actualizar datos del contacto
    public function update(Request $request, $id) {
        $contact = auth()->user()->contacts()->findOrFail($id);
        $contact->update($request->all());
        return response()->json($contact, 200);
    }

    // Eliminar contacto
    public function destroy($id) {
        auth()->user()->contacts()->findOrFail($id)->delete();
        return response()->json(['message' => 'Eliminado'], 200);
    }
}