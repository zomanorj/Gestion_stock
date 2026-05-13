@extends('layouts.app')

@section('title', 'Catégories')

@section('content')
    {{-- En-tête de page --}}
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Catégories</h2>
            <p class="gs-page-subtitle">
                {{ $categories->total() }} catégorie{{ $categories->total() > 1 ? 's' : '' }} enregistrée{{ $categories->total() > 1 ? 's' : '' }}
            </p>
        </div>
        @role('admin')
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouvelle catégorie
            </a>
        @endrole
    </div>

    {{-- Barre de recherche --}}
    <form method="get" action="{{ route('categories.index') }}" class="gs-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche par nom</label>
                <input type="search" name="search" id="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom de la catégorie…">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search me-1"></i> Rechercher
                </button>
            </div>
            @if(request('search'))
                <div class="col-auto">
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Réinitialiser
                    </a>
                </div>
            @endif
        </div>
    </form>

    {{-- Tableau --}}
    <div class="gs-card">
        <div class="gs-table-wrapper">
            <table class="gs-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th class="text-end" style="width: 120px;">Nb produits</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $categorie)
                        <tr>
                            <td style="font-size: 12px; color: var(--color-muted);">{{ $categorie->id }}</td>
                            <td class="fw-medium">{{ $categorie->name }}</td>
                            <td style="font-size: 13px; color: var(--color-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $categorie->description ?? '' }}">
                                {{ \Illuminate\Support\Str::limit($categorie->description ?? '—', 60) }}
                            </td>
                            <td class="text-end">
                                @php $nbProduits = $categorie->produits()->count(); @endphp
                                @if($nbProduits > 0)
                                    <span class="badge badge-primary">{{ $nbProduits }}</span>
                                @else
                                    <span class="badge badge-muted">0</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="display:inline-flex; gap:4px;">
                                    @role('admin')
                                        <a href="{{ route('categories.edit', $categorie) }}" class="gs-action-btn edit" title="Modifier" style="background-color: var(--color-warning-light); color: var(--color-warning);">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $categorie) }}" method="post" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?');">
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
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                Aucune catégorie trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="gs-card-footer d-flex justify-content-end">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection