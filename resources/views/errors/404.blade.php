@extends('clinic.layout')

@section('title', 'Page not found')
@section('description', 'page not found')
@section('keywords', '')
@section('body_class', 'page-404')

@section('content')

  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Page not found</h1>
            <p class="mb-0">the page you are looking for was not found</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">Home</a></li>
          <li class="current">Page not found</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="error-404" class="error-404 section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">

          <div class="error-number" data-aos="zoom-in" data-aos-delay="200">
            404
          </div>

          <h1 class="error-title" data-aos="fade-up" data-aos-delay="300">
            Page Not Found
          </h1>

          <p class="error-description" data-aos="fade-up" data-aos-delay="400">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
          </p>

          <div class="error-actions" data-aos="fade-up" data-aos-delay="500">
            <a href="{{ route('clinic.home') }}" class="btn-primary">
              <i class="bi bi-house"></i>
              Back to Home
            </a>
          </div>

        </div>
      </div>

    </div>

  </section>
@endsection
