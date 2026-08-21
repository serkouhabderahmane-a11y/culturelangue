@extends('layouts.portal')
@section('title', 'Tableau de bord enseignant')
@section('sidebar')
<a href="{{ route('teacher.dashboard') }}" class="admin-nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="{{ route('teacher.students') }}" class="admin-nav-item"><i class="fas fa-user-graduate"></i> Étudiants</a>
<a href="{{ route('teacher.schedule') }}" class="admin-nav-item"><i class="fas fa-calendar"></i> Emploi du temps</a>
<a href="{{ route('teacher.profile') }}" class="admin-nav-item"><i class="fas fa-user"></i> Profil</a>
@endsection
@section('content')
<div class="stats-grid">
  <div class="stat-card"><div class="stat-card-body"><h3>{{ $totalStudents }}</h3><p>Étudiants</p></div></div>
  <div class="stat-card"><div class="stat-card-body"><h3>{{ $upcomingSessions->count() }}</h3><p>Sessions à venir</p></div></div>
</div>
<div class="admin-section"><h2>Sessions à venir</h2>
@if($upcomingSessions->count() > 0)
<table class="admin-table"><thead><tr><th>Étudiant</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
@foreach($upcomingSessions as $session)
<tr><td>{{ $session->student?->full_name ?? 'N/A' }}</td><td>{{ $session->start_time->format('d/m/Y H:i') }}</td><td><span class="badge badge-{{ $session->status }}">{{ $session->status }}</span></td>
<td><a href="{{ route('teacher.session.details', $session) }}" class="btn btn-sm">Voir</a></td></tr>
@endforeach</tbody></table>
@else
<p>Aucune session à venir.</p>
@endif
</div>
@endsection
