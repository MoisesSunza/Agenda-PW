<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Permitimos que estos campos se llenen desde el formulario de tu Dashboard
    protected $fillable = [
        'titulo',
        'fecha_inicio',
        'hora',
        'descripcion'
    ];
}