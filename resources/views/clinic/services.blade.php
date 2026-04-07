@extends('clinic.layout')

@section('title', $clinic_services_title ?? 'Services')
@section('description', $clinic_services_description ?? '')
@section('keywords', $clinic_services_keywords ?? '')
@section('body_class', 'services-page')

@section('content')
  @php
    $services = json_decode($clinic_services_list, true) ?? [];
  @endphp

  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">{{ $clinic_services_page_title ?? '' }}</h1>
            <p class="mb-0">{{ $clinic_services_page_intro ?? '' }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">{{ $clinic_breadcrumb_home ?? '' }}</a></li>
          <li class="current">{{ $clinic_services_page_title ?? '' }}</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="services" class="services section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row gy-4">
        @foreach ($services as $index => $service)
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 50) }}">
            <div class="service-item">
              <div class="service-image">
                <img src="{{ $service['image'] ?? '' }}" alt="{{ $service['image_alt'] ?? '' }}" class="img-fluid">
                <div class="service-overlay">
                  <i class="{{ $service['icon'] ?? '' }}"></i>
                </div>
              </div>
              <div class="service-content">
                <h3>{{ $service['title'] ?? '' }}</h3>
                <p>{{ $service['description'] ?? '' }}</p>
                <div class="service-features">
                  @foreach ($service['features'] ?? [] as $feature)
                    <span class="feature-item"><i class="fas fa-check"></i> {{ $feature }}</span>
                  @endforeach
                </div>
                <a href="{{ $service['url'] ?? '#' }}" class="service-btn">
                  <span>{{ $clinic_services_cta_label ?? '' }}</span>
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
