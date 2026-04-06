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
            'title'           => 'required|string|max:255',
            'neighborhood'    => 'required|string|max:255',
            'address'         => 'required|string|max:500',
            'type'            => 'required|string|in:house,apartment,land,commercial',
            'contract_type'   => 'required|string|in:sale,rent',
            'price'           => 'required|numeric|min:0',
            'm2_land'         => 'required|numeric|min:0',
            'm2_construction' => 'required|numeric|min:0',
            'bedrooms'        => 'nullable|integer|min:0',
            'bathrooms'       => 'nullable|integer|min:0',
            'parking_spots'   => 'nullable|integer|min:0',
            'description'     => 'nullable|string',
            'is_featured'     => 'nullable|boolean',
            'show_address'    => 'nullable|boolean',
        ];
    }
}