<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        // Obtenemos el parámetro 'client' de la ruta
        $client = $this->route('client');

        // Si Laravel está haciendo Route Model Binding, $client será el objeto.
        // Necesitamos el ID, así que lo extraemos con seguridad:
        $clientId = is_object($client) ? $client->id : $client;

        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            // Ahora sí, pasamos el ID numérico para que ignore a este cliente
            'email' => 'nullable|email|max:255|unique:clients,email,' . $clientId,
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'phone.required' => 'El teléfono es necesario para el seguimiento.',
            'email.unique' => 'Este correo ya está registrado con otro cliente.',
        ];
    }
}
