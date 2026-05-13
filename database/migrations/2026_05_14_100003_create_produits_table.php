<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des produits en stock.
 * Dépend de `categories` et `fournisseurs`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->comment('Désignation du produit');
            $table->string('code_produit')->unique()->comment('Référence interne unique');
            $table->text('description')->nullable();

            $table->foreignId('categorie_id')
                ->constrained('categories')
                ->cascadeOnDelete()
                ->comment('Catégorie obligatoire');

            $table->foreignId('fournisseur_id')
                ->nullable()
                ->constrained('fournisseurs')
                ->nullOnDelete()
                ->comment('Fournisseur optionnel ; ce champ passe à NULL si le fournisseur est supprimé');

            $table->decimal('prix_achat', 10, 2)->default(0)->comment('Prix d\'achat unitaire');
            $table->decimal('prix_vente', 10, 2)->default(0)->comment('Prix de vente unitaire');
            $table->unsignedInteger('quantite_stock')->default(0)->comment('Quantité disponible');
            $table->unsignedInteger('seuil_alerte')->default(5)->comment('Seuil pour alerte stock faible');
            $table->string('image')->nullable()->comment('Chemin relatif sous storage/public/produits');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
