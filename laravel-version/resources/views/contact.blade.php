@extends('layouts.public')

@section('title', 'Contact')

@section('content')
<section class="page-header">
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <div class="breadcrumb"><a href="{{ url('/') }}">Accueil</a> / <span>Contact</span></div>
      <h1>Contactez-nous</h1>
      <p class="hero-subtitle">Une question ? Un projet ? Nous sommes là pour vous accompagner.</p>
    </div>
  </div>
</section>

<section class="contact-section">
  <div class="container">
    <div class="contact-grid">
      <div class="contact-info">
        <h2>Nos coordonnées</h2>
        @foreach($contactInfos as $info)
        <div class="contact-info-item">
          <div class="contact-info-icon">
            @if($info->type === 'email')
            <i class="fas fa-envelope"></i>
            @elseif($info->type === 'phone')
            <i class="fas fa-phone"></i>
            @elseif($info->type === 'address')
            <i class="fas fa-map-marker-alt"></i>
            @else
            <i class="fas fa-info-circle"></i>
            @endif
          </div>
          <div>
            <h4>{{ $info->label_fr }}</h4>
            <p>{{ $info->value }}</p>
          </div>
        </div>
        @endforeach
      </div>
      <div class="contact-form-wrap">
        <h2>Envoyez-nous un message</h2>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
          @csrf
          <div class="form-group">
            <label for="name">Nom complet</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control @error('email') is-invalid @enderror">
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <div class="form-group">
            <label for="subject">Sujet</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control">
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" rows="5" required class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
            @error('message') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
          <button type="submit" class="btn btn-primary">Envoyer le message</button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
