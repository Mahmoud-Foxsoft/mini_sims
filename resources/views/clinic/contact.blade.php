@extends('clinic.layout')

@section('title', $clinic_contact_title ?? 'Contact')
@section('description', $clinic_contact_description ?? '')
@section('keywords', $clinic_contact_keywords ?? '')
@section('body_class', 'contact-page')

@section('content')
  @php
    $contactCards = json_decode($clinic_contact_info_cards, true) ?? [];
  @endphp

  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">{{ $clinic_contact_page_title ?? '' }}</h1>
            <p class="mb-0">{{ $clinic_contact_page_intro ?? '' }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">{{ $clinic_breadcrumb_home ?? '' }}</a></li>
          <li class="current">{{ $clinic_contact_page_title ?? '' }}</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="contact" class="contact section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row g-5">
        <div class="col-lg-5">
          <div class="contact-info-wrapper">
            @foreach ($contactCards as $index => $card)
              <div class="contact-info-item" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 100) }}">
                <div class="info-icon">
                  <i class="bi bi-{{ $card['icon'] ?? '' }}"></i>
                </div>
                <div class="info-content">
                  <h3>{{ $card['title'] ?? '' }}</h3>
                  @foreach ($card['lines'] ?? [] as $line)
                    <p>{{ $line }}</p>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="col-lg-7">
          <div class="contact-form-card" data-aos="fade-up" data-aos-delay="200">
            <h2>{{ $clinic_contact_form_heading ?? '' }}</h2>
            <p class="mb-4">{{ $clinic_contact_form_intro ?? '' }}</p>

            <form action="{{ $clinic_contact_form_action ?? '#' }}" method="post" class="php-email-form">
              <div class="row g-4">
                <div class="col-md-6">
                  <input type="text" class="form-control" name="name" id="name" placeholder="{{ $clinic_contact_form_name_placeholder ?? '' }}" required="">
                </div>

                <div class="col-md-6">
                  <input type="email" class="form-control" name="email" id="email" placeholder="{{ $clinic_contact_form_email_placeholder ?? '' }}"
                    required="">
                </div>

                <div class="col-12">
                  <input type="text" class="form-control" name="subject" id="subject" placeholder="{{ $clinic_contact_form_subject_placeholder ?? '' }}"
                    required="">
                </div>

                <div class="col-12">
                  <textarea class="form-control" name="message" id="message" placeholder="{{ $clinic_contact_form_message_placeholder ?? '' }}" rows="6"
                    required=""></textarea>
                </div>

                <div class="col-12">
                  <div class="loading">{{ $clinic_contact_form_loading ?? '' }}</div>
                  <div class="error-message"></div>
                  <div class="sent-message">{{ $clinic_contact_form_success ?? '' }}</div>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn btn-submit">{{ $clinic_contact_form_submit ?? '' }}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </section>
@endsection
