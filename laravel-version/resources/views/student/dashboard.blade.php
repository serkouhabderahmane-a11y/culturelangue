@extends('layouts.portal')
@section('title', 'Tableau de bord étudiant')
@section('sidebar')
<a href="{{ route('student.dashboard') }}" class="admin-nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="{{ route('student.programs') }}" class="admin-nav-item"><i class="fas fa-book"></i> Mes programmes</a>
<a href="{{ route('student.payments') }}" class="admin-nav-item"><i class="fas fa-credit-card"></i> Paiements</a>
<a href="{{ route('student.profile') }}" class="admin-nav-item"><i class="fas fa-user"></i> Profil</a>
<a href="{{ route('student.calendar') }}" class="admin-nav-item"><i class="fas fa-calendar"></i> Calendrier</a>
<a href="{{ route('student.level-tests') }}" class="admin-nav-item"><i class="fas fa-tasks"></i> Tests de niveau</a>
<a href="{{ route('student.support') }}" class="admin-nav-item"><i class="fas fa-headset"></i> Support</a>
@endsection
@section('content')
<div class="stats-grid">
  <div class="stat-card"><div class="stat-card-body"><h3>{{ $user->bookings->count() }}</h3><p>Mes réservations</p></div></div>
  <div class="stat-card"><div class="stat-card-body"><h3>{{ $user->payments->count() }}</h3><p>Paiements</p></div></div>
  <div class="stat-card"><div class="stat-card-body"><h3>{{ $user->bookings->where('status', 'confirmed')->count() }}</h3><p>Programmes actifs</p></div></div>
</div>
<div class="admin-section"><h2>Mes réservations récentes</h2>
@if($user->bookings->count() > 0)
<table class="admin-table"><thead><tr><th>Programme</th><th>Statut</th><th>Date</th></tr></thead><tbody>
@foreach($user->bookings->take(5) as $booking)
<tr><td>{{ $booking->service->name_fr ?? '-' }}</td><td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td><td>{{ $booking->created_at->format('d/m/Y') }}</td></tr>
@endforeach</tbody></table>
@else
<p>Aucune réservation pour le moment.</p>
@endif
</div>
@endsection
