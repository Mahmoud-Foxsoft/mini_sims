
@extends('clinic.layout')

@section('title', $terms_title)
@section('description', $terms_description)
@section('keywords', $terms_keywords)
@section('body_class', 'terms-page')

@section('content')
  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Terms and Conditions</h1>
            <p class="mb-0">{{ $terms_page_intro }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">Home</a></li>
          <li class="current">Terms and Conditions</li>
        </ol>
      </div>
    </nav>
  </div>

  <section class="section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      {!! $terms_content !!}
    </div>
  </section>
@endsection
