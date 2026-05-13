@extends('layouts.app')

@section('title', 'Nouvelle catégorie')

@section('content')
    <div class="gs-page-header">
        <div>
            <h2 class="gs-page-title">Nouvelle catégorie</h2>
            <p class="gs-page-subtitle">Ajouter une catégorie au catalogue</p>
        </div>
    </div>

    <div class="gs-card" style="max-width: 600px;">
        <div class="gs-card-body">
            <form method="post" action="{{ route('categories.store') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection