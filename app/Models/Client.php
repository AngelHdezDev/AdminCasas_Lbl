<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'identification_path'
    ];
    public function sellers()
    {
        return $this->belongsToMany(Seller::class);
    }

    public function properties()
    {
        
        return $this->hasMany(Property::class);
    }
}
