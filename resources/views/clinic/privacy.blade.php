

@extends('clinic.layout')

@section('title', $privacy_title)
@section('description', $privacy_description)
@section('keywords', $privacy_keywords)
@section('body_class', 'privacy-page')

@section('content')
  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Privacy Policy</h1>
            <p class="mb-0">{{ $privacy_page_intro }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">Home</a></li>
          <li class="current">Privacy Policy</li>
        </ol>
      </div>
    </nav>
  </div>

  <section class="section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      {!! $privacy_content !!}
    </div>
  </section>
@endsection
