@extends('layouts.portal')
@section('title', 'Mon calendrier')
@section('sidebar')
@include('student.sidebar')
@endsection
@section('content')
<div class="admin-section">
@if($sessions->count() > 0)
<table class="admin-table"><thead><tr><th>Date</th><th>Enseignant</th><th>Statut</th></tr></thead><tbody>
@foreach($sessions as $session)
<tr><td>{{ $session->start_time->format('d/m/Y H:i') }}</td><td>{{ $session->teacher?->full_name ?? 'N/A' }}</td><td><span class="badge badge-{{ $session->status }}">{{ $session->status }}</span></td></tr>
@endforeach</tbody></table>
@else
<p>Aucune session à venir.</p>
@endif
</div>
@endsection
