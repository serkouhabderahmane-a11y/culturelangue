@extends('layouts.inner')

@section('title', $category->name_fr)
@section('meta_description', $category->description_fr)

@push('head')
<link rel="stylesheet" href="{{ asset('css/service-landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/model-4.css') }}">
<link rel="stylesheet" href="{{ asset('css/read-more.css') }}">
@endpush

@section('content')
<section class="page-header">
  <div class="page-header-bg"><img src="{{ asset($category->banner_image ?: $category->image) }}" alt="" loading="lazy"></div>
  <div class="container" style="grid-template-columns: 1fr;">
    <div class="hero-content">
      <div class="breadcrumb">
        <a href="{{ url('/') }}">Accueil</a>
        <span>→</span>
        <span>{{ $category->name_fr }}</span>
      </div>
      <h1>{!! $category->hero_title_html ?: $category->name_fr !!}</h1>
      <p class="hero-subtitle">{{ $category->hero_subtitle ?: $category->description_fr }}</p>
      @if($category->hero_chips)
      <div class="hero-chips">
        @foreach($category->hero_chips as $chip)
        <span class="hero-chip {{ $chip['class'] ?? '' }}">{!! $chip['icon'] ?? '' !!} {{ $chip['text'] }}</span>
        @endforeach
      </div>
      @endif
      <div class="hero-ctas">
        <a href="#programs" class="btn btn-primary">Voir les programmes &darr;</a>
      </div>
    </div>
  </div>
</section>

<div class="detail-layout" style="margin-top: 48px; grid-template-columns: 1fr; max-width: 1000px;">
  <div class="detail-main">

    @if($services->count() > 0)
    <div class="m4-section-card reveal" id="programs">
      <span class="section-badge section-badge--orange"><i class="fas fa-route"></i> Programmes</span>
      <h2 class="m4-h2">Choisissez votre <span class="text-gradient">parcours</span></h2>
      <p class="m4-sub">{{ $category->programs_intro ?: 'Découvrez nos programmes et choisissez la formule qui vous correspond.' }}</p>
      <div class="prog-icon-grid">
        @foreach($services as $service)
        <a href="{{ route('service.show', $service->slug) }}" class="prog-icon-card">
          @if($service->icon)
          <div class="prog-icon prog-icon--{{ $service->icon_color ?? 'green' }}">{!! $service->icon !!}</div>
          @endif
          <span class="prog-badge prog-badge--{{ $service->icon_color ?? 'green' }}">{{ $service->category->name_fr }}</span>
          <h4 class="prog-title">{{ $service->name_fr }}</h4>
          <p class="prog-summary">{{ $service->short_description_fr }}</p>
          @if($service->pricing_options)
          <div class="prog-tags">
            @foreach($service->pricing_options as $opt)
            <span>{{ $opt['label'] ?? $opt }}</span>
            @endforeach
          </div>
          @endif
          <div class="prog-footer">
            <span class="prog-price">{{ $service->price }}</span>
            <span class="prog-cta">Découvrir le programme &rarr;</span>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    @if($category->description_html)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--green"><i class="fas fa-info-circle"></i> Description</span>
      <h2 class="m4-h2">Description <span class="text-gradient">complète</span></h2>
      <div class="sl-description read-more">
        {!! $category->description_html !!}
      </div>
    </div>
    @endif

    @if($category->benefits)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--orange"><i class="fas fa-star"></i> Avantages</span>
      <h2 class="m4-h2">{!! $category->benefits_title ?: 'Pourquoi choisir ce <span class="text-gradient">programme</span> ?' !!}</h2>
      <p class="m4-sub">{{ $category->benefits_intro }}</p>
      <div class="sl-benefits-grid">
        @foreach($category->benefits as $benefit)
        <div class="sl-benefit-card">
          <div class="sl-benefit-icon">{{ $benefit['icon'] }}</div>
          <h3>{{ $benefit['title'] }}</h3>
          <p>{{ $benefit['text'] }}</p>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($category->audience)
    <div class="m4-section-card reveal">
      <span class="section-badge section-badge--blue"><i class="fas fa-users"></i> Pour qui</span>
      <h2 class="m4-h2">Pour <span class="text-gradient">qui</span> ?</h2>
      <p class="m4-sub">{{ $category->audience_intro }}</p>
      <div class="sl-audience-list">
        @foreach($category->audience as $item)
        <div class="sl-audience-item">
          <div class="sl-audience-icon">{{ $item['icon'] }}</div>
          <div class="sl-audience-content">
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['text'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

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

<section class="ph-cta">
  <div class="ph-cta-deco" aria-hidden="true"></div>
  <div class="ph-cta-inner">
    <h2>Prêt à commencer ?</h2>
    <p>Rejoignez nos étudiants et commencez votre parcours linguistique dès aujourd'hui.</p>
    <a href="{{ route('booking') }}" class="ph-btn ph-btn-white ph-btn-xl">
      <span>Réserver maintenant</span>
      <svg class="ph-btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>
@endsection
