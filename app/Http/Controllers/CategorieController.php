<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD des catégories (admin uniquement via routes).
 */
class CategorieController extends Controller
{
    /**
     * Liste paginée avec recherche par nom.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $request->string('search')->toString();

        $categories = Categorie::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategorieRequest $request): RedirectResponse
    {
        Categorie::query()->create($request->validated());

        return to_route('categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Categorie $categorie): View
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie): RedirectResponse
    {
        $categorie->update($request->validated());

        return to_route('categories.index')->with('success', 'Catégorie mise à jour.');
    }

    /**
     * Suppression interdite si des produits référencent encore cette catégorie.
     */
    public function destroy(Categorie $categorie): RedirectResponse
    {
        if ($categorie->produits()->exists()) {
            return to_route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie : des produits y sont encore liés.');
        }

        $categorie->delete();

        return to_route('categories.index')->with('success', 'Catégorie supprimée.');
    }
}
