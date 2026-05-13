<?php

namespace App\Http\Controllers;

use App\Exports\ProduitsExport;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProduitRequest;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * CRUD des produits : filtres, pagination, upload d'image.
 */
class ProduitController extends Controller
{
    /**
     * Liste avec pagination (12), filtres catégorie / fournisseur et recherche nom ou code.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'categorie_id' => ['nullable', 'integer', 'exists:categories,id'],
            'fournisseur_id' => ['nullable', 'integer', 'exists:fournisseurs,id'],
        ]);

        $search = $request->string('search')->toString();
        $categorieId = $request->integer('categorie_id') ?: null;
        $fournisseurId = $request->integer('fournisseur_id') ?: null;

        $produits = Produit::query()
            ->with(['categorie', 'fournisseur'])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(function ($q2) use ($like) {
                    $q2->where('nom', 'like', $like)
                        ->orWhere('code_produit', 'like', $like);
                });
            })
            ->when($categorieId, fn ($q) => $q->where('categorie_id', $categorieId))
            ->when($fournisseurId, fn ($q) => $q->where('fournisseur_id', $fournisseurId))
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $categories = Categorie::query()->orderBy('name')->get();
        $fournisseurs = Fournisseur::query()->orderBy('name')->get();

        return view('produits.index', compact(
            'produits',
            'search',
            'categorieId',
            'fournisseurId',
            'categories',
            'fournisseurs'
        ));
    }

    public function create(): View
    {
        $categories = Categorie::query()->orderBy('name')->get();
        $fournisseurs = Fournisseur::query()->orderBy('name')->get();

        return view('produits.create', compact('categories', 'fournisseurs'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        Produit::query()->create($data);

        return to_route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Fiche détail + mouvements paginés + statistiques.
     */
    public function show(Produit $produit): View
    {
        $produit->load(['categorie', 'fournisseur']);

        // Mouvements paginés (20 par page)
        $mouvements = $produit->mouvements()
            ->with('user')
            ->latest()
            ->paginate(20);

        // Stats pour l'onglet statistiques
        $totalEntree = $produit->mouvements()->where('type', 'entrée')->sum('quantite');
        $totalSortie = $produit->mouvements()->where('type', 'sortie')->sum('quantite');
        $totalMouvements = $produit->mouvements()->count();
        $nbMois = max(1, $produit->created_at->diffInMonths(now()) + 1);
        $mouvParMois = round($totalMouvements / $nbMois, 1);

        // Évolution du stock dans le temps (30 derniers mouvements, inversés pour le graphique)
        $derniersMouvements = $produit->mouvements()
            ->latest()
            ->take(30)
            ->get()
            ->reverse()
            ->values();

        $evolutionStock = [];
        $stockCumule = 0;
        foreach ($derniersMouvements as $m) {
            if ($m->type === 'entrée') {
                $stockCumule += $m->quantite;
            } else {
                $stockCumule -= $m->quantite;
            }
            $evolutionStock[] = [
                'date' => $m->created_at->format('d/m Y'),
                'stock' => $stockCumule,
            ];
        }

        // Comptages par type
        $nbEntrees = $produit->mouvements()->where('type', 'entrée')->count();
        $nbSorties = $produit->mouvements()->where('type', 'sortie')->count();

        return view('produits.show', compact(
            'produit',
            'mouvements',
            'totalEntree',
            'totalSortie',
            'totalMouvements',
            'mouvParMois',
            'evolutionStock',
            'nbEntrees',
            'nbSorties'
        ));
    }

    public function edit(Produit $produit): View
    {
        $categories = Categorie::query()->orderBy('name')->get();
        $fournisseurs = Fournisseur::query()->orderBy('name')->get();

        return view('produits.edit', compact('produit', 'categories', 'fournisseurs'));
    }

    public function update(UpdateProduitRequest $request, Produit $produit): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            // Remplacer l'image : supprimer l'ancienne si présente
            if ($produit->image && Storage::disk('public')->exists($produit->image)) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        return to_route('produits.index')->with('success', 'Produit mis à jour.');
    }

    /**
     * Supprime le produit et son fichier image sur le disque public.
     */
    public function destroy(Produit $produit): RedirectResponse
    {
        if ($produit->image && Storage::disk('public')->exists($produit->image)) {
            Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        return to_route('produits.index')->with('success', 'Produit supprimé.');
    }

    /**
     * Réponse JSON : stock courant (pour affichage dynamique sur le formulaire de sortie).
     */
    public function stockJson(Produit $produit): JsonResponse
    {
        return response()->json([
            'id' => $produit->id,
            'nom' => $produit->nom,
            'code_produit' => $produit->code_produit,
            'quantite_stock' => $produit->quantite_stock,
        ]);
    }

    /**
     * Export Excel des produits.
     * Accepte ?alerte_only=1 pour n'exporter que les produits en alerte.
     */
    public function exportExcel(Request $request)
    {
        $alerteOnly = $request->boolean('alerte_only');
        
        return Excel::download(
            new ProduitsExport($alerteOnly),
            $alerteOnly ? 'produits-alerte-' . now()->format('Y-m-d') . '.xlsx' : 'produits-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export PDF du rapport des produits en alerte.
     * Retourne un stream pour visualisation dans le navigateur.
     */
    public function exportPdf()
    {
        // Récupérer tous les produits en alerte
        $produits = Produit::query()
            ->with(['categorie', 'fournisseur'])
            ->whereColumn('quantite_stock', '<=', 'seuil_alerte')
            ->orderBy('nom')
            ->get();

        $pdf = Pdf::loadView('pdf.produits-alerte', compact('produits'));
        
        return $pdf->stream('rapport-produits-alerte-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export PDF de la fiche individuelle d'un produit.
     */
    public function ficheProductPdf(Produit $produit): \Illuminate\Http\Response
    {
        $produit->load(['categorie', 'fournisseur', 'mouvements.user']);
        
        $mouvements = $produit->mouvements()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $pdf = Pdf::loadView('pdf.fiche-produit', compact('produit', 'mouvements'));
        
        return $pdf->stream('fiche-produit-' . $produit->code_produit . '.pdf');
    }
}
