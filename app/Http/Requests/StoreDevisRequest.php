<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_nom' => ['required', 'string', 'max:150'],
            'client_email' => ['nullable', 'email'],
            'client_telephone' => ['nullable', 'string', 'max:20'],
            'client_adresse' => ['nullable', 'string', 'max:255'],
            'date_expiration' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.produit_id' => ['required', 'integer', 'exists:produits,id'],
            'lignes.*.quantite' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_nom.required' => 'Le nom du client est obligatoire.',
            'date_expiration.required' => 'La date d\'expiration est obligatoire.',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur.',
            'lignes.required' => 'Ajoutez au moins une ligne au devis.',
            'lignes.*.produit_id.required' => 'Sélectionnez un produit.',
            'lignes.*.quantite.min' => 'La quantité doit être au moins 1.',
        ];
    }
}
