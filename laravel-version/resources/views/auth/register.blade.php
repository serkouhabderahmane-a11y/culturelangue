@extends('layouts.public')

@section('title', 'Inscription')

@section('content')
<section class="page-header">
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <h1>Créer un compte</h1>
      <p class="hero-subtitle">Rejoignez Cultulangues et commencez votre parcours linguistique.</p>
    </div>
  </div>
</section>

<section class="auth-section">
  <div class="container">
    <div class="auth-form-wrap">
      <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label for="first_name">Prénom *</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required class="form-control @error('first_name') is-invalid @enderror">
            @error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-group">
            <label for="last_name">Nom *</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required class="form-control @error('last_name') is-invalid @enderror">
            @error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control @error('email') is-invalid @enderror">
          @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label for="phone">Téléphone</label>
          <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="form-control">
        </div>
        <div class="form-group">
          <label for="password">Mot de passe *</label>
          <input type="password" name="password" id="password" required class="form-control @error('password') is-invalid @enderror">
          @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label for="password_confirmation">Confirmer le mot de passe *</label>
          <input type="password" name="password_confirmation" id="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>
        <div class="auth-links">
          <a href="{{ route('login') }}">Déjà un compte ? Connectez-vous</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
