@extends('layouts.admin')

@section('title', isset($program) ? 'Modifier le programme' : 'Nouveau programme')

@section('content')
<form method="POST" action="{{ isset($program) ? route('admin.calendar.update', $program) : route('admin.calendar.store') }}" class="admin-form">
  @csrf
  @if(isset($program)) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group">
      <label for="name_fr">Nom (FR) *</label>
      <input type="text" name="name_fr" id="name_fr" value="{{ old('name_fr', $program->name_fr ?? '') }}" required class="form-control">
    </div>
    <div class="form-group">
      <label for="name_en">Nom (EN)</label>
      <input type="text" name="name_en" id="name_en" value="{{ old('name_en', $program->name_en ?? '') }}" class="form-control">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="category">Catégorie (famille de filtre)</label>
      <input type="text" name="category" id="category" value="{{ old('category', $program->category ?? '') }}" class="form-control" placeholder="ex : Parcours linguistique, Cap sur l'oral…">
    </div>
    <div class="form-group">
      <label for="language">Langue *</label>
      <select name="language" id="language" class="form-control" required>
        <option value="fr" {{ old('language', $program->language ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
        <option value="en" {{ old('language', $program->language ?? '') === 'en' ? 'selected' : '' }}>English</option>
      </select>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="service_id">Service lié (pour « Voir le programme » / « S’inscrire »)</label>
      <select name="service_id" id="service_id" class="form-control">
        <option value="">— Aucun —</option>
        @foreach($services as $service)
        <option value="{{ $service->id }}" {{ old('service_id', $program->service_id ?? '') == $service->id ? 'selected' : '' }}>{{ $service->name_fr }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="color">Couleur (hex)</label>
      <input type="color" name="color" id="color" value="{{ old('color', $program->color ?? '#2d3a78') }}" class="form-control" style="padding:2px;height:42px">
    </div>
  </div>

  <div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $program->description ?? '') }}</textarea>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="sort_order">Ordre d'affichage</label>
      <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $program->sort_order ?? 0) }}" class="form-control">
    </div>
    <div class="form-check">
      <label>
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active ?? true) ? 'checked' : '' }}>
        Actif
      </label>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">{{ isset($program) ? 'Mettre à jour' : 'Créer' }}</button>
  <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
