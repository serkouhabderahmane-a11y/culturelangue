@extends('layouts.admin')
@section('title', 'Modifier la réservation')
@section('content')
<form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="admin-form">
@csrf @method('PUT')
<div class="admin-section"><h2>Informations client</h2>
<p><strong>Nom :</strong> {{ $booking->first_name }} {{ $booking->last_name }}</p>
<p><strong>Email :</strong> {{ $booking->email }}</p>
<p><strong>Téléphone :</strong> {{ $booking->phone ?? '-' }}</p></div>
<div class="admin-section"><h2>Détails de la réservation</h2>
<p><strong>Service :</strong> {{ $booking->service->name_fr ?? '-' }}</p>
<p><strong>Date préférée :</strong> {{ $booking->preferred_date ? $booking->preferred_date->format('d/m/Y') : '-' }}</p>
<p><strong>Notes :</strong> {{ $booking->notes ?? '-' }}</p></div>
<div class="form-row"><div class="form-group"><label for="status">Statut</label>
<select name="status" id="status" class="form-control"><option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>En attente</option>
<option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
<option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
<option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Terminée</option></select></div>
<div class="form-group"><label for="payment_status">Statut du paiement</label>
<select name="payment_status" id="payment_status" class="form-control"><option value="unpaid" {{ $booking->payment_status == 'unpaid' ? 'selected' : '' }}>Impayé</option>
<option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Payé</option>
<option value="refunded" {{ $booking->payment_status == 'refunded' ? 'selected' : '' }}>Remboursé</option></select></div></div>
<button type="submit" class="btn btn-primary">Mettre à jour</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-outline">Retour</a>
</form>
@endsection
