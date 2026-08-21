@extends('layouts.portal')
@section('title', 'Mon profil')
@section('sidebar')
@include('student.sidebar')
@endsection
@section('content')
<form method="POST" action="{{ route('student.profile.update') }}" class="admin-form">
@csrf @method('PUT')
<div class="form-row"><div class="form-group"><label for="first_name">Prénom</label><input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required class="form-control"></div>
<div class="form-group"><label for="last_name">Nom</label><input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required class="form-control"></div></div>
<div class="form-group"><label for="phone">Téléphone</label><input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-control"></div>
<div class="form-group"><label for="address">Adresse</label><input type="text" name="address" id="address" value="{{ old('address', $user->studentProfile->address ?? '') }}" class="form-control"></div>
<div class="form-row"><div class="form-group"><label for="city">Ville</label><input type="text" name="city" id="city" value="{{ old('city', $user->studentProfile->city ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="province">Province</label><input type="text" name="province" id="province" value="{{ old('province', $user->studentProfile->province ?? '') }}" class="form-control"></div>
<div class="form-group"><label for="postal_code">Code postal</label><input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->studentProfile->postal_code ?? '') }}" class="form-control"></div></div>
<button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
@endsection
