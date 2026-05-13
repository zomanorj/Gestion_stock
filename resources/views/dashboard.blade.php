@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    @php
        /* Le contrôleur envoie $stocksFaibles (5 produits au stock le plus bas). Si $produitsEnAlerte existe plus tard, il sera utilisé à la place. */
        $listeAlerte = isset($produitsEnAlerte) ? $produitsEnAlerte : ($stocksFaibles ?? []);
    @endphp

    {{-- En-tête de page --}}
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Tableau de bord</h2>
            <p class="gs-page-subtitle">Vue d'ensemble de votre stock</p>
        </div>
    </div>

    {{-- Rangée : 4 cartes indicateurs --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="gs-metric-card">
                <div class="gs-metric-label">Total produits</div>
                <p class="gs-metric-value">{{ $totalProduits ?? 0 }}</p>
                <div class="gs-metric-subtext">Référencés dans le système</div>
                <div class="gs-metric-icon gs-metric-icon-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="gs-metric-card">
                <div class="gs-metric-label">Valeur du stock</div>
                <p class="gs-metric-value">{{ number_format($valeurStock ?? 0, 0, ',', ' ') }} Ar</p>
                <div class="gs-metric-subtext">Stock total estimé</div>
                <div class="gs-metric-icon gs-metric-icon-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="gs-metric-card">
                <div class="gs-metric-label">Alertes stock</div>
                <p class="gs-metric-value {{ ($nbAlertes ?? 0) > 0 ? 'text-danger' : '' }}">{{ $nbAlertes ?? 0 }}</p>
                <div class="gs-metric-subtext">Produits en stock critique</div>
                <div class="gs-metric-icon gs-metric-icon-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="gs-metric-card">
                <div class="gs-metric-label">Mouvements ce mois</div>
                <p class="gs-metric-value">{{ $nbMouvementsMois ?? 0 }}</p>
                <div class="gs-metric-subtext">Ce mois-ci</div>
                <div class="gs-metric-icon gs-metric-icon-warning">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 2 : Graphiques Chart.js --}}
    <div class="row g-4 mt-2">
        {{-- Graphique 1 : Activité des 30 derniers jours (pleine largeur) --}}
        <div class="col-12">
            <div class="gs-card">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-activity me-2" style="color: var(--color-muted);"></i>
                        Activité des 30 derniers jours
                    </h3>
                </div>
                <div class="gs-card-body" style="height: 220px;">
                    <canvas id="chartActivite30"></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique 2 : Top 5 produits mouvementés (col-7) --}}
        <div class="col-lg-7">
            <div class="gs-card " style="margin-bottom: 16px;padding-bottom: 16px;">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-trophy me-2" style="color: var(--color-muted);"></i>
                        Top 5 produits mouvementés
                    </h3>
                </div>
                <div class="gs-card-body" style="height: 200px;">
                    <canvas id="chartTopProduits" ></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique 3 : Répartition stock par catégorie (col-5) --}}
        <div class="col-lg-5">
            <div class="gs-card" style="margin-bottom: 16px; padding-bottom: 16px;">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-pie-chart me-2" style="color: var(--color-muted);"></i>
                        Répartition stock par catégorie
                    </h3>
                </div>
                <div class="gs-card-body" style="height: 240px;">
                    <canvas id="chartCategories" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 3 : Derniers mouvements + Produits en alerte --}}
    <div class="row g-4">
        {{-- Derniers mouvements --}}
        <div class="col-lg-8">
            <div class="gs-card h-100">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-clock-history me-2" style="color: var(--color-muted);"></i>
                        Derniers mouvements
                    </h3>
                    <a href="{{ route('mouvements.index') }}" class="text-decoration-none" style="font-size: 13px; color: var(--color-primary); font-weight: 500;">
                        Voir l'historique <i class="bi bi-arrow-right ms-1" style="font-size: 11px;"></i>
                    </a>
                </div>
                <div class="gs-table-wrapper">
                    <table class="gs-table">
                        <thead>
                            <tr>
                                <th>Date / heure</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th class="text-end">Qté</th>
                                <th>Motif</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($derniersMouvements as $m)
                                <tr>
                                    <td class="text-nowrap" style="font-size: 12px; color: var(--color-muted);">
                                        {{ $m->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium">{{ $m->produit?->nom ?? '—' }}</span>
                                            @if($m->produit?->code_produit)
                                                <div style="font-size: 11px; color: var(--color-muted);">{{ $m->produit->code_produit }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($m->type === 'entrée')
                                            <span class="badge badge-success">Entrée</span>
                                        @else
                                            <span class="badge badge-danger">Sortie</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($m->type === 'entrée')
                                            <span style="color: var(--color-success); font-weight: 600;">+{{ $m->quantite }}</span>
                                        @else
                                            <span style="color: var(--color-danger); font-weight: 600;">−{{ $m->quantite }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 12px; color: var(--color-muted); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $m->motif ?? '' }}">
                                        {{ \Illuminate\Support\Str::limit($m->motif ?? '—', 25) }}
                                    </td>
                                    <td style="font-size: 12px;">{{ $m->user?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun mouvement récent.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Colonne droite : Produits en alerte --}}
        <div class="col-lg-4">
            <div class="gs-card mb-4">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-exclamation-octagon me-2" style="color: var(--color-muted);"></i>
                        Produits en alerte
                    </h3>
                </div>
                <div class="gs-card-body p-0">
                    @forelse ($listeAlerte as $p)
                        <div class="p-3 border-bottom" style="border-color: #f3f4f6 !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="fw-medium" style="font-size: 13px;">{{ $p->nom }}</div>
                                    <div style="font-size: 11px; color: var(--color-muted);">{{ $p->code_produit }}</div>
                                </div>
                                <span class="badge {{ $p->quantite_stock <= ($p->seuil_alerte ?? 10) / 2 ? 'badge-danger' : 'badge-warning' }} ms-2">
                                    {{ $p->quantite_stock }} / {{ $p->seuil_alerte }}
                                </span>
                            </div>
                            <div class="gs-progress">
                                @php
                                    $progressWidth = $p->seuil_alerte > 0 ? min(100, ($p->quantite_stock / $p->seuil_alerte) * 100) : 0;
                                    $progressClass = $p->quantite_stock <= ($p->seuil_alerte ?? 10) / 2 ? 'gs-progress-bar-danger' : 'gs-progress-bar-warning';
                                @endphp
                                <div class="gs-progress-bar {{ $progressClass }}" style="width: {{ $progressWidth }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-circle" style="font-size: 24px; color: var(--color-success); margin-bottom: 8px;"></i>
                            <p class="mb-0" style="font-size: 13px;">Aucune alerte de stock</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Répartition par catégorie --}}
            <div class="gs-card">
                <div class="gs-card-header">
                    <h3 class="gs-card-title">
                        <i class="bi bi-pie-chart me-2" style="color: var(--color-muted);"></i>
                        Catégories
                    </h3>
                </div>
                <div class="gs-card-body p-0">
                    @php
                        $couleurs = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
                        /* Récupérer les catégories depuis les produits */
                        $categoriesList = \App\Models\Categorie::withCount('produits')->orderBy('produits_count', 'desc')->take(6)->get();
                    @endphp
                    @forelse ($categoriesList as $index => $cat)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom" style="border-color: #f3f4f6 !important;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $couleurs[$index % count($couleurs)] }}; margin-right: 10px; flex-shrink: 0;"></span>
                            <span class="flex-grow-1" style="font-size: 13px;">{{ $cat->name }}</span>
                            <span class="fw-medium" style="font-size: 13px;">{{ $cat->produits_count }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4" style="font-size: 13px;">
                            Aucune catégorie
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Détruire les charts existants avant recréation
            ['chartActivite30', 'chartTopProduits', 'chartCategories'].forEach(id => {
                const existing = Chart.getChart(id);
                if (existing) existing.destroy();
            });

            // === Graphique 1 : Activité des 30 derniers jours (ligne) ===
            const ctx1 = document.getElementById('chartActivite30');
            if (ctx1 && typeof Chart !== 'undefined') {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels30 ?? []),
                        datasets: [
                            {
                                label: 'Entrées',
                                data: @json($chartEntrees30 ?? []),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#10b981',
                                pointHoverBackgroundColor: '#10b981',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                            },
                            {
                                label: 'Sorties',
                                data: @json($chartSorties30 ?? []),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#ef4444',
                                pointHoverBackgroundColor: '#ef4444',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 800,
                            easing: 'easeInOutQuart',
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 12,
                                    },
                                },
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#1f2937',
                                bodyColor: '#4b5563',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: true,
                                boxPadding: 4,
                                titleFont: {
                                    family: "'Inter', sans-serif",
                                    size: 13,
                                    weight: '600',
                                },
                                bodyFont: {
                                    family: "'Inter', sans-serif",
                                    size: 12,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11,
                                    },
                                    color: '#6b7280',
                                    maxTicksLimit: 10,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11,
                                    },
                                    color: '#6b7280',
                                },
                                grid: {
                                    color: '#f3f4f6',
                                },
                            },
                        },
                    },
                });
            }

            // === Graphique 2 : Top 5 produits mouvementés (barres horizontales) ===
            const ctx2 = document.getElementById('chartTopProduits');
            if (ctx2 && typeof Chart !== 'undefined') {
                const topProduitsData = @json($topProduits ?? []);
                const produitNames = topProduitsData.map(p => p.nom);
                const produitCounts = topProduitsData.map(p => p.mouvements_count);

                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: produitNames,
                        datasets: [{
                            label: 'Mouvements',
                            data: produitCounts,
                            backgroundColor: function(context) {
                                const index = context.dataIndex;
                                const gradient = context.chart.ctx.createLinearGradient(0, 0, 200, 0);
                                gradient.addColorStop(0, '#4f46e5');
                                gradient.addColorStop(1, '#818cf8');
                                return gradient;
                            },
                            borderRadius: 6,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 800,
                            easing: 'easeInOutQuart',
                            delay: function(context) {
                                return context.dataIndex * 100;
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#1f2937',
                                bodyColor: '#4b5563',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                padding: 12,
                                titleFont: {
                                    family: "'Inter', sans-serif",
                                    size: 13,
                                    weight: '600',
                                },
                                bodyFont: {
                                    family: "'Inter', sans-serif",
                                    size: 12,
                                },
                            },
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11,
                                    },
                                    color: '#6b7280',
                                    precision: 0,
                                },
                                grid: {
                                    color: '#f3f4f6',
                                },
                            },
                            y: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11,
                                    },
                                    color: '#4b5563',
                                },
                            },
                        },
                    },
                });
            }

            // === Graphique 3 : Répartition stock par catégorie (doughnut) ===
            const donutCenterPlugin = {
                id: 'donutCenter',
                afterDraw(chart) {
                    const { ctx, chartArea: { top, bottom, left, right } } = chart;
                    const cx = (left + right) / 2;
                    const cy = (top + bottom) / 2;
                    const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    ctx.save();
                    // Ligne 1 : total
                    ctx.font = 'bold 22px Inter, system-ui';
                    ctx.fillStyle = '#111827';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(total, cx, cy - 10);
                    // Ligne 2 : label
                    ctx.font = '11px Inter, system-ui';
                    ctx.fillStyle = '#9ca3af';
                    ctx.fillText('unités', cx, cy + 12);
                    ctx.restore();
                }
            };

            const ctx3 = document.getElementById('chartCategories');
            if (ctx3 && typeof Chart !== 'undefined') {
                new Chart(ctx3, {
                    type: 'doughnut',
                    plugins: [donutCenterPlugin],
                    data: {
                        labels: {!! json_encode($repartitionCategories->pluck('name')) !!},
                        datasets: [{
                            data: {!! json_encode($repartitionCategories->pluck('produits_sum_quantite_stock')) !!},
                            backgroundColor: ['#4f46e5','#10b981','#f59e0b','#ef4444','#8b5cf6'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        animation: { animateRotate: true, duration: 900 },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 16,
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    borderRadius: 3,
                                    font: { size: 12 },
                                    color: '#6b7280'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${ctx.label} : ${ctx.parsed} unités`
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
