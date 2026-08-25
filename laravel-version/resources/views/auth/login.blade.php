<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — Cultulangues</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
      --pink:#FF6B9D;--orange:#FF8E53;--yellow:#FFD93D;--blue:#4A90D9;--green:#2ECC71;
      --bg:#F4F6FA;--text:#2C3E50;--text-secondary:#7F8C8D;--border:#E8ECF1;
      --shadow-lg:0 20px 60px rgba(0,0,0,0.08);--radius-lg:20px;--radius-md:12px;--radius-full:9999px;
      --transition:300ms cubic-bezier(0.4,0,0.2,1);--font:'Plus Jakarta Sans',sans-serif;
    }
    html{font-size:15px}
    body{font-family:var(--font);background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden}
    body::before{content:'';position:fixed;top:-50%;left:-50%;width:200%;height:200%;background:transparent;pointer-events:none;z-index:0}

    .login-wrap{position:relative;z-index:1;width:100%;max-width:440px;animation:fadeInUp 0.5s ease}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

    .login-card{background:white;border-radius:var(--radius-lg);padding:40px;box-shadow:var(--shadow-lg);border:1px solid var(--border)}
    .login-brand{text-align:center;margin-bottom:32px}
    .login-brand-icon{width:52px;height:52px;border-radius:var(--radius-md);background:var(--orange);display:flex;align-items:center;justify-content:center;color:white;font-size:1.4rem;font-weight:800;margin:0 auto 12px}
    .login-brand h1{font-size:1.25rem;font-weight:800;color:var(--pink)}
    .login-brand p{font-size:0.82rem;color:var(--text-secondary);margin-top:4px}

    .form-group{margin-bottom:20px}
    .form-group label{display:block;font-size:0.78rem;font-weight:600;color:var(--text);margin-bottom:6px}
    .form-control{width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:0.9rem;outline:none;transition:all var(--transition);font-family:var(--font);background:var(--bg);color:var(--text)}
    .form-control:focus{border-color:var(--pink);box-shadow:0 0 0 4px rgba(255,107,157,0.1);background:white}
    .input-icon-wrap{position:relative}
    .input-icon-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-secondary);font-size:0.9rem}
    .input-icon-wrap .form-control{padding-left:40px}

    .login-btn{width:100%;padding:14px;border:none;border-radius:var(--radius-md);background:var(--orange);color:white;font-size:0.9rem;font-weight:700;cursor:pointer;transition:all var(--transition);font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:8px}
    .login-btn:hover{box-shadow:0 8px 24px rgba(255,142,83,0.3);transform:translateY(-1px)}
    .login-btn:active{transform:translateY(0)}
    .login-btn i{font-size:0.8rem}

    .login-footer{text-align:center;margin-top:20px;font-size:0.78rem;color:var(--text-secondary)}
    .login-footer a{color:var(--pink);text-decoration:none;font-weight:600}
    .login-footer a:hover{text-decoration:underline}

    @media(max-width:480px){
      .login-card{padding:28px 24px}
    }
  </style>
</head>
<body>

  <div class="login-wrap">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-brand-icon">🌿</div>
        <h1>Cultulangues</h1>
        <p>Connectez-vous à votre espace</p>
      </div>

      @if($errors->any())
      <div style="background:#fee;border:1px solid #fcc;border-radius:8px;padding:12px;margin-bottom:20px;font-size:0.85rem;color:#c33;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
          <label>Email</label>
          <div class="input-icon-wrap">
            <i class="fas fa-envelope"></i>
            <input class="form-control" type="email" name="email" placeholder="votre@email.com" value="{{ old('email') }}" required autofocus>
          </div>
        </div>
        <div class="form-group">
          <label>Mot de passe</label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input class="form-control" type="password" name="password" placeholder="••••••••" required>
          </div>
        </div>

        <button class="login-btn" type="submit">
          <span>Se connecter</span>
          <i class="fas fa-arrow-right"></i>
        </button>
      </form>

      <div class="login-footer">
        <a href="{{ url('/') }}">← Retour à l'accueil</a>
      </div>
    </div>
  </div>

</body>
</html>
