<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role' => 'string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'] ?? 'cliente'
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 200);
    }

        /**
     * @OA\Post(
     * path="/login",
     * summary="Inicio de sesión de usuario",
     * tags={"Autenticación"},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="email", type="string", example="test@agenda.com"),
     * @OA\Property(property="password", type="string", example="password")
     * )
     * ),
     * @OA\Response(response=200, description="Login exitoso"),
     * @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */

    public function logout() {
        auth()->user()->tokens()->delete();
        return ['message' => 'Sesión cerrada'];
    }
}