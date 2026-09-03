@extends('layouts.inner')

@section('title', 'Contact')

@push('head')
<link rel="stylesheet" href="{{ asset('css/page-photos.css') }}">
<style>
  .dmp-wrap{background:var(--color-bg-warm);border:1px solid var(--color-border-light);border-radius:var(--radius-2xl);padding:clamp(32px,5vw,64px)}
  .dmp-head{text-align:center;max-width:620px;margin:0 auto var(--space-2xl)}
  .dmp-title{font-size:clamp(1.8rem,3.5vw,2.4rem);font-weight:800;letter-spacing:-0.03em;line-height:1.15;margin:var(--space-md) 0 var(--space-sm)}
  .dmp-sub{color:var(--color-text-secondary);font-size:1rem;margin:0}
  .dmp-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-lg)}
  .dmp-step{position:relative;background:var(--color-white);border:1px solid var(--color-border-light);border-radius:var(--radius-xl);padding:var(--space-xl);box-shadow:var(--shadow-sm);transition:all var(--transition-base)}
  .dmp-step:hover{transform:translateY(-4px);box-shadow:var(--shadow-card-hover);border-color:var(--color-border)}
  .dmp-step-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-md)}
  .dmp-num{width:56px;height:56px;border-radius:var(--radius-full);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:var(--color-white);letter-spacing:-0.02em}
  .dmp-ico{width:44px;height:44px;border-radius:var(--radius-md);display:inline-flex;align-items:center;justify-content:center;font-size:1.25rem}
  .dmp-step--blue .dmp-num{background:var(--color-blue)}
  .dmp-step--green .dmp-num{background:var(--color-green)}
  .dmp-step--pink .dmp-num{background:var(--color-pink)}
  .dmp-step--blue .dmp-ico{background:var(--ph-blue-bg)}
  .dmp-step--green .dmp-ico{background:var(--ph-green-bg)}
  .dmp-step--pink .dmp-ico{background:var(--ph-orange-bg)}
  .dmp-step-title{font-size:1.0625rem;font-weight:700;letter-spacing:-0.02em;margin-bottom:var(--space-sm)}
  .dmp-step-desc{font-size:0.875rem;color:var(--color-text-secondary);line-height:1.65;margin:0}
  @media (min-width:901px){
    .dmp-step:not(:last-child)::after{content:'';position:absolute;top:calc(var(--space-xl) + 28px);right:calc(-1 * var(--space-lg) - 2px);width:calc(var(--space-lg) + 4px);height:2px;background:var(--color-border)}
  }
  @media (max-width:900px){
    .dmp-steps{grid-template-columns:1fr;gap:var(--space-md)}
    .dmp-step:not(:last-child)::after{content:'';position:absolute;left:60px;bottom:calc(-1 * var(--space-md) - 2px);width:2px;height:calc(var(--space-md) + 4px);background:var(--color-border)}
  }
</style>
@endpush

@section('content')
  <!-- â•â•â• HERO â•â•â• -->
  <section class="contact-hero">
    <div class="contact-hero-bg">
      <div class="contact-hero-blob"></div>
      <div class="contact-hero-blob contact-hero-blob-2"></div>
    </div>
    <div class="container">
      <div class="contact-hero-inner">
        <span class="contact-hero-badge">âœ¦ Nous sommes lÃ  pour vous</span>
        <h1>Parlons de <span class="text-gradient">votre projet</span></h1>
        <p class="contact-hero-desc read-more">Que vous cherchiez un programme adaptÃ©, des informations sur nos formations, ou simplement un conseil personnalisÃ©, notre Ã©quipe est prÃªte Ã  vous guider.</p>
        <div class="contact-hero-features">
          <span><i class="ch-icon">âœ¦</i> RÃ©ponse sous 24 h</span>
          <span><i class="ch-icon">âœ¦</i> Conseils personnalisÃ©s</span>
          <span><i class="ch-icon">âœ¦</i> Accompagnement gratuit</span>
        </div>
        <a href="#contact-form" class="btn btn-primary btn-lg">Envoyer un message <span style="font-size:0.8em;margin-left:4px">â†’</span></a>
      </div>
    </div>
  </section>

  <!-- â•â•â• MAIN CONTACT AREA â•â•â• -->
  <section class="contact-main section" id="contact-form">
    <div class="page-deco page-deco-ring" style="top:-60px;right:-80px" aria-hidden="true"></div>
    <div class="container">
      @if(session('success'))
      <div style="background:#d4edda;border:1px solid #c3e6cb;border-radius:8px;padding:16px;margin-bottom:24px;color:#155724;">
        {{ session('success') }}
      </div>
      @endif
      <div class="contact-layout">
        <!-- LEFT: FORM -->
        <div class="contact-form-col">
          <div class="contact-form-card">
            <div class="contact-form-header">
              <h2>Parlez-nous de votre projet</h2>
              <p>Nous vous rÃ©pondons rapidement pour vous aider Ã  choisir la meilleure solution.</p>
            </div>
            <form class="contact-form" method="POST" action="{{ route('contact.send') }}">
              @csrf
              <div class="form-row-2">
                <div class="form-group">
                  <label class="form-label">PrÃ©nom <span class="required">*</span></label>
                  <input type="text" name="first_name" class="form-input" required placeholder="Votre prÃ©nom" value="{{ old('first_name') }}">
                  @error('first_name')<span style="color:red;font-size:0.85rem">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                  <label class="form-label">Nom <span class="required">*</span></label>
                  <input type="text" name="last_name" class="form-input" required placeholder="Votre nom" value="{{ old('last_name') }}">
                  @error('last_name')<span style="color:red;font-size:0.85rem">{{ $message }}</span>@enderror
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" class="form-input" required placeholder="votre@email.com" value="{{ old('email') }}">
                @error('email')<span style="color:red;font-size:0.85rem">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">TÃ©lÃ©phone <span class="form-label-note">(optionnel)</span></label>
                <input type="tel" name="phone" class="form-input" placeholder="+33 6 12 34 56 78" value="{{ old('phone') }}">
              </div>
              <div class="form-group">
                <label class="form-label">Sujet <span class="required">*</span></label>
                <select name="subject" class="form-select" required>
                  <option value="">SÃ©lectionnez un sujet</option>
                  <option {{ old('subject') === 'Information sur les programmes' ? 'selected' : '' }}>Information sur les programmes</option>
                  <option {{ old('subject') === 'Inscription' ? 'selected' : '' }}>Inscription</option>
                  <option {{ old('subject') === 'Question TCF / Examens' ? 'selected' : '' }}>Question TCF / Examens</option>
                  <option {{ old('subject') === 'Test de niveau / Ã‰valuation orale' ? 'selected' : '' }}>Test de niveau / Ã‰valuation orale</option>
                  <option {{ old('subject') === 'Ateliers' ? 'selected' : '' }}>Ateliers</option>
                  <option {{ old('subject') === 'Autre' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('subject')<span style="color:red;font-size:0.85rem">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">Message <span class="required">*</span></label>
                <textarea name="message" class="form-textarea" required rows="5" placeholder="Bonjour, je souhaiterais obtenir plus d'informations sur...">{{ old('message') }}</textarea>
                @error('message')<span style="color:red;font-size:0.85rem">{{ $message }}</span>@enderror
              </div>
              <div class="form-group form-group-consent">
                <label class="form-checkbox">
                  <input type="checkbox" name="consent" checked>
                  <span class="checkbox-mark"></span>
                  <span class="checkbox-label">J'accepte d'Ãªtre recontactÃ©(e) dans le cadre de ma demande</span>
                </label>
              </div>
              <button type="submit" class="btn btn-primary btn-lg w-full contact-submit">
                <span>Envoyer ma demande</span>
                <span class="contact-submit-arrow">â†’</span>
              </button>
            </form>
          </div>
        </div>

        <!-- RIGHT: INFO PANELS -->
        <div class="contact-info-col">
          <!-- Photo -->
          <div class="contact-photo">
            <img src="{{ asset('img/client-photo-1.png') }}" alt="" loading="lazy">
          </div>

          <!-- Nous joindre -->
          <div class="contact-panel">
            <div class="contact-panel-accent"></div>
            <h3>Nous joindre</h3>
            <div class="contact-panel-items">
              <div class="contact-panel-item">
                <div class="cpi-icon">âœ‰</div>
                <div>
                  <div class="cpi-label">Email</div>
                  <div class="cpi-value">{{ $settings['email'] ?? 'admin@cultulangues.ca' }}</div>
                </div>
              </div>
              <div class="contact-panel-item">
                <div class="cpi-icon">ðŸ“ž</div>
                <div>
                  <div class="cpi-label">TÃ©lÃ©phone</div>
                  <div class="cpi-value">{{ $settings['phone'] ?? '873-973-0513' }}</div>
                </div>
              </div>
              <div class="contact-panel-item">
                <div class="cpi-icon">ðŸ•</div>
                <div>
                  <div class="cpi-label">Horaires</div>
                  <div class="cpi-value">Lunâ€“Ven : 9 h â€“ 19 h<br>Sam : 10 h â€“ 16 h</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Besoin d'aide -->
          <div class="contact-panel contact-panel-help">
            <div class="contact-panel-accent"></div>
            <h3>Besoin d'aide pour choisir ?</h3>
            <p class="read-more">Vous ne savez pas quel programme correspond le mieux Ã  votre niveau, vos objectifs ou votre emploi du temps ? Nous vous aidons Ã  y voir plus clair, sans engagement.</p>
            <ul class="contact-help-list">
              <li>âœ¦ Ã‰valuation de votre niveau</li>
              <li>âœ¦ Recommandation personnalisÃ©e</li>
              <li>âœ¦ RÃ©ponse Ã  vos questions</li>
              <li>âœ¦ Aide Ã  l'inscription</li>
            </ul>
          </div>

          <!-- PrÃªt Ã  commencer -->
          <div class="contact-panel contact-panel-cta">
            <div class="contact-panel-accent"></div>
            <h3>DÃ©jÃ  prÃªt Ã  commencer ?</h3>
            <p>Passez directement Ã  l'action :</p>
            <div class="contact-cta-links">
              <a href="{{ url('/') }}" class="btn btn-primary w-full"><i class="ch-icon">âœ¦</i> DÃ©couvrir nos programmes</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- â•â•â• TRUST / REASSURANCE â•â•â• -->
  <section class="section section-alt reveal">
    <div class="page-deco page-deco-dots" style="bottom:20px;left:40px" aria-hidden="true"></div>
    <div class="container">
      <div class="section-header">
        <h2>Pourquoi nous <span class="text-gradient">contacter</span> ?</h2>
        <p>Nous sommes lÃ  pour vous accompagner Ã  chaque Ã©tape de votre projet linguistique.</p>
      </div>
      <div class="contact-trust-grid">
        <div class="contact-trust-card">
          <div class="ct-icon"><span>âœ¦</span></div>
          <h4>Choix du programme</h4>
          <p>Vous hÃ©sitez entre un parcours en groupe ou un accompagnement solo ? On vous guide.</p>
        </div>
        <div class="contact-trust-card">
          <div class="ct-icon"><span>âœ¦</span></div>
          <h4>PrÃ©paration TCF</h4>
          <p>Des questions sur les examens officiels ? Nos Ã©quipes vous renseignent prÃ©cisÃ©ment.</p>
        </div>
        <div class="contact-trust-card">
          <div class="ct-icon"><span>âœ¦</span></div>
          <h4>Inscription & suivi</h4>
          <p>Un problÃ¨me d'inscription ou besoin d'un suivi personnalisÃ© ? Nous sommes Ã  votre Ã©coute.</p>
        </div>
        <div class="contact-trust-card">
          <div class="ct-icon"><span>âœ¦</span></div>
          <h4>RÃ©ponse rapide</h4>
          <p>Nous rÃ©pondons Ã  toutes les demandes sous 24 heures ouvrables, souvent bien plus tÃ´t.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- â•â• DÃ‰MARCHE â•â• -->
  <section class="section reveal">
    <div class="container">
      <div class="dmp-wrap">
        <div class="dmp-head">
          <span class="section-badge section-badge--blue">DÃ©marche</span>
          <h2 class="dmp-title">Comment <span class="text-gradient">Ã§a marche</span> ?</h2>
          <p class="dmp-sub">Pour commencer votre inscription, suivez ces trois Ã©tapes simples :</p>
        </div>
        <div class="dmp-steps">
          <div class="dmp-step dmp-step--blue">
            <div class="dmp-step-top">
              <span class="dmp-num">1</span>
              <span class="dmp-ico">ðŸ“</span>
            </div>
            <h3 class="dmp-step-title">Remplissez le formulaire de contact</h3>
            <p class="dmp-step-desc">Indiquez vos informations de base afin que nous puissions ouvrir votre dossier.</p>
          </div>
          <div class="dmp-step dmp-step--green">
            <div class="dmp-step-top">
              <span class="dmp-num">2</span>
              <span class="dmp-ico">ðŸ“Š</span>
            </div>
            <h3 class="dmp-step-title">ComplÃ©tez le test de niveau</h3>
            <p class="dmp-step-desc">Il nous permet d'Ã©valuer votre comprÃ©hension Ã©crite et votre expression Ã©crite.</p>
          </div>
          <div class="dmp-step dmp-step--pink">
            <div class="dmp-step-top">
              <span class="dmp-num">3</span>
              <span class="dmp-ico">ðŸ—£ï¸</span>
            </div>
            <h3 class="dmp-step-title">Prenez rendez-vous pour votre Ã©valuation orale</h3>
            <p class="dmp-step-desc">Vous rencontrerez notre Ã©valuateur afin de dÃ©terminer votre niveau d'expression orale.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- â•â•â• CTA BANNER â•â•â• -->
  <section class="section reveal">
    <div class="container">
      <div class="cta-banner contact-cta-banner">
        <h2>Encore une question ?</h2>
        <p>Nous sommes Ã  votre disposition pour discuter de votre projet. Chaque parcours est unique, et nous serons ravis de vous aider Ã  construire le vÃ´tre.</p>
        <div class="contact-cta-banner-actions">
          <a href="tel:+18739730513" class="btn btn-white btn-lg"><span>ðŸ“ž</span> 873-973-0513</a>
          <a href="mailto:admin@cultulangues.ca" class="btn btn-outline-white btn-lg"><span>âœ‰</span> admin@cultulangues.ca</a>
        </div>
        <p class="contact-cta-banner-note">Ou appelez-nous directement â€” nous sommes joignables du lundi au vendredi, de 9 h Ã  19 h.</p>
      </div>
    </div>
  </section>
@endsection
