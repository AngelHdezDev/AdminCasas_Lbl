<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $client = $this->route('client');
        $clientId = is_object($client) ? $client->id : $client;

        return [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email,' . $clientId,
            'notes' => 'nullable|string',
            'identification_path' => 'nullable|file|mimes:jpg,jpeg,png|max:1048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'phone.required' => 'El teléfono es necesario para el seguimiento.',
            'email.unique'   => 'Este correo ya está registrado con otro cliente.',
            'email.email'    => 'El formato del correo electrónico no es válido.',
            'identification_path.file' => 'El archivo de identificación debe ser un archivo válido.',
            'identification_path.mimes' => 'El archivo de identificación debe ser una imagen (jpg, jpeg, png).',
            'identification_path.max' => 'El archivo de identificación no debe superar los 1MB de tamaño.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $client = $this->route('client');
        $clientId = is_object($client) ? $client->id : $client;

        session()->flash('edit_client_id', $clientId);

        throw new ValidationException($validator);
    }
}