@extends('layouts.admin')
@section('title', 'Témoignages')
@section('content')
<div class="admin-toolbar"><a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau témoignage</a></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Nom</th><th>Note</th><th>Actif</th><th>Actions</th></tr></thead><tbody>
@foreach($testimonials as $t)
<tr><td>{{ $t->id }}</td><td>{{ $t->name_fr }}</td><td>{{ $t->rating }}/5</td><td>{{ $t->is_active ? 'Oui' : 'Non' }}</td>
<td class="actions"><a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $testimonials->links() }}
@endsection
