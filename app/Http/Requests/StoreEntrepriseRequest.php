<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntrepriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'       => ['required', 'string', 'max:150'],
            'siret'     => ['nullable', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'pays'      => ['nullable', 'string', 'max:100'],
            'devise'    => ['nullable', 'string', 'max:10'],
            'logo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'entreprise est obligatoire.',
        ];
    }
}
