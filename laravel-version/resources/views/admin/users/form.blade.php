@extends('layouts.admin')

@section('title', isset($user) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('content')
<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-form">
  @csrf
  @if(isset($user)) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group">
      <label for="first_name">Prénom *</label>
      <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required class="form-control">
    </div>
    <div class="form-group">
      <label for="last_name">Nom *</label>
      <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required class="form-control">
    </div>
  </div>

  <div class="form-group">
    <label for="email">Email *</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required class="form-control">
  </div>

  <div class="form-group">
    <label for="phone">Téléphone</label>
    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control">
  </div>

  <div class="form-group">
    <label for="password">{{ isset($user) ? 'Nouveau mot de passe (laisser vide pour conserver)' : 'Mot de passe *' }}</label>
    <input type="password" name="password" id="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
  </div>

  <div class="form-group">
    <label for="role">Rôle *</label>
    <select name="role" id="role" class="form-control" required>
      @foreach($roles as $role)
      <option value="{{ $role }}" {{ (old('role', $userRole ?? 'student') == $role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
      @endforeach
    </select>
  </div>

  <div class="form-check">
    <label>
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
      Actif
    </label>
  </div>

  <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Mettre à jour' : 'Créer' }}</button>
  <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Annuler</a>
</form>
@endsection
