@extends('layouts.app')

@section('title', 'Historique des mouvements')

@section('content')
    {{-- En-tête de page --}}
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Historique des mouvements</h2>
            <p class="gs-page-subtitle">
                {{ $mouvements->total() }} mouvement{{ $mouvements->total() > 1 ? 's' : '' }} enregistré{{ $mouvements->total() > 1 ? 's' : '' }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('mouvements.entree') }}" class="btn btn-sm" style="background-color: var(--color-success); color: white; border: none;">
                <i class="bi bi-plus-lg me-1"></i> Entrée stock
            </a>
            <a href="{{ route('mouvements.sortie') }}" class="btn btn-sm" style="background-color: var(--color-danger); color: white; border: none;">
                <i class="bi bi-dash-lg me-1"></i> Sortie stock
            </a>
            <a href="{{ route('mouvements.export.excel', ['date_debut' => request('date_debut'), 'date_fin' => request('date_fin')]) }}" class="btn btn-sm" style="background-color: var(--color-success-light); color: var(--color-success); border: 1px solid var(--color-success);">
                <i class="bi bi-file-earmark-excel me-1"></i>Exporter Excel
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="get" action="{{ route('mouvements.index') }}" class="gs-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">— Tous —</option>
                    <option value="entrée" @selected(request('type') === 'entrée')>Entrée</option>
                    <option value="sortie" @selected(request('type') === 'sortie')>Sortie</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
                @if(request('type') || request('date_debut') || request('date_fin'))
                    <a href="{{ route('mouvements.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tableau --}}
    <div class="gs-card">
        <div class="gs-table-wrapper">
            <table class="gs-table">
                <thead>
                    <tr>
                        <th>Date / heure</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th class="text-end">Quantité</th>
                        <th>Motif</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mouvements as $m)
                        <tr>
                            <td class="text-nowrap" style="font-size: 12px; color: var(--color-muted);">
                                {{ $m->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if ($m->produit)
                                    <a href="{{ route('produits.show', $m->produit) }}" class="text-decoration-none" style="color: var(--color-primary); font-weight: 500;">
                                        {{ $m->produit->nom }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($m->type === 'entrée')
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
                            <td style="font-size: 13px; color: var(--color-muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $m->motif ?? '' }}">
                                {{ \Illuminate\Support\Str::limit($m->motif ?? '—', 40) }}
                            </td>
                            <td style="font-size: 12px;">{{ $m->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                Aucun mouvement trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($mouvements->hasPages())
            <div class="gs-card-footer d-flex justify-content-end">
                {{ $mouvements->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection