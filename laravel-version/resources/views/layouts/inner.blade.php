<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $settings['site_name'] ?? 'Cultulangues') — {{ $settings['site_description'] ?? 'Formation linguistique' }}</title>
  <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? '')">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
  <link rel="stylesheet" href="{{ asset('css/service-landing.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/read-more.css') }}">
  <link rel="stylesheet" href="{{ asset('css/model-4.css') }}">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
  @stack('head')
</head>
<body>

  <!-- ═══ HEADER ═══ -->
  <header class="public-header" id="header">
    <div class="container">
      <a href="{{ url('/') }}" class="logo">
        <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues" class="logo-img">
      </a>
      <nav class="nav">
        <a href="{{ url('/') }}" data-i18n="nav.home">Accueil</a>
        <div class="dropdown">
          <button class="dropdown-trigger" data-i18n="nav.programs">Programmes</button>
          <div class="dropdown-menu">
            @foreach($navigationItems->whereNull('parent_id') as $parent)
              @if($parent->children->count() > 0)
                @foreach($parent->children as $child)
                <a href="{{ $child->url ?? url('/') }}" class="dropdown-item">
                  <span class="dropdown-item-title">{{ $child->label_fr }}</span>
                  <span class="dropdown-item-desc">{{ $child->label_fr }}</span>
                </a>
                @endforeach
              @else
              <a href="{{ $parent->url ?? url('/') }}" class="dropdown-item">
                <span class="dropdown-item-title">{{ $parent->label_fr }}</span>
              </a>
              @endif
            @endforeach
          </div>
        </div>
        <a href="{{ url('/pages/about') }}" class="{{ request()->is('pages/about*') ? 'active' : '' }}" data-i18n="nav.about">À propos</a>
        <a href="{{ route('contact') }}" class="{{ request()->is('contact*') ? 'active' : '' }}" data-i18n="nav.contact">Contact</a>
        <div class="nav-cta">
          <a href="{{ route('login') }}" class="nav-login" data-i18n="nav.login">Connexion</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm" data-i18n="nav.register">S'inscrire</a>
          <div class="nav-lang">
            <button class="lang-opt" data-lang="fr" onclick="window.switchLanguage && window.switchLanguage('fr')">FR</button>
            <span class="lang-sep">|</span>
            <button class="lang-opt" data-lang="en" onclick="window.switchLanguage && window.switchLanguage('en')">EN</button>
          </div>
        </div>
      </nav>
      <div class="hamburger">
        <span></span><span></span><span></span>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <!-- ═══ FOOTER ═══ -->
  <footer class="public-footer">
    <div class="footer-inner">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues" class="logo-img">
          </a>
          <p class="brand-desc" data-i18n="footer.brand">Formation linguistique & Préparation aux examens.</p>
        </div>
        <div>
          <h4 class="footer-heading" data-i18n="footer.courses">Cours</h4>
          <div class="footer-links">
            <a href="{{ url('/pages/parcours-linguistique') }}" data-i18n="nav.parcours">Parcours linguistique</a>
            <a href="{{ url('/pages/english-linguistic-pathway') }}">English Linguistic Pathway</a>
            <a href="{{ url('/pages/cap-sur-l-oral') }}" data-i18n="nav.oral">Cap sur l'oral</a>
            <a href="{{ url('/pages/tcf-quebec') }}">TCF Québec</a>
            <a href="{{ url('/pages/tcf-canada') }}">TCF Canada</a>
            <a href="{{ url('/pages/formation-en-solo') }}" data-i18n="nav.solo">Formation en solo</a>
            <a href="{{ url('/pages/workshops') }}" data-i18n="nav.ateliers">Ateliers</a>
            <a href="{{ route('contact') }}" data-i18n="nav.contact">Contact</a>
          </div>
        </div>
        <div>
          <h4 class="footer-heading" data-i18n="footer.info">Informations</h4>
          <div class="footer-links">
            <a href="{{ url('/pages/about') }}" data-i18n="footer.info.about">À propos</a>
            <a href="{{ route('contact') }}" data-i18n="footer.info.contact">Contact</a>
            <a href="#" data-i18n="footer.info.terms">Mentions légales</a>
            <a href="#" data-i18n="footer.info.privacy">Politique de confidentialité</a>
          </div>
        </div>
        <div>
          <h4 class="footer-heading" data-i18n="footer.contact.title">Contact</h4>
          <div class="footer-contact-item">
            <span class="fci-icon">✉</span>
            <span class="fci-text" data-i18n="footer.contact.email">{{ $settings['email'] ?? 'admin@cultulangues.ca' }}</span>
          </div>
          <div class="footer-contact-item">
            <span class="fci-icon">📞</span>
            <span class="fci-text" data-i18n="footer.contact.phone">{{ $settings['phone'] ?? '+1 (819) 271-9783' }}</span>
          </div>
          <div class="footer-social-row">
            <a href="#" title="Facebook">f</a>
            <a href="#" title="Instagram">◻</a>
            <a href="#" title="LinkedIn">in</a>
            <a href="#" title="YouTube">▶</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="footer-bottom-left">
          <span>© {{ date('Y') }} <span data-i18n="footer.copyright">Cultulangues. Tous droits réservés.</span></span>
          <span data-i18n="footer.made">🌿 Fait avec bienveillance</span>
        </div>
        <div class="lang-switcher">
          <button class="lang-opt" data-lang="fr" onclick="window.switchLanguage && window.switchLanguage('fr')" data-i18n="lang.fr">FR</button>
          <button class="lang-opt" data-lang="en" onclick="window.switchLanguage && window.switchLanguage('en')" data-i18n="lang.en">EN</button>
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
