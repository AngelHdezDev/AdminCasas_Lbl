<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el objeto o ID de la ruta 'vendedores/{seller}'
        $seller = $this->route('seller');
        $sellerId = is_object($seller) ? $seller->id : $seller;

        return [
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|max:255|unique:sellers,email,' . $sellerId,
            'notes'         => 'nullable|string',
            'contract_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Agregado PDF y 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre del vendedor es obligatorio.',
            'phone.required' => 'El teléfono es necesario para el contacto.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique'   => 'Este correo ya pertenece a otro vendedor registrado.',
            'email.email'    => 'El formato del correo electrónico no es válido.',
            'contract_file.file'  => 'El contrato debe ser un archivo válido.',
            'contract_file.mimes' => 'El contrato debe ser una imagen (jpg, jpeg, png) o un PDF.',
            'contract_file.max'   => 'El archivo no debe superar los 2MB de tamaño.',
        ];
    }

    /**
     * Si la validación falla, guardamos el ID en sesión para reabrir el modal.
     */
    protected function failedValidation(Validator $validator)
    {
        $seller = $this->route('seller');
        $sellerId = is_object($seller) ? $seller->id : $seller;

        // Importante: Usamos 'edit_seller_id' para que coincida con el @php de tu modal
        session()->flash('edit_seller_id', $sellerId);

        throw new ValidationException($validator);
    }
}