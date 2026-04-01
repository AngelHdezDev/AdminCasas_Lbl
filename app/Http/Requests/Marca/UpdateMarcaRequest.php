<?php

namespace App\Http\Requests\Marca;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMarcaRequest extends FormRequest
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
        $marcaId = $this->route('id');
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('marcas', 'nombre')->ignore($marcaId, 'id_marca')
            ],
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Esta marca ya está registrada.',
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'imagen.required' => 'Debes subir un logo para la marca.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.max' => 'La imagen no debe pesar más de 2MB.',
        ];
    }
}
