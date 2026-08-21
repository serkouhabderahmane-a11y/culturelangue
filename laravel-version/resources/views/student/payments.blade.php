@extends('layouts.portal')
@section('title', 'Mes paiements')
@section('sidebar')
@include('student.sidebar')
@endsection
@section('content')
<div class="admin-section">
@if($payments->count() > 0)
<table class="admin-table"><thead><tr><th>Réservation</th><th>Montant</th><th>Méthode</th><th>Statut</th><th>Date</th></tr></thead><tbody>
@foreach($payments as $payment)
<tr><td>{{ $payment->booking->service->name_fr ?? 'N/A' }}</td><td>{{ number_format($payment->amount, 2) }} $</td><td>{{ $payment->payment_method ?? '-' }}</td><td><span class="badge badge-{{ $payment->status }}">{{ $payment->status }}</span></td><td>{{ $payment->created_at->format('d/m/Y') }}</td></tr>
@endforeach</tbody></table>
@else
<p>Aucun paiement enregistré.</p>
@endif
</div>
@endsection
