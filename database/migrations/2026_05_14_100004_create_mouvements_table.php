<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historique des mouvements de stock (entrées / sorties).
 * Dépend de `produits` et `users`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->cascadeOnDelete()
                ->comment('Produit concerné');

            // Enum métier : entrée (réception) ou sortie (retrait / vente interne)
            $table->enum('type', ['entrée', 'sortie'])->comment('Sens du mouvement');

            $table->unsignedInteger('quantite')->comment('Quantité déplacée (toujours positive)');
            $table->string('motif')->nullable()->comment('Commentaire ou motif libre');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Utilisateur ayant enregistré le mouvement');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
