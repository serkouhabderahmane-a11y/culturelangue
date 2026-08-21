@extends('layouts.admin')
@section('title', 'Réservations')
@section('content')
<table class="admin-table"><thead><tr><th>ID</th><th>Client</th><th>Email</th><th>Statut</th><th>Paiement</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@foreach($bookings as $b)
<tr><td>{{ $b->id }}</td><td>{{ $b->first_name }} {{ $b->last_name }}</td><td>{{ $b->email }}</td>
<td><span class="badge badge-{{ $b->status }}">{{ $b->status }}</span></td>
<td><span class="badge badge-{{ $b->payment_status }}">{{ $b->payment_status }}</span></td>
<td>{{ $b->created_at->format('d/m/Y') }}</td>
<td class="actions"><a href="{{ route('admin.bookings.edit', $b) }}" class="btn btn-sm btn-outline">Voir</a>
<form method="POST" action="{{ route('admin.bookings.destroy', $b) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr>
@endforeach</tbody></table>
{{ $bookings->links() }}
@endsection
