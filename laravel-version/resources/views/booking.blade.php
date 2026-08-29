<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — Cultulangues</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <script src="https://js.stripe.com/v3/"></script>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="booking-wrap">

  <!-- HEADER -->
  <div class="booking-header">
    <img src="{{ asset('img/image-Photoroom.png') }}" alt="Cultulangues" style="width:200px;height:auto;object-fit:contain;margin:0 auto 16px;display:block">
    <h1>Inscription</h1>
    <p>Choisissez parmi les options disponibles</p>
  </div>

  <div id="stepCardsContainer"></div>

  <!-- STEP 1: BOOKING -->
  <div class="step-container" id="step1">
    <div class="step-title">Fiche d'inscription</div>

    <div class="bk-summary" id="courseSummary">
      <img class="bk-summary-thumb" id="bkSummaryThumb" src="{{ asset('assets/services/homepage-hero.webp') }}" alt="">
      <div class="bk-summary-info">
        <h3 class="bk-summary-name" id="csName">—</h3>
        <div class="bk-summary-desc" id="csDesc">—</div>
        <span class="bk-summary-badge"><i class="fas fa-check-circle"></i> <span>Déjà sélectionné</span></span>
      </div>
      <div class="bk-summary-price" id="csPrice">—</div>
    </div>

    <div id="soloPackagePicker" class="hidden">
      <div class="solo-section-title">Sélectionnez votre formule</div>
      <div class="package-picker-grid" id="soloPackageGrid"></div>
      <div class="package-section-divider">Ensuite, choisissez votre date et votre créneau</div>
    </div>

    <div id="scheduleSection">
      <div id="soloSchedule" class="hidden">
        <div class="solo-section-title">Choisissez votre date et votre créneau</div>
        <div class="solo-layout">
          <div class="calendar-wrap">
            <div class="calendar-header">
              <h3 id="soloCalTitle">Juillet 2026</h3>
              <div class="calendar-nav">
                <button onclick="soloCalChange(-1)"><i class="fas fa-chevron-left"></i></button>
                <button onclick="soloCalChange(1)"><i class="fas fa-chevron-right"></i></button>
              </div>
            </div>
            <div class="calendar-grid" id="soloCalGrid"></div>
          </div>
          <div class="slot-picker">
            <h3>Créneaux disponibles</h3>
            <div class="selected-date" id="soloDateDisplay">Sélectionnez une date</div>
            <div class="slot-grid" id="soloSlotGrid">
              <div style="grid-column:1/-1;text-align:center;padding:32px 0;color:var(--text-tertiary);font-size:0.85rem">
                <i class="far fa-calendar-alt" style="display:block;font-size:1.8rem;margin-bottom:8px"></i>
                <span>Choisissez une date dans le calendrier</span>
              </div>
            </div>
            <div class="timezone-info"><i class="fas fa-globe"></i> <span>Fuseau horaire : UTC</span><span id="soloTzOffset">-4</span> <span>(heure de Montréal)</span></div>
          </div>
        </div>
      </div>

      <div id="groupSchedule" class="hidden">
        <div class="solo-section-title">Choisissez votre groupe</div>
        <div class="group-grid" id="groupGrid"></div>
      </div>
    </div>

    <div style="margin-top:24px">
      <div style="font-size:0.9rem;font-weight:700;margin-bottom:12px">Vos informations</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Nom complet <span class="req">*</span></label>
          <input type="text" id="infoName" placeholder="Maria Santos" oninput="checkStep1()">
        </div>
        <div class="form-group">
          <label>Courriel <span class="req">*</span></label>
          <input type="email" id="infoEmail" placeholder="maria@email.com" oninput="checkStep1()">
        </div>
        <div class="form-group">
          <label>Téléphone <span class="req">*</span></label>
          <input type="tel" id="infoPhone" placeholder="+1 (555) 123-4567" oninput="checkStep1()">
        </div>
        <div class="form-group">
          <label>Méthode de contact préférée</label>
          <input type="text" id="infoContact" placeholder="Courriel / Téléphone / WhatsApp">
        </div>
        <div class="form-group full">
          <label>Notes <span style="color:var(--text-tertiary);font-weight:400">(optionnel)</span></label>
          <textarea id="infoNotes" placeholder="Questions ou commentaires particuliers..."></textarea>
        </div>
      </div>
    </div>

    <div class="step-footer">
      <div class="info" id="step1Info"><i class="fas fa-info-circle"></i> <span>Choisissez votre horaire et remplissez vos informations</span></div>
      <div class="actions">
        <a href="{{ url('/') }}" class="btn btn-ghost"><i class="fas fa-times"></i> Annuler</a>
        <button class="btn btn-primary" id="toStep2" disabled>Confirmer l'inscription et continuer <i class="fas fa-arrow-right"></i></button>
      </div>
    </div>
  </div>

  <!-- STEP 2: PLACEMENT TEST -->
  <div class="step-container hidden" id="step2">
    <div class="step-title">Test de niveau</div>
    <div class="step-subtitle">Évaluez votre niveau en français</div>

    <div class="test-header">
      <div class="test-progress-wrap">
        <div class="test-progress-bar">
          <div class="test-progress-fill" id="testProgressFill" style="width:5%"></div>
        </div>
        <span class="test-progress-text" id="testProgressText">1 / 20</span>
      </div>
      <div class="test-timer" id="testTimer"><i class="far fa-clock"></i> <span id="timerDisplay">20:00</span></div>
    </div>

    <div id="testQuestionArea">
      <div class="test-question">
        <div class="q-num" id="qNum">QUESTION 1 / 20</div>
        <div class="q-text" id="qText">—</div>
      </div>
      <div class="test-options" id="qOptions"></div>
    </div>

    <div class="test-nav" id="testNav">
      <div class="info" id="testInfo"><i class="fas fa-info-circle"></i> <span>Choisissez une réponse pour continuer</span></div>
      <div class="actions" style="display:flex;gap:10px">
        <button class="btn btn-ghost" id="testBackBtn" onclick="prevQuestion()" disabled><i class="fas fa-arrow-left"></i> Précédent</button>
        <button class="btn btn-primary" id="testNextBtn" disabled>Suivante <i class="fas fa-arrow-right"></i></button>
      </div>
    </div>

    <div class="hidden" id="step2Results">
      <div style="text-align:center;padding:24px 0">
        <div class="results-score-label">Score global</div>
        <div class="results-score" id="step2ResultsScore">—</div>
        <div class="results-level" id="step2ResultsLevel">—</div>
      </div>
      <div class="results-chart-wrap">
        <div class="results-chart" id="step2ResultsChart">
          <div class="bar correct" style="height:0" id="step2CorrectBar"></div>
          <div class="bar incorrect" style="height:100%" id="step2IncorrectBar"></div>
        </div>
        <div class="results-chart-label">
          <span><span class="dot correct"></span> <span>Bonnes réponses</span>: <strong id="step2CorrectCount">0</strong></span>
          <span><span class="dot incorrect"></span> <span>Mauvaises réponses</span>: <strong id="step2IncorrectCount">0</strong></span>
        </div>
      </div>
      <div class="results-breakdown" id="step2ResultsBreakdown"></div>
      <div class="results-explanation" id="step2ResultsExplanation">
        <i class="fas fa-info-circle"></i>
        <div id="step2ResultsExplanationText">—</div>
      </div>
      <div class="step-footer">
        <div class="info"><i class="fas fa-check-circle" style="color:var(--green)"></i> <span>Test terminé</span></div>
        <div class="actions">
          <button class="btn btn-primary" id="step2ResultsNext">Continuer <i class="fas fa-arrow-right"></i></button>
        </div>
      </div>
    </div>
  </div>

  <!-- STEP 3: RESULTS -->
  <div class="step-container hidden" id="step3">
    <div class="step-title">Vos résultats</div>
    <div class="step-subtitle">Voici votre niveau estimé</div>

    <div class="results-overview">
      <div class="results-score-label">Score global</div>
      <div class="results-score" id="resultsScore">—</div>
      <div class="results-level" id="resultsLevel">—</div>
    </div>

    <div class="results-chart-wrap">
      <div class="results-chart" id="resultsChart">
        <div class="bar correct" style="height:0" id="correctBar"></div>
        <div class="bar incorrect" style="height:100%" id="incorrectBar"></div>
      </div>
      <div class="results-chart-label">
        <span><span class="dot correct"></span> <span>Bonnes réponses</span>: <strong id="correctCount">0</strong></span>
        <span><span class="dot incorrect"></span> <span>Mauvaises réponses</span>: <strong id="incorrectCount">0</strong></span>
      </div>
    </div>

    <div class="results-breakdown" id="resultsBreakdown"></div>

    <div class="results-explanation" id="resultsExplanation">
      <i class="fas fa-info-circle"></i>
      <div id="resultsExplanationText">Selon votre résultat, un test oral est requis pour finaliser votre niveau.</div>
    </div>

    <div class="step-footer">
      <div class="info"><i class="fas fa-check-circle" style="color:var(--green)"></i> <span>Test terminé</span></div>
      <div class="actions">
        <button class="btn btn-primary" id="toStep4">Réserver un appel pour le test oral <i class="fas fa-arrow-right"></i></button>
      </div>
    </div>
  </div>

  <!-- STEP 4: ORAL CALL BOOKING -->
  <div class="step-container hidden" id="step4">
    <div class="step-title">Réserver un appel pour le test oral</div>
    <div class="step-subtitle">Choisissez une date et un créneau pour votre entretien oral</div>

    <div class="oral-layout">
      <div class="calendar-wrap">
        <div class="calendar-header">
          <h3 id="calendarTitle">Juillet 2026</h3>
          <div class="calendar-nav">
            <button onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
            <button onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
      </div>

      <div class="slot-picker" id="slotPicker">
        <h3>Créneaux disponibles</h3>
        <div class="selected-date" id="selectedDateDisplay">Sélectionnez une date</div>
        <div class="slot-grid" id="slotGrid">
          <div style="grid-column:1/-1;text-align:center;padding:32px 0;color:var(--text-tertiary);font-size:0.85rem">
            <i class="far fa-calendar-alt" style="display:block;font-size:1.8rem;margin-bottom:8px"></i>
            <span>Choisissez une date dans le calendrier</span>
          </div>
        </div>
        <div class="timezone-info"><i class="fas fa-globe"></i> <span>Fuseau horaire : UTC</span><span id="tzOffset">-4</span> <span>(heure de Montréal)</span></div>
      </div>
    </div>

    <div class="payment-section hidden" id="paymentSection">
      <div class="payment-header">
        <h3>Paiement en ligne</h3>
        <span class="payment-note"><i class="fas fa-lock"></i> Paiement sécurisé par Stripe</span>
      </div>
      <div class="payment-summary">
        <span>Montant à payer</span>
        <strong id="paymentAmountDisplay">—</strong>
      </div>
      <div id="stripePaymentElement" class="stripe-element-wrap"></div>
      <div id="stripePaymentError" class="stripe-error hidden"></div>
    </div>

    <div class="step-footer">
      <div class="info" id="step4Info"><i class="fas fa-info-circle"></i> <span>Choisissez une date et un créneau</span></div>
      <div class="actions">
        <button class="btn btn-ghost" onclick="goToStep(3)"><i class="fas fa-arrow-left"></i> Retour</button>
        <button class="btn btn-success" id="oralConfirmBtn" disabled onclick="confirmOralTest()"><i class="fas fa-check"></i> Payer et confirmer</button>
      </div>
    </div>
  </div>

  <!-- SUCCESS OVERLAY -->
  <div class="step-container hidden" id="stepSuccess">
    <div class="success-page">
      <div class="success-icon"><i class="fas fa-check"></i></div>
      <h2>Test oral planifié avec succès !</h2>
      <p>Votre test oral est planifié avec succès. Vous allez être redirigé vers votre tableau de bord...</p>
      <div class="success-details" id="successDetails"></div>
      <div class="progress-bar" style="max-width:300px;margin:0 auto"><div class="progress-fill" id="redirectProgress" style="background:var(--accent);width:0%;height:6px;border-radius:var(--radius-full);transition:width 3s linear"></div></div>
      <p style="font-size:0.72rem;color:var(--text-tertiary);margin-top:12px">Redirection automatique dans <span id="redirectCountdown">3</span> secondes</p>
    </div>
  </div>

</div>

<script>
  window.bookingRoutes = {
    store: '{{ route('booking.store') }}',
    paymentIntent: '{{ route('booking.payment-intent') }}'
  };
  window.bookingServiceMap = @json($serviceMap ?? []);
  window.stripeKey = '{{ config('services.stripe.key') }}';
</script>
<script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>
