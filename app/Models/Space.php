<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = ['nombre', 'capacidad', 'descripcion'];

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}