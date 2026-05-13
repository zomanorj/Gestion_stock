<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation création d'un produit (inclut upload d'image optionnel).
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'code_produit' => ['required', 'string', 'max:100', 'unique:produits,code_produit'],
            'description' => ['nullable', 'string', 'max:10000'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            'fournisseur_id' => ['nullable', 'integer', 'exists:fournisseurs,id'],
            'prix_achat' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'prix_vente' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'quantite_stock' => ['required', 'integer', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'code_produit' => 'code produit',
            'categorie_id' => 'catégorie',
            'fournisseur_id' => 'fournisseur',
            'prix_achat' => 'prix d\'achat',
            'prix_vente' => 'prix de vente',
            'quantite_stock' => 'quantité en stock',
            'seuil_alerte' => 'seuil d\'alerte',
            'image' => 'image',
        ];
    }
}
