@extends('layouts.public')

@section('title', $category->name_fr)
@section('meta_description', $category->description_fr)

@section('content')
<section class="page-header">
  <div class="page-header-bg"><img src="{{ asset($category->image) }}" alt="" loading="lazy"></div>
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <div class="breadcrumb"><a href="{{ url('/') }}">Accueil</a> / <span>{{ $category->name_fr }}</span></div>
      <h1>{{ $category->name_fr }}</h1>
      <p class="hero-subtitle">{{ $category->description_fr }}</p>
    </div>
  </div>
</section>

<section class="services-grid-section">
  <div class="container">
    <div class="services-grid">
      @foreach($category->activeServices as $service)
      <a href="{{ route('service.show', $service->slug) }}" class="service-card">
        @if($service->image)
        <div class="service-card-img">
          <img src="{{ asset($service->image) }}" alt="{{ $service->name_fr }}" loading="lazy">
        </div>
        @endif
        <div class="service-card-body">
          <h3>{{ $service->name_fr }}</h3>
          <p>{{ $service->short_description_fr }}</p>
          <div class="service-card-meta">
            @if($service->duration)
            <span><i class="fas fa-clock"></i> {{ $service->duration }}</span>
            @endif
            @if($service->price)
            <span><i class="fas fa-tag"></i> {{ $service->price }}</span>
            @endif
          </div>
          <span class="service-card-link">En savoir plus &rarr;</span>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

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
