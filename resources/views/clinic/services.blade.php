
@extends('clinic.layout')

@section('title', $services_title)
@section('description', $services_description)
@section('keywords', $services_keywords)
@section('body_class', 'services-page')

@section('content')
  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Services</h1>
            <p class="mb-0">{{ $services_page_intro }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">Home</a></li>
          <li class="current">Services</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="services" class="services section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">
        @foreach ($services ?? [] as $index => $service)
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 50) }}">
            <div class="service-item">
              <div class="service-content">
                <h3>{{ $service['name'] ?? '' }}</h3>
                <p>{{ $service_price_label }} {{ $service['price'] ?? '' }}</p>
                <a href="{{ $service_card_url }}" class="service-btn">
                  <span>{{ $service_card_cta }}</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endsection
