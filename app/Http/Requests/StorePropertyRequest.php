<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'm2_construction' => 'required|numeric',
            'm2_land' => 'required|numeric',
            'address' => 'required|string',
            'type' => 'required|in:house,apartment,land,commercial',
            'description' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'parking_spots' => 'required|integer|min:0',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'is_featured' => 'sometimes|boolean',
            'show_public_address' => 'sometimes|boolean',
            'seller_id' => 'nullable|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
        ];
    }
}
