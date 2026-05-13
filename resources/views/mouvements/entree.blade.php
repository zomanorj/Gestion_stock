@extends('layouts.app')

@section('title', 'Entrée de stock — GestiStock')

@section('content')
    @include('components.flash')

    @php
        $stockApiPrefix = rtrim(url('/'), '/') . '/api/produits/';
    @endphp

    <h1 class="h3 mb-4">Entrée de stock</h1>

    <div class="card shadow-sm border-0" style="max-width: 32rem;">
        <div class="card-body">
            <form method="post" action="{{ route('mouvements.entree.store') }}" id="form-entree" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="produit_id" class="form-label">Produit <span class="text-danger">*</span></label>
                    <select name="produit_id" id="produit_id" required class="form-select @error('produit_id') is-invalid @enderror">
                        <option value="">— Choisir —</option>
                        @foreach ($produits as $p)
                            <option value="{{ $p->id }}" data-stock="{{ $p->quantite_stock }}" @selected(old('produit_id') == $p->id)>
                                {{ $p->nom }} ({{ $p->code_produit }})
                            </option>
                        @endforeach
                    </select>
                    @error('produit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <p id="stock-info" class="alert alert-secondary py-2 small d-none mb-3" role="status">
                    Stock actuel (temps réel) : <strong id="stock-valeur">—</strong> unités
                </p>
                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" name="quantite" id="quantite" value="{{ old('quantite', 1) }}" min="1" required class="form-control @error('quantite') is-invalid @enderror">
                    @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="motif" class="form-label">Motif</label>
                    <input type="text" name="motif" id="motif" value="{{ old('motif') }}" class="form-control" placeholder="Ex. réapprovisionnement, retour fournisseur…">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Valider l'entrée</button>
                    <a href="{{ route('mouvements.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stockApiPrefix = @json($stockApiPrefix);
            const sel = document.getElementById('produit_id');
            const info = document.getElementById('stock-info');
            const val = document.getElementById('stock-valeur');

            function fetchStock(id) {
                if (!id) {
                    info.classList.add('d-none');
                    return;
                }
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.dataset.stock !== undefined) {
                    val.textContent = opt.dataset.stock;
                    info.classList.remove('d-none');
                }
                fetch(stockApiPrefix + id + '/stock', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof data.quantite_stock !== 'undefined') {
                            val.textContent = data.quantite_stock;
                            info.classList.remove('d-none');
                        }
                    })
                    .catch(() => {});
            }

            sel.addEventListener('change', function () { fetchStock(sel.value); });
            const params = new URLSearchParams(window.location.search);
            const pre = params.get('produit_id');
            if (pre) {
                sel.value = pre;
            }
            fetchStock(sel.value);
        });
    </script>
@endpush
