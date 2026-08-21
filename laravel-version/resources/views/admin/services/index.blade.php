@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="admin-toolbar">
  <a href="{{ route('admin.services.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau service</a>
</div>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Catégorie</th>
      <th>Prix</th>
      <th>Ordre</th>
      <th>Actif</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($services as $service)
    <tr>
      <td>{{ $service->id }}</td>
      <td>{{ $service->name_fr }}</td>
      <td>{{ $service->category->name_fr ?? '-' }}</td>
      <td>{{ $service->price }}</td>
      <td>{{ $service->order }}</td>
      <td>{{ $service->is_active ? 'Oui' : 'Non' }}</td>
      <td class="actions">
        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline">Modifier</a>
        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" style="display:inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $services->links() }}
@endsection
