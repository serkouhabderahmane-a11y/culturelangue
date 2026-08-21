@extends('layouts.portal')
@section('title', 'Détails de la session')
@section('sidebar')
@include('teacher.sidebar')
@endsection
@section('content')
<div class="admin-section">
<p><strong>Étudiant :</strong> {{ $session->student?->full_name ?? 'N/A' }}</p>
<p><strong>Date :</strong> {{ $session->start_time->format('d/m/Y H:i') }}</p>
<p><strong>Fin :</strong> {{ $session->end_time?->format('d/m/Y H:i') ?? '-' }}</p>
<p><strong>Statut :</strong> <span class="badge badge-{{ $session->status }}">{{ $session->status }}</span></p>
@if($session->meeting_link)
<p><strong>Lien de la session :</strong> <a href="{{ $session->meeting_link }}" target="_blank">{{ $session->meeting_link }}</a></p>
@endif
<p><strong>Notes :</strong> {{ $session->notes ?? 'Aucune note' }}</p>
<a href="{{ route('teacher.schedule') }}" class="btn btn-outline">Retour à l'emploi du temps</a>
</div>
@endsection
