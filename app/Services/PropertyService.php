<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyService
{
    public function createProperty(array $data): Property
    {
        // Forzamos el estado inicial de la propiedad
        $data['status'] = 'available';

        return Property::create($data);
    }

    public function getAllPaginated($perPage = 10)
    {
        // Usamos 'with' por si después quieres traer las fotos (Galería)
        return Property::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }


}