@extends('layouts.admin')
@section('title', 'Réservation #' . $booking->id)
@section('content')
<div class="admin-section">
<p><strong>Client :</strong> {{ $booking->first_name }} {{ $booking->last_name }}</p>
<p><strong>Email :</strong> {{ $booking->email }}</p>
<p><strong>Téléphone :</strong> {{ $booking->phone ?? '-' }}</p>
<p><strong>Service :</strong> {{ $booking->service->name_fr ?? '-' }}</p>
<p><strong>Statut :</strong> <span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></p>
<p><strong>Paiement :</strong> <span class="badge badge-{{ $booking->payment_status }}">{{ $booking->payment_status }}</span></p>
<p><strong>Date préférée :</strong> {{ $booking->preferred_date ? $booking->preferred_date->format('d/m/Y') : '-' }}</p>
<p><strong>Notes :</strong> {{ $booking->notes ?? '-' }}</p>
<p><strong>Créée le :</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</p>
</div>
<a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary">Modifier</a>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-outline">Retour</a>
@endsection
