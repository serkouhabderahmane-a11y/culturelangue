  // ════════════════════════════════════════════════════════════
  //  STATE
  // ════════════════════════════════════════════════════════════
  var state = {
    courseId: null,
    program: null,
    courseData: null,

    // Step 1: Booking
    group: null,
    days: [],
    timeFrom: '',
    timeTo: '',
    name: '',
    email: '',
    phone: '',
    contact: '',
    notes: '',

    // Solo booking (package + date + time)
    soloPackage: null,
    soloDate: null,
    soloSlot: null,
    soloCalMonth: null,
    soloCalYear: null,

    // Step 2: Test
    testAnswers: [],
    currentQuestion: 0,

    // Step 4: Oral
    selectedDate: null,
    selectedSlot: null,
    calMonth: null,
    calYear: null
  };

  // ════════════════════════════════════════════════════════════
  //  COURSE DATA
  // ════════════════════════════════════════════════════════════
  var courseDB = {
    'private': {
      name:'Cours particuliers',
      type:'solo',
      desc:'Cours 1-on-1 avec un enseignant certifié. Programme personnalisé selon vos objectifs.',
      price:'1 200 $'
    },
    'solo': {
      name:'Formation en solo',
      type:'solo',
      desc:'Accompagnement 100 % personnalisé. Programme adapté à vos objectifs et à votre rythme.',
      price:'À partir de 225 $',
      packages: [
        {id:'5h', label:'Forfait Découverte', hours:5, sessions:5, rate:45, price:225, popular:false},
        {id:'10h', label:'Forfait Standard', hours:10, sessions:10, rate:42, price:420, popular:false},
        {id:'15h', label:'Forfait Avancé', hours:15, sessions:15, rate:40, price:600, popular:true},
        {id:'20h', label:'Forfait Intensif', hours:20, sessions:20, rate:38, price:760, popular:false}
      ]
    },
    'tcf-quebec': {
      name:'Préparation TCF Québec',
      type:'group',
      desc:'Préparation complète au TCF Québec. Cours en groupe avec horaire fixe.',
      price:'800 $'
    },
    'tcf-quebec-partiel': {
      name:'Préparation TCF Québec — Temps partiel',
      type:'group',
      desc:'Préparation au TCF Québec en temps partiel. Dates à venir.',
      price:'À venir'
    },
    'tcf-quebec-intensif': {
      name:'Préparation TCF Québec — Intensif',
      type:'group',
      desc:'Préparation au TCF Québec en format intensif. Dates à venir.',
      price:'À venir'
    },
    'tcf-canada': {
      name:'Préparation TCF Canada',
      type:'group',
      desc:'Préparation complète au TCF Canada. Cours en groupe avec horaire fixe.',
      price:'800 $'
    },
    'tcf-canada-partiel': {
      name:'Préparation TCF Canada — Temps partiel',
      type:'group',
      desc:'Préparation au TCF Canada en temps partiel. Dates à venir.',
      price:'À venir'
    },
    'tcf-canada-intensif': {
      name:'Préparation TCF Canada — Intensif',
      type:'group',
      desc:'Préparation au TCF Canada en format intensif. Dates à venir.',
      price:'À venir'
    },
    'tcf': {
      name:'Préparation TCF',
      type:'group',
      desc:'Préparation au TCF Québec et TCF Canada. Cours en groupe adaptés à votre objectif.',
      price:'800 $'
    },
    'oral-bc': {
      name:'Préparation orale — British Columbia',
      type:'group',
      desc:'Préparation en petit groupe à l\'examen oral de la Colombie-Britannique. Maximum 4 élèves.',
      price:'600 $'
    },
    'oral': {
      name:'Cap sur l\'oral',
      type:'group',
      desc:'Développer une expression orale fluide et stratégique. Préparation en groupe.',
      price:'600 $'
    },
    'oral-b': {
      name:'Préparation Oral B',
      type:'group',
      desc:'Préparation à l\'examen Oral B (TCO). Cours en groupe. Temps partiel ou intensif.',
      price:'600 $'
    },
    'oral-b-partiel': {
      name:'Préparation Oral B — Temps partiel',
      type:'group',
      desc:'Préparation à l\'examen Oral B (TCO) en temps partiel. Dates à venir.',
      price:'320 $'
    },
    'oral-b-intensif': {
      name:'Préparation Oral B — Intensif',
      type:'group',
      desc:'Préparation à l\'examen Oral B (TCO) en format intensif. Dates à venir.',
      price:'320 $'
    },
    'oral-c': {
      name:'Préparation Oral C',
      type:'group',
      desc:'Préparation à l\'examen Oral C (TCO). Cours en groupe. Temps partiel ou intensif.',
      price:'600 $'
    },
    'oral-c-partiel': {
      name:'Préparation Oral C — Temps partiel',
      type:'group',
      desc:'Préparation à l\'examen Oral C (TCO) en temps partiel. Dates à venir.',
      price:'320 $'
    },
    'oral-c-intensif': {
      name:'Préparation Oral C — Intensif',
      type:'group',
      desc:'Préparation à l\'examen Oral C (TCO) en format intensif. Dates à venir.',
      price:'320 $'
    },
    'intensif': {
      name:'Programme intensif',
      type:'group',
      desc:'Cours intensif en groupe. 4 séances par semaine pour des progrès rapides.',
      price:'900 $'
    },
    'groupe': {
      name:'Cours en groupe',
      type:'group',
      desc:'Apprenez en groupe avec d\'autres apprenants. Ambiance conviviale et motivante.',
      price:'500 $'
    },
    'samedi': {
      name:'Programme du samedi',
      type:'group',
      desc:'Cours en groupe chaque samedi matin. Parfait pour les professionnels occupés.',
      price:'400 $'
    },
    'workshop-conversation': {
      name:'Atelier de conversation',
      type:'group',
      desc:'Atelier hebdomadaire de conversation. Thèmes variés pour pratiquer l\'oral.',
      price:'100 $'
    },
    'workshop-culture': {
      name:'Atelier culturel',
      type:'group',
      desc:'Atelier hebdomadaire sur la culture francophone. Cinéma, littérature, actualité.',
      price:'200 $'
    },
    'workshop-maintenance': {
      name:'Atelier de maintien',
      type:'group',
      desc:'Atelier hebdomadaire pour maintenir et pratiquer votre français.',
      price:'100 $'
    },
    'workshop': {
      name:'Ateliers de français',
      type:'group',
      desc:'Ateliers thématiques de français en petits groupes. Conversation, culture et maintien.',
      price:'À partir de 100 $'
    },
    'francais-express': {
      name:'Français Express',
      type:'group',
      desc:'Programme intensif de 60 h — 4 semaines, 3 h/jour, lun–ven, 17 h–20 h.',
      price:'600 $'
    },
    'english-express': {
      name:'English Express Pathway',
      type:'group',
      desc:'Intensive 60-hour program — 4 weeks, 3 h/day, Mon–Fri, 5 p.m.–8 p.m.',
      price:'$600'
    },
    'english-linguistic-pathway': {
      name:'English Linguistic Pathway',
      type:'group',
      desc:'Three English pathways for adult learners: Express, Evening and Saturdays.',
      price:'From $300'
    },
    'soiree-linguo': {
      name:'Parcours Soirée Linguo',
      type:'group',
      desc:'10 semaines, 1 cours de 3 h/sem (17 h–20 h). Pour professionnels.',
      price:'300 $'
    },
    'evening-lingo': {
      name:'Evening Lingo Pathway',
      type:'group',
      desc:'10 weeks, 1 class of 3 h/week (5 p.m.–8 p.m.). For busy professionals.',
      price:'$300'
    },
    'samedis-francais': {
      name:'Parcours Samedis en français',
      type:'group',
      desc:'5 samedis, 6 h/samedi (9 h–12 h / 13 h–16 h). Immersion hebdomadaire.',
      price:'300 $'
    },
    'saturdays-english': {
      name:'Saturdays in English Pathway',
      type:'group',
      desc:'5 Saturdays, 6 h/Saturday (9 a.m.–12 p.m. / 1 p.m.–4 p.m.). Weekly immersion.',
      price:'$300'
    }
  };

  var stepConfigs = {
    'default': [
      { id: 'registration', title: 'Complétez et soumettez le formulaire d\'inscription', desc: 'Remplissez toutes les sections requises et envoyez le formulaire en ligne pour ouvrir votre dossier.', type: 'internal' },
      { id: 'placement', title: 'Complétez le test de niveau', desc: 'Ce test permet d\'évaluer votre niveau de compréhension écrite et votre niveau d\'expression écrite. Il nous aide à déterminer le groupe le plus adapté à votre progression.', type: 'internal' },
      { id: 'oral', title: 'Prenez un rendez-vous pour votre évaluation orale', desc: 'L\'évaluation orale permet de déterminer votre niveau d\'expression orale. Le rendez-vous se fait avec notre évaluateur, lors d\'un court entretien en visioconférence.', type: 'external', url: 'https://calendly.com/cultulangues/evaluation-orale' },
      { id: 'payment', title: 'Réservez votre place', desc: 'Une fois votre niveau confirmé, vous pouvez réserver votre place en choisissant la session ainsi que le créneau horaire qui vous convient.', type: 'redirect', url: 'paiement.html' }
    ]
  };
  stepConfigs['workshop-conversation'] = [
    { id: 'registration', title: 'Complétez et soumettez le formulaire d\'inscription', desc: 'Remplissez toutes les sections requises et envoyez le formulaire en ligne pour ouvrir votre dossier.', type: 'internal' },
    { id: 'payment', title: 'Réservez votre place', desc: 'Une fois votre inscription complétée, vous pouvez réserver votre place et effectuer votre paiement afin de confirmer votre participation à l\'atelier choisi.', type: 'redirect', url: 'paiement.html' }
  ];
  stepConfigs['workshop-culture'] = stepConfigs['workshop-conversation'];
  stepConfigs['workshop-maintenance'] = stepConfigs['workshop-conversation'];

  function getStepConfig(courseId) {
    return stepConfigs[courseId] || stepConfigs['default'];
  }

  function renderBookingCards() {
    var config = getStepConfig(state.courseId);
    var html = '<div class="booking-cards">';
    for (var i = 0; i < config.length; i++) {
      var step = config[i];
      html += '<div class="booking-step-card" data-index="' + i + '" data-step-id="' + step.id + '" tabindex="0" role="button">' +
        '<div class="bsc-number">' + (i + 1) + '</div>' +
        '<div class="bsc-content">' +
          '<div class="bsc-title">' + step.title + '</div>' +
          '<div class="bsc-desc">' + step.desc + '</div>' +
        '</div>' +
        '<div class="bsc-arrow"><i class="fas fa-arrow-right"></i></div>' +
      '</div>';
    }
    html += '</div>';
    document.getElementById('stepCardsContainer').innerHTML = html;

    document.querySelectorAll('.booking-step-card').forEach(function(card, idx) {
      card.addEventListener('click', function() {
        var step = config[idx];
        if (step.type === 'external') {
          window.open(step.url, '_blank');
        } else if (step.type === 'redirect') {
          var qs = '?course=' + encodeURIComponent(state.courseId);
          if (state.program) qs += '&program=' + encodeURIComponent(state.program);
          if (state.soloPackage) qs += '&pkg=' + encodeURIComponent(state.soloPackage);
          if (state.soloDate) qs += '&date=' + encodeURIComponent(state.soloDate.toISOString().split('T')[0]);
          if (state.soloSlot) qs += '&slot=' + encodeURIComponent(state.soloSlot);
          if (state.name) qs += '&name=' + encodeURIComponent(state.name);
          if (state.email) qs += '&email=' + encodeURIComponent(state.email);
          if (state.phone) qs += '&phone=' + encodeURIComponent(state.phone);
          window.location.href = step.url + qs;
        } else {
          activateStepCard(this, idx);
        }
      });
    });

    if (config.length > 0) {
      var firstCard = document.querySelector('.booking-step-card');
      if (firstCard) activateStepCard(firstCard, 0);
    }
  }

  function activateStepCard(cardEl, index) {
    document.querySelectorAll('.booking-step-card').forEach(function(c) {
      c.classList.remove('active');
    });
    cardEl.classList.add('active');

    state.currentStep = index + 1;

    var ids = ['step1','step2','step3','step4','stepSuccess'];
    ids.forEach(function(id) {
      var el = document.getElementById(id);
      if (el) el.classList.add('hidden');
    });

    var config = getStepConfig(state.courseId);
    var step = config[index];

    if (step.id === 'registration') {
      document.getElementById('step1').classList.remove('hidden');
    } else if (step.id === 'placement') {
      document.getElementById('step2').classList.remove('hidden');
      document.getElementById('step2Results').classList.add('hidden');
      document.getElementById('testQuestionArea').classList.remove('hidden');
      document.getElementById('testNav').classList.remove('hidden');
      if (typeof startTest === 'function') startTest();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  var bannerMap = {
    'private': 'assets/booking/formation-solo-booking.webp',
    'solo': 'assets/booking/formation-solo-booking.webp',
    'tcf-quebec': 'assets/booking/tcf-quebec-booking.webp',
    'tcf-quebec-partiel': 'assets/booking/tcf-quebec-booking.webp',
    'tcf-quebec-intensif': 'assets/booking/tcf-quebec-booking.webp',
    'tcf-canada': 'assets/booking/tcf-canada-booking.webp',
    'tcf-canada-partiel': 'assets/booking/tcf-canada-booking.webp',
    'tcf-canada-intensif': 'assets/booking/tcf-canada-booking.webp',
    'tcf': 'assets/booking/tcf-quebec-booking.webp',
    'oral-bc': 'assets/booking/cap-sur-loral-booking.webp',
    'oral': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-b': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-b-partiel': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-b-intensif': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-c': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-c-partiel': 'assets/booking/cap-sur-loral-booking.webp',
    'oral-c-intensif': 'assets/booking/cap-sur-loral-booking.webp',
    'intensif': 'assets/booking/parcours-linguistique-booking.webp',
    'groupe': 'assets/booking/parcours-linguistique-booking.webp',
    'samedi': 'assets/booking/parcours-linguistique-booking.webp',
    'workshop-conversation': 'assets/booking/ateliers-booking.webp',
    'workshop-culture': 'assets/booking/ateliers-booking.webp',
    'workshop-maintenance': 'assets/booking/ateliers-booking.webp',
    'workshop': 'assets/booking/ateliers-booking.webp',
    'francais-express': 'assets/booking/parcours-linguistique-booking.webp',
    'english-express': 'assets/booking/english-booking.webp',
    'english-linguistic-pathway': 'assets/booking/english-booking.webp',
    'soiree-linguo': 'assets/booking/parcours-linguistique-booking.webp',
    'evening-lingo': 'assets/booking/english-booking.webp',
    'samedis-francais': 'assets/booking/parcours-linguistique-booking.webp',
    'saturdays-english': 'assets/booking/english-booking.webp'
  };

  var programData = {
    'complete-french': 'Français complet',
    'oral-communication': 'Communication orale',
    'tcf-preparation': 'Préparation au TCF',
    'french-refresher': 'Remise à niveau',
    'public-service': 'Fonction publique',
    'exam-simulations': 'Simulations d\'examen',
    'french-maintenance': 'Maintien du français',
    'intensif': 'Intensif',
    'regulier': 'Régulier'
  };

  var descriptionOverrides = {
    'private-complete-french': 'Programme complet de français en cours particuliers. Idéal pour une immersion totale.',
    'private-oral-communication': 'Cours particuliers axés sur la communication orale. Améliorez votre expression et votre compréhension.',
    'private-tcf-preparation': 'Préparation individuelle au TCF avec un enseignant dédié.',
    'private-french-refresher': 'Remise à niveau en français. Parfait pour réactiver vos connaissances.',
    'private-public-service': 'Français pour la fonction publique. Vocabulaire et situations administratives.',
    'private-exam-simulations': 'Simulations d\'examen en conditions réelles. Préparez-vous en toute confiance.',
    'private-french-maintenance': 'Cours de maintien pour garder votre niveau de français.'
  };

  var groupData = {
    'tcf-quebec': [
      { id:'A', label:'Groupe A', days:'Lun · Mer · Ven', time:'18:00 – 20:00', date:'7 juil 2026', seats:4, total:12 },
      { id:'B', label:'Groupe B', days:'Mar · Jeu', time:'09:00 – 11:00', date:'8 juil 2026', seats:2, total:12 },
      { id:'C', label:'Groupe C', days:'Samedi', time:'09:00 – 12:00', date:'12 juil 2026', seats:7, total:12 }
    ],
    'tcf-canada': [
      { id:'A', label:'Groupe A', days:'Lun · Mer · Ven', time:'18:00 – 20:00', date:'7 juil 2026', seats:3, total:12 },
      { id:'B', label:'Groupe B', days:'Mar · Jeu', time:'10:00 – 12:00', date:'8 juil 2026', seats:5, total:12 }
    ],
    'intensif': [
      { id:'A', label:'Groupe A', days:'Lun · Mar · Mer · Jeu', time:'09:00 – 12:00', date:'7 juil 2026', seats:6, total:15 },
      { id:'B', label:'Groupe B', days:'Lun · Mar · Mer · Jeu', time:'18:00 – 21:00', date:'7 juil 2026', seats:8, total:15 }
    ],
    'groupe': [
      { id:'A', label:'Groupe A', days:'Mar · Jeu', time:'18:00 – 20:00', date:'8 juil 2026', seats:5, total:12 },
      { id:'B', label:'Groupe B', days:'Mercredi', time:'10:00 – 12:00', date:'9 juil 2026', seats:3, total:12 }
    ],
    'samedi': [
      { id:'A', label:'Groupe A', days:'Samedi', time:'09:00 – 12:00', date:'12 juil 2026', seats:8, total:15 },
      { id:'B', label:'Groupe B', days:'Samedi', time:'13:00 – 16:00', date:'12 juil 2026', seats:5, total:15 }
    ],
    'oral-bc': [
      { id:'A', label:'Groupe A', days:'Lun · Mer', time:'10:00 – 11:30', date:'7 juil 2026', seats:2, total:4 },
      { id:'B', label:'Groupe B', days:'Mar · Jeu', time:'18:00 – 19:30', date:'8 juil 2026', seats:1, total:4 },
      { id:'C', label:'Groupe C', days:'Mer · Ven', time:'14:00 – 15:30', date:'9 juil 2026', seats:3, total:4 }
    ],
    'workshop-conversation': [
      { id:'A', label:'Groupe A', days:'Mercredi', time:'18:00 – 19:30', date:'9 juil 2026', seats:6, total:10 },
      { id:'B', label:'Groupe B', days:'Vendredi', time:'10:00 – 11:30', date:'11 juil 2026', seats:9, total:10 }
    ],
    'workshop-culture': [
      { id:'A', label:'Groupe A', days:'Jeudi', time:'18:00 – 19:30', date:'10 juil 2026', seats:7, total:10 },
      { id:'B', label:'Groupe B', days:'Samedi', time:'11:00 – 12:30', date:'12 juil 2026', seats:4, total:10 }
    ],
    'workshop-maintenance': [
      { id:'A', label:'Groupe A', days:'Mardi', time:'10:00 – 11:30', date:'8 juil 2026', seats:5, total:10 },
      { id:'B', label:'Groupe B', days:'Jeudi', time:'18:00 – 19:30', date:'10 juil 2026', seats:8, total:10 }
    ],
    'francais-express': [
      { id:'A', label:'Groupe A — Matin', days:'Lun · Mar · Mer · Jeu · Ven', time:'09:00 – 12:00', date:'7 juil 2026', seats:8, total:15 },
      { id:'B', label:'Groupe B — Soir', days:'Lun · Mar · Mer · Jeu · Ven', time:'17:00 – 20:00', date:'7 juil 2026', seats:10, total:15 }
    ],
    'english-express': [
      { id:'A', label:'Group A — Morning', days:'Mon · Tue · Wed · Thu · Fri', time:'9:00 a.m. – 12:00 p.m.', date:'7 Jul 2026', seats:8, total:15 },
      { id:'B', label:'Group B — Evening', days:'Mon · Tue · Wed · Thu · Fri', time:'5:00 p.m. – 8:00 p.m.', date:'7 Jul 2026', seats:10, total:15 }
    ],
    'soiree-linguo': [
      { id:'A', label:'Groupe A', days:'Mercredi', time:'17:00 – 20:00', date:'9 juil 2026', seats:6, total:12 },
      { id:'B', label:'Groupe B', days:'Jeudi', time:'17:00 – 20:00', date:'10 juil 2026', seats:8, total:12 }
    ],
    'evening-lingo': [
      { id:'A', label:'Group A', days:'Wednesday', time:'5:00 p.m. – 8:00 p.m.', date:'9 Jul 2026', seats:6, total:12 },
      { id:'B', label:'Group B', days:'Thursday', time:'5:00 p.m. – 8:00 p.m.', date:'10 Jul 2026', seats:8, total:12 }
    ],
    'samedis-francais': [
      { id:'A', label:'Groupe A — Matin', days:'Samedi', time:'09:00 – 12:00', date:'12 juil 2026', seats:10, total:15 },
      { id:'B', label:'Groupe B — Après-midi', days:'Samedi', time:'13:00 – 16:00', date:'12 juil 2026', seats:8, total:15 }
    ],
    'saturdays-english': [
      { id:'A', label:'Group A — Morning', days:'Saturday', time:'9:00 a.m. – 12:00 p.m.', date:'12 Jul 2026', seats:10, total:15 },
      { id:'B', label:'Group B — Afternoon', days:'Saturday', time:'1:00 p.m. – 4:00 p.m.', date:'12 Jul 2026', seats:8, total:15 }
    ]
  };

  // ════════════════════════════════════════════════════════════
  //  TEST DATA — 20 questions
  // ════════════════════════════════════════════════════════════
  var testQuestions = [
    // 1–4: Grammar
    { q:'Elle ___ une pomme.', opts:['mange','manges','mangent','mangeons'], correct:0, cat:'Grammaire' },
    { q:'Nous ___ à la plage demain.', opts:['allons','allez','vont','allions'], correct:0, cat:'Grammaire' },
    { q:'Il ___ déjà fini ses devoirs.', opts:['a','as','ont','avons'], correct:0, cat:'Grammaire' },
    { q:'Les enfants ___ jouer dans le jardin.', opts:['aiment','aimes','aimez','aime'], correct:0, cat:'Grammaire' },
    // 5–8: Vocabulary
    { q:'Quel est le synonyme de "content" ?', opts:['Triste','Heureux','Fâché','Fatigué'], correct:1, cat:'Vocabulaire' },
    { q:'Que signifie "néanmoins" ?', opts:['Donc','Pourtant','Ensuite','Parce que'], correct:1, cat:'Vocabulaire' },
    { q:'Choisissez le mot correct : Elle porte une ___ élégante.', opts:['chemise','chaussette','ceinture','chaise'], correct:0, cat:'Vocabulaire' },
    { q:'Quel mot est un antonyme de "facile" ?', opts:['Simple','Difficile','Rapide','Lent'], correct:1, cat:'Vocabulaire' },
    // 9–12: Reading comprehension
    { q:'"Marie a visité le musée hier. Elle a adoré les peintures impressionnistes." Quand Marie a-t-elle visité le musée ?', opts:['Aujourd\'hui','Hier','Demain','La semaine prochaine'], correct:1, cat:'Compréhension' },
    { q:'"Il pleuvait donc nous sommes restés à la maison." Pourquoi sont-ils restés à la maison ?', opts:['Parce qu\'il faisait beau','Parce qu\'il pleuvait','Parce qu\'ils étaient fatigués','Parce qu\'ils avaient peur'], correct:1, cat:'Compréhension' },
    { q:'"Pierre a acheté du pain, du fromage et des fruits au marché." Qu\'a acheté Pierre ?', opts:['Des vêtements','De la nourriture','Des livres','Des meubles'], correct:1, cat:'Compréhension' },
    { q:'"Le TGV part à 14h30 de la gare de Lyon." Quelle est l\'heure de départ ?', opts:['4h30','2h30 du matin','14h30','Minuit et demi'], correct:2, cat:'Compréhension' },
    // 13–16: Verbs and conjugation
    { q:'Conjuguez : Je ___ (vouloir) un café.', opts:['veut','veux','voulez','voudrais'], correct:1, cat:'Conjugaison' },
    { q:'Ils ___ (pouvoir) venir demain.', opts:['peut','pouvons','peuvent','pouvez'], correct:2, cat:'Conjugaison' },
    { q:'Si j\'___ (avoir) du temps, je voyagerais.', opts:['ai','avais','aurai','avais'], correct:1, cat:'Conjugaison' },
    { q:'Il faut que tu ___ (faire) tes devoirs.', opts:['fais','fasses','fait','ferez'], correct:1, cat:'Conjugaison' },
    // 17–20: Mixed
    { q:'"Dont" dans une phrase indique :', opts:['La possession','La négation','La cause','Le lieu'], correct:0, cat:'Grammaire' },
    { q:'Quelle est la forme correcte ?', opts:['Je me suis levé','Je me suis levé(e)','Je suis levé','Je me suis levée'], correct:1, cat:'Grammaire' },
    { q:'Choisissez la préposition correcte : Il habite ___ Québec.', opts:['à','en','au','dans'], correct:0, cat:'Grammaire' },
    { q:'Complétez : C\'est le livre ___ j\'ai parlé.', opts:['que','dont','qui','où'], correct:1, cat:'Grammaire' }
  ];

  // ════════════════════════════════════════════════════════════
  //  GO TO STEP (backward compat)
  // ════════════════════════════════════════════════════════════
  function goToStep(n) {
    var cards = document.querySelectorAll('.booking-step-card');
    var idx = n - 1;
    if (idx >= 0 && idx < cards.length) {
      var config = getStepConfig(state.courseId);
      var step = config[idx];
      if (step.type === 'external') {
        window.open(step.url, '_blank');
        return;
      }
      if (step.type === 'redirect') {
        var qs = '?course=' + encodeURIComponent(state.courseId);
        if (state.program) qs += '&program=' + encodeURIComponent(state.program);
        window.location.href = step.url + qs;
        return;
      }
      activateStepCard(cards[idx], idx);
    }
  }

  // ════════════════════════════════════════════════════════════
  //  INIT — Parse URL params
  // ════════════════════════════════════════════════════════════
  function getParam(name) {
    var match = location.search.match(new RegExp('[?&]' + name + '=([^&]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function init() {
    // Map DB service slugs / aliases to courseDB wizard entries
    var courseAliasMap = {
      'solo-5h': 'solo', 'solo-10h': 'solo', 'solo-15h': 'solo', 'solo-20h': 'solo',
      'samedis-en-francais': 'samedis-francais',
      'saturdays-in-english': 'saturdays-english',
      'english-linguistic-pathway': 'english-linguistic-pathway',
      'tcf': 'tcf'
    };
    var requestedParam = getParam('course') || getParam('service');
    if (!requestedParam) {
      requestedParam = 'private';
    } else if (!courseDB[requestedParam]) {
      requestedParam = courseAliasMap[requestedParam] || 'private';
    }
    state.courseId = requestedParam;
    state.program = getParam('program') || null;
    state.courseData = courseDB[state.courseId];

    if (!state.courseData) {
      state.courseId = 'private';
      state.courseData = courseDB['private'];
    }

    // Display course summary
    renderBookingCourseSummary();

    // Render booking step cards for this program
    renderBookingCards();

    // Show/hide package picker and schedule section based on type
    var soloPkgEl = document.getElementById('soloPackagePicker');
    var soloEl = document.getElementById('soloSchedule');
    var groupEl = document.getElementById('groupSchedule');

    if (state.courseData.type === 'solo') {
      groupEl.classList.add('hidden');
      soloEl.classList.add('hidden');
      soloPkgEl.classList.add('hidden');
      // Only show package picker if course has packages defined
      if (state.courseData.packages) {
        soloPkgEl.classList.remove('hidden');
        // Auto-select package if program param is present
        if (state.program) {
          var foundPkg = null;
          for (var pi = 0; pi < state.courseData.packages.length; pi++) {
            if (state.courseData.packages[pi].id === state.program) { foundPkg = state.courseData.packages[pi]; break; }
          }
          if (foundPkg) state.soloPackage = state.program;
        }
        renderSoloPackages();
        if (state.soloPackage) showSoloSchedule();
      } else {
        // No packages → show schedule directly
        soloEl.classList.remove('hidden');
        initSoloCalendar();
      }
    } else {
      soloPkgEl.classList.add('hidden');
      soloEl.classList.add('hidden');
      groupEl.classList.remove('hidden');
    }

    // Update CTA text based on service type
    updateStep1CTA();
  }

  function updateStep1CTA() {
    var config = getStepConfig(state.courseId);
    var nextStep = config[1];
    var btn = document.getElementById('toStep2');
    if (nextStep && nextStep.id === 'payment') {
      btn.innerHTML = 'Confirmer et passer au paiement <i class="fas fa-arrow-right"></i>';
    } else {
      btn.innerHTML = 'Confirmer l\'inscription et continuer <i class="fas fa-arrow-right"></i>';
    }
  }

  function renderBookingCourseSummary() {
    var lang = window.currentLang || localStorage.getItem('cultulangues_lang') || 'fr';
    var cdb = window.courseContent && window.courseContent[lang]
              && window.courseContent[lang].booking
              && window.courseContent[lang].booking.courseDB;
    var pd = window.courseContent && window.courseContent[lang]
              && window.courseContent[lang].booking
              && window.courseContent[lang].booking.programData;
    var doverrides = window.courseContent && window.courseContent[lang]
              && window.courseContent[lang].booking
              && window.courseContent[lang].booking.descriptionOverrides;
    var courseEntry = cdb ? cdb[state.courseId] : null;
    var overrideKey = state.courseId + '-' + state.program;
    var desc = doverrides && doverrides[overrideKey]
               ? doverrides[overrideKey]
               : (courseEntry ? courseEntry.desc
                  : descriptionOverrides[overrideKey] || state.courseData.desc);
    var name = courseEntry ? courseEntry.name : state.courseData.name;
    var price = state.courseData.price;

    if (state.courseData.type === 'solo' && state.soloPackage && state.courseData.packages) {
      var pkgs = state.courseData.packages;
      var pkg = null;
      for (var i = 0; i < pkgs.length; i++) {
        if (pkgs[i].id === state.soloPackage) { pkg = pkgs[i]; break; }
      }
      if (pkg) {
        name += ' — ' + pkg.label + ' (' + pkg.hours + ' h)';
        price = pkg.price + ' $';
      }
    }

    document.getElementById('csName').textContent = name;
    document.getElementById('csDesc').textContent = desc;
    document.getElementById('csPrice').textContent = price;
    var thumbImg = bannerMap[state.courseId] || 'assets/services/homepage-hero.webp';
    document.getElementById('bkSummaryThumb').src = thumbImg;
  }

  // ════════════════════════════════════════════════════════════
  //  STEP 1: Solo package picker
  // ════════════════════════════════════════════════════════════
  function renderSoloPackages() {
    var pkgs = state.courseData.packages;
    if (!pkgs) return;
    var grid = document.getElementById('soloPackageGrid');
    var html = '';
    for (var i = 0; i < pkgs.length; i++) {
      var p = pkgs[i];
      var sel = state.soloPackage === p.id ? ' selected' : '';
      var pop = p.popular ? '<div class="popular-badge" data-i18n="common.popular">Populaire</div>' : '';
      html +=
        '<div class="package-card' + sel + '" data-pkg="' + p.id + '">' +
          pop +
          '<div class="pkg-name">' + p.label + '</div>' +
          '<div class="pkg-hours">' + p.hours + ' h</div>' +
          '<div class="pkg-hours-sub">' + p.sessions + ' séances</div>' +
          '<div class="pkg-rate">' + p.rate + ' $ / h</div>' +
          '<div class="pkg-price">' + p.price + ' $</div>' +
          '<div class="pkg-check"><i class="fas fa-check"></i></div>' +
        '</div>';
    }
    grid.innerHTML = html;
    Array.from(grid.children).forEach(function(el) {
      el.addEventListener('click', function() { selectSoloPackage(this.getAttribute('data-pkg')); });
    });
  }

  function selectSoloPackage(pkgId) {
    state.soloPackage = pkgId;
    renderSoloPackages();
    renderBookingCourseSummary();
    showSoloSchedule();
    checkStep1();
  }

  function showSoloSchedule() {
    var soloEl = document.getElementById('soloSchedule');
    if (state.soloPackage) {
      soloEl.classList.remove('hidden');
      initSoloCalendar();
    } else {
      soloEl.classList.add('hidden');
    }
  }

  // ════════════════════════════════════════════════════════════
  //  STEP 1: Solo calendar & time slots
  // ════════════════════════════════════════════════════════════
  var soloToday = new Date();

  function initSoloCalendar() {
    state.soloCalMonth = soloToday.getMonth();
    state.soloCalYear = soloToday.getFullYear();
    renderSoloCalendar();
  }

  function renderSoloCalendar() {
    var grid = document.getElementById('soloCalGrid');
    var monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    document.getElementById('soloCalTitle').textContent = monthNames[state.soloCalMonth] + ' ' + state.soloCalYear;

    var firstDay = new Date(state.soloCalYear, state.soloCalMonth, 1).getDay();
    firstDay = firstDay === 0 ? 6 : firstDay - 1;
    var daysInMonth = new Date(state.soloCalYear, state.soloCalMonth + 1, 0).getDate();

    var html = '<div class="day-name">Lun</div><div class="day-name">Mar</div><div class="day-name">Mer</div><div class="day-name">Jeu</div><div class="day-name">Ven</div><div class="day-name">Sam</div><div class="day-name">Dim</div>';

    for (var e = 0; e < firstDay; e++) {
      html += '<div></div>';
    }

    for (var d = 1; d <= daysInMonth; d++) {
      var date = new Date(state.soloCalYear, state.soloCalMonth, d);
      var isToday = date.toDateString() === soloToday.toDateString();
      var isPast = date < new Date(soloToday.getFullYear(), soloToday.getMonth(), soloToday.getDate());
      var isWeekend = date.getDay() === 0 || date.getDay() === 6;
      var isAvailable = !isPast && !isWeekend;
      var isSelected = state.soloDate && date.toDateString() === state.soloDate.toDateString();
      var classes = 'day-num';
      if (isAvailable) classes += ' available';
      if (isSelected) classes += ' selected';
      if (isToday) classes += ' today';
      if (isPast) classes += ' past';

      html += '<div class="' + classes + '" data-day="' + d + '">' + d + '</div>';
    }

    grid.innerHTML = html;

    grid.querySelectorAll('.day-num.available').forEach(function(el) {
      el.addEventListener('click', function() {
        var day = parseInt(this.getAttribute('data-day'));
        selectSoloDate(day);
      });
    });
  }

  function soloCalChange(delta) {
    state.soloCalMonth += delta;
    if (state.soloCalMonth < 0) { state.soloCalMonth = 11; state.soloCalYear--; }
    if (state.soloCalMonth > 11) { state.soloCalMonth = 0; state.soloCalYear++; }
    renderSoloCalendar();
  }

  function selectSoloDate(day) {
    var d = new Date(state.soloCalYear, state.soloCalMonth, day);
    state.soloDate = d;

    document.querySelectorAll('#soloCalGrid .day-num').forEach(function(el) { el.classList.remove('selected'); });
    var calGrid = document.getElementById('soloCalGrid');
    calGrid.querySelectorAll('.day-num[data-day="' + day + '"]').forEach(function(el) { el.classList.add('selected'); });

    renderSoloTimeSlots(d);
    checkStep1();
  }

  function renderSoloTimeSlots(date) {
    var slotGrid = document.getElementById('soloSlotGrid');
    var dayNames = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    document.getElementById('soloDateDisplay').textContent =
      dayNames[date.getDay()] + ' ' + date.getDate() + ' ' +
      ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'][date.getMonth()];

    var slots = ['09:00','09:30','10:00','10:30','11:00','11:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30'];
    var html = '';
    slots.forEach(function(s) {
      var isTaken = Math.random() < 0.2;
      var dis = isTaken ? ' disabled' : '';
      var sel = state.soloSlot === s ? ' selected' : '';
      html += '<button class="slot-btn' + sel + dis + '" data-slot="' + s + '">' + s + '</button>';
    });
    slotGrid.innerHTML = html;

    slotGrid.querySelectorAll('.slot-btn:not(:disabled)').forEach(function(btn) {
      btn.addEventListener('click', function() {
        slotGrid.querySelectorAll('.slot-btn').forEach(function(b) { b.classList.remove('selected'); });
        this.classList.add('selected');
        state.soloSlot = this.getAttribute('data-slot');
        checkStep1();
      });
    });
  }

  var tz = new Date().getTimezoneOffset();
  var tzHours = -Math.round(tz / 60);
  document.getElementById('soloTzOffset').textContent = (tzHours >= 0 ? '+' : '') + tzHours;

  // ════════════════════════════════════════════════════════════
  //  STEP 1: Group selection
  // ════════════════════════════════════════════════════════════
  function renderGroups() {
    var grid = document.getElementById('groupGrid');
    var data = groupData[state.courseId] || [];
    if (data.length === 0) {
      grid.innerHTML = '<div style="text-align:center;padding:32px;color:var(--text-secondary);grid-column:1/-1">Aucun groupe disponible pour ce cours.</div>';
      return;
    }
    var html = '';
    data.forEach(function(g) {
      var seatClass = g.seats <= 2 ? 'group-seats low' : 'group-seats';
      html += '<div class="group-card" data-group-id="' + g.id + '">' +
        '<div class="group-name">' + g.label + '</div>' +
        '<div class="group-days"><i class="fas fa-calendar-alt"></i> ' + g.days + '</div>' +
        '<div class="group-time"><i class="fas fa-clock"></i> ' + g.time + '</div>' +
        '<div class="group-meta"><span>Début : ' + g.date + '</span><span class="' + seatClass + '">' + g.seats + '/' + g.total + ' places</span></div>' +
        '<div class="check"><i class="fas fa-check"></i></div>' +
        '</div>';
    });
    grid.innerHTML = html;

    grid.querySelectorAll('.group-card').forEach(function(card) {
      card.addEventListener('click', function() {
        grid.querySelectorAll('.group-card').forEach(function(c) { c.classList.remove('selected'); });
        this.classList.add('selected');
        var id = this.getAttribute('data-group-id');
        var d = data.find(function(g) { return g.id === id; });
        if (d) {
          state.group = d;
          checkStep1();
        }
      });
    });
  }

  function checkStep1() {
    state.name = document.getElementById('infoName').value.trim();
    state.email = document.getElementById('infoEmail').value.trim();
    state.phone = document.getElementById('infoPhone').value.trim();
    state.contact = document.getElementById('infoContact').value.trim();
    state.notes = document.getElementById('infoNotes').value.trim();

    var infoValid = state.name && state.email && state.phone;

    var scheduleValid = true;
    var infoMsg = '';

    if (state.courseData.type === 'solo') {
      var hasPkg = state.courseData.packages ? state.soloPackage !== null : true;
      var hasDate = state.soloDate !== null;
      var hasSlot = state.soloSlot !== null;
      scheduleValid = hasPkg && hasDate && hasSlot;

      if (!hasPkg && state.courseData.packages) {
        infoMsg = '<i class="fas fa-info-circle"></i> Choisissez un forfait';
      } else if (!hasDate) {
        infoMsg = '<i class="fas fa-info-circle"></i> Choisissez une date';
      } else if (!hasSlot) {
        infoMsg = '<i class="fas fa-info-circle"></i> Choisissez un créneau';
      } else {
        var dayNames = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
        var monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        infoMsg = '<i class="fas fa-check-circle" style="color:var(--green)"></i> ' +
          dayNames[state.soloDate.getDay()] + ' ' + state.soloDate.getDate() + ' ' + monthNames[state.soloDate.getMonth()] +
          ' à ' + state.soloSlot;
      }
    } else {
      // Group: no group picker needed — group assigned after oral eval
      scheduleValid = true;
      infoMsg = '<i class="fas fa-check-circle" style="color:var(--green)"></i> Groupe attribué après l\'évaluation orale';
    }

    var allValid = infoValid && scheduleValid;
    document.getElementById('toStep2').disabled = !allValid;
    if (allValid) {
      document.getElementById('step1Info').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green)"></i> Tout est complet';
    } else {
      document.getElementById('step1Info').innerHTML = infoMsg + ' · ' + (infoValid ? '' : 'Remplissez tous les champs');
    }
  }

  document.getElementById('toStep2').addEventListener('click', function() {
    if (this.disabled) return;
    var config = getStepConfig(state.courseId);
    var nextStep = config[1];
    if (nextStep && nextStep.type === 'external') {
      window.open(nextStep.url, '_blank');
    } else if (nextStep && nextStep.type === 'redirect') {
      var qs = '?course=' + encodeURIComponent(state.courseId);
      if (state.program) qs += '&program=' + encodeURIComponent(state.program);
      if (state.soloPackage) qs += '&pkg=' + encodeURIComponent(state.soloPackage);
      if (state.soloDate) qs += '&date=' + encodeURIComponent(state.soloDate.toISOString().split('T')[0]);
      if (state.soloSlot) qs += '&slot=' + encodeURIComponent(state.soloSlot);
      if (state.name) qs += '&name=' + encodeURIComponent(state.name);
      if (state.email) qs += '&email=' + encodeURIComponent(state.email);
      if (state.phone) qs += '&phone=' + encodeURIComponent(state.phone);
      window.location.href = nextStep.url + qs;
    } else if (nextStep) {
      goToStep(2);
    }
  });

  // ════════════════════════════════════════════════════════════
  //  STEP 2: Placement Test
  // ════════════════════════════════════════════════════════════
  var timerInterval = null;
  var timerSeconds = 1200; // 20 minutes

  function startTest() {
    state.testAnswers = new Array(testQuestions.length).fill(null);
    state.currentQuestion = 0;
    renderQuestion();
    startTimer();
  }

  function renderQuestion() {
    var idx = state.currentQuestion;
    var q = testQuestions[idx];

    document.getElementById('qNum').textContent = 'QUESTION ' + (idx + 1) + ' / ' + testQuestions.length;
    document.getElementById('qText').textContent = q.q;
    document.getElementById('testProgressFill').style.width = ((idx + 1) / testQuestions.length * 100) + '%';
    document.getElementById('testProgressText').textContent = (idx + 1) + ' / ' + testQuestions.length;

    var optHtml = '';
    var letters = ['A','B','C','D'];
    q.opts.forEach(function(opt, oi) {
      var sel = state.testAnswers[idx] === oi ? ' selected' : '';
      optHtml += '<div class="test-option' + sel + '" data-opt="' + oi + '">' +
        '<span class="letter">' + letters[oi] + '</span>' +
        '<span>' + opt + '</span></div>';
    });
    document.getElementById('qOptions').innerHTML = optHtml;

    document.querySelectorAll('.test-option').forEach(function(el) {
      el.addEventListener('click', function() {
        selectAnswer(parseInt(this.getAttribute('data-opt')));
      });
    });

    document.getElementById('testBackBtn').disabled = idx === 0;

    var answered = state.testAnswers[idx] !== null;
    document.getElementById('testNextBtn').disabled = !answered;
    if (answered) {
      document.getElementById('testInfo').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green)"></i> Réponse enregistrée';
    } else {
      document.getElementById('testInfo').innerHTML = '<i class="fas fa-info-circle"></i> Choisissez une réponse pour continuer';
    }

    var isLast = idx === testQuestions.length - 1;
    document.getElementById('testNextBtn').innerHTML = isLast
      ? 'Voir mes résultats <i class="fas fa-arrow-right"></i>'
      : 'Suivante <i class="fas fa-arrow-right"></i>';
  }

  function selectAnswer(optIdx) {
    var idx = state.currentQuestion;
    state.testAnswers[idx] = optIdx;

    document.querySelectorAll('.test-option').forEach(function(el) {
      el.classList.remove('selected');
      if (parseInt(el.getAttribute('data-opt')) === optIdx) {
        el.classList.add('selected');
      }
    });

    document.getElementById('testNextBtn').disabled = false;
    document.getElementById('testInfo').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green)"></i> Réponse enregistrée';
  }

  document.getElementById('testNextBtn').addEventListener('click', function() {
    if (this.disabled) return;
    var isLast = state.currentQuestion === testQuestions.length - 1;
    if (isLast) {
      finishTest();
    } else {
      state.currentQuestion++;
      renderQuestion();
    }
  });

  function prevQuestion() {
    if (state.currentQuestion > 0) {
      state.currentQuestion--;
      renderQuestion();
    }
  }

  function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    timerSeconds = 1200;
    updateTimerDisplay();
    timerInterval = setInterval(function() {
      timerSeconds--;
      updateTimerDisplay();
      if (timerSeconds <= 0) {
        clearInterval(timerInterval);
        finishTest();
      }
    }, 1000);
  }

  function updateTimerDisplay() {
    var min = Math.floor(timerSeconds / 60);
    var sec = timerSeconds % 60;
    document.getElementById('timerDisplay').textContent =
      (min < 10 ? '0' : '') + min + ':' + (sec < 10 ? '0' : '') + sec;
  }

  function finishTest() {
    clearInterval(timerInterval);
    var correct = 0;
    state.testAnswers.forEach(function(ans, i) {
      if (ans === testQuestions[i].correct) correct++;
    });

    var total = testQuestions.length;
    var pct = Math.round(correct / total * 100);
    var level = getLevel(pct);

    // Populate inline results
    document.getElementById('step2ResultsScore').textContent = correct + ' / ' + total;
    document.getElementById('step2ResultsLevel').textContent = 'Niveau ' + level;
    document.getElementById('step2ResultsLevel').className = 'results-level ' + level;

    // Chart bars
    var incorrect = total - correct;
    var correctH = Math.max(4, correct / total * 100);
    var incorrectH = Math.max(4, incorrect / total * 100);
    document.getElementById('step2CorrectBar').style.height = correctH + '%';
    document.getElementById('step2IncorrectBar').style.height = incorrectH + '%';
    document.getElementById('step2CorrectCount').textContent = correct;
    document.getElementById('step2IncorrectCount').textContent = incorrect;

    // Breakdown by category
    var cats = {};
    testQuestions.forEach(function(q, i) {
      if (!cats[q.cat]) cats[q.cat] = { total: 0, correct: 0 };
      cats[q.cat].total++;
      if (state.testAnswers[i] === q.correct) cats[q.cat].correct++;
    });

    var bdHtml = '';
    for (var cat in cats) {
      var c = cats[cat];
      var cpct = Math.round(c.correct / c.total * 100);
      var cls = cpct >= 70 ? 'high' : (cpct >= 40 ? 'med' : 'low');
      bdHtml += '<div class="results-category">' +
        '<div class="cat-label">' + cat + '</div>' +
        '<div class="cat-score ' + cls + '">' + c.correct + '/' + c.total + '</div>' +
        '</div>';
    }
    document.getElementById('step2ResultsBreakdown').innerHTML = bdHtml;

    // Explanation
    var levels = {
      C2: { min: 95, label: 'C2 — Maîtrise', msg: 'Félicitations ! Vous avez un niveau de maîtrise avancé. Un test oral confirmera votre classement.' },
      C1: { min: 80, label: 'C1 — Autonome', msg: 'Excellent niveau ! Vous êtes à l\'aise dans la plupart des situations. Un test oral est requis pour valider votre niveau.' },
      B2: { min: 60, label: 'B2 — Intermédiaire avancé', msg: 'Bon niveau ! Vous pouvez communiquer avec une certaine aisance. Le test oral permettra d\'affiner votre classement.' },
      B1: { min: 40, label: 'B1 — Intermédiaire', msg: 'Vous avez des bases solides. Un test oral est nécessaire pour évaluer votre expression et compréhension orales.' },
      A2: { min: 20, label: 'A2 — Élémentaire', msg: 'Vous commencez à construire des phrases simples. Le test oral nous aidera à mieux cibler vos besoins.' },
      A1: { min: 0, label: 'A1 — Débutant', msg: 'Vous faites vos premiers pas en français. Ne vous inquiétez pas, nos cours sont adaptés à tous les niveaux !' }
    };
    var levelInfo = levels[level] || levels.A1;
    document.getElementById('step2ResultsExplanationText').innerHTML =
      'Votre score indique un niveau <strong>' + levelInfo.label + '</strong>. ' + levelInfo.msg +
      '<br><br>Selon votre résultat, un test oral est requis pour finaliser votre niveau. Réservez votre appel ci-dessous.';

    // Show results within step 2
    document.getElementById('testQuestionArea').classList.add('hidden');
    document.getElementById('testNav').classList.add('hidden');
    document.getElementById('step2Results').classList.remove('hidden');

    // Set up the continue button
    var config = getStepConfig(state.courseId);
    var nextStep = config[2];
    var nextBtn = document.getElementById('step2ResultsNext');
    if (nextStep && nextStep.type === 'external') {
      nextBtn.innerHTML = 'Réserver l\'évaluation orale <i class="fas fa-arrow-right"></i>';
      nextBtn.onclick = function() { window.open(nextStep.url, '_blank'); };
    } else {
      nextBtn.innerHTML = 'Continuer <i class="fas fa-arrow-right"></i>';
      nextBtn.onclick = function() { goToStep(state.currentStep + 1); };
    }
  }

  function getLevel(pct) {
    if (pct >= 95) return 'C2';
    if (pct >= 80) return 'C1';
    if (pct >= 60) return 'B2';
    if (pct >= 40) return 'B1';
    if (pct >= 20) return 'A2';
    return 'A1';
  }

  // ════════════════════════════════════════════════════════════
  //  STEP 4: Oral Call Booking — Calendar
  // ════════════════════════════════════════════════════════════
  var today = new Date();

  function initCalendar() {
    state.calMonth = today.getMonth();
    state.calYear = today.getFullYear();
    renderCalendar();
  }

  function renderCalendar() {
    var grid = document.getElementById('calendarGrid');
    var monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    document.getElementById('calendarTitle').textContent = monthNames[state.calMonth] + ' ' + state.calYear;

    var firstDay = new Date(state.calYear, state.calMonth, 1).getDay();
    firstDay = firstDay === 0 ? 6 : firstDay - 1; // Convert Sunday=0 to Monday=0
    var daysInMonth = new Date(state.calYear, state.calMonth + 1, 0).getDate();

    var html = '<div class="day-name">Lun</div><div class="day-name">Mar</div><div class="day-name">Mer</div><div class="day-name">Jeu</div><div class="day-name">Ven</div><div class="day-name">Sam</div><div class="day-name">Dim</div>';

    // Empty cells
    for (var e = 0; e < firstDay; e++) {
      html += '<div></div>';
    }

    for (var d = 1; d <= daysInMonth; d++) {
      var date = new Date(state.calYear, state.calMonth, d);
      var isToday = date.toDateString() === today.toDateString();
      var isPast = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
      var isWeekend = date.getDay() === 0 || date.getDay() === 6;
      var isAvailable = !isPast && !isWeekend;
      var isSelected = state.selectedDate && date.toDateString() === state.selectedDate.toDateString();
      var classes = 'day-num';
      if (isAvailable) classes += ' available';
      if (isSelected) classes += ' selected';
      if (isToday) classes += ' today';
      if (isPast) classes += ' past';

      html += '<div class="' + classes + '" data-day="' + d + '">' + d + '</div>';
    }

    grid.innerHTML = html;

    grid.querySelectorAll('.day-num.available').forEach(function(el) {
      el.addEventListener('click', function() {
        var day = parseInt(this.getAttribute('data-day'));
        selectDate(day);
      });
    });
  }

  function changeMonth(delta) {
    state.calMonth += delta;
    if (state.calMonth < 0) { state.calMonth = 11; state.calYear--; }
    if (state.calMonth > 11) { state.calMonth = 0; state.calYear++; }
    renderCalendar();
  }

  function selectDate(day) {
    var d = new Date(state.calYear, state.calMonth, day);
    state.selectedDate = d;

    document.querySelectorAll('.day-num').forEach(function(el) { el.classList.remove('selected'); });
    var calGrid = document.getElementById('calendarGrid');
    calGrid.querySelectorAll('.day-num[data-day="' + day + '"]').forEach(function(el) { el.classList.add('selected'); });

    renderTimeSlots(d);
  }

  function renderTimeSlots(date) {
    var slotGrid = document.getElementById('slotGrid');
    var dayNames = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    document.getElementById('selectedDateDisplay').textContent =
      dayNames[date.getDay()] + ' ' + date.getDate() + ' ' +
      ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'][date.getMonth()];

    // Generate time slots
    var slots = ['09:00','09:30','10:00','10:30','11:00','11:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30'];
    var html = '';
    slots.forEach(function(s) {
      var isTaken = Math.random() < 0.2; // Simulate some slots already booked
      var dis = isTaken ? ' disabled' : '';
      var sel = state.selectedSlot === s ? ' selected' : '';
      html += '<button class="slot-btn' + sel + dis + '" data-slot="' + s + '">' + s + '</button>';
    });
    slotGrid.innerHTML = html;

    slotGrid.querySelectorAll('.slot-btn:not(:disabled)').forEach(function(btn) {
      btn.addEventListener('click', function() {
        slotGrid.querySelectorAll('.slot-btn').forEach(function(b) { b.classList.remove('selected'); });
        this.classList.add('selected');
        state.selectedSlot = this.getAttribute('data-slot');
        document.getElementById('oralConfirmBtn').disabled = false;
        document.getElementById('step4Info').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green)"></i> ' +
          document.getElementById('selectedDateDisplay').textContent + ' à ' + state.selectedSlot;
        setupStripePayment().catch(function() { /* non-blocking */ });
      });
    });
  }

  document.getElementById('toStep4').addEventListener('click', function() {
    var config = getStepConfig(state.courseId);
    var step = config[2];
    if (step && step.type === 'external') {
      window.open(step.url, '_blank');
    } else {
      initCalendar();
      goToStep(4);
    }
  });

  // Send the completed registration to the Laravel backend so it persists to the DB.
  // Resolves with { redirect } on success, rejects with a friendly error message on failure.
  function persistBooking(scheduleStr, correct, level, paymentIntentId) {
    var fullName = (document.getElementById('infoName').value || '').trim();
    var nameParts = fullName.split(/\s+/);
    var lastName = nameParts.length > 1 ? nameParts.pop() : '';
    var firstName = nameParts.join(' ');

    var soloDateStr = '';
    if (state.courseData.type === 'solo' && state.soloDate) {
      soloDateStr = state.soloDate.getFullYear() + '-' +
        ('0' + (state.soloDate.getMonth() + 1)).slice(-2) + '-' +
        ('0' + state.soloDate.getDate()).slice(-2);
    }

    var payload = {
      first_name: firstName,
      last_name: lastName,
      full_name: fullName,
      email: (document.getElementById('infoEmail').value || '').trim(),
      phone: (document.getElementById('infoPhone').value || '').trim(),
      contact_method: (document.getElementById('infoContact').value || '').trim(),
      notes: (document.getElementById('infoNotes').value || '').trim(),
      course: state.courseId,
      program: state.program || null,
      package: state.soloPackage || null,
      group: state.group ? state.group.id : null,
      preferred_date: soloDateStr,
      preferred_slot: state.soloSlot || null,
      placement_score: correct,
      placement_level: level,
      oral_test_date: state.selectedDate
        ? state.selectedDate.getFullYear() + '-' +
          ('0' + (state.selectedDate.getMonth() + 1)).slice(-2) + '-' +
          ('0' + state.selectedDate.getDate()).slice(-2)
        : null,
      oral_test_slot: state.selectedSlot || null,
      oral_test_status: 'planifie',
      payment_intent_id: paymentIntentId || null
    };

    var url = (window.bookingRoutes && window.bookingRoutes.store) || '/booking';
    var csrf = document.querySelector('meta[name="csrf-token"]') &&
               document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf || ''
      },
      body: JSON.stringify(payload)
    }).then(function(res) {
      if (!res.ok) {
        return res.json().then(function(data) {
          var msg = (data && data.message) || 'Votre réservation n\'a pas pu être enregistrée. Veuillez réessayer.';
          throw new Error(msg);
        }).catch(function(err) {
          throw new Error(err && err.message ? err.message : 'Votre réservation n\'a pas pu être enregistrée. Veuillez réessayer.');
        });
      }
      return res.json();
    });
  }

  // ════════════════════════════════════════════════════════════
  //  STRIPE — Payment Element integration
  // ════════════════════════════════════════════════════════════
  var stripe = null;
  var stripeElements = null;
  var stripePaymentElement = null;
  var stripeIntentId = null;
  var stripeRequiresPayment = true;

  function formatCurrency(amount, currency) {
    try {
      return new Intl.NumberFormat('fr-CA', { style: 'currency', currency: (currency || 'cad').toUpperCase() }).format(amount);
    } catch (e) {
      return amount + ' $';
    }
  }

  function csrfToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  // Create a server-computed PaymentIntent for the selected program.
  function createPaymentIntent() {
    var payload = {
      email: (document.getElementById('infoEmail').value || '').trim(),
      course: state.courseId,
      package: state.soloPackage || null,
      program: state.program || null,
      group: state.group ? state.group.id : null
    };
    var url = (window.bookingRoutes && window.bookingRoutes.paymentIntent) || '/booking/payment-intent';

    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify(payload)
    }).then(function(res) { return res.json(); }).then(function(data) {
      if (!data || data.success === false) {
        stripeRequiresPayment = !!(data && data.requires_payment);
        throw new Error((data && data.message) || 'Impossible de préparer le paiement. Veuillez réessayer.');
      }
      stripeRequiresPayment = !!data.requires_payment;
      stripeIntentId = data.payment_intent_id || null;

      var section = document.getElementById('paymentSection');
      var amtEl = document.getElementById('paymentAmountDisplay');
      var errEl = document.getElementById('stripePaymentError');
      if (data.requires_payment && data.amount > 0) {
        if (section) section.classList.remove('hidden');
        if (amtEl) amtEl.textContent = formatCurrency(data.amount, data.currency || 'CAD');
        if (errEl) errEl.classList.add('hidden');
      } else {
        if (section) section.classList.add('hidden');
        if (amtEl) amtEl.textContent = '—';
        if (errEl) errEl.classList.add('hidden');
      }
      return data;
    });
  }

  // Ensure a PaymentIntent exists and the Payment Element is mounted.
  function setupStripePayment() {
    if (stripe && stripePaymentElement) return Promise.resolve();
    return createPaymentIntent().then(function(data) {
      if (!data.requires_payment || !window.stripeKey) return;
      if (stripe && stripePaymentElement) return;
      try {
        stripe = window.Stripe(window.stripeKey);
        stripeElements = stripe.elements();
        stripePaymentElement = stripeElements.create('payment');
        var container = document.getElementById('stripePaymentElement');
        if (container) stripePaymentElement.mount(container);
      } catch (err) {
        throw new Error('Impossible de charger le paiement sécurisé. Veuillez réessayer.');
      }
    });
  }

  // Confirm the Stripe PaymentIntent. Resolves { id, status } on success.
  function confirmStripePayment() {
    if (!stripeRequiresPayment) return Promise.resolve({ id: null, status: 'noop' });
    var doConfirm = function() {
      if (!stripe || !stripeElements || !stripePaymentElement) throw new Error('Le formulaire de paiement n\'est pas prêt. Veuillez réessayer.');
      return stripe.confirmPayment({
        elements: stripeElements,
        confirmParams: { return_url: window.location.href.split('#')[0] },
        redirect: 'if_required'
      }).then(function(result) {
        if (result.error) throw new Error(result.error.message || 'Le paiement a échoué. Veuillez vérifier vos informations et réessayer.');
        return { id: stripeIntentId, status: 'ok' };
      });
    };
    if (stripe && stripePaymentElement) return doConfirm();
    return setupStripePayment().then(function() {
      if (!stripeRequiresPayment) return { id: null, status: 'noop' };
      return doConfirm();
    });
  }

  // ════════════════════════════════════════════════════════════
  //  FINAL CONFIRMATION — Oral test is last step
  // ════════════════════════════════════════════════════════════
  function confirmOralTest() {
    if (document.getElementById('oralConfirmBtn').disabled) return;
    document.getElementById('oralConfirmBtn').disabled = true;
    document.getElementById('oralConfirmBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirmation...';

    // Save to localStorage for dashboard
    var dayNames = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    var monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    var oralDate = state.selectedDate
      ? dayNames[state.selectedDate.getDay()] + ' ' + state.selectedDate.getDate() + ' ' + monthNames[state.selectedDate.getMonth()] + ' ' + state.selectedDate.getFullYear()
      : '—';
    var scheduleStr = '';
    var courseNameStr = state.courseData.name;
    if (state.courseData.type === 'solo' && state.soloDate && state.soloSlot) {
      scheduleStr = dayNames[state.soloDate.getDay()] + ' ' + state.soloDate.getDate() + ' ' + monthNames[state.soloDate.getMonth()] + ' à ' + state.soloSlot;
      if (state.soloPackage && state.courseData.packages) {
        var pkgs = state.courseData.packages;
        for (var pi = 0; pi < pkgs.length; pi++) {
          if (pkgs[pi].id === state.soloPackage) { courseNameStr += ' — ' + pkgs[pi].label + ' (' + pkgs[pi].hours + ' h)'; break; }
        }
      }
    } else if (state.courseData.type === 'group') {
      scheduleStr = 'Groupe attribué après évaluation orale';
    } else {
      scheduleStr = (state.days.length ? state.days.join(', ') : 'À déterminer') + ' à ' + (state.timeFrom || '—') + ' – ' + (state.timeTo || '—');
    }
    var correct = state.testAnswers.filter(function(a, i) { return a === testQuestions[i].correct; }).length;
    var level = getLevel(Math.round(correct / testQuestions.length * 100));

    var oralData = {
      courseName: courseNameStr,
      date: oralDate,
      time: state.selectedSlot,
      status: 'Planifié',
      note: 'Lien envoyé par email',
      bookingRef: 'BK-' + Date.now().toString().slice(-6)
    };
    try { localStorage.setItem('cultulangues_oral_test', JSON.stringify(oralData)); } catch(e) {}

    // Confirm the Stripe payment first (when required), then persist to the server.
    // The success overlay + redirect are only shown once the DB write has truly succeeded.
    confirmStripePayment().then(function(paymentResult) {
      return persistBooking(scheduleStr, correct, level, paymentResult.id);
    }).then(function(data) {
      document.getElementById('successDetails').innerHTML =
        '<div style="display:grid;gap:8px">' +
        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-secondary)">Cours</span><span style="font-weight:600">' + courseNameStr + '</span></div>' +
        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-secondary)">Horaire cours</span><span style="font-weight:600">' + scheduleStr + '</span></div>' +
        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-secondary)">Résultat test</span><span style="font-weight:600">' + correct + '/' + testQuestions.length + ' · Niveau ' + level + '</span></div>' +
        '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-secondary)">Test oral</span><span style="font-weight:600">' + oralDate + ' à ' + state.selectedSlot + '</span></div>' +
        '</div>';

      for (var i = 1; i <= 4; i++) {
        var el = document.getElementById('step' + i);
        if (el) el.classList.add('hidden');
      }
      document.getElementById('stepSuccess').classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });

      // Auto-redirect to the student dashboard -> My Courses (real web routes).
      var dest = (data && (data.redirect || data.redirect_url)) || '/student/programs';
      var seconds = 3;
      var progress = document.getElementById('redirectProgress');
      var countdown = document.getElementById('redirectCountdown');
      progress.style.width = '100%';
      var iv = setInterval(function() {
        seconds--;
        if (countdown) countdown.textContent = seconds;
        if (seconds <= 0) {
          clearInterval(iv);
          window.location.href = dest;
        }
      }, 1000);
    }).catch(function(err) {
      var message = (err && err.message) || 'Votre réservation n\'a pas pu être enregistrée. Veuillez réessayer.';
      document.getElementById('oralConfirmBtn').disabled = false;
      document.getElementById('oralConfirmBtn').innerHTML = '<i class="fas fa-check"></i> Payer et confirmer';
      document.getElementById('step4Info').innerHTML =
        '<i class="fas fa-exclamation-triangle" style="color:var(--red)"></i> ' + message;
    });
  }

  // ════════════════════════════════════════════════════════════
  //  TIMEZONE
  // ════════════════════════════════════════════════════════════
  var tz = new Date().getTimezoneOffset();
  var tzHours = -Math.round(tz / 60);
  document.getElementById('tzOffset').textContent = (tzHours >= 0 ? '+' : '') + tzHours;

  // ════════════════════════════════════════════════════════════
  //  BOOT
  // ════════════════════════════════════════════════════════════
  init();
