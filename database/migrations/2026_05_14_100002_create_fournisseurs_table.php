<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des fournisseurs (référentiel métier).
 * Doit être créée avant `produits` (clé étrangère optionnelle).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Raison sociale ou nom du fournisseur');
            $table->string('contact')->nullable()->comment('Personne de contact');
            $table->string('email')->nullable()->comment('Adresse e-mail');
            $table->string('telephone')->nullable()->comment('Numéro de téléphone');
            $table->text('adresse')->nullable()->comment('Adresse postale complète');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
