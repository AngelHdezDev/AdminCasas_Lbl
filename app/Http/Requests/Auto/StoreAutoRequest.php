<?php

namespace App\Http\Requests\Auto;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'id_marca'      => 'required|exists:marcas,id_marca',
            'modelo'        => 'required|string|max:100',
            'year'          => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color'         => 'required|string|max:50',
            'transmision'   => 'required|string',
            'combustible'   => 'required|string',
            'kilometraje'   => 'required|integer|min:0',
            'precio'        => 'required|numeric|min:0',
            'tipo' => 'required|string',
            'descripcion'   => 'nullable|string|max:1000',
            // Checkboxes (Laravel los recibe como strings o null si no se marcan)
            'ocultar_kilometraje'    => 'nullable|boolean',
            'consignacion'  => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id_marca.required' => 'Debes seleccionar una marca de la lista.',
            'id_marca.exists'   => 'La marca seleccionada no es válida.',
            'modelo.required'   => 'El nombre del modelo (ej. Civic) es obligatorio.',
            'year.required'     => 'El año es necesario para el inventario.',
            'precio.numeric'    => 'El precio debe ser un número válido.',
            'kilometraje.min'   => 'El kilometraje no puede ser negativo.',
        ];
    }
}
