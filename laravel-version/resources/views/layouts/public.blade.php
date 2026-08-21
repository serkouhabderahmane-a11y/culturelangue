<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'fr' ? 'fr' : 'en' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $settings['site_name'] ?? 'Cultulangues') — {{ $settings['site_description'] ?? 'Formation linguistique' }}</title>
  <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? '')">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/premium-home.css') }}">
  <link rel="stylesheet" href="{{ asset('css/read-more.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#x1F33F;</text></svg>">
  @stack('head')
</head>
<body class="@yield('body_class')">
  <header class="ph-nav" id="header">
    <div class="ph-nav-inner">
      <a href="{{ url('/') }}" class="ph-nav-logo">
        <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues">
      </a>
      <nav class="ph-nav-links">
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Accueil</a>
        <div class="ph-dropdown">
          <button class="ph-dropdown-trigger">Programmes</button>
          <div class="ph-dropdown-menu">
            @foreach($navigationItems->whereNull('parent_id') as $parent)
              @if($parent->children->count() > 0)
                @foreach($parent->children as $child)
                <a href="{{ $child->url ?? url($child->route ? route($child->route, ['slug' => str_replace('/services/', '', $child->url)]) : $child->url) }}" class="ph-dropdown-item">
                  <span class="ph-dd-title">{{ $child->label_fr }}</span>
                  <span class="ph-dd-desc">{{ $child->label_fr }} — description</span>
                </a>
                @endforeach
              @else
              <a href="{{ $parent->url ?? url('/') }}" class="ph-dropdown-item">
                <span class="ph-dd-title">{{ $parent->label_fr }}</span>
              </a>
              @endif
            @endforeach
          </div>
        </div>
        <a href="{{ url('/pages/about') }}">À propos</a>
        <a href="{{ route('contact') }}">Contact</a>
      </nav>
      <div class="ph-nav-actions">
        @auth
          @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.dashboard') }}" class="ph-nav-cta">Admin</a>
          @elseif(auth()->user()->hasRole('teacher'))
            <a href="{{ route('teacher.dashboard') }}" class="ph-nav-cta">Dashboard</a>
          @else
            <a href="{{ route('student.dashboard') }}" class="ph-nav-cta">Dashboard</a>
          @endif
          <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="ph-nav-login" style="background:none;border:none;cursor:pointer">Déconnexion</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="ph-nav-login">Connexion</a>
          <a href="{{ route('register') }}" class="ph-nav-cta">S'inscrire</a>
        @endauth
        <div class="ph-nav-lang">
          <button class="ph-lang-opt" data-lang="fr">FR</button>
          <span class="ph-lang-sep">|</span>
          <button class="ph-lang-opt" data-lang="en">EN</button>
        </div>
      </div>
      <div class="ph-hamburger" id="hamburger"><span></span><span></span><span></span></div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <footer class="ph-footer">
    <div class="ph-footer-inner">
      <div class="ph-footer-top">
        <div class="ph-footer-brand">
          <a href="{{ url('/') }}" class="ph-footer-logo">
            <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues">
          </a>
          <p>Formation linguistique & Préparation aux examens.</p>
          <div class="ph-footer-social">
            <a href="#" title="Facebook" class="ph-social-link">f</a>
            <a href="#" title="Instagram" class="ph-social-link">◻</a>
            <a href="#" title="LinkedIn" class="ph-social-link">in</a>
            <a href="#" title="YouTube" class="ph-social-link">▶</a>
          </div>
        </div>
        <div class="ph-footer-col">
          <h4>Cours</h4>
          @foreach($navigationItems->whereNull('parent_id') as $item)
            @if($item->children->count() > 0)
              @foreach($item->children as $child)
                <a href="{{ $child->url ?? '#' }}">{{ $child->label_fr }}</a>
              @endforeach
            @elseif($item->route !== 'home')
              <a href="{{ $item->url ?? '#' }}">{{ $item->label_fr }}</a>
            @endif
          @endforeach
        </div>
        <div class="ph-footer-col">
          <h4>Informations</h4>
          <a href="{{ url('/pages/about') }}">À propos</a>
          <a href="{{ route('contact') }}">Contact</a>
          <a href="#">Mentions légales</a>
          <a href="#">Politique de confidentialité</a>
        </div>
        <div class="ph-footer-col">
          <h4>Contact</h4>
          <div class="ph-footer-contact">
            <span>✉</span>
            <span>{{ $settings['email'] ?? 'Admin@cultulangues.ca' }}</span>
          </div>
          <div class="ph-footer-contact">
            <span>📍</span>
            <span>{{ $settings['address'] ?? '468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)' }}</span>
          </div>
        </div>
      </div>
      <div class="ph-footer-bottom">
        <span>© {{ date('Y') }} Cultulangues. Tous droits réservés.</span>
        <span>🌿 Fait avec bienveillance</span>
        <div class="ph-footer-lang">
          <button class="ph-lang-opt" data-lang="fr">FR</button>
          <button class="ph-lang-opt" data-lang="en">EN</button>
        </div>
      </div>
    </div>
  </footer>

  <div class="mobile-nav-overlay"></div>

  <script src="{{ asset('js/content-loader.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/read-more.js') }}"></script>
  @stack('scripts')
</body>
</html>
