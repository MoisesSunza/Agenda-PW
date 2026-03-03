<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 * title="API MiAgenda - Proyecto UAC",
 * version="1.0.0",
 * description="Documentación técnica de la API para gestión de contactos y eventos.",
 * @OA\Contact(
 * name="Moisés Abraham Sunza Vázquez & Fernando Adriano Sabido Quijano",
 * email="moises.sunza@uac.mx"
 * )
 * )
 *
 * @OA\Server(
 * url="http://localhost:8000/api",
 * description="Servidor de Desarrollo Local"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 * schema="User",
 * title="Usuario",
 * description="Esquema del modelo de usuario para el sistema MiAgenda",
 * @OA\Property(property="id", type="integer", format="int64", example=1),
 * @OA\Property(property="name", type="string", example="Moisés Sunza"),
 * @OA\Property(property="email", type="string", format="email", example="moises@uac.mx"),
 * @OA\Property(property="role", type="string", example="user"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}