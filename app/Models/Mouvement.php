<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne d'historique : entrée ou sortie de stock pour un produit.
 */
class Mouvement extends Model
{
    use HasFactory;

    protected $table = 'mouvements';

    public const TYPE_ENTREE = 'entrée';

    public const TYPE_SORTIE = 'sortie';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'produit_id',
        'type',
        'quantite',
        'motif',
        'user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Produit concerné par le mouvement.
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Utilisateur ayant saisi le mouvement (peut être null si compte supprimé).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Vrai si le mouvement est une entrée de stock.
     */
    public function estEntree(): bool
    {
        return $this->type === self::TYPE_ENTREE;
    }

    /**
     * Vrai si le mouvement est une sortie de stock.
     */
    public function estSortie(): bool
    {
        return $this->type === self::TYPE_SORTIE;
    }
}
