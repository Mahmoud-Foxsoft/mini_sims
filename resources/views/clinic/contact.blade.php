
@extends('clinic.layout')

@section('title', $contact_title)
@section('description', $contact_description)
@section('keywords', $contact_keywords)
@section('body_class', 'contact-page')

@section('content')
  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Contact Us</h1>
            <p class="mb-0">{{ $contact_page_intro }}</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="{{ route('clinic.home') }}">Home</a></li>
          <li class="current">Contact Us</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="contact" class="contact section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row g-5">
        <div class="col-lg-5">
          <div class="contact-info-wrapper">

            <div class="contact-info-item" data-aos="fade-up" data-aos-delay="100">
              <div class="info-icon">
                <i class="bi bi-envelope"></i>
              </div>
              <div class="info-content">
                <h3>Email Address</h3>
                <p>{{ $site_email }}</p>
              </div>
            </div>

            <div class="contact-info-item" data-aos="fade-up" data-aos-delay="200">
              <div class="info-icon">
                <i class="bi bi-telephone"></i>
              </div>
              <div class="info-content">
                <h3>Call Us</h3>
                <p>{{ $site_phone }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="contact-form-card" data-aos="fade-up" data-aos-delay="200">
            <h2>{{ $contact_form_heading }}</h2>
            <p class="mb-4">{{ $contact_form_intro }}</p>

            <form  method="post" class="php-email-form">
              <div class="row g-4">
                <div class="col-md-6">
                  <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" required="">
                </div>

                <div class="col-md-6">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email"
                    required="">
                </div>

                <div class="col-12">
                  <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject"
                    required="">
                </div>

                <div class="col-12">
                  <textarea class="form-control" name="message" id="message" placeholder="Your Message" rows="6"
                    required=""></textarea>
                </div>

                <div class="col-12">
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn btn-submit">Send Message</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </section>

  @include('clinic.partials.faqs')
@endsection
