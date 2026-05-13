<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Mouvement;
use App\Models\Produit;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Tableau de bord : indicateurs, alertes, derniers mouvements et données pour Chart.js.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Cartes statistiques ---
        $totalProduits = Produit::query()->count();

        $valeurStock = (float) Produit::query()
            ->selectRaw('COALESCE(SUM(quantite_stock * prix_achat), 0) as total')
            ->value('total');

        $nbAlertes = Produit::query()
            ->whereColumn('quantite_stock', '<=', 'seuil_alerte')
            ->count();

        $debutMois = Carbon::now()->startOfMonth();
        $nbMouvementsMois = Mouvement::query()
            ->where('created_at', '>=', $debutMois)
            ->count();

        // --- Listes ---
        $derniersMouvements = Mouvement::query()
            ->with(['produit', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $stocksFaibles = Produit::query()
            ->with(['categorie', 'fournisseur'])
            ->orderBy('quantite_stock')
            ->take(5)
            ->get();

        // --- Données graphique : 7 derniers jours (entrées vs sorties, somme des quantités) ---
        $chartLabels = [];
        $chartEntrees = [];
        $chartSorties = [];

        for ($i = 6; $i >= 0; $i--) {
            $jour = Carbon::now()->copy()->subDays($i)->startOfDay();
            $chartLabels[] = $jour->locale('fr')->isoFormat('DD/MM');

            $chartEntrees[] = (int) Mouvement::query()
                ->where('type', Mouvement::TYPE_ENTREE)
                ->whereDate('created_at', $jour->toDateString())
                ->sum('quantite');

            $chartSorties[] = (int) Mouvement::query()
                ->where('type', Mouvement::TYPE_SORTIE)
                ->whereDate('created_at', $jour->toDateString())
                ->sum('quantite');
        }

        // --- Données graphique : 30 derniers jours (entrées vs sorties) ---
        $chartLabels30 = [];
        $chartEntrees30 = [];
        $chartSorties30 = [];

        for ($i = 29; $i >= 0; $i--) {
            $jour = Carbon::now()->copy()->subDays($i)->startOfDay();
            $chartLabels30[] = $jour->locale('fr')->isoFormat('DD/MM');

            $chartEntrees30[] = (int) Mouvement::query()
                ->where('type', Mouvement::TYPE_ENTREE)
                ->whereDate('created_at', $jour->toDateString())
                ->sum('quantite');

            $chartSorties30[] = (int) Mouvement::query()
                ->where('type', Mouvement::TYPE_SORTIE)
                ->whereDate('created_at', $jour->toDateString())
                ->sum('quantite');
        }

        // --- Top 5 produits les plus mouvementés (nb mouvements total) ---
        $topProduits = Produit::withCount('mouvements')
            ->orderByDesc('mouvements_count')
            ->take(5)
            ->get();

        // --- Répartition stock par catégorie ---
        $repartitionCategories = Categorie::withSum('produits', 'quantite_stock')
            ->having('produits_sum_quantite_stock', '>', 0)
            ->get();

        // --- Taux de rotation (sorties ce mois / stock moyen) par produit top 5 ---
        $debutMois = Carbon::now()->startOfMonth();
        $tauxRotation = [];
        foreach ($topProduits as $produit) {
            $sortiesMois = Mouvement::query()
                ->where('produit_id', $produit->id)
                ->where('type', Mouvement::TYPE_SORTIE)
                ->where('created_at', '>=', $debutMois)
                ->sum('quantite');
            $stockMoyen = $produit->quantite_stock > 0 ? $produit->quantite_stock : 1;
            $tauxRotation[$produit->nom] = round($sortiesMois / $stockMoyen, 2);
        }

        return view('dashboard', compact(
            'totalProduits',
            'valeurStock',
            'nbAlertes',
            'nbMouvementsMois',
            'derniersMouvements',
            'stocksFaibles',
            'chartLabels',
            'chartEntrees',
            'chartSorties',
            'chartLabels30',
            'chartEntrees30',
            'chartSorties30',
            'topProduits',
            'repartitionCategories',
            'tauxRotation'
        ));
    }
}
