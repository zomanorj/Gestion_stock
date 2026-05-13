@extends('layouts.app')

@section('title', 'Nouveau fournisseur')

@section('content')
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Nouveau fournisseur</h2>
            <p class="gs-page-subtitle">Ajouter un fournisseur au catalogue</p>
        </div>
    </div>

    <div class="gs-card" style="max-width: 600px;">
        <div class="gs-card-body">
            <form method="post" action="{{ route('fournisseurs.store') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" name="contact" id="contact" value="{{ old('contact') }}" class="form-control @error('contact') is-invalid @enderror">
                    @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" class="form-control @error('telephone') is-invalid @enderror">
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea name="adresse" id="adresse" rows="3" class="form-control @error('adresse') is-invalid @enderror">{{ old('adresse') }}</textarea>
                    @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection