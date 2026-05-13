<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fournisseur (référentiel).
 */
class Fournisseur extends Model
{
    use HasFactory;

    protected $table = 'fournisseurs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'contact',
        'email',
        'telephone',
        'adresse',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Produits associés à ce fournisseur.
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'fournisseur_id');
    }
}
