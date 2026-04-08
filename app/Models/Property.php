<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PropertyImage;

class Property extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'bedrooms',
        'bathrooms',
        'half_bathrooms',
        'parking_spots',
        'm2_construction',
        'm2_land',
        'address',
        'neighborhood',
        'type',
        'status',
        'contract_type',
        'active',
        'state',
        'city',
        'show_public_address',
        'is_featured',
        'seller_id', 
        'client_id',
        'cp'
    ];

    // Relación con las fotos (similar a como lo hicimos con los autos)
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }
}
