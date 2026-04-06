<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function createProperty(array $data): Property
    {
        $data['status'] = 'available';

        return Property::create($data);
    }

    public function getAllPaginated($perPage = 10)
    {
        return Property::with('images')
            ->where('active', true) 
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateProperty(Property $property, array $data)
    {
        return $property->update($data);
    }


}