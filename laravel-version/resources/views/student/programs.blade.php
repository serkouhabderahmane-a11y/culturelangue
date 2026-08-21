@extends('layouts.portal')
@section('title', 'Mes programmes')
@section('sidebar')
@include('student.sidebar')
@endsection
@section('content')
<div class="admin-section">
@if($enrollments->count() > 0)
<table class="admin-table"><thead><tr><th>Programme</th><th>Date de réservation</th><th>Statut</th></tr></thead><tbody>
@foreach($enrollments as $booking)
<tr><td>{{ $booking->service->name_fr ?? '-' }}</td><td>{{ $booking->created_at->format('d/m/Y') }}</td><td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td></tr>
@endforeach</tbody></table>
@else
<p>Vous n'êtes inscrit à aucun programme pour le moment.</p>
<a href="{{ route('booking') }}" class="btn btn-primary">Explorer les programmes</a>
@endif
</div>
@endsection
