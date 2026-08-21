@extends('layouts.admin')
@section('title', isset($page) ? 'Modifier la page' : 'Nouvelle page')
@section('content')
<form method="POST" action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="admin-form">
@csrf @if(isset($page)) @method('PUT') @endif
<div class="form-row"><div class="form-group"><label for="title_fr">Titre (FR) *</label><input type="text" name="title_fr" id="title_fr" value="{{ old('title_fr', $page->title_fr ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="title_en">Titre (EN)</label><input type="text" name="title_en" id="title_en" value="{{ old('title_en', $page->title_en ?? '') }}" class="form-control"></div></div>
<div class="form-group"><label for="slug">Slug *</label><input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug ?? '') }}" required class="form-control"></div>
<div class="form-group"><label for="template">Template</label>
<select name="template" id="template" class="form-control"><option value="default" {{ (old('template', $page->template ?? '') == 'default') ? 'selected' : '' }}>Par défaut</option>
<option value="contact" {{ (old('template', $page->template ?? '') == 'contact') ? 'selected' : '' }}>Contact</option>
<option value="programs" {{ (old('template', $page->template ?? '') == 'programs') ? 'selected' : '' }}>Programmes</option></select></div>
<div class="form-row"><div class="form-group"><label for="meta_title_fr">Meta titre (FR)</label><input type="text" name="meta_title_fr" id="meta_title_fr" value="{{ old('meta_title_fr', $page->meta_title_fr ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="meta_title_en">Meta titre (EN)</label><input type="text" name="meta_title_en" id="meta_title_en" value="{{ old('meta_title_en', $page->meta_title_en ?? '') }}" class="form-control"></div></div>
<div class="form-row"><div class="form-group"><label for="meta_description_fr">Meta description (FR)</label><textarea name="meta_description_fr" id="meta_description_fr" class="form-control">{{ old('meta_description_fr', $page->meta_description_fr ?? '') }}</textarea></div>
<div class="form-group"><label for="meta_description_en">Meta description (EN)</label><textarea name="meta_description_en" id="meta_description_en" class="form-control">{{ old('meta_description_en', $page->meta_description_en ?? '') }}</textarea></div></div>
<div class="form-group"><label for="content_fr">Contenu (FR)</label><textarea name="content_fr" id="content_fr" rows="10" class="form-control">{{ old('content_fr', $page->content_fr ?? '') }}</textarea></div>
<div class="form-group"><label for="content_en">Contenu (EN)</label><textarea name="content_en" id="content_en" rows="10" class="form-control">{{ old('content_en', $page->content_en ?? '') }}</textarea></div>
<button type="submit" class="btn btn-primary">{{ isset($page) ? 'Mettre à jour' : 'Créer' }}</button>
<a href="{{ route('admin.pages.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
