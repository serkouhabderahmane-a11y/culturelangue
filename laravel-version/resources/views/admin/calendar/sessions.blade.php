@extends('layouts.admin')

@section('title', 'Sessions — ' . $program->name_fr)

@section('content')
<div class="admin-toolbar">
  <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour</a>
  <span class="text-muted">{{ $sessions->count() }} session(s)</span>
</div>

<div class="admin-card" style="margin-bottom:22px">
  <h3 style="margin:0 0 14px">Ajouter une session</h3>
  <form method="POST" action="{{ route('admin.calendar.sessions.store', $program) }}" class="admin-form">
    @csrf
    <div class="form-row">
      <div class="form-group">
        <label for="session_number">N° de session</label>
        <input type="number" name="session_number" id="session_number" value="{{ $program->sessions()->count() + 1 }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="start_date">Date de début</label>
        <input type="date" name="start_date" id="start_date" class="form-control">
      </div>
      <div class="form-group">
        <label for="end_date">Date de fin</label>
        <input type="date" name="end_date" id="end_date" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Jours de la semaine</label>
        <div class="weekday-checks">
          @foreach([1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam',7=>'Dim'] as $dow => $label)
          <label class="form-check-inline" style="margin-right:10px">
            <input type="checkbox" name="weekdays[]" value="{{ $dow }}"> {{ $label }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="start_time">Heure de début</label>
        <input type="time" name="start_time" id="start_time" class="form-control">
      </div>
      <div class="form-group">
        <label for="end_time">Heure de fin</label>
        <input type="time" name="end_time" id="end_time" class="form-control">
      </div>
      <div class="form-group">
        <label for="start_time_2">Heure début (2ᵉ bloc, optionnel)</label>
        <input type="time" name="start_time_2" id="start_time_2" class="form-control">
      </div>
      <div class="form-group">
        <label for="end_time_2">Heure fin (2ᵉ bloc, optionnel)</label>
        <input type="time" name="end_time_2" id="end_time_2" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="duration_text">Durée (texte)</label>
        <input type="text" name="duration_text" id="duration_text" class="form-control" placeholder="ex : 4 semaines">
      </div>
      <div class="form-group">
        <label for="duration_weeks">Durée (semaines)</label>
        <input type="number" name="duration_weeks" id="duration_weeks" class="form-control">
      </div>
      <div class="form-group">
        <label for="notes">Notes</label>
        <input type="text" name="notes" id="notes" class="form-control">
      </div>
    </div>
    <div class="form-check" style="margin-bottom:14px">
      <label><input type="checkbox" name="is_active" value="1" checked> Actif</label>
    </div>
    <button type="submit" class="btn btn-primary">Créer et générer les cours</button>
  </form>
</div>

@if($sessions->isEmpty())
<p class="text-muted">Aucune session pour ce programme.</p>
@else
<table class="admin-table">
  <thead>
    <tr>
      <th>N°</th>
      <th>Titre</th>
      <th>Période</th>
      <th>Jours</th>
      <th>Heures</th>
      <th>Durée</th>
      <th>Cours</th>
      <th>Actif</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sessions as $session)
    <tr>
      <td>{{ $session->session_number }}</td>
      <td>{{ $session->title }}</td>
      <td>{{ $session->start_date ? $session->start_date->format('d/m/Y') : '-' }} → {{ $session->end_date ? $session->end_date->format('d/m/Y') : '-' }}</td>
      <td>{{ $session->days_text }}</td>
      <td>{{ $session->start_time }}{{ $session->end_time ? '-' . $session->end_time : '' }}</td>
      <td>{{ $session->duration_text }}</td>
      <td>{{ $session->meetings_count }}</td>
      <td>{{ $session->is_active ? 'Oui' : 'Non' }}</td>
      <td class="actions" style="white-space:nowrap">
        <a href="{{ route('admin.calendar.sessions.edit', [$program, $session]) }}" class="btn btn-sm btn-outline">Modifier</a>
        <form method="POST" action="{{ route('admin.calendar.sessions.destroy', [$program, $session]) }}" style="display:inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette session et ses cours ?')">Supprimer</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif
@endsection
