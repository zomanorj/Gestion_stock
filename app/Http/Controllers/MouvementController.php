<?php

namespace App\Http\Controllers;

use App\Exports\MouvementsExport;
use App\Http\Requests\StoreMouvementEntreeRequest;
use App\Http\Requests\StoreMouvementSortieRequest;
use App\Models\Mouvement;
use App\Models\Produit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Historique des mouvements et formulaires entrée / sortie de stock.
 */
class MouvementController extends Controller
{
    /**
     * Historique paginé avec filtres type et plage de dates.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'type' => ['nullable', 'in:entrée,sortie'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ]);

        $type = $request->string('type')->toString() ?: null;
        $dateDebut = $request->date('date_debut');
        $dateFin = $request->date('date_fin');

        $mouvements = Mouvement::query()
            ->with(['produit', 'user'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($dateDebut, fn ($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mouvements.index', compact('mouvements', 'type', 'dateDebut', 'dateFin'));
    }

    /**
     * Formulaire d'entrée de stock.
     */
    public function entree(): View
    {
        $produits = Produit::query()->orderBy('nom')->get(['id', 'nom', 'code_produit', 'quantite_stock']);

        return view('mouvements.entree', compact('produits'));
    }

    /**
     * Enregistre une entrée : transaction + mise à jour du stock + trace utilisateur.
     */
    public function storeEntree(StoreMouvementEntreeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $produit = Produit::query()->lockForUpdate()->findOrFail($validated['produit_id']);
            $produit->increment('quantite_stock', $validated['quantite']);

            Mouvement::query()->create([
                'produit_id' => $produit->id,
                'type' => Mouvement::TYPE_ENTREE,
                'quantite' => $validated['quantite'],
                'motif' => $validated['motif'] ?? null,
                'user_id' => $request->user()->id,
            ]);
        });

        return to_route('mouvements.index')->with('success', 'Entrée de stock enregistrée.');
    }

    /**
     * Formulaire de sortie de stock.
     */
    public function sortie(): View
    {
        $produits = Produit::query()->orderBy('nom')->get(['id', 'nom', 'code_produit', 'quantite_stock']);

        // Préfixe pour l'appel AJAX du stock (évite les chemins relatifs incorrects)
        $stockApiPrefix = rtrim(url('/'), '/').'/api/produits/';

        return view('mouvements.sortie', compact('produits', 'stockApiPrefix'));
    }

    /**
     * Enregistre une sortie : vérifie le stock puis transaction.
     */
    public function storeSortie(StoreMouvementSortieRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $produit = Produit::query()->findOrFail($validated['produit_id']);

        if ($produit->quantite_stock < $validated['quantite']) {
            return back()
                ->withInput()
                ->with('error', sprintf(
                    'Stock insuffisant pour « %s » : disponible %d, demandé %d.',
                    $produit->nom,
                    $produit->quantite_stock,
                    $validated['quantite']
                ));
        }

        DB::transaction(function () use ($validated, $request) {
            $produit = Produit::query()->lockForUpdate()->findOrFail($validated['produit_id']);

            if ($produit->quantite_stock < $validated['quantite']) {
                // Sécurité si concurrence entre deux requêtes
                throw ValidationException::withMessages([
                    'quantite' => 'Stock insuffisant au moment de l\'enregistrement (concurrence).',
                ]);
            }

            $produit->decrement('quantite_stock', $validated['quantite']);

            Mouvement::query()->create([
                'produit_id' => $produit->id,
                'type' => Mouvement::TYPE_SORTIE,
                'quantite' => $validated['quantite'],
                'motif' => $validated['motif'] ?? null,
                'user_id' => $request->user()->id,
            ]);
        });

        return to_route('mouvements.index')->with('success', 'Sortie de stock enregistrée.');
    }

    /**
     * Export Excel des mouvements avec filtre par dates.
     */
    public function exportExcel(Request $request)
    {
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        
        $filename = 'mouvements-' . now()->format('Y-m-d');
        if ($dateDebut && $dateFin) {
            $filename .= '-du-' . $dateDebut . '-au-' . $dateFin;
        } elseif ($dateDebut) {
            $filename .= '-depuis-' . $dateDebut;
        } elseif ($dateFin) {
            $filename .= '-jusqu-au-' . $dateFin;
        }
        
        return Excel::download(
            new MouvementsExport($dateDebut, $dateFin),
            $filename . '.xlsx'
        );
    }
}
