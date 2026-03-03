<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(name="Autenticación", description="Registro e inicio de sesión de usuarios")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/register",
     * summary="Registrar un nuevo usuario",
     * tags={"Autenticación"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="string", example="Juan Pérez"),
     * @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Usuario registrado exitosamente",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", example="1|abc123..."),
     * @OA\Property(property="token_type", type="string", example="Bearer"),
     * @OA\Property(property="user", ref="#/components/schemas/User")
     * )
     * )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/login",
     * summary="Iniciar sesión",
     * tags={"Autenticación"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", example="1|abc123..."),
     * @OA\Property(property="token_type", type="string", example="Bearer"),
     * @OA\Property(property="user", ref="#/components/schemas/User")
     * )
     * ),
     * @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
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
     * Obtener el usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     * path="/user",
     * summary="Obtener datos del usuario autenticado",
     * tags={"Autenticación"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Datos del usuario",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(
     * response=401,
     * description="No autorizado (token inválido o ausente)"
     * )
     * )
     */
    public function me()
    {
        // auth()->user() recupera al usuario a partir del token JWT enviado en la cabecera
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     * path="/logout",
     * summary="Cerrar sesión",
     * tags={"Autenticación"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Sesión cerrada",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"))
     * )
     * )
     */
    public function logout() {
        auth()->user()->tokens()->delete();
        return ['message' => 'Sesión cerrada'];
    }
}