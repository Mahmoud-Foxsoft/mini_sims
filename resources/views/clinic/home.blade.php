@extends('clinic.layout')

@section('title', $clinic_home_title?? 'Home')
@section('description', $clinic_home_description?? '')
@section('keywords', $clinic_home_keywords?? '')
@section('body_class', 'index-page')

@section('content')
  @php
    $heroBadges = json_decode($clinic_home_hero_badges, true) ?? [];
    $heroStats = json_decode($clinic_home_hero_stats, true) ??  [];
    $aboutStats = json_decode($clinic_home_about_stats, true) ?? [];
    $services = json_decode($clinic_services_list, true) ?? [];
  @endphp

  <!-- Hero Section -->
  <section id="hero" class="hero section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-content">
            <div class="trust-badges mb-4" data-aos="fade-right" data-aos-delay="200">
              @foreach ($heroBadges as $badge)
                <div class="badge-item">
                  <i class="bi bi-{{ $badge['icon'] ?? '' }}"></i>
                  <span>{{ $badge['label'] ?? '' }}</span>
                </div>
              @endforeach
            </div>

            <h1 data-aos="fade-right" data-aos-delay="300">
              {!! $clinic_home_hero_title ?? '' !!}
            </h1>

            <p class="hero-description" data-aos="fade-right" data-aos-delay="400">
              {{ $clinic_home_hero_description ?? '' }}
            </p>

            <div class="hero-stats mb-4" data-aos="fade-right" data-aos-delay="500">
              @foreach ($heroStats as $stat)
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="{{ $stat['value'] ?? 0 }}" data-purecounter-duration="2"
                      class="purecounter"></span>{{ $stat['suffix'] ?? '' }}</h3>
                  <p>{{ $stat['label'] ?? '' }}</p>
                </div>
              @endforeach
            </div>

            <div class="hero-actions" data-aos="fade-right" data-aos-delay="600">
              <a href="{{ $clinic_home_hero_primary_url ?? '#' }}" class="btn btn-primary">{{ $clinic_home_hero_primary_label ?? '' }}</a>
              <a href="{{ $clinic_home_hero_secondary_url ?? '#' }}" class="btn btn-outline glightbox">
                <i class="bi bi-play-circle me-2"></i>
                {{ $clinic_home_hero_secondary_label ?? '' }}
              </a>
            </div>

            <div class="emergency-contact" data-aos="fade-right" data-aos-delay="700">
              <div class="emergency-icon">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <div class="emergency-info">
                <small>{{ $clinic_home_emergency_label ?? '' }}</small>
                <strong>{{ $clinic_home_emergency_phone ?? '' }}</strong>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="hero-visual" data-aos="fade-left" data-aos-delay="400">
            <div class="main-image">
              <img src="{{ $clinic_home_hero_image ?? '' }}" alt="{{ $clinic_home_hero_image_alt ?? '' }}" class="img-fluid">
              <div class="floating-card appointment-card">
                <div class="card-icon">
                  <i class="bi bi-calendar-check"></i>
                </div>
                <div class="card-content">
                  <h6>{{ $clinic_home_hero_card_appointment_title ?? '' }}</h6>
                  <p>{{ $clinic_home_hero_card_appointment_time ?? '' }}</p>
                  <small>{{ $clinic_home_hero_card_appointment_doctor ?? '' }}</small>
                </div>
              </div>
              <div class="floating-card rating-card">
                <div class="card-content">
                  <div class="rating-stars">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                  <h6>{{ $clinic_home_hero_card_rating_value ?? '' }}</h6>
                  <small>{{ $clinic_home_hero_card_rating_count ?? '' }}</small>
                </div>
              </div>
            </div>
            <div class="background-elements">
              <div class="element element-1"></div>
              <div class="element element-2"></div>
              <div class="element element-3"></div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </section>

  <!-- Home About Section -->
  <section id="home-about" class="home-about section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right" data-aos-delay="200">
          <div class="about-content">
            <h2 class="section-heading">{{ $clinic_home_about_heading ?? '' }}</h2>
            <p class="lead-text">{{ $clinic_home_about_lead ?? '' }}</p>

            <p>{{ $clinic_home_about_body ?? '' }}</p>

            <div class="stats-grid">
              @foreach ($aboutStats as $stat)
                <div class="stat-item">
                  <div class="stat-number purecounter" data-purecounter-start="0" data-purecounter-end="{{ $stat['value'] ?? 0 }}"
                    data-purecounter-duration="1"></div>
                  <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
                </div>
              @endforeach
            </div>

            <div class="cta-section">
              <a href="{{ $clinic_home_about_cta_url ?? '#' }}" class="btn-primary">{{ $clinic_home_about_cta_label ?? '' }}</a>
            </div>
          </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="300">
          <div class="about-visual">
            <div class="main-image">
              <img src="{{ $clinic_home_about_image ?? '' }}" alt="{{ $clinic_home_about_image_alt ?? '' }}" class="img-fluid">
            </div>
            <div class="floating-card">
              <div class="card-content">
                <div class="icon">
                  <i class="bi bi-{{ $clinic_home_about_floating_icon ?? '' }}"></i>
                </div>
                <div class="card-text">
                  <h4>{{ $clinic_home_about_floating_title ?? '' }}</h4>
                  <p>{{ $clinic_home_about_floating_text ?? '' }}</p>
                </div>
              </div>
            </div>
            <div class="experience-badge">
              <div class="badge-content">
                <span class="years">{{ $clinic_home_about_badge_years ?? '' }}</span>
                <span class="text">{{ $clinic_home_about_badge_text ?? '' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </section>

  <!-- Services Section -->
  <section id="services" class="services section">

    <div class="container section-title" data-aos="fade-up">
      <h2>{{ $clinic_home_services_heading ?? '' }}</h2>
      <p>{{ $clinic_home_services_subheading ?? '' }}</p>
    </div>

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
