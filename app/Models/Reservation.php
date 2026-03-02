<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'space_id', 'titulo', 'descripcion', 'fecha', 'hora', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function space() {
        return $this->belongsTo(Space::class);
    }
}