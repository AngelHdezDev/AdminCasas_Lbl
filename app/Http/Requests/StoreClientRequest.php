<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'notes' => 'nullable|string',
            'identification_path' => 'nullable|file|mimes:jpg,jpeg,png|max:1048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre del cliente es obligatorio.',
            'phone.required' => 'El teléfono es necesario para el seguimiento.',
            'email.email'    => 'El formato del correo electrónico no es válido.',
            'email.unique'   => 'Este correo ya pertenece a un cliente registrado.',
            'identification_path.file' => 'El archivo de identificación debe ser un archivo válido.',
            'identification_path.mimes' => 'El archivo de identificación debe ser una imagen (jpg, jpeg, png).',
            'identification_path.max' => 'El archivo de identificación no debe superar los 1MB de tamaño.',
        ];
    }
}
