<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport — Produits en alerte</title>
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
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header-logo {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .header-logo span {
            color: #e74c3c;
        }

        .header-date {
            text-align: right;
            font-size: 9pt;
            color: #666;
        }

        .header-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-title h1 {
            font-size: 16pt;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Tableau */
        .table-container {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9.5pt;
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

        /* Ligne rouge si stock = 0 */
        tbody tr.stock-zero {
            background-color: #ffcccc !important;
        }

        /* Ligne orange si stock > 0 mais <= seuil */
        tbody tr.stock-low {
            background-color: #ffe0b2;
        }

        td {
            padding: 8px;
            vertical-align: middle;
        }

        /* Pied de page */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }

        /* Statistiques */
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #e74c3c;
        }

        .stat-label {
            font-size: 8.5pt;
            color: #666;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <div class="header-logo">Gesti<span>Stock</span></div>
        <div class="header-date">
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    {{-- Titre --}}
    <div class="header-title">
        <h1>Rapport — Produits en alerte</h1>
    </div>

    {{-- Statistiques --}}
    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $produits->count() }}</div>
            <div class="stat-label">Produits en alerte</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $produits->where('quantite_stock', 0)->count() }}</div>
            <div class="stat-label">Rupture de stock</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($produits->sum(function($p) { return $p->quantite_stock * $p->prix_achat; }), 0, ',', ' ') }} Ar</div>
            <div class="stat-label">Valeur totale stock</div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th class="text-center">Stock actuel</th>
                    <th class="text-center">Seuil</th>
                    <th class="text-right">Valeur stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produits as $p)
                    @php
                        $stockClass = '';
                        if ($p->quantite_stock == 0) {
                            $stockClass = 'stock-zero';
                        } elseif ($p->quantite_stock <= $p->seuil_alerte) {
                            $stockClass = 'stock-low';
                        }
                        $valeurStock = number_format($p->quantite_stock * $p->prix_achat, 2, ',', ' ');
                    @endphp
                    <tr class="{{ $stockClass }}">
                        <td><strong>{{ $p->code_produit }}</strong></td>
                        <td>{{ $p->nom }}</td>
                        <td>{{ $p->categorie?->name ?? '—' }}</td>
                        <td class="text-center">
                            <strong>{{ $p->quantite_stock }}</strong>
                            @if($p->quantite_stock == 0)
                                <span style="color: #c0392b; font-size: 8pt;">⚠ Rupture</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $p->seuil_alerte }}</td>
                        <td class="text-right">{{ $valeurStock }} Ar</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            Aucun produit en alerte.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        Généré le {{ now()->format('d/m/Y H:i') }} par GestiStock — Application de gestion de stock
    </div>
</body>
</html>