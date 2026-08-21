@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
<div class="admin-toolbar"><a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle page</a></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Titre</th><th>Slug</th><th>Template</th><th>Actif</th><th>Actions</th></tr></thead><tbody>
@foreach($pages as $p)
<tr><td>{{ $p->id }}</td><td>{{ $p->title_fr }}</td><td>{{ $p->slug }}</td><td>{{ $p->template }}</td><td>{{ $p->is_active ? 'Oui' : 'Non' }}</td>
<td class="actions"><a href="{{ route('admin.pages.edit', $p) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.pages.destroy', $p) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $pages->links() }}
@endsection
