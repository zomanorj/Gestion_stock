@extends('layouts.app')

@section('title', 'Produits')

@section('content')
    {{-- En-tête de page --}}
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Produits</h2>
            <p class="gs-page-subtitle">
                {{ $produits->total() }} produit{{ $produits->total() > 1 ? 's' : '' }} référencé{{ $produits->total() > 1 ? 's' : '' }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @role('admin')
                <a href="{{ route('produits.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Nouveau produit
                </a>
            @endrole
            <div class="btn-group">
                <a href="{{ route('produits.export.excel') }}" class="btn btn-sm" style="background-color: var(--color-success-light); color: var(--color-success); border: 1px solid var(--color-success);">
                    <i class="bi bi-file-earmark-excel me-1"></i>Exporter tout
                </a>
                <a href="{{ route('produits.export.excel', ['alerte_only' => 1]) }}" class="btn btn-sm" style="background-color: var(--color-warning-light); color: var(--color-warning); border: 1px solid var(--color-warning);">
                    <i class="bi bi-exclamation-triangle me-1"></i>Alertes
                </a>
                <a href="{{ route('produits.export.pdf') }}" class="btn btn-sm" style="background-color: var(--color-danger-light); color: var(--color-danger); border: 1px solid var(--color-danger);">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="get" action="{{ route('produits.index') }}" class="gs-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <select name="categorie_id" class="form-select form-select-sm">
                    <option value="">— Toutes —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('categorie_id') === (string) $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fournisseur</label>
                <select name="fournisseur_id" class="form-select form-select-sm">
                    <option value="">— Tous —</option>
                    @foreach ($fournisseurs as $f)
                        <option value="{{ $f->id }}" @selected((string) request('fournisseur_id') === (string) $f->id)>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Recherche</label>
                <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom ou code…">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
                @if(request('categorie_id') || request('fournisseur_id') || request('search'))
                    <a href="{{ route('produits.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <th style="width: 50px;">Img</th>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Catégorie</th>
                        <th>Fournisseur</th>
                        <th class="text-end" style="width: 80px;">Stock</th>
                        <th class="text-end" style="width: 100px;">Prix vente</th>
                        <th style="width: 80px;">Alerte</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produits as $p)
                        <tr>
                            <td>
                                @if ($p->image)
                                    <img src="{{ asset('storage/'.$p->image) }}" alt="" class="gs-product-img">
                                @else
                                    <div class="gs-product-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $p->nom }}</td>
                            <td>
                                <code style="font-size: 12px; background-color: #f3f4f6; padding: 2px 6px; border-radius: 4px;">{{ $p->code_produit }}</code>
                            </td>
                            <td>
                                @if($p->categorie)
                                    <span class="badge badge-primary">{{ $p->categorie->name }}</span>
                                @else
                                    <span class="badge badge-muted">—</span>
                                @endif
                            </td>
                            <td style="font-size: 13px;">{{ $p->fournisseur?->name ?? '—' }}</td>
                            <td class="text-end">
                                <span class="fw-medium">{{ $p->quantite_stock }}</span>
                            </td>
                            <td class="text-end" style="font-size: 13px;">
                                {{ number_format($p->prix_vente, 0, ',', ' ') }} Ar
                            </td>
                            <td>
                                @if ($p->quantite_stock <= $p->seuil_alerte)
                                    <span class="badge badge-danger">Alerte</span>
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="display:inline-flex; gap:4px;">
                                    <a href="{{ route('produits.show', $p) }}" class="gs-action-btn view" title="Voir" style="background-color: var(--color-primary-light); color: var(--color-primary);">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @role('admin')
                                        <a href="{{ route('produits.edit', $p) }}" class="gs-action-btn edit" title="Modifier" style="background-color: var(--color-warning-light); color: var(--color-warning);">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('produits.destroy', $p) }}" method="post" class="d-inline" onsubmit="return confirm('Supprimer ce produit ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="gs-action-btn delete" title="Supprimer" style="background-color: var(--color-danger-light); color: var(--color-danger);">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                Aucun produit trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($produits->hasPages())
            <div class="gs-card-footer d-flex justify-content-end">
                {{ $produits->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection