<?php

namespace App\Exports;

use App\Models\Produit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * Export Excel des produits avec filtrage optionnel des alertes.
 * Implémente FromCollection, WithHeadings, WithStyles et ShouldAutoSize.
 */
class ProduitsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Filtre optionnel pour n'exporter que les produits en alerte.
     */
    protected bool $alerteOnly;

    /**
     * Constructeur.
     *
     * @param bool $alerteOnly Si true, exporte uniquement les produits dont le stock <= seuil alerte
     */
    public function __construct(bool $alerteOnly = false)
    {
        $this->alerteOnly = $alerteOnly;
    }

    /**
     * Retourne la collection de produits à exporter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Produit::query()
            ->with(['categorie', 'fournisseur'])
            ->orderBy('nom');

        // Filtre pour n'exporter que les produits en alerte
        if ($this->alerteOnly) {
            $query->whereColumn('quantite_stock', '<=', 'seuil_alerte');
        }

        return $query->get()->map(function (Produit $produit) {
            return [
                $produit->code_produit,
                $produit->nom,
                $produit->categorie?->name ?? '—',
                $produit->fournisseur?->name ?? '—',
                number_format($produit->prix_achat, 2, ',', ' '),
                number_format($produit->prix_vente, 2, ',', ' '),
                $produit->quantite_stock,
                $produit->seuil_alerte,
                $produit->estEnAlerte() ? 'Alerte' : 'OK',
            ];
        });
    }

    /**
     * En-têtes de colonnes.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Code',
            'Nom',
            'Catégorie',
            'Fournisseur',
            'Prix achat',
            'Prix vente',
            'Stock actuel',
            'Seuil alerte',
            'Statut',
        ];
    }

    /**
     * Styles du tableau : en-têtes en gras avec fond sombre.
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style des en-têtes : fond #2c3e50, texte blanc, gras
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2c3e50'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Appliquer un fond rouge clair aux lignes en alerte
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            // Colonne I (9) contient le statut
            $statut = $sheet->getCell('I' . $row)->getValue();
            if ($statut === 'Alerte') {
                $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFe0e0'],
                    ],
                ]);
            }
        }

        // Bordures pour toutes les cellules
        $sheet->getStyle('A1:I' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ]);
    }
}