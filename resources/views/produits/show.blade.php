@extends('layouts.app')

@section('title', $produit->nom.' — GestiStock')

@section('content')
    @include('components.flash')

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $produit->nom }}</h1>
            <small class="text-muted">{{ $produit->code_produit }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
            <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary btn-sm">← Liste</a>
            <a href="{{ route('produits.fiche.pdf', $produit) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Fiche PDF
            </a>
            @role('admin')
                <a href="{{ route('produits.edit', $produit) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Modifier</a>
            @endrole
            <a href="{{ route('mouvements.entree') }}?produit_id={{ $produit->id }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Entrée stock</a>
            <a href="{{ route('mouvements.sortie') }}?produit_id={{ $produit->id }}" class="btn btn-danger btn-sm"><i class="bi bi-dash-lg"></i> Sortie stock</a>
        </div>
    </div>

    {{-- Onglets Bootstrap --}}
    <ul class="nav nav-tabs mb-4" id="produitTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="fiche-tab" data-bs-toggle="tab" data-bs-target="#fiche" type="button" role="tab" aria-controls="fiche" aria-selected="true">
                <i class="bi bi-card-heading me-1"></i> Fiche produit
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab" aria-controls="historique" aria-selected="false">
                <i class="bi bi-clock-history me-1"></i> Historique complet
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="statistiques-tab" data-bs-toggle="tab" data-bs-target="#statistiques" type="button" role="tab" aria-controls="statistiques" aria-selected="false">
                <i class="bi bi-graph-up me-1"></i> Statistiques
            </button>
        </li>
    </ul>

    <div class="tab-content" id="produitTabsContent">
        {{-- ONGLET 1 : FICHE PRODUIT --}}
        <div class="tab-pane fade show active" id="fiche" role="tabpanel" aria-labelledby="fiche-tab">
            <div class="row g-4">
                {{-- Colonne image + infos --}}
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            @if ($produit->image)
                                <img src="{{ asset('storage/'.$produit->image) }}" alt="{{ $produit->nom }}" class="img-fluid rounded border mb-3 w-100" style="max-height: 280px; object-fit: contain;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted mb-3" style="min-height: 200px;">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            @endif
                            <dl class="row mb-0 small">
                                <dt class="col-sm-4 text-muted">Code</dt>
                                <dd class="col-sm-8"><code>{{ $produit->code_produit }}</code></dd>
                                <dt class="col-sm-4 text-muted">Catégorie</dt>
                                <dd class="col-sm-8">{{ $produit->categorie?->name ?? '—' }}</dd>
                                <dt class="col-sm-4 text-muted">Fournisseur</dt>
                                <dd class="col-sm-8">{{ $produit->fournisseur?->name ?? '—' }}</dd>
                                <dt class="col-sm-4 text-muted">Prix achat</dt>
                                <dd class="col-sm-8">{{ number_format($produit->prix_achat, 2, ',', ' ') }} Ar</dd>
                                <dt class="col-sm-4 text-muted">Prix vente</dt>
                                <dd class="col-sm-8">{{ number_format($produit->prix_vente, 2, ',', ' ') }} Ar</dd>
                                <dt class="col-sm-4 text-muted">Seuil alerte</dt>
                                <dd class="col-sm-8">{{ $produit->seuil_alerte }}</dd>
                            </dl>
                            @if ($produit->description)
                                <hr>
                                <p class="small text-muted mb-0">{{ $produit->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <span class="text-muted small">Stock actuel</span>
                            @if ($produit->estEnAlerte())
                                <span class="badge bg-danger fs-6">{{ $produit->quantite_stock }} unités (alerte)</span>
                            @else
                                <span class="badge bg-success fs-6">{{ $produit->quantite_stock }} unités</span>
                            @endif
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-0"><i class="bi bi-clock-history me-2"></i>10 derniers mouvements</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th class="text-end">Qté</th>
                                        <th>Motif</th>
                                        <th>Utilisateur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mouvements->take(10) as $m)
                                        <tr>
                                            <td class="text-nowrap small">{{ $m->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($m->type === 'entrée')
                                                    <span class="badge bg-success">Entrée</span>
                                                @else
                                                    <span class="badge bg-danger">Sortie</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $m->quantite }}</td>
                                            <td class="small text-muted">{{ $m->motif ?? '—' }}</td>
                                            <td class="small">{{ $m->user?->name ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aucun mouvement.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET 2 : HISTORIQUE COMPLET --}}
        <div class="tab-pane fade" id="historique" role="tabpanel" aria-labelledby="historique-tab">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h6 mb-0"><i class="bi bi-list-ul me-2"></i>Tous les mouvements</h2>
                        {{-- Filtres rapides --}}
                        <div class="btn-group" role="group" aria-label="Filtres">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" data-filter="all">Tous</button>
                            <button type="button" class="btn btn-sm btn-outline-success" data-filter="entree">Entrées</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-filter="sortie">Sorties</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Timeline verticale --}}
                    <div class="timeline" id="timelineContainer">
                        @forelse ($mouvements as $m)
                            <div class="timeline-item" data-type="{{ $m->type }}">
                                <div class="timeline-marker {{ $m->type === 'entrée' ? 'bg-success' : 'bg-danger' }}">
                                    @if ($loop->first && $m->id == $mouvements->first()->id)
                                        <i class="bi bi-star-fill text-white"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-date text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $m->created_at?->isoFormat('DD MMMM YYYY, HH:mm') }}
                                        @if ($loop->first && $m->id == $mouvements->first()->id)
                                            <span class="badge bg-primary ms-2" style="font-size: 10px;">
                                                <i class="bi bi-star me-1"></i>Dernier mouvement
                                            </span>
                                        @endif
                                    </div>
                                    <div class="timeline-card card border-0 shadow-sm">
                                        <div class="card-body py-2 px-3">
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                {{-- Badge type --}}
                                                @if ($m->type === 'entrée')
                                                    <span class="badge bg-success"><i class="bi bi-plus-lg me-1"></i>Entrée</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="bi bi-dash-lg me-1"></i>Sortie</span>
                                                @endif
                                                {{-- Quantité --}}
                                                <span class="fw-bold {{ $m->type === 'entrée' ? 'text-success' : 'text-danger' }}">
                                                    {{ $m->type === 'entrée' ? '+' : '-' }}{{ $m->quantite }} unité{{ $m->quantite > 1 ? 's' : '' }}
                                                </span>
                                                {{-- Motif --}}
                                                @if ($m->motif)
                                                    <span class="text-muted small">
                                                        <i class="bi bi-chat-left-text me-1"></i>{{ $m->motif }}
                                                    </span>
                                                @endif
                                                {{-- Utilisateur --}}
                                                <span class="text-muted small ms-auto">
                                                    <i class="bi bi-person me-1"></i>{{ $m->user?->name ?? '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p>Aucun mouvement enregistré pour ce produit.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $mouvements->links() }}
                    </div>

                    {{-- Total --}}
                    <div class="mt-3 pt-3 border-top">
                        <p class="text-muted small mb-0">
                            <strong>{{ $totalMouvements }}</strong> mouvement{{ $totalMouvements > 1 ? 's' : '' }} au total
                            · <span class="text-success"><strong>{{ $nbEntrees }}</strong> entrée{{ $nbEntrees > 1 ? 's' : '' }}</span>
                            · <span class="text-danger"><strong>{{ $nbSorties }}</strong> sortie{{ $nbSorties > 1 ? 's' : '' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET 3 : STATISTIQUES --}}
        <div class="tab-pane fade" id="statistiques" role="tabpanel" aria-labelledby="statistiques-tab">
            <div class="row g-4">
                {{-- Carte : Stock actuel vs Seuil d'alerte --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-0"><i class="bi bi-gauge me-2"></i>Stock actuel</h2>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <span class="display-4 fw-bold {{ $produit->estEnAlerte() ? 'text-danger' : 'text-success' }}">
                                    {{ $produit->quantite_stock }}
                                </span>
                                <span class="text-muted">unités</span>
                            </div>
                            {{-- Jauge visuelle --}}
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>0</span>
                                    <span>Seuil : {{ $produit->seuil_alerte }}</span>
                                </div>
                                <div class="gs-progress" style="height: 12px;">
                                    @php
                                        $pourcentage = $produit->seuil_alerte > 0
                                            ? min(100, ($produit->quantite_stock / ($produit->seuil_alerte * 3)) * 100)
                                            : 50;
                                        $classeJauge = $produit->estEnAlerte() ? 'gs-progress-bar-danger' : 'gs-progress-bar';
                                    @endphp
                                    <div class="gs-progress-bar {{ $classeJauge }} {{ $produit->estEnAlerte() ? '' : 'bg-success' }}"
                                         style="width: {{ $pourcentage }}%; background-color: {{ $produit->estEnAlerte() ? 'var(--color-danger)' : 'var(--color-success)' }};"></div>
                                </div>
                            </div>
                            @if ($produit->estEnAlerte())
                                <div class="alert alert-danger d-flex align-items-center mb-0 small">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Stock en dessous du seuil d'alerte !
                                </div>
                            @else
                                <div class="alert alert-success d-flex align-items-center mb-0 small">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Stock suffisant
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Carte : Total entré / Total sorti --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-0"><i class="bi bi-arrow-left-right me-2"></i>Mouvements totaux</h2>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 rounded" style="background-color: var(--color-success-light);">
                                        <div class="display-6 fw-bold text-success">+{{ $totalEntree }}</div>
                                        <div class="small text-muted">Unités entrées</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 rounded" style="background-color: var(--color-danger-light);">
                                        <div class="display-6 fw-bold text-danger">−{{ $totalSortie }}</div>
                                        <div class="small text-muted">Unités sorties</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Depuis la création du produit</span>
                                <span class="text-muted">{{ $produit->created_at->diffInMonths(now()) + 1 }} mois</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte : Mouvement moyen par mois --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-0"><i class="bi bi-calendar3 me-2"></i>Activité mensuelle</h2>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <span class="display-4 fw-bold">{{ $mouvParMois }}</span>
                                <span class="text-muted">mouvements/mois</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 rounded text-center" style="background-color: #f8f9fa;">
                                        <div class="fw-bold">{{ $nbEntrees }}</div>
                                        <small class="text-success">Entrées</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded text-center" style="background-color: #f8f9fa;">
                                        <div class="fw-bold">{{ $nbSorties }}</div>
                                        <small class="text-danger">Sorties</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Graphique Chart.js : évolution du stock --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-0"><i class="bi bi-graph-up me-2"></i>Évolution du stock dans le temps</h2>
                        </div>
                        <div class="card-body">
                            @if (count($evolutionStock) > 0)
                                <canvas id="stockChart" height="80"></canvas>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-graph-down-arrow fs-1 d-block mb-3"></i>
                                    <p>Aucune donnée disponible pour afficher l'évolution.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles pour la timeline --}}
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .timeline-marker i {
            font-size: 8px;
        }

        .timeline-content {
            margin-left: 10px;
        }

        .timeline-date {
            margin-bottom: 0.25rem;
        }

        .timeline-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .timeline-card:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        /* Filtres */
        .timeline-item.hidden {
            display: none;
        }

        .btn-group .btn-outline-secondary.active {
            background-color: var(--color-secondary);
            color: white;
        }
    </style>

    {{-- Script pour les filtres et le graphique --}}
    @push('scripts')
    <script>
        // Filtres de la timeline
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', function() {
                // Gestion des classes actives
                document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                document.querySelectorAll('.timeline-item').forEach(item => {
                    if (filter === 'all') {
                        item.classList.remove('hidden');
                    } else if (filter === 'entree') {
                        item.classList.toggle('hidden', item.dataset.type !== 'entrée');
                    } else if (filter === 'sortie') {
                        item.classList.toggle('hidden', item.dataset.type !== 'sortie');
                    }
                });
            });
        });

        // Graphique Chart.js
        @if (count($evolutionStock) > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stockChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_column($evolutionStock, 'date')) !!},
                        datasets: [{
                            label: 'Stock cumulé',
                            data: {!! json_encode(array_column($evolutionStock, 'stock')) !!},
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#4f46e5',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleFont: { size: 13 },
                                bodyFont: { size: 12 },
                                padding: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Stock : ' + context.parsed.y + ' unités';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: Math.ceil(Math.max(...{!! json_encode(array_column($evolutionStock, 'stock')) !!}) / 5)
                                },
                                grid: {
                                    color: '#f3f4f6'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
        @endif
    </script>
    @endpush
@endsection