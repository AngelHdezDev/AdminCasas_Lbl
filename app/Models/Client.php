<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    public function sellers()
    {
        return $this->belongsToMany(Seller::class);
    }

    public function properties()
    {
        // Un cliente puede estar interesado en muchas propiedades
        return $this->hasMany(Property::class);
    }
}
