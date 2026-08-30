<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin') — Cultulangues</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  @stack('head')
</head>
<body>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="admin-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo">Cultulangues Admin</a>
      </div>
      <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="fas fa-tachometer-alt"></i> Tableau de bord
        </a>
        <a href="{{ route('admin.services.index') }}" class="admin-nav-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
          <i class="fas fa-cogs"></i> Services
        </a>
        <a href="{{ route('admin.testimonials.index') }}" class="admin-nav-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
          <i class="fas fa-quote-right"></i> Témoignages
        </a>
        <a href="{{ route('admin.faqs.index') }}" class="admin-nav-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
          <i class="fas fa-question-circle"></i> FAQ
        </a>
        <a href="{{ route('admin.statistics.index') }}" class="admin-nav-item {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}">
          <i class="fas fa-chart-bar"></i> Statistiques
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="admin-nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
          <i class="fas fa-calendar-check"></i> Réservations
        </a>
        <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
          <i class="fas fa-users"></i> Utilisateurs
        </a>
        <a href="{{ route('admin.pages.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
          <i class="fas fa-file"></i> Pages
        </a>
        <a href="{{ route('admin.navigation.index') }}" class="admin-nav-item {{ request()->routeIs('admin.navigation.*') ? 'active' : '' }}">
          <i class="fas fa-bars"></i> Navigation
        </a>
        <a href="{{ route('admin.media.index') }}" class="admin-nav-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
          <i class="fas fa-images"></i> Média
        </a>
        <a href="{{ route('admin.settings') }}" class="admin-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
          <i class="fas fa-cog"></i> Paramètres
        </a>
        <a href="{{ route('admin.analytics') }}" class="admin-nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
          <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="{{ route('admin.calendar') }}" class="admin-nav-item {{ request()->routeIs('admin.calendar*') ? 'active' : '' }}">
          <i class="fas fa-calendar"></i> Calendrier
        </a>
        <a href="{{ route('admin.payments') }}" class="admin-nav-item {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
          <i class="fas fa-credit-card"></i> Paiements
        </a>
        <hr>
        <a href="{{ url('/') }}" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Retour au site</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="admin-nav-item" style="background:none;border:none;cursor:pointer;width:100%;text-align:left"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
        </form>
      </nav>
    </aside>
    <main class="admin-main">
      <header class="admin-topbar">
        <h1>@yield('title', 'Tableau de bord')</h1>
        <div class="admin-topbar-user">
          <span>{{ auth()->user()->full_name }}</span>
        </div>
      </header>
      <div class="admin-content">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
      </div>
    </main>
  </div>
  @stack('scripts')
</body>
</html>
