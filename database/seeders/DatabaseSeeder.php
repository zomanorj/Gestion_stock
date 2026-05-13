<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Jeu de données de démonstration GestiStock (français).
     * Recommandé : php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        // Réinitialise le cache des permissions Spatie (important après création de rôles)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {
            // --- Nettoyage des tables métier (respecter l'ordre des clés étrangères) ---
            Mouvement::query()->delete();
            Produit::query()->delete();
            Categorie::query()->delete();
            Fournisseur::query()->delete();

            // --- Rôles Spatie (guard web, cohérent avec Breeze) ---
            $roleAdmin = Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => 'web']
            );
            $roleGestionnaire = Role::firstOrCreate(
                ['name' => 'gestionnaire', 'guard_name' => 'web']
            );

            // --- Comptes de démo (mot de passe : password) ---
            $admin = User::updateOrCreate(
                ['email' => 'admin@gestistock.com'],
                [
                    'name' => 'Administrateur GestiStock',
                    'password' => Hash::make('password'),
                ]
            );
            $admin->syncRoles([$roleAdmin]);

            $gestionnaire = User::updateOrCreate(
                ['email' => 'gestionnaire@gestistock.com'],
                [
                    'name' => 'Gestionnaire entrepôt',
                    'password' => Hash::make('password'),
                ]
            );
            $gestionnaire->syncRoles([$roleGestionnaire]);

            // --- Catégories ---
            $categories = [
                'Électronique' => 'Composants et petit matériel électronique.',
                'Mobilier' => 'Bureaux, chaises et aménagement des locaux.',
                'Fournitures de bureau' => 'Consommables et accessoires quotidiens.',
                'Informatique' => 'Périphériques, réseau et matériel informatique.',
                'Outillage' => 'Outils portatifs et consommables pour atelier.',
            ];

            $categorieModels = collect();
            foreach ($categories as $nom => $desc) {
                $categorieModels->put(
                    $nom,
                    Categorie::create(['name' => $nom, 'description' => $desc])
                );
            }

            // --- Fournisseurs (données fictives réalistes) ---
            $fournisseurDistri = Fournisseur::create([
                'name' => 'DistriBuro SARL',
                'contact' => 'Marie Dupont',
                'email' => 'marie.dupont@distriburo.fr',
                'telephone' => '01 23 45 67 89',
                'adresse' => '12 rue Voltaire, 75011 Paris, France',
            ]);

            $fournisseurPrologis = Fournisseur::create([
                'name' => 'ProLogis Ouest',
                'contact' => 'Ahmed Benali',
                'email' => 'a.benali@prologis-ouest.fr',
                'telephone' => '02 98 11 22 33',
                'adresse' => 'ZA Keroman, 56100 Lorient, France',
            ]);

            $fournisseurTechnova = Fournisseur::create([
                'name' => 'TechNova Europe',
                'contact' => 'Sophie Martin',
                'email' => 'sophie.martin@technova.eu',
                'telephone' => '+33 4 72 55 00 11',
                'adresse' => '45 avenue Tony Garnier, 69007 Lyon, France',
            ]);

            // --- 12 produits répartis sur les catégories ---
            $produitsPayload = [
                [
                    'nom' => 'Écran LED 24 pouces',
                    'code_produit' => 'ELEC-ECR-001',
                    'description' => 'Dalle IPS, HDMI / DisplayPort, cadre fin.',
                    'categorie' => 'Électronique',
                    'fournisseur' => $fournisseurTechnova,
                    'prix_achat' => 89.90,
                    'prix_vente' => 149.00,
                    'quantite_stock' => 42,
                    'seuil_alerte' => 8,
                ],
                [
                    'nom' => 'Souris sans fil ergonomique',
                    'code_produit' => 'INFO-SOU-002',
                    'description' => 'Capteur optique 1600 DPI, pile AA incluse.',
                    'categorie' => 'Informatique',
                    'fournisseur' => $fournisseurTechnova,
                    'prix_achat' => 12.50,
                    'prix_vente' => 24.90,
                    'quantite_stock' => 120,
                    'seuil_alerte' => 15,
                ],
                [
                    'nom' => 'Clavier mécanique AZERTY',
                    'code_produit' => 'INFO-CLA-003',
                    'description' => 'Switches bleus, rétroéclairage blanc.',
                    'categorie' => 'Informatique',
                    'fournisseur' => $fournisseurDistri,
                    'prix_achat' => 45.00,
                    'prix_vente' => 79.00,
                    'quantite_stock' => 35,
                    'seuil_alerte' => 10,
                ],
                [
                    'nom' => 'Bureau droit 140 cm',
                    'code_produit' => 'MOB-BUR-004',
                    'description' => 'Plateau mélaminé chêne, piètement métal noir.',
                    'categorie' => 'Mobilier',
                    'fournisseur' => $fournisseurPrologis,
                    'prix_achat' => 118.00,
                    'prix_vente' => 199.00,
                    'quantite_stock' => 18,
                    'seuil_alerte' => 5,
                ],
                [
                    'nom' => 'Chaise de bureau ergonomique',
                    'code_produit' => 'MOB-CHA-005',
                    'description' => 'Accoudoirs réglables, support lombaire.',
                    'categorie' => 'Mobilier',
                    'fournisseur' => $fournisseurPrologis,
                    'prix_achat' => 95.00,
                    'prix_vente' => 165.00,
                    'quantite_stock' => 22,
                    'seuil_alerte' => 6,
                ],
                [
                    'nom' => 'Ramette papier A4 80 g (500 feuilles)',
                    'code_produit' => 'FDB-PAP-006',
                    'description' => 'Blanc extra, idéal impression laser.',
                    'categorie' => 'Fournitures de bureau',
                    'fournisseur' => $fournisseurDistri,
                    'prix_achat' => 3.20,
                    'prix_vente' => 5.50,
                    'quantite_stock' => 200,
                    'seuil_alerte' => 40,
                ],
                [
                    'nom' => 'Lot de 50 stylos à bille bleus',
                    'code_produit' => 'FDB-STY-007',
                    'description' => 'Pointe moyenne 1 mm, grip caoutchouc.',
                    'categorie' => 'Fournitures de bureau',
                    'fournisseur' => $fournisseurDistri,
                    'prix_achat' => 6.80,
                    'prix_vente' => 12.90,
                    'quantite_stock' => 85,
                    'seuil_alerte' => 20,
                ],
                [
                    'nom' => 'Perceuse-visseuse sans fil 18 V',
                    'code_produit' => 'OUT-PER-008',
                    'description' => '2 batteries Li-ion, coffret 24 embouts.',
                    'categorie' => 'Outillage',
                    'fournisseur' => $fournisseurTechnova,
                    'prix_achat' => 79.00,
                    'prix_vente' => 139.00,
                    'quantite_stock' => 14,
                    'seuil_alerte' => 4,
                ],
                [
                    'nom' => 'Coffret vis et chevilles assorties',
                    'code_produit' => 'OUT-VIS-009',
                    'description' => '600 pièces, boîte compartimentée.',
                    'categorie' => 'Outillage',
                    'fournisseur' => $fournisseurPrologis,
                    'prix_achat' => 11.40,
                    'prix_vente' => 19.90,
                    'quantite_stock' => 48,
                    'seuil_alerte' => 12,
                ],
                [
                    'nom' => 'Routeur Wi-Fi 6 bi-bande',
                    'code_produit' => 'INFO-ROU-010',
                    'description' => 'AX3000, 4 ports Gigabit, configuration web.',
                    'categorie' => 'Informatique',
                    'fournisseur' => $fournisseurTechnova,
                    'prix_achat' => 62.00,
                    'prix_vente' => 109.00,
                    'quantite_stock' => 28,
                    'seuil_alerte' => 8,
                ],
                [
                    'nom' => 'Lampe de bureau LED dimmable',
                    'code_produit' => 'FDB-LAM-011',
                    'description' => 'Température de couleur réglable, port USB.',
                    'categorie' => 'Fournitures de bureau',
                    'fournisseur' => $fournisseurDistri,
                    'prix_achat' => 18.90,
                    'prix_vente' => 34.50,
                    'quantite_stock' => 6,
                    'seuil_alerte' => 8,
                ],
                [
                    'nom' => 'Multimètre digital compact',
                    'code_produit' => 'ELEC-MUL-012',
                    'description' => 'Mesure tension, courant, résistance, test diode.',
                    'categorie' => 'Électronique',
                    'fournisseur' => $fournisseurTechnova,
                    'prix_achat' => 24.00,
                    'prix_vente' => 45.00,
                    'quantite_stock' => 3,
                    'seuil_alerte' => 5,
                ],
            ];

            $produitModels = collect();
            foreach ($produitsPayload as $row) {
                /** @var Fournisseur|null $f */
                $f = $row['fournisseur'] ?? null;
                $produitModels->push(Produit::create([
                    'nom' => $row['nom'],
                    'code_produit' => $row['code_produit'],
                    'description' => $row['description'],
                    'categorie_id' => $categorieModels->get($row['categorie'])->id,
                    'fournisseur_id' => $f?->id,
                    'prix_achat' => $row['prix_achat'],
                    'prix_vente' => $row['prix_vente'],
                    'quantite_stock' => $row['quantite_stock'],
                    'seuil_alerte' => $row['seuil_alerte'],
                    'image' => null,
                ]));
            }

            // Accès rapide par code produit pour les mouvements
            $parCode = $produitModels->keyBy('code_produit');

            // Historique de démo (dates réparties sur 7 jours) — les quantités en base sur
            // chaque produit restent celles définies ci-dessus (état courant pour le tableau).
            // --- 10 mouvements (entrées et sorties) ---
            $mouvementsPayload = [
                ['code' => 'FDB-PAP-006', 'type' => Mouvement::TYPE_ENTREE, 'qte' => 80, 'motif' => 'Réception livraison n° LB-2401', 'user' => $admin, 'jour' => 6],
                ['code' => 'INFO-SOU-002', 'type' => Mouvement::TYPE_SORTIE, 'qte' => 15, 'motif' => 'Dotation service comptabilité', 'user' => $gestionnaire, 'jour' => 6],
                ['code' => 'MOB-BUR-004', 'type' => Mouvement::TYPE_ENTREE, 'qte' => 10, 'motif' => 'Réapprovisionnement trimestriel', 'user' => $admin, 'jour' => 5],
                ['code' => 'ELEC-MUL-012', 'type' => Mouvement::TYPE_SORTIE, 'qte' => 2, 'motif' => 'Prêt atelier maintenance', 'user' => $gestionnaire, 'jour' => 5],
                ['code' => 'OUT-PER-008', 'type' => Mouvement::TYPE_ENTREE, 'qte' => 6, 'motif' => 'Commande urgente chantier', 'user' => $admin, 'jour' => 4],
                ['code' => 'INFO-ROU-010', 'type' => Mouvement::TYPE_SORTIE, 'qte' => 4, 'motif' => 'Installation salle serveur', 'user' => $gestionnaire, 'jour' => 3],
                ['code' => 'FDB-LAM-011', 'type' => Mouvement::TYPE_ENTREE, 'qte' => 12, 'motif' => 'Retour fournisseur (échange)', 'user' => $admin, 'jour' => 3],
                ['code' => 'MOB-CHA-005', 'type' => Mouvement::TYPE_SORTIE, 'qte' => 3, 'motif' => 'Affectation nouveaux postes', 'user' => $gestionnaire, 'jour' => 2],
                ['code' => 'INFO-CLA-003', 'type' => Mouvement::TYPE_ENTREE, 'qte' => 20, 'motif' => 'Stock initial complément', 'user' => $admin, 'jour' => 1],
                ['code' => 'FDB-STY-007', 'type' => Mouvement::TYPE_SORTIE, 'qte' => 25, 'motif' => 'Distribution agences régionales', 'user' => $gestionnaire, 'jour' => 0],
            ];

            foreach ($mouvementsPayload as $m) {
                $p = $parCode->get($m['code']);
                $createdAt = Carbon::now()->subDays($m['jour'])->setTime(10, 30, 0);

                // Dates personnalisées : hors $fillable, on hydrate puis save()
                $mv = new Mouvement([
                    'produit_id' => $p->id,
                    'type' => $m['type'],
                    'quantite' => $m['qte'],
                    'motif' => $m['motif'],
                    'user_id' => $m['user']->id,
                ]);
                $mv->created_at = $createdAt;
                $mv->updated_at = $createdAt;
                $mv->save();
            }
        });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
