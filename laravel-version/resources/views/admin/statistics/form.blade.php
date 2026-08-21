@extends('layouts.admin')
@section('title', isset($statistic) ? 'Modifier la statistique' : 'Nouvelle statistique')
@section('content')
<form method="POST" action="{{ isset($statistic) ? route('admin.statistics.update', $statistic) : route('admin.statistics.store') }}" class="admin-form">
@csrf @if(isset($statistic)) @method('PUT') @endif
<div class="form-row"><div class="form-group"><label for="label_fr">Label (FR) *</label><input type="text" name="label_fr" id="label_fr" value="{{ old('label_fr', $statistic->label_fr ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="label_en">Label (EN)</label><input type="text" name="label_en" id="label_en" value="{{ old('label_en', $statistic->label_en ?? '') }}" class="form-control"></div></div>
<div class="form-row"><div class="form-group"><label for="value">Valeur *</label><input type="text" name="value" id="value" value="{{ old('value', $statistic->value ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="suffix_fr">Suffixe (FR)</label><input type="text" name="suffix_fr" id="suffix_fr" value="{{ old('suffix_fr', $statistic->suffix_fr ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="suffix_en">Suffixe (EN)</label><input type="text" name="suffix_en" id="suffix_en" value="{{ old('suffix_en', $statistic->suffix_en ?? '') }}" class="form-control"></div></div>
<div class="form-group"><label for="order">Ordre</label><input type="number" name="order" id="order" value="{{ old('order', $statistic->order ?? 0) }}" class="form-control"></div>
<button type="submit" class="btn btn-primary">{{ isset($statistic) ? 'Mettre à jour' : 'Créer' }}</button>
<a href="{{ route('admin.statistics.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
