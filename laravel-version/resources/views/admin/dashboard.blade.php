@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-icon"><i class="fas fa-user-graduate"></i></div>
    <div class="stat-card-body">
      <h3>{{ $stats['students_count'] }}</h3>
      <p>Étudiants</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
    <div class="stat-card-body">
      <h3>{{ $stats['teachers_count'] }}</h3>
      <p>Enseignants</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
    <div class="stat-card-body">
      <h3>{{ $stats['pending_bookings'] }}</h3>
      <p>Réservations en attente</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><i class="fas fa-calendar-check"></i></div>
    <div class="stat-card-body">
      <h3>{{ $stats['total_bookings'] }}</h3>
      <p>Réservations totales</p>
    </div>
  </div>
</div>

<div class="admin-section">
  <h2>Réservations récentes</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Email</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($recentBookings as $booking)
      <tr>
        <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
        <td>{{ $booking->email }}</td>
        <td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td>
        <td>{{ $booking->created_at->format('d/m/Y') }}</td>
        <td><a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm">Voir</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
