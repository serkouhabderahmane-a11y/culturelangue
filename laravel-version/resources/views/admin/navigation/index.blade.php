@extends('layouts.admin')
@section('title', 'Navigation')
@section('content')
<div class="admin-toolbar"><a href="{{ route('admin.navigation.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel élément</a></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Label (FR)</th><th>URL</th><th>Parent</th><th>Ordre</th><th>Actif</th><th>Actions</th></tr></thead><tbody>
@foreach($items as $item)
<tr><td>{{ $item->id }}</td><td><strong>{{ $item->label_fr }}</strong></td><td>{{ $item->url ?? $item->route ?? '-' }}</td><td>-</td><td>{{ $item->order }}</td><td>{{ $item->is_active ? 'Oui' : 'Non' }}</td>
<td class="actions"><a href="{{ route('admin.navigation.edit', $item) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.navigation.destroy', $item) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@foreach($item->children as $child)
<tr><td>{{ $child->id }}</td><td>&nbsp;&nbsp;&nbsp;↳ {{ $child->label_fr }}</td><td>{{ $child->url ?? $child->route ?? '-' }}</td><td>{{ $item->label_fr }}</td><td>{{ $child->order }}</td><td>{{ $child->is_active ? 'Oui' : 'Non' }}</td>
<td class="actions"><a href="{{ route('admin.navigation.edit', $child) }}" class="btn btn-sm btn-outline">Modifier</a>
<form method="POST" action="{{ route('admin.navigation.destroy', $child) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach
@endforeach</tbody></table>
@endsection
