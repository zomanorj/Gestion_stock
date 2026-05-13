@extends('layouts.app')

@section('title', 'Modifier la catégorie')

@section('content')
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Modifier la catégorie</h2>
            <p class="gs-page-subtitle">Éditer les informations de la catégorie</p>
        </div>
    </div>

    <div class="gs-card" style="max-width: 600px;">
        <div class="gs-card-body">
            <form method="post" action="{{ route('categories.update', $categorie) }}" novalidate>
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $categorie->name) }}" required maxlength="255"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $categorie->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection