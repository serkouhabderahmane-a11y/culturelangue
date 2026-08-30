@php
    $programs = $service->calendarPrograms->filter(fn ($p) => $p->sessions->count() > 0);

    $fmtTime = function ($t) {
        if (empty($t)) {
            return '';
        }

        [$h, $m] = array_pad(explode(':', $t), 2, '0');
        $h = (int) $h;

        return $h . ' h' . ((int) $m ? sprintf(' %02d', (int) $m) : '');
    };

    $fmtDate = function ($d, $format = 'j M') {
        return rtrim(\Illuminate\Support\Carbon::parse($d)->translatedFormat($format), '.');
    };
@endphp

@if($programs->count())
<div class="m4-section-card sc-card reveal">
  <span class="section-badge section-badge--blue"><i class="fas fa-calendar-alt"></i> Calendrier</span>
  <h2 class="m4-h2">Calendrier des <span class="text-gradient">prochaines sessions</span></h2>
  <p class="m4-sub">Consultez les dates et horaires des prochaines sessions de {{ $service->name_fr }}. Les places sont limitées à 5 participants.</p>

  @foreach($programs as $program)
    @if($programs->count() > 1)
      <h3 class="sc-program-title">{{ $program->name_fr }}</h3>
    @endif

    @foreach($program->sessions as $session)
      @php
        $isWorkshop = empty($session->days_text) && $session->meetings->count() > 0;
      @endphp

      @if($isWorkshop)
        <div class="sc-session sc-session--workshop">
          <div class="sc-head">
            <h4 class="sc-title">
              {{ $program->name_fr }} — {{ $session->title ?: 'Session ' . $session->session_number }}
              @if($session->duration_text)<span class="sc-dur-pill">{{ $session->duration_text }}</span>@endif
            </h4>
          </div>
          <ul class="sc-workshop-list">
            @foreach($session->meetings->values() as $i => $m)
              <li class="sc-workshop-item">
                <span class="sc-wk-num">Atelier {{ $i + 1 }}</span>
                <span class="sc-wk-date">{{ $fmtDate($m->event_date, 'j F') }}</span>
                <span class="sc-wk-time">
                  <i class="fas fa-clock"></i> {{ $fmtTime($m->start_time) }}&nbsp;–&nbsp;{{ $fmtTime($m->end_time) }}
                </span>
              </li>
            @endforeach
          </ul>
          <div class="sc-actions">
            <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary btn-sm">S'inscrire &rarr;</a>
          </div>
        </div>
      @else
        <div class="sc-session">
          <h4 class="sc-title">
            {{ $program->name_fr }} — {{ $session->title ?: 'Session ' . $session->session_number }}
          </h4>
          <div class="sc-grid">
            <div class="sc-cell">
              <span class="sc-cell-label"><i class="fas fa-calendar-alt"></i> Dates</span>
              <span class="sc-cell-value">{{ $fmtDate($session->start_date) }} → {{ $fmtDate($session->end_date) }}</span>
            </div>
            @if($session->days_text)
            <div class="sc-cell">
              <span class="sc-cell-label"><i class="fas fa-calendar-day"></i> Jours</span>
              <span class="sc-cell-value">{{ $session->days_text }}</span>
            </div>
            @endif
            <div class="sc-cell">
              <span class="sc-cell-label"><i class="fas fa-clock"></i> Horaire</span>
              <span class="sc-cell-value">{{ $fmtTime($session->start_time) }} – {{ $fmtTime($session->end_time) }}
                @if($session->start_time_2 && $session->end_time_2)
                  &amp; {{ $fmtTime($session->start_time_2) }} – {{ $fmtTime($session->end_time_2) }}
                @endif
              </span>
            </div>
            @if($session->duration_text)
            <div class="sc-cell">
              <span class="sc-cell-label"><i class="fas fa-hourglass-half"></i> Durée</span>
              <span class="sc-cell-value">{{ $session->duration_text }}</span>
            </div>
            @endif
          </div>
          <div class="sc-actions">
            <a href="{{ route('booking') }}?service={{ $service->slug }}" class="btn btn-primary btn-sm">S'inscrire &rarr;</a>
          </div>
        </div>
      @endif
    @endforeach
  @endforeach
</div>

@push('head')
<link rel="stylesheet" href="{{ asset('css/service-calendar.css') }}">
@endpush
@endif
