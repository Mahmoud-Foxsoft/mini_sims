@extends('clinic.layout')

@section('title', $about_title)
@section('description', $about_description)
@section('keywords', $about_keywords)
@section('body_class', 'about-page')

@section('content')
    <div class="page-title">
        <div class="heading">
            <div class="container">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1 class="heading-title">About Us</h1>
                        <p class="mb-0">{{ $about_page_intro }}</p>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumbs">
            <div class="container">
                <ol>
                    <li><a href="{{ route('clinic.home') }}">Home</a></li>
                    <li class="current">About Us</li>
                </ol>
            </div>
        </nav>
    </div>

    <section id="about" class="about section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="about-content">
                        <h2>{{ $about_heading }}</h2>
                        <p class="lead">{{ $about_lead }}</p>
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
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="image-wrapper">
                        <img src="{{ $about_image_main }}" class="img-fluid main-image" alt="{{ $about_image_main_alt }}">
                        <div class="floating-image" data-aos="zoom-in" data-aos-delay="400">
                            <img src="{{ $about_image_float }}" class="img-fluid" alt="{{ $about_image_float_alt }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="values-section" data-aos="fade-up" data-aos-delay="300">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h3>{{ $about_values_heading }}</h3>
                        <p class="section-description">{{ $about_values_description }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="bi bi-{{ $about_value_1_icon }}"></i>
                            </div>
                            <h4>{{ $about_value_1_title }}</h4>
                            <p>{{ $about_value_1_description }}</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="bi bi-{{ $about_value_2_icon }}"></i>
                            </div>
                            <h4>{{ $about_value_2_title }}</h4>
                            <p>{{ $about_value_2_description }}</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="bi bi-{{ $about_value_3_icon }}"></i>
                            </div>
                            <h4>{{ $about_value_3_title }}</h4>
                            <p>{{ $about_value_3_description }}</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="bi bi-{{ $about_value_4_icon }}"></i>
                            </div>
                            <h4>{{ $about_value_4_title }}</h4>
                            <p>{{ $about_value_4_description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="certifications-section" data-aos="fade-up" data-aos-delay="400">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h3>{{ $about_certs_heading }}</h3>
                        <p class="section-description">{{ $about_certs_description }}</p>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
