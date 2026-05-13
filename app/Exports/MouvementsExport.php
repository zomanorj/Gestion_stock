<?php

namespace App\Exports;

use App\Models\Mouvement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * Export Excel des mouvements de stock avec filtrage par période.
 * Implémente FromCollection, WithHeadings, WithStyles et ShouldAutoSize.
 */
class MouvementsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Date de début du filtre (nullable).
     */
    protected ?string $dateDebut;

    /**
     * Date de fin du filtre (nullable).
     */
    protected ?string $dateFin;

    /**
     * Constructeur.
     *
     * @param string|null $dateDebut Date de début au format Y-m-d
     * @param string|null $dateFin Date de fin au format Y-m-d
     */
    public function __construct(?string $dateDebut = null, ?string $dateFin = null)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }

    /**
     * Retourne la collection de mouvements à exporter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Mouvement::query()
            ->with(['produit', 'user'])
            ->orderBy('created_at', 'desc');

        // Filtre par date de début
        if ($this->dateDebut) {
            $query->whereDate('created_at', '>=', $this->dateDebut);
        }

        // Filtre par date de fin
        if ($this->dateFin) {
            $query->whereDate('created_at', '<=', $this->dateFin);
        }

        return $query->get()->map(function (Mouvement $mouvement) {
            return [
                $mouvement->created_at?->format('d/m/Y H:i'),
                $mouvement->produit?->nom ?? '—',
                $mouvement->produit?->code_produit ?? '—',
                ucfirst($mouvement->type),
                $mouvement->quantite,
                $mouvement->motif ?? '—',
                $mouvement->user?->name ?? '—',
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
            'Date',
            'Produit',
            'Code produit',
            'Type',
            'Quantité',
            'Motif',
            'Utilisateur',
        ];
    }

    /**
     * Styles du tableau : en-têtes en gras avec fond sombre.
     * Lignes Entrée en vert clair, Sortie en rouge clair.
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style des en-têtes : fond #2c3e50, texte blanc, gras
        $sheet->getStyle('A1:G1')->applyFromArray([
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

        // Appliquer des couleurs selon le type de mouvement
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            // Colonne D (4) contient le type
            $type = $sheet->getCell('D' . $row)->getValue();
            
            if ($type === 'Entrée') {
                // Fond vert très clair pour les entrées
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFe8f5e9'],
                    ],
                ]);
            } elseif ($type === 'Sortie') {
                // Fond rouge très clair pour les sorties
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFe0e0'],
                    ],
                ]);
            }
        }

        // Bordures pour toutes les cellules
        $sheet->getStyle('A1:G' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ]);
    }
}