<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellerRequest extends FormRequest
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
    public function rules(): bool|array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers,email',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'nullable',
            'contract_file' => 'nullable|file|mimes:jpg,jpeg,png|max:1048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre del vendedor es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique'   => 'Este correo ya está registrado en el sistema.',
            'contract_file.mimes' => 'El contrato debe ser una imagen (JPG, PNG).',
            'contract_file.max' => 'El contrato no debe superar los 1MB.',
            
        ];
    }
}
