<?php

use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Référentiels réservés à l'admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategorieController::class)
            ->except(['show'])
            ->parameters(['categories' => 'categorie']);

        Route::resource('fournisseurs', FournisseurController::class)
            ->except(['show'])
            ->parameters(['fournisseurs' => 'fournisseur']);

        // Création / édition / suppression produits (admin uniquement)
        Route::get('produits/create', [ProduitController::class, 'create'])->name('produits.create');
        Route::post('produits', [ProduitController::class, 'store'])->name('produits.store');
        Route::get('produits/{produit}/edit', [ProduitController::class, 'edit'])->name('produits.edit');
        Route::put('produits/{produit}', [ProduitController::class, 'update'])->name('produits.update');
        Route::patch('produits/{produit}', [ProduitController::class, 'update']);
        Route::delete('produits/{produit}', [ProduitController::class, 'destroy'])->name('produits.destroy');
    });

    // Lecture produits + mouvements : admin ou gestionnaire
    Route::middleware('role:admin|gestionnaire')->group(function () {
        // Routes d'export (à placer avant Route::resource pour éviter conflit avec {produit})
        Route::get('produits/export-excel', [ProduitController::class, 'exportExcel'])->name('produits.export.excel');
        Route::get('produits/export-pdf', [ProduitController::class, 'exportPdf'])->name('produits.export.pdf');
        Route::get('produits/{produit}/fiche-pdf', [ProduitController::class, 'ficheProductPdf'])->name('produits.fiche.pdf');
        
        Route::get('produits', [ProduitController::class, 'index'])->name('produits.index');
        Route::get('api/produits/{produit}/stock', [ProduitController::class, 'stockJson'])->name('api.produits.stock');
        Route::get('produits/{produit}', [ProduitController::class, 'show'])->name('produits.show');

        // Export mouvements
        Route::get('mouvements/export-excel', [MouvementController::class, 'exportExcel'])->name('mouvements.export.excel');

        Route::get('mouvements', [MouvementController::class, 'index'])->name('mouvements.index');
        Route::get('mouvements/entree', [MouvementController::class, 'entree'])->name('mouvements.entree');
        Route::post('mouvements/entree', [MouvementController::class, 'storeEntree'])->name('mouvements.entree.store');
        Route::get('mouvements/sortie', [MouvementController::class, 'sortie'])->name('mouvements.sortie');
        Route::post('mouvements/sortie', [MouvementController::class, 'storeSortie'])->name('mouvements.sortie.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
