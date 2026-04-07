<section id="faq" class="faq section">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $faq_header }}</h2>
        <p>{{ $faq_subheader }}</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="faq-container">
                    @foreach (json_decode($faqs, true) ?? [] as $index => $faq)
                        <div class="faq-item" data-aos="fade-up" data-aos-delay="150">
                            <div class="faq-header">
                                <span class="faq-number">{{ $index + 1 }}</span>
                                <h4>{{ $faq['q'] ?? '' }}</h4>
                                <div class="faq-toggle">
                                    <i class="bi bi-plus"></i>
                                    <i class="bi bi-dash"></i>
                                </div>
                            </div>
                            <div class="faq-content">
                                <div class="content-inner">
                                    <p>{{ $faq['a'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div><!-- End FAQ Item -->
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
