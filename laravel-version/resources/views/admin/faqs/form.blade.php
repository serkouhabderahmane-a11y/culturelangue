@extends('layouts.admin')
@section('title', isset($faq) ? 'Modifier la FAQ' : 'Nouvelle FAQ')
@section('content')
<form method="POST" action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="admin-form">
@csrf @if(isset($faq)) @method('PUT') @endif
<div class="form-row"><div class="form-group"><label for="question_fr">Question (FR) *</label><input type="text" name="question_fr" id="question_fr" value="{{ old('question_fr', $faq->question_fr ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="question_en">Question (EN)</label><input type="text" name="question_en" id="question_en" value="{{ old('question_en', $faq->question_en ?? '') }}" class="form-control"></div></div>
<div class="form-row"><div class="form-group"><label for="answer_fr">Réponse (FR) *</label><textarea name="answer_fr" id="answer_fr" rows="4" required class="form-control">{{ old('answer_fr', $faq->answer_fr ?? '') }}</textarea></div>
<div class="form-group"><label for="answer_en">Réponse (EN)</label><textarea name="answer_en" id="answer_en" rows="4" class="form-control">{{ old('answer_en', $faq->answer_en ?? '') }}</textarea></div></div>
<div class="form-row"><div class="form-group"><label for="category">Catégorie</label><input type="text" name="category" id="category" value="{{ old('category', $faq->category ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="order">Ordre</label><input type="number" name="order" id="order" value="{{ old('order', $faq->order ?? 0) }}" class="form-control"></div></div>
<div class="form-check"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}> Actif</label></div>
<button type="submit" class="btn btn-primary">{{ isset($faq) ? 'Mettre à jour' : 'Créer' }}</button>
<a href="{{ route('admin.faqs.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
