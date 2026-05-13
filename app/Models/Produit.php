<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Article en stock avec prix et seuil d'alerte.
 */
class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'code_produit',
        'description',
        'categorie_id',
        'fournisseur_id',
        'prix_achat',
        'prix_vente',
        'quantite_stock',
        'seuil_alerte',
        'image',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prix_achat' => 'decimal:2',
            'prix_vente' => 'decimal:2',
            'quantite_stock' => 'integer',
            'seuil_alerte' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Catégorie obligatoire du produit.
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Fournisseur optionnel.
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    /**
     * Historique des mouvements (entrées / sorties).
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class, 'produit_id');
    }

    /**
     * Indique si le stock est au ou sous le seuil d'alerte.
     */
    public function estEnAlerte(): bool
    {
        return $this->quantite_stock <= $this->seuil_alerte;
    }
}
