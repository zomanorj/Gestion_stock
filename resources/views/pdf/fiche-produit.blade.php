<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche produit — {{ $produit->nom }}</title>
    <style>
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        /* En-tête */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header-logo {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .header-logo span {
            color: #e74c3c;
        }

        .header-ref {
            text-align: right;
            font-size: 9pt;
            color: #666;
        }

        /* Section titre */
        .product-title {
            font-size: 16pt;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* Layout 2 colonnes */
        .content {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
        }

        .col-left {
            flex: 1;
        }

        .col-right {
            flex: 1;
        }

        /* Blocs d'information */
        .info-block {
            margin-bottom: 20px;
        }

        .info-block h3 {
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #eee;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 10pt;
        }

        .info-value {
            color: #333;
            font-size: 10pt;
        }

        .info-value.highlight {
            font-weight: bold;
            color: #2c3e50;
        }

        /* Badge stock */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Description */
        .description {
            font-size: 10pt;
            color: #666;
            line-height: 1.6;
            text-align: justify;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #2c3e50;
        }

        /* Tableau mouvements */
        .movements-section {
            margin-top: 20px;
        }

        .movements-section h3 {
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
        }

        th.text-center, td.text-center {
            text-align: center;
        }

        th.text-right, td.text-right {
            text-align: right;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        td {
            padding: 6px;
            vertical-align: middle;
        }

        /* Type mouvement */
        .type-entree {
            color: #28a745;
            font-weight: 600;
        }

        .type-sortie {
            color: #dc3545;
            font-weight: 600;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8.5pt;
            color: #999;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <div class="header-logo">Gesti<span>Stock</span></div>
        <div class="header-ref">
            <div>Fiche produit</div>
            <div>Réf : {{ $produit->code_produit }}</div>
        </div>
    </div>

    {{-- Titre --}}
    <h1 class="product-title">{{ $produit->nom }}</h1>

    {{-- Contenu principal --}}
    <div class="content">
        {{-- Colonne gauche --}}
        <div class="col-left">
            <div class="info-block">
                <h3>Informations générales</h3>
                <div class="info-row">
                    <span class="info-label">Code produit</span>
                    <span class="info-value highlight">{{ $produit->code_produit }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Catégorie</span>
                    <span class="info-value">{{ $produit->categorie?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fournisseur</span>
                    <span class="info-value">{{ $produit->fournisseur?->name ?? '—' }}</span>
                </div>
            </div>

            <div class="info-block">
                <h3>Prix</h3>
                <div class="info-row">
                    <span class="info-label">Prix d'achat</span>
                    <span class="info-value highlight">{{ number_format($produit->prix_achat, 2, ',', ' ') }} Ar</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Prix de vente</span>
                    <span class="info-value highlight">{{ number_format($produit->prix_vente, 2, ',', ' ') }} Ar</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Marge</span>
                    <span class="info-value">{{ number_format((($produit->prix_vente - $produit->prix_achat) / $produit->prix_vente) * 100, 1, ',', ' ') }} %</span>
                </div>
            </div>

            @if($produit->description)
            <div class="info-block">
                <h3>Description</h3>
                <div class="description">{{ $produit->description }}</div>
            </div>
            @endif
        </div>

        {{-- Colonne droite --}}
        <div class="col-right">
            <div class="info-block">
                <h3>Stock</h3>
                <div class="info-row">
                    <span class="info-label">Quantité disponible</span>
                    <span class="info-value highlight" style="font-size: 14pt;">{{ $produit->quantite_stock }} unités</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Seuil d'alerte</span>
                    <span class="info-value">{{ $produit->seuil_alerte }} unités</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut</span>
                    @if($produit->quantite_stock == 0)
                        <span class="badge badge-danger">Rupture de stock</span>
                    @elseif($produit->quantite_stock <= $produit->seuil_alerte)
                        <span class="badge badge-warning">Stock faible</span>
                    @else
                        <span class="badge badge-success">Stock normal</span>
                    @endif
                </div>
                <div class="info-row">
                    <span class="info-label">Valeur du stock</span>
                    <span class="info-value highlight">{{ number_format($produit->quantite_stock * $produit->prix_achat, 2, ',', ' ') }} Ar</span>
                </div>
            </div>

            <div class="info-block">
                <h3>Métriques</h3>
                <div class="info-row">
                    <span class="info-label">Créé le</span>
                    <span class="info-value">{{ $produit->created_at?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dernière modification</span>
                    <span class="info-value">{{ $produit->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total mouvements</span>
                    <span class="info-value">{{ $produit->mouvements->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Derniers mouvements --}}
    <div class="movements-section">
        <h3><i class="bi bi-clock-history"></i> 10 derniers mouvements</h3>
        @if($mouvements && $mouvements->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-center">Quantité</th>
                        <th>Motif</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mouvements as $m)
                        <tr>
                            <td>{{ $m->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($m->type === 'entrée')
                                    <span class="type-entree">Entrée</span>
                                @else
                                    <span class="type-sortie">Sortie</span>
                                @endif
                            </td>
                            <td class="text-center"><strong>{{ $m->quantite }}</strong></td>
                            <td>{{ $m->motif ?? '—' }}</td>
                            <td>{{ $m->user?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; padding: 20px; font-style: italic;">
                Aucun mouvement enregistré pour ce produit.
            </p>
        @endif
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        Fiche générée le {{ now()->format('d/m/Y à H:i') }} par GestiStock — Application de gestion de stock
    </div>
</body>
</html>