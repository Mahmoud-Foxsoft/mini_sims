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
                <div class="col-12">
                    <div class="col-lg-12">
                        @include('clinic.partials.services')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
