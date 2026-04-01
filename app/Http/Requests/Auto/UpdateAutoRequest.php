<?php

namespace App\Http\Requests\Auto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutoRequest extends FormRequest
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
        $yearMax = date('Y') + 1;

        return [
            'id_marca' => 'required|exists:marcas,id_marca',
            'modelo' => 'required|string|max:255',
            'year' => "required|integer|min:1900|max:$yearMax",
            'color' => 'required|string|max:50',
            'tipo' => 'required|string',
            'transmision'   => 'required|string',
            'combustible'   => 'required|string',
            'precio' => 'required|numeric|min:0',
            'kilometraje' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'descripcion'   => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'id_marca.exists' => 'La marca seleccionada no es válida.',
            'year.max' => 'El año no puede ser superior a ' . (date('Y') + 1) . '.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'kilometraje.min' => 'El kilometraje no puede ser negativo.',
        ];
    }
}

