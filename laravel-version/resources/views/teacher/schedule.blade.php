@extends('layouts.portal')
@section('title', 'Emploi du temps')
@section('sidebar')
@include('teacher.sidebar')
@endsection
@section('content')
<div class="admin-section">
@if($sessions->count() > 0)
<table class="admin-table"><thead><tr><th>Étudiant</th><th>Début</th><th>Fin</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
@foreach($sessions as $session)
<tr><td>{{ $session->student?->full_name ?? 'N/A' }}</td>
<td>{{ $session->start_time->format('d/m/Y H:i') }}</td>
<td>{{ $session->end_time?->format('d/m/Y H:i') ?? '-' }}</td>
<td><span class="badge badge-{{ $session->status }}">{{ $session->status }}</span></td>
<td><a href="{{ route('teacher.session.details', $session) }}" class="btn btn-sm">Détails</a></td></tr>
@endforeach</tbody></table>
@else
<p>Aucune session programmée.</p>
@endif
</div>
@endsection
