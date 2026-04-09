@extends('clinic.layout')

@section('title', $home_title)
@section('description', $home_description)
@section('keywords', $home_keywords)
@section('body_class', 'index-page')

@section('content')
    <section id="hero" class="hero section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="trust-badges mb-4" data-aos="fade-right" data-aos-delay="200">
                            <div class="badge-item">
                                <i class="bi bi-{{ $home_badge_1_icon }}"></i>
                                <span>{{ $home_badge_1_label }}</span>
                            </div>
                            <div class="badge-item">
                                <i class="bi bi-{{ $home_badge_2_icon }}"></i>
                                <span>{{ $home_badge_2_label }}</span>
                            </div>
                            <div class="badge-item">
                                <i class="bi bi-{{ $home_badge_3_icon }}"></i>
                                <span>{{ $home_badge_3_label }}</span>
                            </div>
                        </div>

                        <h1 data-aos="fade-right" data-aos-delay="300">
                            {!! $home_hero_title !!}
                        </h1>

                        <p class="hero-description" data-aos="fade-right" data-aos-delay="400">
                            {{ $home_hero_description }}
                        </p>

                        <div class="hero-stats mb-4" data-aos="fade-right" data-aos-delay="500">
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0" data-purecounter-end="{{ $home_stat_1_value }}"
                                        data-purecounter-duration="2" class="purecounter"></span>{{ $home_stat_1_suffix }}
                                </h3>
                                <p>{{ $home_stat_1_label }}</p>
                            </div>
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0" data-purecounter-end="{{ $home_stat_2_value }}"
                                        data-purecounter-duration="2" class="purecounter"></span>{{ $home_stat_2_suffix }}
                                </h3>
                                <p>{{ $home_stat_2_label }}</p>
                            </div>
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0" data-purecounter-end="{{ $home_stat_3_value }}"
                                        data-purecounter-duration="2" class="purecounter"></span>{{ $home_stat_3_suffix }}
                                </h3>
                                <p>{{ $home_stat_3_label }}</p>
                            </div>
                        </div>

                        <div class="hero-actions" data-aos="fade-right" data-aos-delay="600">
                            <a href="/dashboard/auth/login" class="btn btn-primary">{{ $home_primary_label }}</a>
                            <a href="{{ route('clinic.docs') }}" class="btn btn-outline">
                                <i class="bi bi-{{ $home_secondary_icon }} me-2"></i>
                                {{ $home_secondary_label }}
                            </a>
                        </div>

                        <div class="emergency-contact" data-aos="fade-right" data-aos-delay="700">
                            <div class="emergency-icon">
                                <i class="bi bi-{{ $home_highlight_icon }}"></i>
                            </div>
                            <div class="emergency-info">
                                <small>{{ $home_highlight_label }}</small>
                                <strong>{{ $home_highlight_value }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual" data-aos="fade-left" data-aos-delay="400">
                        <div class="main-image">
                            <img src="{{ $home_hero_image }}" alt="{{ $home_hero_image_alt }}" class="img-fluid">
                            <div class="floating-card appointment-card">
                                <div class="card-icon">
                                    <i class="bi bi-{{ $home_card_1_icon }}"></i>
                                </div>
                                <div class="card-content">
                                    <h6>{{ $home_card_1_title }}</h6>
                                    <p>{{ $home_card_1_value }}</p>
                                    <small>{{ $home_card_1_note }}</small>
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
                                    <h6>{{ $home_card_2_value }}</h6>
                                    <small>{{ $home_card_2_note }}</small>
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

    <section id="home-about" class="home-about section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right" data-aos-delay="200">
                    <div class="about-content">
                        <h2 class="section-heading">{{ $about_heading }}</h2>
                        <p class="lead-text">{{ $about_lead }}</p>
                        <p>{{ $about_body }}</p>

                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <div class="stat-number purecounter" data-purecounter-start="0"
                                        data-purecounter-end="{{ $about_stat_1_value }}" data-purecounter-duration="1">
                                    </div> <span class="stat-number">k</span>
                                </div>
                                <div class="stat-label">{{ $about_stat_1_label }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <div class="stat-number purecounter" data-purecounter-start="0"
                                        data-purecounter-end="{{ $about_stat_2_value }}" data-purecounter-duration="1">
                                    </div> <span class="stat-number">min</span>
                                </div>

                                <div class="stat-label">{{ $about_stat_2_label }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="d-flex align-items-baseline justify-content-center gap-1">
                                    <div class="stat-number purecounter" data-purecounter-start="0"
                                        data-purecounter-end="{{ $about_stat_3_value }}" data-purecounter-duration="1">
                                    </div> <span class="stat-number">%</span>
                                </div>

                                <div class="stat-label">{{ $about_stat_3_label }}</div>
                            </div>
                        </div>

                        <div class="cta-section">
                            <a href="{{ route('clinic.about') }}" class="btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="300">
                    <div class="about-visual">
                        <div class="main-image">
                            <img src="{{ $home_about_image }}" alt="" class="img-fluid">
                        </div>
                        <div class="floating-card">
                            <div class="card-content">
                                <div class="icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="card-text">
                                    <h4>{{ $home_about_card_title }}</h4>
                                    <p>{{ $home_about_card_text }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="experience-badge">
                            <div class="badge-content">
                                <span class="years">{{ $home_about_badge_value }}</span>
                                <span class="text">{{ $home_about_badge_text }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="services section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $home_services_heading }}</h2>
            <p>{{ $home_services_subheading }}</p>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                {{-- @foreach ($services ?? [] as $index => $service)
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 50) }}">
            <div class="service-item">
              <div class="service-content">
                <h3>{{ $service['name'] ?? '' }}</h3>
                <p>{{ $service_price_label }} {{ $service['price'] ?? '' }}</p>
                <a href="{{ route('clinic.service', $service['slug'] ?? '') }}" class="service-btn">
                  <span>{{ $service_card_cta }}</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach --}}
            </div>
        </div>
    </section>

    @include('clinic.partials.faqs')
@endsection
