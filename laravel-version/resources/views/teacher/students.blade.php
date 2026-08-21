@extends('layouts.portal')
@section('title', 'Mes étudiants')
@section('sidebar')
@include('teacher.sidebar')
@endsection
@section('content')
<div class="admin-section">
@if($students->count() > 0)
<table class="admin-table"><thead><tr><th>Nom</th><th>Email</th><th>Actions</th></tr></thead><tbody>
@foreach($students as $student)
<tr><td>{{ $student->full_name }}</td><td>{{ $student->email }}</td>
<td><a href="mailto:{{ $student->email }}" class="btn btn-sm">Contacter</a></td></tr>
@endforeach</tbody></table>
@else
<p>Aucun étudiant pour le moment.</p>
@endif
</div>
@endsection
