<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'user_id', // Agregado: Vital para vincular con el dueño
        'titulo',
        'fecha_inicio',
        'hora',
        'descripcion'
    ];

    /**
     * RELACIÓN VITAL: Define que el evento pertenece a un usuario.
     * Sin esto, el comando SendEventReminders no puede encontrar el email.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}   