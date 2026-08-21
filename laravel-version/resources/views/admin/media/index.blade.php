@extends('layouts.admin')
@section('title', 'Média')
@section('content')
<form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="admin-form">
@csrf
<div class="form-group"><label for="file">Télécharger un fichier</label>
<input type="file" name="file" id="file" required class="form-control" accept="image/*,.pdf,.doc,.docx"></div>
<button type="submit" class="btn btn-primary">Télécharger</button>
</form>
<div class="admin-section"><h2>Fichiers</h2>
<table class="admin-table"><thead><tr><th>Nom</th><th>Type</th><th>Taille</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@foreach($media as $m)
<tr><td>{{ $m->file_name }}</td><td>{{ $m->mime_type }}</td><td>{{ round($m->size / 1024) }} KB</td><td>{{ $m->created_at->format('d/m/Y') }}</td>
<td class="actions"><form method="POST" action="{{ route('admin.media.destroy', $m) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $media->links() }}
</div>
@endsection
