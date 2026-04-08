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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'phone.required' => 'El teléfono es necesario para el seguimiento.',
            'email.unique'   => 'Este correo ya está registrado con otro cliente.',
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