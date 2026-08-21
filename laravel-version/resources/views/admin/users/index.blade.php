@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
<div class="admin-toolbar">
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel utilisateur</a>
</div>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Email</th>
      <th>Rôle</th>
      <th>Actif</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($users as $user)
    <tr>
      <td>{{ $user->id }}</td>
      <td>{{ $user->full_name }}</td>
      <td>{{ $user->email }}</td>
      <td>{{ $user->roles->first()?->name ?? '-' }}</td>
      <td>{{ $user->is_active ? 'Oui' : 'Non' }}</td>
      <td class="actions">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline">Modifier</a>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $users->links() }}
@endsection
