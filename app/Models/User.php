<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Importante para la autenticación de la API
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Indispensable para diferenciar Admin y Cliente
    ];

    /**
     * Atributos que deben ocultarse para la serialización de la API.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relaciones del usuario con otras tablas.
     */
    public function notifications() {
        return $this->hasMany(\App\Models\Notification::class);
    }

    
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Relación: Un usuario tiene muchos contactos.
     * (Asegúrate de tener esta también para que funcione contacts.js)
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Casts de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}