@extends('layouts.inner')

@section('title', 'À propos')

@push('head')
<link rel="stylesheet" href="{{ asset('css/page-photos.css') }}">
@endpush

@section('content')
  <!-- ═══ MISSION ═══ -->
  <section class="about-mission">
    <div class="about-mission-bg"><img src="{{ asset('img/home/hero-client.png') }}" alt="" loading="lazy"></div>
    <div class="container container-sm">
      <h2 data-i18n="about.mission.title">Notre mission</h2>
      <p style="font-size: 1.125rem; line-height: 1.8; margin-top: var(--space-md);" data-i18n="about.mission.text" class="read-more">Chez Cultulangues, nous croyons que la maîtrise du français est la clé de la réussite linguistique et professionnelle. Notre mission est d'accompagner chaque apprenant avec bienveillance, professionnalisme et exigence vers la réussite de ses projets.</p>
    </div>
  </section>

  <!-- ═══ VALUES ═══ -->
  <section class="section reveal">
    <div class="page-deco page-deco-ring" style="top:-50px;right:-70px" aria-hidden="true"></div>
    <div class="page-deco page-deco-dots" style="bottom:20px;left:40px" aria-hidden="true"></div>
    <div class="container">
      <div class="section-header">
        <h2 data-i18n="about.pedagogy.title">Notre approche pédagogique</h2>
        <p data-i18n="about.pedagogy.subtitle">Une méthode qui place l'humain au centre de l'apprentissage.</p>
      </div>
      <div class="about-values-split stagger-children">
        <div class="about-values-photo">
          <img src="{{ asset('img/client-photo-6.png') }}" alt="" loading="lazy">
        </div>
        <div class="about-values">
          <div class="card about-value-card">
            <div class="value-icon">❤️</div>
            <h3 data-i18n="about.value1.title">Bienveillance</h3>
            <p data-i18n="about.value1.desc">Nous créons un environnement sécurisant où chaque apprenant peut progresser à son rythme, sans jugement.</p>
          </div>
          <div class="card about-value-card">
            <div class="value-icon">🎯</div>
            <h3 data-i18n="about.value2.title">Exigence</h3>
            <p data-i18n="about.value2.desc">Nous fixons des objectifs clairs et accompagnons chaque élève avec rigueur pour les atteindre.</p>
          </div>
          <div class="card about-value-card">
            <div class="value-icon">🤝</div>
            <h3 data-i18n="about.value3.title">Proximité</h3>
            <p data-i18n="about.value3.desc">Un suivi personnalisé et une écoute attentive pour répondre aux besoins spécifiques de chacun.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══ TRUST ═══ -->
  <section class="section section-cream reveal">
    <div class="page-deco page-deco-blob" style="top:10%;right:-3%" aria-hidden="true"></div>
    <div class="page-deco page-deco-ring-2" style="bottom:-40px;left:-50px" aria-hidden="true"></div>
    <div class="container">
      <div class="section-header">
        <h2 data-i18n="about.trust.title">Pourquoi nous faire confiance ?</h2>
      </div>
      <div class="about-trust-photo reveal">
        <img src="{{ asset('img/client-photo-3.png') }}" alt="" loading="lazy">
      </div>
      <div class="grid-3 stagger-children">
        <div class="card card-flat" style="text-align: center; padding: var(--space-xl);">
          <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">🏆</div>
          <h4 data-i18n="about.trust1.title">Des enseignants certifiés qui font vraiment la différence</h4>
          <p style="font-size: 0.875rem;" data-i18n="about.trust1.desc">Nos formateurs sont certifiés, expérimentés et sélectionnés pour leur excellence pédagogique. Ils savent transformer un cours en une expérience, créer des déclics, et vous faire progresser plus vite que vous ne l'imaginiez.<br><strong>Un enseignement de haut niveau, pensé pour des résultats visibles.</strong></p>
        </div>
        <div class="card card-flat" style="text-align: center; padding: var(--space-xl);">
          <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">📈</div>
          <h4 data-i18n="about.trust2.title">Une communauté internationale venue des quatre coins du monde</h4>
          <p style="font-size: 0.875rem;" data-i18n="about.trust2.desc">Cultulangues rassemble des apprenants issus de plusieurs pays, cultures et parcours. Vous pratiquez avec des personnes qui vivent au Canada, en Europe, en Afrique, en Amérique du Sud… une diversité qui enrichit chaque échange et ouvre vos horizons.<br><strong>Vous apprenez une langue, vous rejoignez une communauté mondiale.</strong></p>
        </div>
        <div class="card card-flat" style="text-align: center; padding: var(--space-xl);">
          <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">🌍</div>
          <h4 data-i18n="about.trust3.title">Des résultats rapides, concrets et durables</h4>
          <p style="font-size: 0.875rem;" data-i18n="about.trust3.desc">Nos apprenants voient la différence dès les premières semaines : plus d'aisance, plus de confiance, plus de précision. Chaque étape est mesurable, chaque progrès est réel, et chaque objectif devient atteignable.<br><strong>Votre réussite n'est pas un hasard — c'est notre méthode.</strong></p>
        </div>
      </div>
    </div>
  </section>
@endsection
