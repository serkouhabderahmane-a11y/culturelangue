@extends('layouts.admin')
@section('title', 'FAQ')
@section('content')
<div class="admin-toolbar"><a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle FAQ</a></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Question (FR)</th><th>Actif</th><th>Actions</th></tr></thead><tbody>
@foreach($faqs as $f)
<tr><td>{{ $f->id }}</td><td>{{ Str::limit($f->question_fr, 60) }}</td><td>{{ $f->is_active ? 'Oui' : 'Non' }}</td>
<td class="actions"><a href="{{ route('admin.faqs.edit', $f) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.faqs.destroy', $f) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $faqs->links() }}
@endsection
