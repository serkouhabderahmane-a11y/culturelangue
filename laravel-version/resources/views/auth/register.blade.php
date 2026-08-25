<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — Cultulangues</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
</head>
<body>

  <!-- ═══ AUTH PAGE ═══ -->
  <div class="auth-page">
    <div style="position:absolute;inset:0;z-index:0;opacity:0.04;pointer-events:none"><img src="{{ asset('img/hero-maitrisez-langues.png') }}" alt="" style="width:100%;height:100%;object-fit:cover"></div>
    <div class="auth-card" style="max-width: 540px;">
      <a href="{{ url('/') }}" class="logo">
        <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues" class="logo-img">
      </a>
      <h2>Créer votre compte</h2>
      <p class="auth-subtitle">Rejoignez Cultulangues et commencez votre parcours linguistique</p>

      @if($errors->any())
      <div style="background:#fee;border:1px solid #fcc;border-radius:8px;padding:12px;margin-bottom:20px;font-size:0.85rem;color:#c33;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Prénom <span class="required">*</span></label>
            <input type="text" name="first_name" class="form-input" required placeholder="Votre prénom" value="{{ old('first_name') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Nom <span class="required">*</span></label>
            <input type="text" name="last_name" class="form-input" required placeholder="Votre nom" value="{{ old('last_name') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-input" required placeholder="votre@email.com" value="{{ old('email') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Mot de passe <span class="required">*</span></label>
          <div style="position: relative;">
            <input type="password" name="password" class="form-input" required placeholder="Minimum 8 caractères">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer le mot de passe <span class="required">*</span></label>
          <div style="position: relative;">
            <input type="password" name="password_confirmation" class="form-input" required placeholder="Répétez le mot de passe">
          </div>
        </div>
        <div class="form-group">
          <label class="form-checkbox">
            <input type="checkbox" required> J'accepte les <a href="#" style="color: var(--color-emerald); font-weight: 600;">conditions d'utilisation</a>
          </label>
        </div>
        <button type="submit" class="btn btn-primary w-full btn-lg">Créer mon compte</button>
      </form>

      <div class="auth-footer mt-lg">
        <span>Déjà un compte ?</span> <a href="{{ route('login') }}">Se connecter</a>
      </div>
    </div>
  </div>

</body>
</html>
