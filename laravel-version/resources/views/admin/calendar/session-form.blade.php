@extends('layouts.admin')

@section('title', 'Modifier la session — ' . $program->name_fr)

@section('content')
<div class="admin-toolbar">
  <a href="{{ route('admin.calendar.sessions', $program) }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour aux sessions</a>
</div>

<form method="POST" action="{{ route('admin.calendar.sessions.update', [$program, $session]) }}" class="admin-form">
  @csrf
  @method('PUT')

  <div class="form-row">
    <div class="form-group">
      <label for="session_number">N° de session</label>
      <input type="number" name="session_number" id="session_number" value="{{ old('session_number', $session->session_number) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="start_date">Date de début</label>
      <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $session->start_date?->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="end_date">Date de fin</label>
      <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $session->end_date?->format('Y-m-d')) }}" class="form-control">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label>Jours de la semaine</label>
      <div class="weekday-checks">
        @foreach([1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam',7=>'Dim'] as $dow => $label)
        <label class="form-check-inline" style="margin-right:10px">
          <input type="checkbox" name="weekdays[]" value="{{ $dow }}" {{ in_array($dow, old('weekdays', $weekdays)) ? 'checked' : '' }}> {{ $label }}
        </label>
        @endforeach
      </div>
      <small class="text-muted">Déduit des cours existants. Modifiez si nécessaire.</small>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="start_time">Heure de début</label>
      <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $session->start_time) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="end_time">Heure de fin</label>
      <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $session->end_time) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="start_time_2">Heure début (2ᵉ bloc, optionnel)</label>
      <input type="time" name="start_time_2" id="start_time_2" value="{{ old('start_time_2', $session->start_time_2) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="end_time_2">Heure fin (2ᵉ bloc, optionnel)</label>
      <input type="time" name="end_time_2" id="end_time_2" value="{{ old('end_time_2', $session->end_time_2) }}" class="form-control">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="duration_text">Durée (texte)</label>
      <input type="text" name="duration_text" id="duration_text" value="{{ old('duration_text', $session->duration_text) }}" class="form-control" placeholder="ex : 4 semaines">
    </div>
    <div class="form-group">
      <label for="duration_weeks">Durée (semaines)</label>
      <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks', $session->duration_weeks) }}" class="form-control">
    </div>
    <div class="form-group">
      <label for="notes">Notes</label>
      <input type="text" name="notes" id="notes" value="{{ old('notes', $session->notes) }}" class="form-control">
    </div>
  </div>

  <div class="form-check" style="margin-bottom:14px">
    <label>
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $session->is_active) ? 'checked' : '' }}>
      Actif
    </label>
  </div>

  <button type="submit" class="btn btn-primary">Enregistrer et régénérer les cours</button>
  <a href="{{ route('admin.calendar.sessions', $program) }}" class="btn btn-outline">Annuler</a>
</form>

<hr style="border-color:#e7eaf0;margin:28px 0">

<div class="admin-card">
  <h3 style="margin:0 0 6px">
    @if($isWorkshop)
      Ateliers / dates individuelles
    @else
      Cours / dates individuelles
    @endif
    <span class="text-muted">({{ $meetings->count() }})</span>
  </h3>
  <p class="text-muted" style="margin:0 0 16px">
    @if($isWorkshop)
      Gérez chaque date d'atelier individuellement. Les modifications apparaissent immédiatement sur la page publique du programme.
    @else
      Ajoutez ou modifiez des dates ponctuelles. Utilisez le bouton "Enregistrer et régénérer les cours" ci-dessus pour reconstruire les dates récurrentes.
    @endif
  </p>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Date & heure</th>
        <th>Type</th>
        <th>Actif</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($meetings as $meeting)
      <tr>
        <td>
          <form method="POST" action="{{ route('admin.calendar.meetings.update', [$program, $session, $meeting]) }}" class="admin-form" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            @csrf @method('PUT')
            <input type="date" name="event_date" value="{{ $meeting->event_date->format('Y-m-d') }}" class="form-control" style="width:auto">
            <input type="time" name="start_time" value="{{ $meeting->start_time }}" class="form-control" style="width:auto">
            <span>–</span>
            <input type="time" name="end_time" value="{{ $meeting->end_time }}" class="form-control" style="width:auto">
            <button type="submit" class="btn btn-sm btn-outline">Enregistrer</button>
          </form>
        </td>
        <td>{{ ucfirst($meeting->event_type) }}</td>
        <td>
          <form method="POST" action="{{ route('admin.calendar.meetings.update', [$program, $session, $meeting]) }}" style="display:inline">
            @csrf @method('PUT')
            <input type="hidden" name="event_date" value="{{ $meeting->event_date->format('Y-m-d') }}">
            <input type="hidden" name="start_time" value="{{ $meeting->start_time }}">
            <input type="hidden" name="end_time" value="{{ $meeting->end_time }}">
            <input type="hidden" name="is_active" value="{{ $meeting->is_active ? 0 : 1 }}">
            <button type="submit" class="btn btn-sm {{ $meeting->is_active ? 'btn-outline' : 'btn-danger' }}">
              {{ $meeting->is_active ? 'Actif' : 'Inactif' }}
            </button>
          </form>
        </td>
        <td class="actions" style="white-space:nowrap">
          <form method="POST" action="{{ route('admin.calendar.meetings.destroy', [$program, $session, $meeting]) }}" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette date ?')">Supprimer</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="text-muted">Aucune date individuelle.</td></tr>
      @endforelse
    </tbody>
  </table>

  <h4 style="margin:20px 0 10px">Ajouter une date</h4>
  <form method="POST" action="{{ route('admin.calendar.meetings.store', [$program, $session]) }}" class="admin-form">
    @csrf
    <div class="form-row">
      <div class="form-group">
        <label for="event_date">Date</label>
        <input type="date" name="event_date" id="event_date" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="start_time">Heure de début</label>
        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $session->start_time) }}">
      </div>
      <div class="form-group">
        <label for="end_time">Heure de fin</label>
        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $session->end_time) }}">
      </div>
      <div class="form-group" style="align-self:flex-end">
        <button type="submit" class="btn btn-primary">Ajouter la date</button>
      </div>
    </div>
  </form>
</div>
@endsection
