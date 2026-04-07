@extends('clinic.layout')

@section('title', $clinic_about_title ?? 'About')
@section('description', $clinic_about_description ?? '')
@section('keywords', $clinic_about_keywords ?? '')
@section('body_class', 'about-page')

@section('content')
  @php
    $aboutStats = json_decode($clinic_about_stats, true) ??  [];
    $aboutValues = json_decode($clinic_about_values, true) ?? [];
    $aboutCerts = json_decode($clinic_about_certs, true) ?? [];
  @endphp

  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">{{ $clinic_about_page_title ?? '' }}</h1>
            <p class="mb-0">{{ $clinic_about_page_intro ?? '' }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">{{ $clinic_breadcrumb_home ?? '' }}</a></li>
          <li class="current">{{ $clinic_about_page_title ?? '' }}</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="about" class="about section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
          <div class="about-content">
            <h2>{{ $clinic_about_heading ?? '' }}</h2>
            <p class="lead">{{ $clinic_about_lead ?? '' }}</p>

            <p>{{ $clinic_about_body ?? '' }}</p>

            <div class="stats-grid">
              @foreach ($aboutStats as $stat)
                <div class="stat-item">
                  <span class="stat-number" data-purecounter-start="0" data-purecounter-end="{{ $stat['value'] ?? 0 }}"
                    data-purecounter-duration="2">{{ $stat['value'] ?? '' }}</span>
                  <span class="stat-label">{{ $stat['label'] ?? '' }}</span>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
          <div class="image-wrapper">
            <img src="{{ $clinic_about_image_main ?? '' }}" class="img-fluid main-image" alt="{{ $clinic_about_image_main_alt ?? '' }}">
            <div class="floating-image" data-aos="zoom-in" data-aos-delay="400">
              <img src="{{ $clinic_about_image_float ?? '' }}" class="img-fluid" alt="{{ $clinic_about_image_float_alt ?? '' }}">
            </div>
          </div>
        </div>
      </div>

      <div class="values-section" data-aos="fade-up" data-aos-delay="300">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h3>{{ $clinic_about_values_heading ?? '' }}</h3>
            <p class="section-description">{{ $clinic_about_values_description ?? '' }}</p>
          </div>
        </div>

        <div class="row">
          @foreach ($aboutValues as $index => $value)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 100) }}">
              <div class="value-item">
                <div class="value-icon">
                  <i class="bi bi-{{ $value['icon'] ?? '' }}"></i>
                </div>
                <h4>{{ $value['title'] ?? '' }}</h4>
                <p>{{ $value['description'] ?? '' }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="certifications-section" data-aos="fade-up" data-aos-delay="400">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h3>{{ $clinic_about_certs_heading ?? '' }}</h3>
            <p class="section-description">{{ $clinic_about_certs_description ?? '' }}</p>
          </div>
        </div>

        <div class="row justify-content-center">
          @foreach ($aboutCerts as $index => $cert)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6" data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 100) }}">
              <div class="certification-item">
                <img src="{{ $cert['image'] ?? '' }}" class="img-fluid" alt="{{ $cert['alt'] ?? '' }}">
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>

  </section>
@endsection
