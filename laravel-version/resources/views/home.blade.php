@extends('layouts.public', ['body_class' => 'home-page'])

@section('title', $settings['site_name'] ?? 'Cultulangues')

@section('content')
<section class="ph-hero" id="hero">
  <div class="ph-hero-bg" aria-hidden="true">
    <div class="ph-hero-glow ph-hero-glow-1"></div>
    <div class="ph-hero-glow ph-hero-glow-2"></div>
    <div class="ph-hero-glow ph-hero-glow-3"></div>
    <div class="ph-hero-grid-pattern"></div>
  </div>
  <div class="ph-hero-container">
    <div class="ph-hero-content">
      <div class="ph-hero-badge ph-reveal">
        <div class="ph-hero-badge-dot"></div>
        <span>{{ $settings['hero_badge'] ?? 'École certifiée — +950 étudiants' }}</span>
      </div>
      <h1 class="ph-hero-h1">{!! $settings['hero_title'] ?? "Chez CultuLangues, nous construisons votre réussite" !!}</h1>
      <p class="ph-hero-sub">{{ $settings['hero_subtitle'] ?? "Vous souhaitez apprendre l'une des langues officielles du Canada pour :" }}</p>
      <div class="ph-hero-actions-row">
        <div class="ph-hero-checklist">
          <div class="ph-check-item">
            <div class="ph-check-icon ph-check-orange">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span>Faire évoluer votre carrière</span>
          </div>
          <div class="ph-check-item">
            <div class="ph-check-icon ph-check-green">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span>Réussir votre projet d'immigration</span>
          </div>
          <div class="ph-check-item">
            <div class="ph-check-icon ph-check-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span>Gagner confiance et autonomie dans la vie quotidienne</span>
          </div>
        </div>
        <div class="ph-hero-ctas">
          <a href="{{ route('register') }}" class="ph-btn ph-btn-primary ph-btn-hero">
            <span>{{ $settings['hero_cta_primary'] ?? "Découvrez nos parcours d'apprentissage" }}</span>
            <svg class="ph-btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <a href="{{ route('service.show', ['slug' => 'solo-5h']) }}" class="ph-btn ph-btn-ghost ph-btn-hero">
            <span>{{ $settings['hero_cta_secondary'] ?? "Choisissez le format qui vous ressemble" }}</span>
          </a>
        </div>
      </div>
    </div>
    <div class="ph-hero-visual">
      <div class="ph-hero-image-glow" aria-hidden="true"></div>
      <div class="ph-hero-image-shape">
        <img src="{{ asset('img/hero-maitrisez-langues.png') }}" alt="Cultulangues — Apprendre une langue" class="ph-hero-image" loading="eager">
      </div>
      <div class="ph-hero-float ph-hero-float-1">
        <div class="ph-hero-float-icon">👩‍🏫</div>
        <div class="ph-hero-float-text">
          <span class="ph-hero-float-value">+950 étudiants</span>
          <span class="ph-hero-float-label">accompagnés avec succès</span>
        </div>
      </div>
      <div class="ph-hero-float ph-hero-float-2">
        <div class="ph-hero-float-icon">⭐</div>
        <div class="ph-hero-float-text">
          <span class="ph-hero-float-value">4.9 / 5</span>
          <span class="ph-hero-float-label">note moyenne Google</span>
        </div>
      </div>
      <div class="ph-hero-float ph-hero-float-3">
        <div class="ph-hero-float-icon">🎓</div>
        <div class="ph-hero-float-text">
          <span class="ph-hero-float-value">12 ans</span>
          <span class="ph-hero-float-label">d'expérience</span>
        </div>
      </div>
    </div>
  </div>
  <div class="ph-hero-search-wrap">
    <div class="ph-hero-search">
      <svg class="ph-hero-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <span class="ph-hero-search-text">Que souhaitez-vous apprendre ?</span>
      <span class="ph-hero-search-btn">Explorer</span>
    </div>
  </div>
</section>

<section class="ph-stats" id="stats">
  <div class="ph-stats-inner">
    @foreach($statistics as $stat)
    <div class="ph-stat-item ph-reveal">
      <span class="ph-stat-value">{{ $stat->value }}{{ $stat->suffix_fr ?? '' }}</span>
      <span class="ph-stat-label">{{ $stat->label_fr }}</span>
    </div>
    @endforeach
  </div>
</section>

<section class="ph-explore" id="programmes">
  <div class="ph-explore-container">
    <div class="ph-explore-header ph-reveal">
      <span class="ph-eyebrow">Nos programmes</span>
      <h2 class="ph-explore-title">Choisissez votre <span class="ph-text-gradient">parcours</span></h2>
      <p class="ph-explore-subtitle">Six voies d'apprentissage pensées pour vos objectifs. Explorez celle qui vous ressemble.</p>
    </div>
    <div class="ph-journey">
      @foreach($categories as $index => $category)
      @if($loop->index % 3 === 0)
      <div class="ph-journey-row">
      @endif
        <a href="{{ route('services.category', $category->slug) }}" class="ph-journey-card ph-journey-card--{{ $loop->iteration }} ph-reveal" data-delay="{{ $loop->index }}">
          <div class="ph-journey-card-bg">
            <img src="{{ asset($category->image) }}" alt="" loading="lazy">
            <div class="ph-journey-card-overlay"></div>
          </div>
          <div class="ph-journey-card-content">
            <span class="ph-journey-card-count">{{ $category->activeServices->count() }} parcours disponibles</span>
            <h3 class="ph-journey-card-title">{{ $category->name_fr }}</h3>
            <p class="ph-journey-card-desc">{{ $category->description_fr }}</p>
            <span class="ph-journey-card-btn">
              <span>Découvrir</span>
              <svg class="ph-journey-card-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
          </div>
        </a>
      @if($loop->index % 3 === 2 || $loop->last)
      </div>
      @endif
      @endforeach
    </div>
  </div>
</section>

<section class="ph-oral" id="cap-sur-l-oral">
  <div class="ph-oral-container">
    <div class="ph-oral-header ph-reveal">
      <span class="ph-eyebrow">Cap sur l'oral</span>
      <h2 class="ph-oral-title">Un programme pensé pour <span class="ph-text-gradient">votre oral</span></h2>
      <p class="ph-oral-subtitle">Des parcours collaboratifs pour maîtriser l'expression orale en français avec confiance.</p>
    </div>
    <div class="ph-oral-grid">
      @php $oralCategory = $categories->firstWhere('slug', 'cap-sur-l-oral'); @endphp
      @if($oralCategory)
        @foreach($oralCategory->activeServices as $service)
        <a href="{{ route('service.show', $service->slug) }}" class="ph-oral-card ph-reveal">
          <div class="ph-oral-card-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
          </div>
          <h3 class="ph-oral-card-title">{{ $service->name_fr }}</h3>
          <p class="ph-oral-card-desc">{{ $service->short_description_fr }}</p>
          <span class="ph-oral-card-meta">{{ $service->duration }}</span>
          <span class="ph-oral-card-arrow">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </a>
        @endforeach
      @endif
    </div>
  </div>
</section>

<section class="ph-testimonials" id="temoignages">
  <div class="ph-testimonials-container">
    <div class="ph-testimonials-header ph-reveal">
      <span class="ph-eyebrow">Ils nous font confiance</span>
      <h2 class="ph-testimonials-title">Ce que nos <span class="ph-text-gradient">étudiants</span> en disent</h2>
    </div>
    <div class="ph-testimonials-grid">
      @foreach($testimonials as $testimonial)
      <div class="ph-testimonial-card ph-reveal">
        <div class="ph-testimonial-stars">{!! str_repeat('★', $testimonial->rating) !!}{!! str_repeat('☆', 5 - $testimonial->rating) !!}</div>
        <p class="ph-testimonial-text">{{ $testimonial->content_fr }}</p>
        <div class="ph-testimonial-author">
          <div class="ph-testimonial-avatar">{{ substr($testimonial->name_fr, 0, 1) }}</div>
          <div>
            <h4>{{ $testimonial->name_fr }}</h4>
            <span>{{ $testimonial->role_fr ?? '' }}</span>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="ph-faq" id="faq">
  <div class="ph-faq-container">
    <div class="ph-faq-header ph-reveal">
      <span class="ph-eyebrow">FAQ</span>
      <h2 class="ph-faq-title">Questions <span class="ph-text-gradient">fréquentes</span></h2>
    </div>
    <div class="ph-faq-list">
      @foreach($faqs as $faq)
      <div class="ph-faq-item ph-reveal">
        <button class="ph-faq-question" aria-expanded="false">
          <span>{{ $faq->question_fr }}</span>
          <svg class="ph-faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="ph-faq-answer" hidden>
          <p>{{ $faq->answer_fr }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="ph-cta">
  <div class="ph-cta-deco" aria-hidden="true"></div>
  <div class="ph-cta-inner ph-reveal">
    <h2>Prêt à commencer votre parcours ?</h2>
    <p>Rejoignez plus de 950 étudiants qui nous font confiance pour leur préparation linguistique.</p>
    <a href="{{ route('register') }}" class="ph-btn ph-btn-white ph-btn-xl">
      <span>Créer mon compte gratuit</span>
      <svg class="ph-btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <div class="ph-cta-trust">
      <div class="ph-cta-trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>Plus de 950 étudiants</span>
      </div>
      <div class="ph-cta-trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>Paiement sécurisé</span>
      </div>
      <div class="ph-cta-trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>Accompagnement personnalisé</span>
      </div>
    </div>
  </div>
</section>
@endsection
