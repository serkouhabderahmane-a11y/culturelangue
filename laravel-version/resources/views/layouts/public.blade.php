<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $settings['site_name'] ?? 'Cultulangues') — {{ $settings['site_description'] ?? 'Formation linguistique & Préparation aux examens' }}</title>
  <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? '')">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/premium-home.css') }}">
  <link rel="stylesheet" href="{{ asset('css/read-more.css') }}">
  <link rel="stylesheet" href="{{ asset('css/service-themes.css') }}">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
  @stack('head')
</head>
<body class="{{ $body_class ?? '' }}">

  <!-- ═══════════════════════════════════════════
       NAVIGATION
       ═══════════════════════════════════════════ -->
  <header class="ph-nav" id="header">
    <div class="ph-nav-inner">
      <a href="{{ url('/') }}" class="ph-nav-logo">
        <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues">
      </a>
      <nav class="ph-nav-links">
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}" data-i18n="nav.home">Accueil</a>
        <div class="ph-dropdown">
          <button class="ph-dropdown-trigger" data-i18n="nav.programs">Programmes</button>
          <div class="ph-dropdown-menu">
            @foreach($navigationItems->whereNull('parent_id') as $parent)
              @if($parent->children->count() > 0)
                @foreach($parent->children as $child)
                <a href="{{ $child->url ?? url('/') }}" class="ph-dropdown-item">
                  <span class="ph-dd-title">{{ $child->label_fr }}</span>
                  <span class="ph-dd-desc">{{ $child->description_fr }}</span>
                </a>
                @endforeach
              @endif
            @endforeach
          </div>
        </div>
        <a href="{{ url('/pages/about') }}" data-i18n="nav.about">À propos</a>
        <a href="{{ route('contact') }}" data-i18n="nav.contact">Contact</a>
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
            <button type="submit" class="ph-nav-login" style="background:none;border:none;cursor:pointer" data-i18n="nav.logout">Déconnexion</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="ph-nav-login" data-i18n="nav.login">Connexion</a>
          <a href="{{ route('register') }}" class="ph-nav-cta" data-i18n="nav.register">S'inscrire</a>
        @endauth
        <div class="ph-nav-lang">
          <button class="ph-lang-opt" data-lang="fr" onclick="window.switchLanguage && window.switchLanguage('fr')">FR</button>
          <span class="ph-lang-sep">|</span>
          <button class="ph-lang-opt" data-lang="en" onclick="window.switchLanguage && window.switchLanguage('en')">EN</button>
        </div>
      </div>
      <div class="ph-hamburger" id="hamburger">
        <span></span><span></span><span></span>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <!-- ═══════════════════════════════════════════
       FOOTER
       ═══════════════════════════════════════════ -->
  <footer class="ph-footer">
    <div class="ph-footer-inner">
      <div class="ph-footer-top">
        <div class="ph-footer-brand">
          <a href="{{ url('/') }}" class="ph-footer-logo">
            <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues">
          </a>
          <p data-i18n="footer.brand">Formation linguistique & Préparation aux examens.</p>
          <div class="ph-footer-social">
            <a href="#" title="Facebook" class="ph-social-link">f</a>
            <a href="#" title="Instagram" class="ph-social-link">◻</a>
            <a href="#" title="LinkedIn" class="ph-social-link">in</a>
            <a href="#" title="YouTube" class="ph-social-link">▶</a>
          </div>
        </div>
        <div class="ph-footer-col">
          <h4 data-i18n="footer.courses">Cours</h4>
          <a href="{{ url('/services/category/parcours-linguistique') }}" data-i18n="nav.parcours">Parcours linguistique</a>
          <a href="{{ url('/services/category/english-linguistic-pathway') }}">English Linguistic Pathway</a>
          <a href="{{ url('/services/category/cap-sur-l-oral') }}" data-i18n="nav.oral">Cap sur l'oral</a>
          <a href="{{ url('/services/category/tcf-quebec') }}">TCF Québec</a>
          <a href="{{ url('/services/category/tcf-canada') }}">TCF Canada</a>
          <a href="{{ url('/services/category/formation-en-solo') }}" data-i18n="nav.solo">Formation en solo</a>
          <a href="{{ url('/services/category/ateliers') }}" data-i18n="nav.ateliers">Ateliers</a>
          <a href="{{ route('contact') }}" data-i18n="nav.contact">Contact</a>
        </div>
        <div class="ph-footer-col">
          <h4 data-i18n="footer.info">Informations</h4>
          <a href="{{ url('/pages/about') }}" data-i18n="footer.info.about">À propos</a>
          <a href="{{ route('contact') }}" data-i18n="footer.info.contact">Contact</a>
          <a href="#" data-i18n="footer.info.terms">Mentions légales</a>
          <a href="#" data-i18n="footer.info.privacy">Politique de confidentialité</a>
        </div>
        <div class="ph-footer-col">
          <h4 data-i18n="footer.contact.title">Contact</h4>
          <div class="ph-footer-contact">
            <span>✉</span>
            <span data-i18n="footer.contact.email">{{ $settings['email'] ?? 'admin@cultulangues.ca' }}</span>
          </div>
          <div class="ph-footer-contact">
            <span>📞</span>
            <span data-i18n="footer.contact.phone">{{ $settings['phone'] ?? '+1 (819) 271-9783' }}</span>
          </div>
          <div class="ph-footer-contact">
            <span>📍</span>
            <span>{{ $settings['address'] ?? '468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)' }}</span>
          </div>
        </div>
      </div>
      <div class="ph-footer-bottom">
        <span>© {{ date('Y') }} <span data-i18n="footer.copyright">Cultulangues. Tous droits réservés.</span></span>
        <span data-i18n="footer.made">🌿 Fait avec bienveillance</span>
        <div class="ph-footer-lang">
          <button class="ph-lang-opt" data-lang="fr" onclick="window.switchLanguage && window.switchLanguage('fr')" data-i18n="lang.fr">FR</button>
          <button class="ph-lang-opt" data-lang="en" onclick="window.switchLanguage && window.switchLanguage('en')" data-i18n="lang.en">EN</button>
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
