<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') — Cultulangues</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  @stack('head')
</head>
<body>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="admin-sidebar-header">
        <a href="{{ url('/') }}" class="admin-logo">Cultulangues</a>
      </div>
      <nav class="admin-nav">
        @yield('sidebar')
        <hr>
        <a href="{{ url('/') }}" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Retour au site</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button type="submit" class="admin-nav-item" style="background:none;border:none;cursor:pointer;width:100%;text-align:left"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
        </form>
      </nav>
    </aside>
    <main class="admin-main">
      <header class="admin-topbar">
        <h1>@yield('title')</h1>
        <div class="admin-topbar-user"><span>{{ auth()->user()->full_name }}</span></div>
      </header>
      <div class="admin-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @yield('content')
      </div>
    </main>
  </div>
  @stack('scripts')
</body>
</html>
