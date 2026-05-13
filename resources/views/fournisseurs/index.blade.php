@extends('layouts.app')

@section('title', 'Fournisseurs')

@section('content')
    {{-- En-tête de page --}}
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Fournisseurs</h2>
            <p class="gs-page-subtitle">
                {{ $fournisseurs->total() }} fournisseur{{ $fournisseurs->total() > 1 ? 's' : '' }} enregistré{{ $fournisseurs->total() > 1 ? 's' : '' }}
            </p>
        </div>
        @role('admin')
            <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouveau fournisseur
            </a>
        @endrole
    </div>

    {{-- Barre de recherche --}}
    <form method="get" action="{{ route('fournisseurs.index') }}" class="gs-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Recherche</label>
                <input type="search" name="search" id="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom, contact ou e-mail…">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search me-1"></i> Rechercher
                </button>
            </div>
            @if(request('search'))
                <div class="col-auto">
                    <a href="{{ route('fournisseurs.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <th style="width: 50px;"></th>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th class="text-end" style="width: 100px;">Nb produits</th>
                        <th class="text-end" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fournisseurs as $f)
                        <tr>
                            <td>
                                <div class="gs-avatar" style="width: 32px; height: 32px; font-size: 11px;">
                                    {{ strtoupper(substr($f->name, 0, 2)) }}
                                </div>
                            </td>
                            <td class="fw-medium">{{ $f->name }}</td>
                            <td style="font-size: 13px;">{{ $f->contact ?? '—' }}</td>
                            <td style="font-size: 13px; color: var(--color-muted);">
                                @if($f->email)
                                    <a href="mailto:{{ $f->email }}" class="text-decoration-none" style="color: var(--color-primary);">{{ $f->email }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size: 13px;" class="text-nowrap">{{ $f->telephone ?? '—' }}</td>
                            <td class="text-end">
                                @php $nbProduits = $f->produits()->count(); @endphp
                                <span class="badge {{ $nbProduits > 0 ? 'badge-primary' : 'badge-muted' }}">{{ $nbProduits }}</span>
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="display:inline-flex; gap:4px;">
                                    @role('admin')
                                        <a href="{{ route('fournisseurs.edit', $f) }}" class="gs-action-btn edit" title="Modifier" style="background-color: var(--color-warning-light); color: var(--color-warning);">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('fournisseurs.destroy', $f) }}" method="post" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?');">
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
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                Aucun fournisseur trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($fournisseurs->hasPages())
            <div class="gs-card-footer d-flex justify-content-end">
                {{ $fournisseurs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection