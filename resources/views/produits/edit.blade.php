@extends('layouts.app')

@section('title', 'Modifier le produit')

@section('content')
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Modifier le produit</h2>
            <p class="gs-page-subtitle">Éditer les informations du produit</p>
        </div>
    </div>

    <div class="gs-card">
        <div class="gs-card-body">
            <form method="post" action="{{ route('produits.update', $produit) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @if ($produit->image)
                    <div class="mb-3">
                        <p class="form-label mb-1">Image actuelle</p>
                        <img src="{{ asset('storage/'.$produit->image) }}" alt="" class="rounded border" style="max-height: 180px;">
                    </div>
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $produit->nom) }}" required class="form-control @error('nom') is-invalid @enderror">
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="code_produit" class="form-label">Code produit <span class="text-danger">*</span></label>
                        <input type="text" name="code_produit" id="code_produit" value="{{ old('code_produit', $produit->code_produit) }}" required class="form-control @error('code_produit') is-invalid @enderror">
                        @error('code_produit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $produit->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select name="categorie_id" id="categorie_id" required class="form-select @error('categorie_id') is-invalid @enderror">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('categorie_id', $produit->categorie_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="fournisseur_id" class="form-label">Fournisseur</label>
                        <select name="fournisseur_id" id="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror">
                            <option value="">— Aucun —</option>
                            @foreach ($fournisseurs as $f)
                                <option value="{{ $f->id }}" @selected(old('fournisseur_id', $produit->fournisseur_id) == $f->id)>{{ $f->name }}</option>
                            @endforeach
                        </select>
                        @error('fournisseur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="prix_achat" class="form-label">Prix d'achat <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="prix_achat" id="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" required class="form-control @error('prix_achat') is-invalid @enderror">
                        @error('prix_achat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="prix_vente" class="form-label">Prix de vente <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="prix_vente" id="prix_vente" value="{{ old('prix_vente', $produit->prix_vente) }}" required class="form-control @error('prix_vente') is-invalid @enderror">
                        @error('prix_vente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="quantite_stock" class="form-label">Quantité stock <span class="text-danger">*</span></label>
                        <input type="number" name="quantite_stock" id="quantite_stock" value="{{ old('quantite_stock', $produit->quantite_stock) }}" required min="0" class="form-control @error('quantite_stock') is-invalid @enderror">
                        @error('quantite_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="seuil_alerte" class="form-label">Seuil d'alerte <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" required min="0" class="form-control @error('seuil_alerte') is-invalid @enderror">
                        @error('seuil_alerte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Nouvelle image (optionnel)</label>
                        <input type="file" name="image" id="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="mt-2">
                            <img id="preview-image" src="" alt="" class="rounded border d-none" style="max-height: 180px;">
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2" style="padding-top: 20px;">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('image')?.addEventListener('change', function (e) {
            const f = e.target.files?.[0];
            const img = document.getElementById('preview-image');
            if (!f || !img) return;
            img.src = URL.createObjectURL(f);
            img.classList.remove('d-none');
        });
    </script>
@endpush