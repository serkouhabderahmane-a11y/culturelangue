@extends('layouts.public')

@section('title', $page->title_fr)
@section('meta_description', $page->meta_description_fr ?? '')

@section('content')
<section class="page-header">
  <div class="page-deco page-deco-circle"></div>
  <div class="page-deco page-deco-circle-2"></div>
  <div class="container">
    <div class="hero-content">
      <div class="breadcrumb"><a href="{{ url('/') }}">Accueil</a> / <span>{{ $page->title_fr }}</span></div>
      <h1>{{ $page->title_fr }}</h1>
    </div>
  </div>
</section>

<section class="page-content">
  <div class="container">
    <div class="content-body">
      {!! $page->content_fr !!}
    </div>
  </div>
</section>
@endsection
