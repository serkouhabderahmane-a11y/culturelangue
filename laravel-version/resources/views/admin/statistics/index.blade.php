@extends('layouts.admin')
@section('title', 'Statistiques')
@section('content')
<div class="admin-toolbar"><a href="{{ route('admin.statistics.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle statistique</a></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Label (FR)</th><th>Valeur</th><th>Ordre</th><th>Actions</th></tr></thead><tbody>
@foreach($statistics as $s)
<tr><td>{{ $s->id }}</td><td>{{ $s->label_fr }}</td><td>{{ $s->value }}{{ $s->suffix_fr }}</td><td>{{ $s->order }}</td>
<td class="actions"><a href="{{ route('admin.statistics.edit', $s) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.statistics.destroy', $s) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $statistics->links() }}
@endsection
