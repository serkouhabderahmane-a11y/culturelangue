@extends('layouts.inner')

@section('title', $service->name_fr)
@section('meta_description', $service->short_description_fr)

@section('content')
<section class="page-header">
  <div class="page-header-bg"><img src="{{ asset($service->image ?: $service->category->image) }}" alt="" loading="lazy"></div>
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
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
        <span class="hero-chip"><i class="fas fa-tag"></i> {{ $service->price }}</span>
        @endif
      </div>
      <div class="hero-ctas">
        <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary btn-lg">Réserver maintenant &rarr;</a>
        <a href="{{ route('contact') }}" class="btn btn-outline btn-lg">Nous contacter</a>
      </div>
    </div>
    <div class="hero-booking-card">
      <div class="hero-booking-header">
        <h3>{{ $service->name_fr }}</h3>
        <div class="hbh-price">{{ $service->price }} <small>Tout compris</small></div>
      </div>
      <div class="hero-booking-body">
        @if($service->duration)
        <div class="hero-booking-fact">
          <i class="fas fa-calendar-alt"></i>
          <span>Durée : {{ $service->duration }}</span>
        </div>
        @endif
        <div class="hero-booking-fact">
          <i class="fas fa-users"></i>
          <span>Groupes de 5 max</span>
        </div>
        <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary btn-full">Réserver cette formation</a>
      </div>
    </div>
  </div>
</section>

<section class="service-content">
  <div class="container">
    <div class="service-description">
      <h2>À propos de cette formation</h2>
      <div class="read-more">{!! nl2br(e($service->description_fr)) !!}</div>
    </div>

    @if($service->benefits_fr)
    <div class="service-benefits">
      <h2>Pourquoi choisir ce programme ?</h2>
      <ul class="benefits-list">
        @foreach($service->benefits_fr as $benefit)
        <li><i class="fas fa-check-circle"></i> {{ $benefit }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    @if($service->learning_objectives_fr)
    <div class="service-objectives">
      <h2>Objectifs d'apprentissage</h2>
      <ul class="objectives-list">
        @foreach($service->learning_objectives_fr as $objective)
        <li><i class="fas fa-bullseye"></i> {{ $objective }}</li>
        @endforeach
      </ul>
    </div>
    @endif
  </div>
</section>

@if($relatedServices->count() > 0)
<section class="related-services">
  <div class="container">
    <h2>Autres programmes dans cette catégorie</h2>
    <div class="services-grid">
      @foreach($relatedServices as $related)
      <a href="{{ route('service.show', $related->slug) }}" class="service-card">
        <div class="service-card-body">
          <h3>{{ $related->name_fr }}</h3>
          <p>{{ $related->short_description_fr }}</p>
          <span class="service-card-link">En savoir plus &rarr;</span>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="ph-cta">
  <div class="ph-cta-deco" aria-hidden="true"></div>
  <div class="ph-cta-inner">
    <h2>Prêt à commencer {{ $service->name_fr }} ?</h2>
    <p>Réservez votre place dès maintenant et commencez votre parcours linguistique.</p>
    <a href="{{ route('booking') }}?service={{ $service->slug }}" class="ph-btn ph-btn-white ph-btn-xl">
      <span>Réserver maintenant</span>
      <svg class="ph-btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>
@endsection
