
<footer id="footer" class="footer-16 footer position-relative">

    <div class="container">

        <div class="footer-main" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-start">

                <div class="col-lg-5">
                    <div class="brand-section">
                        <a href="{{ route('clinic.home') }}" class="logo d-flex align-items-center mb-4">
                            <span class="sitename">{{ config('app.name') }}</span>
                        </a>
                        <p class="brand-description">{{ $footer_description }}</p>

                        <div class="contact-info mt-5">
                            <div class="contact-item">
                                <i class="bi bi-telephone"></i>
                                <span>{{ $site_phone }}</span>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-envelope"></i>
                                <span>{{ $site_email }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="footer-nav-wrapper">
                        <div class="row">
                            <div class="col-6 col-lg-6">
                                <div class="nav-column">
                                    <h6>Useful
                                        Links</h6>
                                    <nav class="footer-nav"><a href="/">Home</a><a href="/about">About</a><a
                                            href="/services">Services</a></nav>
                                </div>
                            </div>
                            <div class="col-6 col-lg-6">
                                <div class="nav-column">
                                    <h6>Legal
                                    </h6>
                                    <nav class="footer-nav"><a href="/privacy">Privacy</a><a href="/terms">Terms</a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="bottom-content" data-aos="fade-up" data-aos-delay="300">
                <div class="row align-items-center">

                    <div class="col-lg-6">
                        <div class="copyright">
                            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</footer>
