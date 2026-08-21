@extends('layouts.portal')
@section('title', 'Mon profil')
@section('sidebar')
@include('teacher.sidebar')
@endsection
@section('content')
<form method="POST" action="{{ route('teacher.profile.update') }}" class="admin-form">
@csrf @method('PUT')
<div class="form-row"><div class="form-group"><label for="first_name">Prénom</label><input type="text" name="first_name" id="first_name" value="{{ old('first_name', $teacher->first_name) }}" required class="form-control"></div>
<div class="form-group"><label for="last_name">Nom</label><input type="text" name="last_name" id="last_name" value="{{ old('last_name', $teacher->last_name) }}" required class="form-control"></div></div>
<div class="form-group"><label for="phone">Téléphone</label><input type="text" name="phone" id="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control"></div>
<div class="form-group"><label for="bio_fr">Bio (FR)</label><textarea name="bio_fr" id="bio_fr" rows="5" class="form-control">{{ old('bio_fr', $teacher->teacherProfile->bio_fr ?? '') }}</textarea></div>
<div class="form-group"><label for="bio_en">Bio (EN)</label><textarea name="bio_en" id="bio_en" rows="5" class="form-control">{{ old('bio_en', $teacher->teacherProfile->bio_en ?? '') }}</textarea></div>
<div class="form-group"><label for="hourly_rate">Taux horaire ($)</label><input type="number" name="hourly_rate" id="hourly_rate" step="0.01" value="{{ old('hourly_rate', $teacher->teacherProfile->hourly_rate ?? '') }}" class="form-control"></div>
<button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
@endsection
