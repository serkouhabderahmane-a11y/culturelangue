@extends('layouts.public', ['body_class' => 'home-page'])

@section('title', $settings['site_name'] ?? 'Cultulangues')

@section('head')
<link rel="stylesheet" href="{{ asset('css/model-1.css') }}">
<style>
  .m1-hero{padding-top:calc(var(--header-height) + 28px);padding-bottom:48px}
  .m1-hero-container{min-height:0}
  .m1-visual{flex-direction:column;align-items:center;justify-content:flex-start;gap:20px;min-height:0}
  .m1-caption{position:relative;z-index:2;text-align:center}
  .m1-caption .hm-h1{font-size:clamp(2rem,3.4vw,2.9rem);margin-top:12px}
  .m1-caption .hm-h2{margin-top:8px;font-size:clamp(1.15rem,2vw,1.5rem)}
  .m1-figure{position:relative;width:min(94%,520px);margin:0}
  .m1-figure .m1-wave{top:-18px;right:-18px;left:auto;bottom:auto;width:100%;height:100%}
  .m1-figure img{display:block}
  @media (max-width:768px){
    .m1-hero{padding-top:calc(var(--header-height) + 16px)}
    .m1-figure{width:min(96%,460px)}
    .m1-figure .m1-wave{top:-12px;right:-10px}
  }
</style>
@endsection

@section('content')
  <!-- ═══════════════════════════════════════════
       HERO — Premium Editorial Rebuild v3
       ═══════════════════════════════════════════ -->
  <section class="m1-hero" id="hero">
    <div class="m1-hero-container">
      <div class="m1-content">
        <h1 class="m1-h1" data-i18n="hero.h1">{!! $settings['hero_title'] ?? "Chez CultuLangues, nous construisons<br>votre réussite et donnons un nouvel<br>élan à vos <span class=\"text-gradient\">projets</span>." !!}</h1>

        <p class="m1-sub" data-i18n="hero.intro">{{ $settings['hero_subtitle'] ?? "Vous souhaitez apprendre l'une des langues officielles du Canada pour :" }}</p>

        <div class="m1-check">
          <div class="m1-check-item">
            <div class="m1-check-icon m1-check-orange">
              <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span data-i18n="hero.list.1">Faire évoluer votre carrière</span>
          </div>
          <div class="m1-check-item">
            <div class="m1-check-icon m1-check-green">
              <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span data-i18n="hero.list.2">Réussir votre projet d'immigration</span>
          </div>
          <div class="m1-check-item">
            <div class="m1-check-icon m1-check-blue">
              <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span data-i18n="hero.list.3">Gagner confiance et autonomie dans la vie quotidienne</span>
          </div>
        </div>
      </div>

      <div class="m1-visual">
        <div class="m1-caption">
          <span class="hm-eyebrow" data-i18n="hero.client.brand">Cultulangues</span>
          <p class="hm-h1" data-i18n="hero.client.line1">Maîtrisez les langues !</p>
          <p class="hm-h2" data-i18n="hero.client.line2">Transformez votre avenir !</p>
        </div>
        <figure class="m1-figure">
          <span class="m1-wave" aria-hidden="true"></span>
          <img src="{{ asset('img/home/hero-client.png') }}" alt="Cultulangues — Apprendre une langue" loading="eager">
        </figure>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       CHOOSE YOUR LEARNING JOURNEY — Premium showcase
       ═══════════════════════════════════════════ -->
  <section class="ph-explore">
    <div class="ph-explore-container">
      <div class="ph-explore-header ph-reveal">
        <span class="ph-eyebrow">Nos programmes</span>
        <h2 class="ph-explore-title">CHOISISSEZ VOTRE <span class="ph-text-gradient">PROGRAMME</span></h2>
        <p class="ph-explore-subtitle">7 programmes. Une vision. Une stratégie. Votre réussite. Cultulangues vous guide vers l'avenir en transformant vos objectifs en performance durable.</p>
      </div>

      <div class="ph-journey">
        <!-- Row 1: 4 feature cards -->
        <div class="ph-journey-row ph-journey-row--4">
          @php
            $journeyCards = [
              ['slug' => 'parcours-linguistique', 'id' => 'parcours-linguistique', 'class' => 'ph-journey-card--1', 'img' => 'img/home/parcours-linguistiques-new.png', 'count' => '6 parcours disponibles', 'title' => 'Parcours linguistique', 'desc' => 'Cours de groupe en petit groupe pour progresser avec confiance', 'href' => 'pages/parcours-linguistique.html', 'modal' => 'ph-modal-parcours'],
              ['slug' => 'cap-sur-l-oral', 'id' => 'cap-sur-l-oral', 'class' => 'ph-journey-card--2', 'img' => 'img/home/cap-sur-l-oral-new.png', 'count' => '4 parcours disponibles', 'title' => "Cap sur l'oral", 'desc' => "Maîtrisez l'expression orale avec des parcours collaboratifs en petit groupe", 'href' => 'pages/cap-sur-l-oral.html', 'modal' => 'ph-modal-lingo-test'],
              ['slug' => 'tcf-quebec', 'id' => 'tcf-quebec', 'class' => 'ph-journey-card--3', 'img' => 'img/home/tcf-quebec-new.png', 'count' => '2 parcours disponibles', 'title' => 'TCF Québec', 'desc' => "Préparation au TCF Québec pour l'immigration", 'href' => 'pages/tcf-quebec.html', 'modal' => 'ph-modal-lingo-test'],
              ['slug' => 'english-linguistic-pathway', 'id' => 'english-linguistic-pathway', 'class' => 'ph-journey-card--4', 'img' => 'img/home/english-linguistic-pathway-new.png', 'count' => '3 parcours disponibles', 'title' => 'English Linguistic Pathway', 'desc' => 'English courses designed to help you make real progress in a motivating environment', 'href' => 'pages/english-linguistic-pathway.html', 'modal' => 'ph-modal-english'],
            ];
          @endphp
          @foreach($journeyCards as $index => $card)
          <div id="{{ $card['id'] }}" class="ph-journey-card {{ $card['class'] }} ph-reveal" data-delay="{{ $index }}">
            <div class="ph-journey-card-bg">
              <img src="{{ asset($card['img']) }}" alt="" loading="lazy">
              <div class="ph-journey-card-overlay"></div>
            </div>
            <div class="ph-journey-card-content">
              <span class="ph-journey-card-count">{{ $card['count'] }}</span>
              <h3 class="ph-journey-card-title">{{ $card['title'] }}</h3>
              <p class="ph-journey-card-desc">{{ $card['desc'] }}</p>
              <div class="ph-journey-card-actions">
                <a href="{{ url('/' . $card['href']) }}" class="ph-journey-card-btn"><span>Découvrir</span><svg class="ph-journey-card-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                <button type="button" class="ph-journey-card-btn ph-journey-card-btn--ghost" onclick="openModal('{{ $card['modal'] }}')"><span>Lire plus</span></button>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Row 2: 3 feature cards -->
        <div class="ph-journey-row ph-journey-row--3">
          @php
            $journeyCards2 = [
              ['slug' => 'tcf-canada', 'id' => 'tcf-canada', 'class' => 'ph-journey-card--5', 'img' => 'img/home/tcf-canada-new.png', 'count' => '2 parcours disponibles', 'title' => 'TCF Canada', 'desc' => "Préparation au TCF Canada pour l'immigration IRCC", 'href' => 'pages/tcf-canada.html', 'modal' => 'ph-modal-lingo-test'],
              ['slug' => 'formation-en-solo', 'id' => 'formation-solo', 'class' => 'ph-journey-card--6', 'img' => 'img/home/formation-solo-new.png', 'count' => '4 parcours disponibles', 'title' => 'Formation en solo', 'desc' => 'Accompagnement 1-to-1 flexible et 100 % personnalisé', 'href' => 'pages/formation-en-solo.html', 'modal' => 'ph-modal-solo'],
              ['slug' => 'ateliers', 'id' => 'ateliers', 'class' => 'ph-journey-card--7', 'img' => 'img/home/ateliers-new.png', 'count' => '3 parcours disponibles', 'title' => 'Ateliers', 'desc' => 'Ateliers thématiques pour pratiquer et échanger en groupe', 'href' => 'pages/workshops.html', 'modal' => 'ph-modal-ateliers'],
            ];
          @endphp
          @foreach($journeyCards2 as $index => $card)
          <div id="{{ $card['id'] }}" class="ph-journey-card {{ $card['class'] }} ph-reveal" data-delay="{{ $index + 4 }}">
            <div class="ph-journey-card-bg">
              <img src="{{ asset($card['img']) }}" alt="" loading="lazy">
              <div class="ph-journey-card-overlay"></div>
            </div>
            <div class="ph-journey-card-content">
              <span class="ph-journey-card-count">{{ $card['count'] }}</span>
              <h3 class="ph-journey-card-title">{{ $card['title'] }}</h3>
              <p class="ph-journey-card-desc">{{ $card['desc'] }}</p>
              <div class="ph-journey-card-actions">
                <a href="{{ url('/' . $card['href']) }}" class="ph-journey-card-btn"><span>Découvrir</span><svg class="ph-journey-card-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                <button type="button" class="ph-journey-card-btn ph-journey-card-btn--ghost" onclick="openModal('{{ $card['modal'] }}')"><span>Lire plus</span></button>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       MODALS — Program detail overlays
       ═══════════════════════════════════════════ -->
  <div class="ph-modal-overlay" id="ph-modal-parcours" onclick="if(event.target===this)closeModal('ph-modal-parcours')">
    <div class="ph-modal-card" role="dialog" aria-modal="true" aria-labelledby="ph-modal-parcours-title">
      <div class="ph-modal-header">
        <h3 id="ph-modal-parcours-title">Parcours linguistique</h3>
        <button type="button" class="ph-modal-close" onclick="closeModal('ph-modal-parcours')" aria-label="Fermer">&times;</button>
      </div>
      <div class="ph-modal-body">
        <div class="ph-modal-hero">
          <img src="{{ asset('img/home/parcours-linguistiques-new.png') }}" alt="Parcours linguistique" loading="lazy">
        </div>
        <p class="ph-modal-desc">Rejoignez un programme conçu pour vous faire progresser réellement, dans un environnement motivant et stimulant, spécialement pensé pour les adultes. Nos cours de groupe à effectif réduit (maximum 5 participants) vous offrent bien plus qu'une simple formation : vous bénéficiez d'un espace dynamique où vous pouvez pratiquer, poser vos questions et développer votre confiance à chaque séance. Grâce à une approche structurée et centrée sur vos objectifs, vous progressez plus rapidement et plus efficacement, tout en étant accompagné à chaque étape de votre apprentissage. Que vous souhaitiez améliorer vos compétences pour le travail, vos études ou votre vie quotidienne, ce programme vous donne les outils et l'encadrement nécessaires pour atteindre vos objectifs.</p>
      </div>
      <div class="ph-modal-footer">
        <a href="{{ url('/pages/parcours-linguistique') }}" class="ph-btn ph-btn-primary">
          <span>Découvrir le parcours</span>
          <svg class="ph-btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="ph-modal-overlay" id="ph-modal-english" onclick="if(event.target===this)closeModal('ph-modal-english')">
    <div class="ph-modal-card" role="dialog" aria-modal="true" aria-labelledby="ph-modal-english-title">
      <div class="ph-modal-header">
        <h3 id="ph-modal-english-title">English Linguistic Pathway</h3>
        <button type="button" class="ph-modal-close" onclick="closeModal('ph-modal-english')" aria-label="Close">&times;</button>
      </div>
      <div class="ph-modal-body">
        <div class="ph-modal-hero">
          <img src="{{ asset('img/home/english-linguistic-pathway-new.png') }}" alt="English Linguistic Pathway" loading="lazy">
        </div>
        <p class="ph-modal-desc">An immersive English learning experience tailored to your goals. Our group classes (maximum 5 participants) offer more than just lessons — you gain a dynamic space to practice, ask questions, and build confidence at every session. Choose from English Express (intensive 5-week program), Evening Lingo (weekly immersive social experience), or Saturdays in English (festive weekend sessions). With a structured approach focused on your objectives, you progress more quickly and effectively while being supported at every stage of your learning.</p>
      </div>
      <div class="ph-modal-footer">
        <a href="{{ url('/pages/english-linguistic-pathway') }}" class="ph-btn ph-btn-primary">
          <span>Discover the pathway</span>
          <svg class="ph-btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="ph-modal-overlay" id="ph-modal-lingo-test" onclick="if(event.target===this)closeModal('ph-modal-lingo-test')">
    <div class="ph-modal-card" role="dialog" aria-modal="true" aria-labelledby="ph-modal-lingo-test-title">
      <div class="ph-modal-header">
        <h3 id="ph-modal-lingo-test-title">Lingo Test</h3>
        <button type="button" class="ph-modal-close" onclick="closeModal('ph-modal-lingo-test')" aria-label="Fermer">&times;</button>
      </div>
      <div class="ph-modal-body">
        <div class="ph-modal-hero">
          <img src="{{ asset('img/home/cap-sur-l-oral-new.png') }}" alt="Lingo Test" loading="lazy">
        </div>
        <p class="ph-modal-desc">Les tests officiels de français demandent une préparation stratégique et un entraînement régulier. Chez Cultulangues, nous vous accompagnons en petit groupe (maximum 5 participants) dans un cadre bienveillant, humain et exigeant. Cap sur l'oral vous prépare au test de compétence orale du gouvernement du Canada (TCO) ; TCF Québec vous guide vers le test exigé pour l'immigration au Québec et le PEQ ; TCF Canada vous entraîne au test demandé par IRCC. Trois programmes, une méthode : comprendre les attentes du test, maîtriser les stratégies gagnantes et gagner en confiance jusqu'au jour J.</p>
      </div>
    </div>
  </div>

  <div class="ph-modal-overlay" id="ph-modal-solo" onclick="if(event.target===this)closeModal('ph-modal-solo')">
    <div class="ph-modal-card" role="dialog" aria-modal="true" aria-labelledby="ph-modal-solo-title">
      <div class="ph-modal-header">
        <h3 id="ph-modal-solo-title">Formation en solo</h3>
        <button type="button" class="ph-modal-close" onclick="closeModal('ph-modal-solo')" aria-label="Fermer">&times;</button>
      </div>
      <div class="ph-modal-body">
        <div class="ph-modal-hero">
          <img src="{{ asset('img/home/formation-solo-new.png') }}" alt="Formation en solo" loading="lazy">
        </div>
        <p class="ph-modal-desc">Boostez votre français avec les cours solo Cultulangues, la formule la plus flexible et la plus efficace pour atteindre rapidement vos objectifs linguistiques. Choisissez exactement ce dont vous avez besoin grâce à nos cours à la carte : un parcours linguistique complet, un programme Cap sur l'oral, une préparation ciblée au TCF, un module pour rafraîchir votre français, un entraînement intensif aux tests écrits de la fonction publique, des simulations d'examen, des séances pour maintenir votre niveau en toute simplicité. Avec nos forfaits de 5 h, 10 h, 15 h ou 20 h, vous avancez à votre rythme, avec un accompagnement sur mesure et des résultats concrets.</p>
      </div>
      <div class="ph-modal-footer">
        <a href="{{ url('/pages/formation-en-solo') }}" class="ph-btn ph-btn-primary">
          <span>Découvrir la formation en solo</span>
          <svg class="ph-btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="ph-modal-overlay" id="ph-modal-ateliers" onclick="if(event.target===this)closeModal('ph-modal-ateliers')">
    <div class="ph-modal-card" role="dialog" aria-modal="true" aria-labelledby="ph-modal-ateliers-title">
      <div class="ph-modal-header">
        <h3 id="ph-modal-ateliers-title">Ateliers</h3>
        <button type="button" class="ph-modal-close" onclick="closeModal('ph-modal-ateliers')" aria-label="Fermer">&times;</button>
      </div>
      <div class="ph-modal-body">
        <div class="ph-modal-hero">
          <img src="{{ asset('img/home/ateliers-new.png') }}" alt="Ateliers" loading="lazy">
        </div>
        <p class="ph-modal-desc">Un espace chaleureux, dynamique et bienveillant pour pratiquer le français en toute simplicité. Pendant 1 h par semaine, à l'heure du dîner ou en soirée, vous échangez sur des thèmes variés, développez votre spontanéité et gagnez en fluidité, sans pression, sans jugement, juste du vrai plaisir à parler. Conversation, culture canadienne, maintien et renforcement. Un cadre humain, rassurant et motivant où chacun trouve sa place, progresse avec confiance et ose parler davantage.</p>
      </div>
      <div class="ph-modal-footer">
        <a href="{{ url('/pages/workshops') }}" class="ph-btn ph-btn-primary">
          <span>Découvrir les ateliers</span>
          <svg class="ph-btn-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════
       WHY CHOOSE US — Asymmetric masonry
       ═══════════════════════════════════════════ -->
  <section class="ph-why">
    <div class="ph-why-header ph-reveal">
      <span class="ph-eyebrow">Pourquoi nous</span>
      <h2 class="ph-section-title">Pourquoi <span class="ph-text-gradient">Cultulangues</span> ?</h2>
      <p class="ph-section-subtitle" data-i18n="why.subtitle">Nous croyons en une approche humaine et exigeante pour vous accompagner vers la réussite.</p>
    </div>

    <div class="ph-why-grid">
      <div class="ph-why-visual ph-reveal" data-delay="0">
        <img src="{{ asset('img/client-photo-4.png') }}" alt="" loading="lazy">
        <span class="ph-why-visual-frame" aria-hidden="true"></span>
      </div>
      <div class="ph-why-cards">
        <div class="ph-why-card ph-why-card-large ph-why-card--1 ph-reveal" data-delay="1">
          <div class="ph-why-icon-wrap"><span class="ph-why-icon">👩‍🏫</span></div>
          <h3 data-i18n="why.card1.title">Enseignants certifiés</h3>
          <p data-i18n="why.card1.desc">Tous nos enseignants sont diplômés FLE et spécialisés dans la préparation aux examens TCF.</p>
        </div>
        <div class="ph-why-card ph-why-card--2 ph-reveal" data-delay="2">
          <div class="ph-why-icon-wrap"><span class="ph-why-icon">🌐</span></div>
          <h3>Communauté internationale</h3>
          <p>Rejoignez un réseau d'apprenants du monde entier dans un cadre bienveillant et stimulant.</p>
        </div>
        <div class="ph-why-card ph-why-card--3 ph-reveal" data-delay="3">
          <div class="ph-why-icon-wrap"><span class="ph-why-icon">⚡</span></div>
          <h3>Résultats rapides</h3>
          <p>Des méthodes éprouvées pour progresser rapidement et atteindre vos objectifs linguistiques.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       COMPARISON TABLE — Premium dark wrapper
       ═══════════════════════════════════════════ -->
  <section class="ph-compare">
    <div class="ph-compare-header ph-reveal">
      <span class="ph-eyebrow">Comparaison</span>
      <h2 class="ph-section-title">Quel parcours vous <span class="ph-text-gradient">correspond</span> ?</h2>
      <p class="ph-section-subtitle">Comparez nos 4 parcours phares pour trouver celui qui correspond à vos objectifs, votre rythme et vos besoins.</p>
    </div>
    <div class="comparison-table ph-reveal">
      <table>
        <thead>
          <tr>
            <th data-i18n="compare.th.critere">Critère</th>
            <th data-i18n="compare.th.pl">Parcours linguistique</th>
            <th data-i18n="compare.th.oral">Cap sur l'oral</th>
            <th data-i18n="compare.th.tcf">Préparation TCF</th>
            <th data-i18n="compare.th.solo">Formation solo</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td data-i18n="compare.row.objectif">Objectif</td>
            <td data-i18n="compare.pl.objectif">Progrès général en français</td>
            <td data-i18n="compare.oral.objectif">Maîtrise orale & TCO</td>
            <td data-i18n="compare.tcf.objectif">TCF Québec & immigration</td>
            <td data-i18n="compare.solo.objectif">Objectifs personnalisés</td>
          </tr>
          <tr>
            <td data-i18n="compare.row.format">Format</td>
            <td data-i18n="compare.pl.format">Groupe (max 5)</td>
            <td data-i18n="compare.oral.format">Groupe (max 5), temps partiel ou intensif</td>
            <td data-i18n="compare.tcf.format">Parcours guidé, simulations</td>
            <td data-i18n="compare.solo.format">1-to-1, full flexible</td>
          </tr>
          <tr>
            <td data-i18n="compare.row.accompagnement">Accompagnement</td>
            <td data-i18n="compare.pl.accomp">Structuré, collectif</td>
            <td data-i18n="compare.oral.accomp">Expert oral, collaboratif</td>
            <td data-i18n="compare.tcf.accomp">Étape par étape, bienveillant</td>
            <td data-i18n="compare.solo.accomp">100 % sur-mesure</td>
          </tr>
          <tr>
            <td data-i18n="compare.row.ideal">Idéal pour</td>
            <td data-i18n="compare.pl.ideal">Adultes tous niveaux</td>
            <td data-i18n="compare.oral.ideal">Candidats TCO</td>
            <td data-i18n="compare.tcf.ideal">Projets immigration Québec</td>
            <td data-i18n="compare.solo.ideal">Profil flexible ou ciblé</td>
          </tr>
          <tr>
            <td data-i18n="compare.row.flexibilite">Flexibilité</td>
            <td data-i18n="compare.pl.flex">Calendrier fixe</td>
            <td data-i18n="compare.oral.flex">Temps partiel ou intensif</td>
            <td data-i18n="compare.tcf.flex">Parcours progressif</td>
            <td data-i18n="compare.solo.flex">Totale (5h–20h)</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       TESTIMONIALS — Featured + stacked layout
       ═══════════════════════════════════════════ -->
  <section class="ph-testimonials">
    <div class="ph-testimonials-header ph-reveal">
      <span class="ph-eyebrow">Témoignages</span>
      <h2 class="ph-section-title">Ils nous font <span class="ph-text-gradient">confiance</span></h2>
      <p class="ph-section-subtitle" data-i18n="testimonials.subtitle">Découvrez les témoignages de nos étudiants.</p>
    </div>
    <div class="ph-testimonials-layout">
      <div class="ph-testimonial-photo ph-reveal">
        <img src="{{ asset('img/client-photo-5.png') }}" alt="" loading="lazy">
      </div>
      @if($testimonials->count() > 0)
      <div class="ph-testimonial-featured ph-reveal">
        <div class="ph-testimonial-stars">★★★★★</div>
        <p class="ph-testimonial-text">{{ $testimonials->first()->content_fr }}</p>
        <div class="ph-testimonial-author">
          <div class="ph-testimonial-avatar">{{ substr($testimonials->first()->name_fr, 0, 1) }}</div>
          <div>
            <h4>{{ $testimonials->first()->name_fr }}</h4>
            <span>{{ $testimonials->first()->role_fr }}</span>
          </div>
        </div>
      </div>
      @endif
      <div class="ph-testimonial-stack">
        @foreach($testimonials->skip(1) as $testimonial)
        <div class="ph-testimonial-card ph-reveal">
          <div class="ph-testimonial-stars">★★★★★</div>
          <p class="ph-testimonial-text">{{ $testimonial->content_fr }}</p>
          <div class="ph-testimonial-author">
            <div class="ph-testimonial-avatar">{{ substr($testimonial->name_fr, 0, 1) }}</div>
            <div>
              <h4>{{ $testimonial->name_fr }}</h4>
              <span>{{ $testimonial->role_fr }}</span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       CTA — Immersive full-width dark section
       ═══════════════════════════════════════════ -->
  <section class="ph-cta">
    <div class="ph-cta-deco" aria-hidden="true"></div>
    <div class="ph-cta-inner ph-reveal">
      <h2 data-i18n="cta.home.title">Prêt à commencer votre parcours ?</h2>
      <p data-i18n="cta.home.desc">Rejoignez plus de 950 étudiants qui nous font confiance pour leur préparation linguistique.</p>
      <a href="{{ route('register') }}" class="ph-btn ph-btn-white ph-btn-xl" data-i18n="cta.home.btn">
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
