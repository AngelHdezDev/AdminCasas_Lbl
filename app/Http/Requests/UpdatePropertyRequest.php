<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'type' => 'required|string|in:house,apartment,land,commercial',
            'contract_type' => 'required|string|in:sale,rent,consignment',
            'price' => 'required|numeric|min:0',
            'm2_land' => 'required|numeric|min:0',
            'm2_construction' => 'required|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'parking_spots' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_featured' => [
                'nullable',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value == 1) {
                        // Obtenemos el ID de la propiedad desde la ruta de la petición URL
                        $propertyId = $this->route('id') ?? $this->route('propiedade');

                        $totalDestacados = \App\Models\Property::where('is_featured', 1)
                            ->where('id', '!=', $propertyId)
                            ->count();

                        if ($totalDestacados >= 6) {
                            $fail('No se puede destacar esta propiedad. Ya alcanzaste el límite máximo de 6 propiedades destacadas.');
                        }
                    }
                },
            ],      // CORREGIDO
            'show_public_address' => 'sometimes|boolean',      // CORREGIDO + RENOMBRADO
            'state' => 'required|string|max:255', // NUEVO
            'city' => 'required|string|max:255', // NUEVO
            'seller_id' => 'nullable|exists:sellers,id',    // NUEVO
            'client_id' => 'nullable|exists:clients,id',  // NUEVO
            'cp' => 'required|string|size:5|regex:/^[0-9]+$/', // NUEVO
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ];
    }
}