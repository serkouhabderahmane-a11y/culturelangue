@extends('layouts.public')

@section('title', 'Connexion')

@section('content')
<section class="page-header">
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <h1>Connexion</h1>
      <p class="hero-subtitle">Connectez-vous à votre compte Cultulangues.</p>
    </div>
  </div>
</section>

<section class="auth-section">
  <div class="container">
    <div class="auth-form-wrap">
      <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror">
          @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" name="password" id="password" required class="form-control @error('password') is-invalid @enderror">
          @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group form-check">
          <label>
            <input type="checkbox" name="remember"> Se souvenir de moi
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        <div class="auth-links">
          <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
          <a href="{{ route('register') }}">Créer un compte</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
