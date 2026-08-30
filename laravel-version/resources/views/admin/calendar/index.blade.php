@extends('layouts.admin')

@section('title', 'Calendrier des programmes')

@section('content')
<div class="admin-toolbar" style="justify-content: space-between; flex-wrap: wrap; gap: 10px;">
  <div class="admin-toolbar">
    <a href="{{ route('admin.calendar.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau programme</a>
    <form method="POST" action="{{ route('admin.calendar.import') }}" style="display:inline">
      @csrf
      <button type="submit" class="btn btn-outline" onclick="return confirm('Réimporter tout le calendrier depuis le document source ?\nCela remplacera toutes les données actuelles.')">
        <i class="fas fa-file-import"></i> Réimporter depuis le document
      </button>
    </form>
  </div>
  <span class="text-muted">{{ $programs->count() }} programme(s)</span>
</div>

@if($programs->isEmpty())
<p class="text-muted">Aucun programme pour le moment. Créez le premier programme ou réimportez depuis le document.</p>
@endif

<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Langue</th>
      <th>Service lié</th>
      <th>Sessions</th>
      <th>Cours/Ateliers</th>
      <th>Actif</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($programs as $program)
    <tr>
      <td>{{ $program->id }}</td>
      <td>
        <span class="badge-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $program->color ?? '#2d3a78' }}"></span>
        {{ $program->name_fr }}
        @if($program->category)
          <div class="text-muted" style="font-size:.8rem">{{ $program->category }}</div>
        @endif
      </td>
      <td>{{ $program->language === 'en' ? 'EN' : 'FR' }}</td>
      <td>{{ $program->service->name_fr ?? '-' }}</td>
      <td>{{ $program->sessions_count }}</td>
      <td>{{ $program->meetings_count }}</td>
      <td>{{ $program->is_active ? 'Oui' : 'Non' }}</td>
      <td class="actions" style="white-space:nowrap">
        <a href="{{ route('admin.calendar.sessions', $program) }}" class="btn btn-sm btn-outline">Sessions</a>
        <a href="{{ route('admin.calendar.edit', $program) }}" class="btn btn-sm btn-outline">Modifier</a>
        <form method="POST" action="{{ route('admin.calendar.refresh', $program) }}" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline" title="Régénérer les cours/ateliers à partir des sessions">Régénérer</button>
        </form>
        <form method="POST" action="{{ route('admin.calendar.destroy', $program) }}" style="display:inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce programme et tous ses cours ?')">Supprimer</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
