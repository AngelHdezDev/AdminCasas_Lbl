<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    //
    public function clients()
    {
        // Relación muchos a muchos con la tabla pivote
        return $this->belongsToMany(Client::class);
    }

    public function properties()
    {
        // Un vendedor tiene muchas propiedades asignadas
        return $this->hasMany(Property::class);
    }
}
