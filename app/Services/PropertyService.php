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

    public function getAllPaginated($perPage = 10, $filters = [])
    {
        $query = Property::with('images')
            ->where('active', true);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filtro por Tipo (Casa, Departamento, etc.)
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // 3. Filtro por Colonia (Neighborhood)
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // 4. Filtro por Operación (Venta o Renta)
        if (!empty($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateProperty(Property $property, array $data)
    {
        return $property->update($data);
    }


}