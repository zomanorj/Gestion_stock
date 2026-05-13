<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use App\Models\Fournisseur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD des fournisseurs (admin uniquement via routes).
 */
class FournisseurController extends Controller
{
    /**
     * Liste paginée avec recherche sur nom, contact ou e-mail.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $request->string('search')->toString();

        $fournisseurs = Fournisseur::query()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(function ($q2) use ($like) {
                    $q2->where('name', 'like', $like)
                        ->orWhere('contact', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs', 'search'));
    }

    public function create(): View
    {
        return view('fournisseurs.create');
    }

    public function store(StoreFournisseurRequest $request): RedirectResponse
    {
        Fournisseur::query()->create($request->validated());

        return to_route('fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit(Fournisseur $fournisseur): View
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(UpdateFournisseurRequest $request, Fournisseur $fournisseur): RedirectResponse
    {
        $fournisseur->update($request->validated());

        return to_route('fournisseurs.index')->with('success', 'Fournisseur mis à jour.');
    }

    /**
     * Suppression interdite si des produits sont encore liés à ce fournisseur.
     */
    public function destroy(Fournisseur $fournisseur): RedirectResponse
    {
        if ($fournisseur->produits()->exists()) {
            return to_route('fournisseurs.index')
                ->with('error', 'Impossible de supprimer ce fournisseur : des produits y sont encore liés.');
        }

        $fournisseur->delete();

        return to_route('fournisseurs.index')->with('success', 'Fournisseur supprimé.');
    }
}
