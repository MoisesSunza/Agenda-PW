<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Space;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    // Listar espacios (Lo ven todos)
    public function index() {
        return response()->json(Space::all(), 200);
    }

    // Crear espacio (Solo Admin)
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string',
            'capacidad' => 'required|integer',
            'descripcion' => 'nullable|string'
        ]);

        $space = Space::create($data);
        return response()->json($space, 201);
    }

    // Mostrar un espacio
    public function show($id) {
        return Space::findOrFail($id);
    }

    // Actualizar espacio (Solo Admin)
    public function update(Request $request, $id) {
        $space = Space::findOrFail($id);
        $space->update($request->all());
        return response()->json($space, 200);
    }

    // Eliminar espacio (Solo Admin)
    public function destroy($id) {
        Space::findOrFail($id)->delete();
        return response()->json(['message' => 'Espacio eliminado'], 200);
    }
}