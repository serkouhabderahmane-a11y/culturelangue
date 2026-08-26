@extends('layouts.inner')

@section('title', $service->name_fr)
@section('meta_description', $service->short_description_fr)

@section('content')
<section class="page-header">
  <div class="page-header-bg"><img src="{{ asset($service->banner_image ?: $service->image ?: $service->category->banner_image ?: $service->category->image) }}" alt="" loading="lazy"></div>
  <div class="container" style="grid-template-columns: 1fr;">
    <div class="hero-content">
      <div class="breadcrumb">
        <a href="{{ url('/') }}">Accueil</a> /
        <a href="{{ route('services.category', $service->category->slug) }}">{{ $service->category->name_fr }}</a> /
        <span>{{ $service->name_fr }}</span>
      </div>
      <h1>{{ $service->name_fr }} <span class="text-gradient">– {{ $service->short_description_fr }}</span></h1>
      <p class="hero-subtitle">{{ Str::limit($service->description_fr, 200) }}</p>
      <div class="hero-chips">
        @if($service->duration)
        <span class="hero-chip"><i class="fas fa-clock"></i> {{ $service->duration }}</span>
        @endif
        @if($service->price)
        <span class="hero-chip m4-chip-green"><i class="fas fa-tag"></i> {{ $service->price }}</span>
        @endif
      </div>
      <div class="hero-ctas">
        <a href="#programs" class="btn btn-primary">Voir les programmes &darr;</a>
      </div>
    </div>
  </div>
</section>

<div class="sl-trust-strip">
  <div class="sl-trust-item"><i class="fas fa-check-circle"></i> Petits groupes (max 5)</div>
  <div class="sl-trust-item"><i class="fas fa-check-circle"></i> Certificat de fin de session</div>
  <div class="sl-trust-item"><i class="fas fa-check-circle"></i> Rapport de progression</div>
  <div class="sl-trust-item"><i class="fas fa-check-circle"></i> Nouvelle session chaque mois</div>
</div>

<div class="detail-layout" style="margin-top: 48px;">
  <div class="detail-main">

    @if($service->duration || $service->price)
    <div class="summary-pills">
      @if($service->duration)
      <span class="summary-pill"><i class="fas fa-clock"></i> {{ $service->duration }}</span>
      @endif
      @if($service->price)
      <span class="summary-pill"><i class="fas fa-tag"></i> {{ $service->price }}</span>
      @endif
    </div>
    @endif

    @if($service->benefits_fr)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--blue">Ce que vous obtenez</span>
      <div class="feature-cards">
        @foreach($service->benefits_fr as $benefit)
        <div class="feature-card">
          <div class="feature-card-icon"><i class="fas fa-check-circle"></i></div>
          <div class="feature-card-text"><p>{{ is_array($benefit) ? ($benefit['text'] ?? $benefit['title'] ?? '') : $benefit }}</p></div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($service->learning_objectives_fr)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--orange">Pourquoi ça fonctionne</span>
      <div class="feature-cards">
        @foreach($service->learning_objectives_fr as $objective)
        <div class="feature-card">
          <div class="feature-card-icon"><i class="fas fa-bullseye"></i></div>
          <div class="feature-card-text"><p>{{ is_array($objective) ? ($objective['text'] ?? $objective['title'] ?? '') : $objective }}</p></div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($service->price)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--green">Tarif</span>
      <p class="tarif-text">{{ $service->price }} – Évaluation de placement, {{ $service->duration ?? '' }} d'enseignement qualifié et rapport final inclus</p>
    </div>
    @endif

    @if($service->description_fr)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--green"><i class="fas fa-info-circle"></i> Description</span>
      <h2 class="m4-h2">Description <span class="text-gradient">complète</span></h2>
      <div class="sl-description read-more">
        {!! nl2br(e($service->description_fr)) !!}
      </div>
    </div>
    @endif

    <div class="m4-cta reveal">
      <h3>Prêt à progresser ?</h3>
      <p>Rejoignez la prochaine session de {{ $service->name_fr }} et gagnez en confiance.</p>
      <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary btn-lg">Réservez votre place &rarr;</a>
    </div>

  </div>

  <div class="detail-sidebar">
    <div class="booking-card">
      <div class="booking-card-header">
        <h3>{{ $service->name_fr }}</h3>
        <div class="bch-price">{{ $service->price }} <small>Tout compris</small></div>
      </div>
      <div class="booking-card-body">
        <div class="booking-facts">
          @if($service->duration)
          <div class="booking-fact"><span class="bf-icon"><i class="fas fa-clock"></i></span><span class="bf-value">{{ $service->duration }}</span><span class="bf-label">Durée</span></div>
          @endif
          <div class="booking-fact"><span class="bf-icon"><i class="fas fa-users"></i></span><span class="bf-value">5 max</span><span class="bf-label">Groupe</span></div>
        </div>
        <div class="trust-badges">
          <span class="trust-badge"><i class="fas fa-shield-alt"></i> Paiement sécurisé</span>
          <span class="trust-badge"><i class="fas fa-award"></i> Certificat inclus</span>
        </div>
        <div class="booking-cta">
          <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary">Réservez maintenant &rarr;</a>
        </div>
      </div>
    </div>

    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--blue"><i class="fas fa-route"></i> Démarche</span>
      <h2 class="m4-h2">Comment <span class="text-gradient">ça marche</span> ?</h2>
      <p class="m4-sub">Pour commencer votre inscription, suivez ces trois étapes simples :</p>
      <div class="sl-process">
        <div class="sl-process-step">
          <div class="sl-process-num">1</div>
          <h3 class="sl-process-title">Remplissez le formulaire de contact</h3>
          <p class="sl-process-desc">Indiquez vos informations de base afin que nous puissions ouvrir votre dossier.</p>
        </div>
        <div class="sl-process-step">
          <div class="sl-process-num">2</div>
          <h3 class="sl-process-title">Complétez le test de niveau</h3>
          <p class="sl-process-desc">Il nous permet d'évaluer votre compréhension écrite et votre expression écrite.</p>
        </div>
        <div class="sl-process-step">
          <div class="sl-process-num">3</div>
          <h3 class="sl-process-title">Prenez rendez-vous pour votre évaluation orale</h3>
          <p class="sl-process-desc">Vous rencontrerez notre évaluateur afin de déterminer votre niveau d'expression orale.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
