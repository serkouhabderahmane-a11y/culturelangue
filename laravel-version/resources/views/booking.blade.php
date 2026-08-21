@extends('layouts.public')

@section('title', 'Réservation')

@section('content')
<section class="page-header">
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <div class="breadcrumb"><a href="{{ url('/') }}">Accueil</a> / <span>Réservation</span></div>
      <h1>Réservez votre formation</h1>
      <p class="hero-subtitle">Choisissez le programme qui vous correspond et réservez votre place.</p>
    </div>
  </div>
</section>

<section class="booking-section">
  <div class="container">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('booking.store') }}" class="booking-form">
      @csrf
      <div class="booking-grid">
        <div class="booking-form-fields">
          <h2>Informations personnelles</h2>
          <div class="form-row">
            <div class="form-group">
              <label for="first_name">Prénom *</label>
              <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required class="form-control @error('first_name') is-invalid @enderror">
              @error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
              <label for="last_name">Nom *</label>
              <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required class="form-control @error('last_name') is-invalid @enderror">
              @error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control @error('email') is-invalid @enderror">
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="form-control">
          </div>

          <h2>Choix du programme</h2>
          <div class="form-group">
            <label for="service_id">Programme *</label>
            <select name="service_id" id="service_id" class="form-control">
              <option value="">Sélectionnez un programme</option>
              @foreach($categories as $category)
              <optgroup label="{{ $category->name_fr }}">
                @foreach($category->activeServices as $service)
                <option value="{{ $service->id }}" {{ old('service_id') == $service->id || request('service') == $service->slug ? 'selected' : '' }}>{{ $service->name_fr }}</option>
                @endforeach
              </optgroup>
              @endforeach
            </select>
          </div>

          <h2>Date et heure</h2>
          <div class="form-row">
            <div class="form-group">
              <label for="preferred_date">Date préférée</label>
              <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date') }}" class="form-control">
            </div>
            <div class="form-group">
              <label for="preferred_time">Heure préférée</label>
              <input type="time" name="preferred_time" id="preferred_time" value="{{ old('preferred_time') }}" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label for="notes">Notes supplémentaires</label>
            <textarea name="notes" id="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg">Envoyer ma réservation</button>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection
