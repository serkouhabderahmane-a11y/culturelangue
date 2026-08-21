@extends('layouts.admin')
@section('title', isset($navigation) ? 'Modifier l\'élément' : 'Nouvel élément')
@section('content')
<form method="POST" action="{{ isset($navigation) ? route('admin.navigation.update', $navigation) : route('admin.navigation.store') }}" class="admin-form">
@csrf @if(isset($navigation)) @method('PUT') @endif
<div class="form-row"><div class="form-group"><label for="label_fr">Label (FR) *</label><input type="text" name="label_fr" id="label_fr" value="{{ old('label_fr', $navigation->label_fr ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="label_en">Label (EN)</label><input type="text" name="label_en" id="label_en" value="{{ old('label_en', $navigation->label_en ?? '') }}" class="form-control"></div></div>
<div class="form-row"><div class="form-group"><label for="url">URL</label><input type="text" name="url" id="url" value="{{ old('url', $navigation->url ?? '') }}" class="form-control" placeholder="/services/parcours-linguistique"></div>
<div class="form-group"><label for="route">Route</label><input type="text" name="route" id="route" value="{{ old('route', $navigation->route ?? '') }}" class="form-control"></div></div>
<div class="form-group"><label for="parent_id">Parent</label><select name="parent_id" id="parent_id" class="form-control"><option value="">Aucun (menu principal)</option>
@foreach($parents ?? [] as $id => $label)<option value="{{ $id }}" {{ (old('parent_id', $navigation->parent_id ?? '') == $id) ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
<div class="form-row"><div class="form-group"><label for="order">Ordre</label><input type="number" name="order" id="order" value="{{ old('order', $navigation->order ?? 0) }}" class="form-control"></div>
<div class="form-group"><label for="target">Cible</label><select name="target" id="target" class="form-control"><option value="_self" {{ (old('target', $navigation->target ?? '') == '_self') ? 'selected' : '' }}>Même fenêtre</option><option value="_blank" {{ (old('target', $navigation->target ?? '') == '_blank') ? 'selected' : '' }}>Nouvelle fenêtre</option></select></div></div>
<div class="form-check"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $navigation->is_active ?? true) ? 'checked' : '' }}> Actif</label></div>
<button type="submit" class="btn btn-primary">{{ isset($navigation) ? 'Mettre à jour' : 'Créer' }}</button>
<a href="{{ route('admin.navigation.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
