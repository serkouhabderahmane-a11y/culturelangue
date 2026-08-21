@extends('layouts.public')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<section class="auth-section">
  <div class="container">
    <div class="auth-form-wrap">
      <h1>Réinitialiser le mot de passe</h1>
      <form method="POST" action="{{ route('password.store') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control">
        </div>
        <div class="form-group">
          <label for="password">Nouveau mot de passe</label>
          <input type="password" name="password" id="password" required class="form-control">
        </div>
        <div class="form-group">
          <label for="password_confirmation">Confirmer le mot de passe</label>
          <input type="password" name="password_confirmation" id="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Réinitialiser le mot de passe</button>
      </form>
    </div>
  </div>
</section>
@endsection
