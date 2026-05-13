@extends('layouts.app')

@section('title', 'Sortie de stock — GestiStock')

@section('content')
    @include('components.flash')

    <h1 class="h3 mb-4">Sortie de stock</h1>

    <div class="card shadow-sm border-0" style="max-width: 32rem;">
        <div class="card-body">
            <form method="post" action="{{ route('mouvements.sortie.store') }}" id="form-sortie" novalidate>
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
                <p id="stock-info" class="alert alert-light border py-2 small mb-2" role="status">
                    Stock actuel : <strong id="stock-valeur">—</strong> unités
                </p>
                <p id="stock-warning" class="text-danger small fw-semibold d-none mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> La quantité demandée dépasse le stock disponible.
                </p>
                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" name="quantite" id="quantite" value="{{ old('quantite', 1) }}" min="1" required class="form-control @error('quantite') is-invalid @enderror">
                    @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="motif" class="form-label">Motif</label>
                    <input type="text" name="motif" id="motif" value="{{ old('motif') }}" class="form-control" placeholder="Ex. affectation service, casse…">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger" id="btn-submit-sortie">Valider la sortie</button>
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
            const qty = document.getElementById('quantite');
            const val = document.getElementById('stock-valeur');
            const warn = document.getElementById('stock-warning');
            const btn = document.getElementById('btn-submit-sortie');
            let stockCourant = null;

            function fetchStock(id) {
                stockCourant = null;
                if (!id) return;
                fetch(stockApiPrefix + id + '/stock', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof data.quantite_stock !== 'undefined') {
                            stockCourant = parseInt(data.quantite_stock, 10);
                            val.textContent = stockCourant;
                            checkQty();
                        }
                    })
                    .catch(() => {
                        const opt = sel.options[sel.selectedIndex];
                        if (opt && opt.dataset.stock !== undefined) {
                            stockCourant = parseInt(opt.dataset.stock, 10);
                            val.textContent = stockCourant;
                            checkQty();
                        }
                    });
            }

            function checkQty() {
                const q = parseInt(qty.value, 10) || 0;
                if (stockCourant !== null && q > stockCourant) {
                    warn.classList.remove('d-none');
                    btn.disabled = true;
                } else {
                    warn.classList.add('d-none');
                    btn.disabled = false;
                }
            }

            sel.addEventListener('change', function () { fetchStock(sel.value); });
            qty.addEventListener('input', checkQty);

            const params = new URLSearchParams(window.location.search);
            const pre = params.get('produit_id');
            if (pre) sel.value = pre;
            fetchStock(sel.value);

            document.getElementById('form-sortie').addEventListener('submit', function (e) {
                const q = parseInt(qty.value, 10) || 0;
                if (stockCourant !== null && q > stockCourant) {
                    e.preventDefault();
                    warn.classList.remove('d-none');
                }
            });
        });
    </script>
@endpush
