<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        /* Auto-générer un SKU si le champ est vide */
        if (empty($this->reference_sku)) {
            $base = $this->nom ? strtoupper(Str::slug($this->nom, '-')) : 'PROD';
            $this->merge(['reference_sku' => $base . '-' . strtoupper(Str::random(5))]);
        }
    }

    public function rules(): array
    {
        $entrepriseId = session('entreprise_id');
        $produitId    = $this->route('produit')?->id;

        return [
            'nom'           => ['required', 'string', 'max:150'],
            'description'   => ['nullable', 'string'],
            'reference_sku' => [
                'required', 'string', 'max:100',
                "unique:produits,reference_sku,{$produitId},id,entreprise_id,{$entrepriseId}",
            ],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'stock_actuel'  => ['required', 'integer', 'min:0'],
            'stock_minimum' => ['required', 'integer', 'min:0'],
            'categorie'     => ['nullable', 'string', 'max:100'],
            'actif'         => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'           => 'Le nom du produit est obligatoire.',
            'nom.max'                => 'Le nom ne peut pas dépasser 150 caractères.',
            'reference_sku.required' => 'La référence SKU est obligatoire.',
            'reference_sku.unique'   => 'Cette référence SKU est déjà utilisée par un autre produit.',
            'reference_sku.max'      => 'La référence SKU ne peut pas dépasser 100 caractères.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric'  => 'Le prix unitaire doit être un nombre.',
            'prix_unitaire.min'      => 'Le prix unitaire ne peut pas être négatif.',
            'stock_actuel.required'  => 'Le stock actuel est obligatoire.',
            'stock_actuel.integer'   => 'Le stock actuel doit être un nombre entier.',
            'stock_actuel.min'       => 'Le stock actuel ne peut pas être négatif.',
            'stock_minimum.required' => 'Le stock minimum est obligatoire.',
            'stock_minimum.integer'  => 'Le stock minimum doit être un nombre entier.',
            'stock_minimum.min'      => 'Le stock minimum ne peut pas être négatif.',
            'categorie.max'          => 'La catégorie ne peut pas dépasser 100 caractères.',
        ];
    }
}
