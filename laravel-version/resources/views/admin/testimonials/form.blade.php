@extends('layouts.admin')
@section('title', isset($testimonial) ? 'Modifier le témoignage' : 'Nouveau témoignage')
@section('content')
<form method="POST" action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" class="admin-form">
@csrf @if(isset($testimonial)) @method('PUT') @endif
<div class="form-row"><div class="form-group"><label for="name_fr">Nom (FR) *</label><input type="text" name="name_fr" id="name_fr" value="{{ old('name_fr', $testimonial->name_fr ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="name_en">Nom (EN)</label><input type="text" name="name_en" id="name_en" value="{{ old('name_en', $testimonial->name_en ?? '') }}" class="form-control"></div></div>
<div class="form-row"><div class="form-group"><label for="role_fr">Rôle (FR)</label><input type="text" name="role_fr" id="role_fr" value="{{ old('role_fr', $testimonial->role_fr ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="role_en">Rôle (EN)</label><input type="text" name="role_en" id="role_en" value="{{ old('role_en', $testimonial->role_en ?? '') }}" class="form-control"></div></div>
<div class="form-group"><label for="content_fr">Contenu (FR) *</label><textarea name="content_fr" id="content_fr" rows="4" required class="form-control">{{ old('content_fr', $testimonial->content_fr ?? '') }}</textarea></div>
<div class="form-group"><label for="content_en">Contenu (EN)</label><textarea name="content_en" id="content_en" rows="4" class="form-control">{{ old('content_en', $testimonial->content_en ?? '') }}</textarea></div>
<div class="form-row"><div class="form-group"><label for="rating">Note (1-5)</label><input type="number" name="rating" id="rating" min="1" max="5" value="{{ old('rating', $testimonial->rating ?? 5) }}" class="form-control"></div>
<div class="form-group"><label for="order">Ordre</label><input type="number" name="order" id="order" value="{{ old('order', $testimonial->order ?? 0) }}" class="form-control"></div></div>
<div class="form-check"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}> Actif</label></div>
<button type="submit" class="btn btn-primary">{{ isset($testimonial) ? 'Mettre à jour' : 'Créer' }}</button>
<a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
