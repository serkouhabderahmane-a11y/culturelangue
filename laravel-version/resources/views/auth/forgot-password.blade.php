@extends('layouts.public')

@section('title', 'Mot de passe oublié')

@section('content')
<section class="auth-section">
  <div class="container">
    <div class="auth-form-wrap">
      <h1>Mot de passe oublié</h1>
      @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
      @endif
      <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Envoyer le lien de réinitialisation</button>
      </form>
    </div>
  </div>
</section>
@endsection
