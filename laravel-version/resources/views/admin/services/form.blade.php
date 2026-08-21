@extends('layouts.admin')

@section('title', isset($service) ? 'Modifier le service' : 'Nouveau service')

@section('content')
<form method="POST" action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" class="admin-form">
  @csrf
  @if(isset($service)) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group">
      <label for="name_fr">Nom (FR) *</label>
      <input type="text" name="name_fr" id="name_fr" value="{{ old('name_fr', $service->name_fr ?? '') }}" required class="form-control">
    </div>
    <div class="form-group">
      <label for="name_en">Nom (EN)</label>
      <input type="text" name="name_en" id="name_en" value="{{ old('name_en', $service->name_en ?? '') }}" class="form-control">
    </div>
  </div>

  <div class="form-group">
    <label for="slug">Slug *</label>
    <input type="text" name="slug" id="slug" value="{{ old('slug', $service->slug ?? '') }}" required class="form-control">
  </div>

  <div class="form-group">
    <label for="service_category_id">Catégorie *</label>
    <select name="service_category_id" id="service_category_id" class="form-control" required>
      @foreach($categories as $id => $name)
      <option value="{{ $id }}" {{ (old('service_category_id', $service->service_category_id ?? '') == $id) ? 'selected' : '' }}>{{ $name }}</option>
      @endforeach
    </select>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="short_description_fr">Description courte (FR)</label>
      <textarea name="short_description_fr" id="short_description_fr" class="form-control">{{ old('short_description_fr', $service->short_description_fr ?? '') }}</textarea>
    </div>
    <div class="form-group">
      <label for="short_description_en">Description courte (EN)</label>
      <textarea name="short_description_en" id="short_description_en" class="form-control">{{ old('short_description_en', $service->short_description_en ?? '') }}</textarea>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="duration">Durée</label>
      <input type="text" name="duration" id="duration" value="{{ old('duration', $service->duration ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="price">Prix</label>
      <input type="text" name="price" id="price" value="{{ old('price', $service->price ?? '') }}" class="form-control">
    </div>
  </div>

  <div class="form-group">
    <label for="image">Image</label>
    <input type="text" name="image" id="image" value="{{ old('image', $service->image ?? '') }}" class="form-control" placeholder="img/service-image.png">
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="order">Ordre</label>
      <input type="number" name="order" id="order" value="{{ old('order', $service->order ?? 0) }}" class="form-control">
    </div>
    <div class="form-check">
      <label>
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
        Actif
      </label>
    </div>
    <div class="form-check">
      <label>
        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured ?? false) ? 'checked' : '' }}>
        Mis en avant
      </label>
    </div>
  </div>

  <div class="form-group">
    <label for="description_fr">Description complète (FR)</label>
    <textarea name="description_fr" id="description_fr" rows="10" class="form-control">{{ old('description_fr', $service->description_fr ?? '') }}</textarea>
  </div>

  <button type="submit" class="btn btn-primary">{{ isset($service) ? 'Mettre à jour' : 'Créer' }}</button>
  <a href="{{ route('admin.services.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
