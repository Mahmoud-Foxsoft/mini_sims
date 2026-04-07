<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->truncate();

        // Add default settings
        $settings = [
            // seo
            [
                'key' => 'home_title',
                'value' => 'Home',
                'type' => 'string',
                'title' => 'Home Tab Title',
            ],
            [
                'key' => 'home_des',
                'value' => 'Home Page',
                'type' => 'string',
                'title' => 'Home seo description',
            ],
            [
                'key' => 'home_keys',
                'value' => 'home, welcome',
                'type' => 'string',
                'title' => 'Home seo keys',
            ],
            [
                'key' => 'contact_title',
                'value' => 'Contact Us',
                'type' => 'string',
                'title' => 'Contact Tab Title',
            ],
            [
                'key' => 'contact_des',
                'value' => 'contact Page',
                'type' => 'string',
                'title' => 'Contact seo description',
            ],
            [
                'key' => 'contact_keys',
                'value' => 'contact, welcome',
                'type' => 'string',
                'title' => 'Contact seo keys',
            ],
            [
                'key' => 'privacy_title',
                'value' => 'Privacy Policy',
                'type' => 'string',
                'title' => 'Privacy Tab Title',
            ],
            [
                'key' => 'privacy_des',
                'value' => 'Privacy Page',
                'type' => 'string',
                'title' => 'Privacy seo description',
            ],
            [
                'key' => 'privacy_keys',
                'value' => 'privacy, welcome',
                'type' => 'string',
                'title' => 'privacy seo keys',
            ],
            // home
            [
                'key' => 'home_header',
                'value' => 'Residential Proxies — Always <span class="gold-text" data-text="$0.9 per GB">$0.9 per GB</span>Fast, Private, Limitless',
                'type' => 'html',
                'title' => 'Home Header',
            ],
            [
                'key' => 'home_subheader',
                'value' => 'Get Access to 15M+ Residential IPs Worldwide — No Commitments, No Hidden Fees At FoxProx, we keep it simple: premium residential proxies at just $0.9 per GB, no matter how much you use — 1 GB or 1,000.  Enjoy full control with country, state, and city-level targeting to get exactly the IPs you need. No bulk commitments. No hidden fees. Just fast, reliable proxies with flexible pay-as-you-go billing and 24/7 support.  Fair. Transparent. Always on.',
                'type' => 'string',
                'title' => 'home sub header',
            ],
            // --- Section 1 ---
            [
                'key' => 'hs1_img',
                'value' => '/web_assets/img/media/cs1.png',
                'type' => 'image',
                'title' => 'Home Section 1 Main Image',
            ],
            [
                'key' => 'hs1_img_alt',
                'value' => 'Secure Internet Access',
                'type' => 'string',
                'title' => 'Home Section 1 Main Image Alt',
            ],
            [
                'key' => 'hs1head',
                'value' => 'Why Choose FoxProx',
                'type' => 'string',
                'title' => 'Home Section 1 Heading',
            ],
            [
                'key' => 'hs1sub',
                'value' => 'Fast & Secure Access to the Web',
                'type' => 'string',
                'title' => 'Home Section 1 Subheading',
            ],
            [
                'key' => 'hs1desc',
                'value' => 'With FoxProx, your real IP address vanishes, making it impossible to track your online activity. Our strict zero-log policy ensures your identity remains hidden and your privacy protected—always.',
                'type' => 'string',
                'title' => 'Home Section 1 Description',
            ],
            [
                'key' => 'hs1i1_img',
                'value' => '/web_assets/img/media/css1.png',
                'type' => 'image',
                'title' => 'Home Section 1 Item 1 Image',
            ],
            [
                'key' => 'hs1i1_alt',
                'value' => 'Secure Access',
                'type' => 'string',
                'title' => 'Home Section 1 Item 1 Image Alt',
            ],
            [
                'key' => 'hs1i1head',
                'value' => 'Secure Access',
                'type' => 'string',
                'title' => 'Home Section 1 Item 1 Heading',
            ],
            [
                'key' => 'hs1i1text',
                'value' => 'Connect safely to your favorite content, while keeping your location and sensitive data private and encrypted.',
                'type' => 'string',
                'title' => 'Home Section 1 Item 1 Text',
            ],
            [
                'key' => 'hs1i2_img',
                'value' => '/web_assets/img/media/css2.png',
                'type' => 'image',
                'title' => 'Home Section 1 Item 2 Image',
            ],
            [
                'key' => 'hs1i2_alt',
                'value' => 'Fast Internet',
                'type' => 'string',
                'title' => 'Home Section 1 Item 2 Image Alt',
            ],
            [
                'key' => 'hs1i2head',
                'value' => 'Lightning-Fast Speeds',
                'type' => 'string',
                'title' => 'Home Section 1 Item 2 Heading',
            ],
            [
                'key' => 'hs1i2text',
                'value' => 'Enjoy high-speed browsing and streaming with global servers optimized for performance and privacy.',
                'type' => 'string',
                'title' => 'Home Section 1 Item 2 Text',
            ],

            // --- Section 2 ---
            [
                'key' => 'hs2head',
                'value' => 'Expert-Backed Protection',
                'type' => 'string',
                'title' => 'Home Section 2 Heading',
            ],
            [
                'key' => 'hs2sub',
                'value' => 'Browse Freely with Confidence',
                'type' => 'string',
                'title' => 'Home Section 2 Subheading',
            ],
            [
                'key' => 'hs2desc',
                'value' => 'FoxProx is backed by industry professionals ensuring top-tier security. Our no-tracking guarantee means you can explore the web without compromise.',
                'type' => 'string',
                'title' => 'Home Section 2 Description',
            ],
            [
                'key' => 'hs2i1_img',
                'value' => '/web_assets/img/media/css1.png',
                'type' => 'image',
                'title' => 'Home Section 2 Item 1 Image',
            ],
            [
                'key' => 'hs2i1_alt',
                'value' => 'Verified Experts',
                'type' => 'string',
                'title' => 'Home Section 2 Item 1 Image Alt',
            ],
            [
                'key' => 'hs2i1head',
                'value' => 'Trusted by Experts',
                'type' => 'string',
                'title' => 'Home Section 2 Item 1 Heading',
            ],
            [
                'key' => 'hs2i1text',
                'value' => 'Our technology is built and maintained by cybersecurity professionals for maximum protection and reliability.',
                'type' => 'string',
                'title' => 'Home Section 2 Item 1 Text',
            ],
            [
                'key' => 'hs2i2_img',
                'value' => '/web_assets/img/media/css2.png',
                'type' => 'image',
                'title' => 'Home Section 2 Item 2 Image',
            ],
            [
                'key' => 'hs2i2_alt',
                'value' => 'Browse World-Wide',
                'type' => 'string',
                'title' => 'Home Section 2 Item 2 Image Alt',
            ],
            [
                'key' => 'hs2i2head',
                'value' => 'Worldwide Coverage',
                'type' => 'string',
                'title' => 'Home Section 2 Item 2 Heading',
            ],
            [
                'key' => 'hs2i2text',
                'value' => 'Access content from any corner of the globe with FoxProx’s wide network of secure proxy servers.',
                'type' => 'string',
                'title' => 'Home Section 2 Item 2 Text',
            ],
            [
                'key' => 'hs2_img',
                'value' => '/web_assets/img/media/cs2.png',
                'type' => 'image',
                'title' => 'Home Section 2 Main Image',
            ],
            [
                'key' => 'hs2_img_alt',
                'value' => 'Global Coverage',
                'type' => 'string',
                'title' => 'Home Section 2 Main Image Alt',
            ],

            // --- Section 3 ---
            [
                'key' => 'hs3_img',
                'value' => '/web_assets/img/media/cs3.png',
                'type' => 'image',
                'title' => 'Home Section 3 Main Image',
            ],
            [
                'key' => 'hs3img_alt',
                'value' => 'Privacy Protection',
                'type' => 'string',
                'title' => 'Home Section 3 Main Image Alt',
            ],
            [
                'key' => 'hs3head',
                'value' => 'Advanced Privacy Tools',
                'type' => 'string',
                'title' => 'Home Section 3 Heading',
            ],
            [
                'key' => 'hs3sub',
                'value' => 'Protect Your Personal Data',
                'type' => 'string',
                'title' => 'Home Section 3 Subheading',
            ],
            [
                'key' => 'hs3desc',
                'value' => 'FoxProx blocks trackers, hides your IP, and prevents unauthorized data collection. We never log your browsing habits—ever.',
                'type' => 'string',
                'title' => 'Home Section 3 Description',
            ],
            [
                'key' => 'hs3i1_img',
                'value' => '/web_assets/img/media/css1.png',
                'type' => 'image',
                'title' => 'Home Section 3 Item 1 Image',
            ],
            [
                'key' => 'hs3i1_alt',
                'value' => 'Hidden Privacy',
                'type' => 'string',
                'title' => 'Home Section 3 Item 1 Image Alt',
            ],
            [
                'key' => 'hs3i1head',
                'value' => 'Anonymous Browsing',
                'type' => 'string',
                'title' => 'Home Section 3 Item 1 Heading',
            ],
            [
                'key' => 'hs3i1text',
                'value' => 'Stay invisible online and stop companies, governments, and hackers from monitoring your digital activity.',
                'type' => 'string',
                'title' => 'Home Section 3 Item 1 Text',
            ],
            [
                'key' => 'hs3i2_img',
                'value' => '/web_assets/img/media/css2.png',
                'type' => 'image',
                'title' => 'Home Section 3 Item 2 Image',
            ],
            [
                'key' => 'hs3i2_alt',
                'value' => 'Save Browsing History',
                'type' => 'string',
                'title' => 'Home Section 3 Item 2 Image Alt',
            ],
            [
                'key' => 'hs3i2head',
                'value' => 'No History Tracking',
                'type' => 'string',
                'title' => 'Home Section 3 Item 2 Heading',
            ],
            [
                'key' => 'hs3i2text',
                'value' => 'We don’t monitor or store your browsing history. With FoxProx, your past stays in the past.',
                'type' => 'string',
                'title' => 'Home Section 3 Item 2 Text',
            ],
            // price section
            [
                'key' => 'price_header',
                'value' => 'Our Pricing Plan',
                'type' => 'string',
                'title' => 'Price Header',
            ],
            [
                'key' => 'price_subheader',
                'value' => 'It takes more than a private internet browser to go incognito. We’ll make
                            your real IP address disappear so that your.',
                'type' => 'string',
                'title' => 'Price Sub Header',
            ],
            [
                'key' => 'price_bottom',
                'value' => 'Get started risk-free with our 30-Day All Product Money-Back Guarantee.
                            We are accept to all payment getway.',
                'type' => 'string',
                'title' => 'Price Bottom',
            ],

            // --- Separator Image ---
            [
                'key' => 'hs_seperate_img',
                'value' => '/web_assets/img/media/service-seperator.png',
                'type' => 'image',
                'title' => 'Home Separator Image',
            ],
            // faq
            [
                'key' => 'faq_header',
                'value' => 'Questions? look here!',
                'type' => 'string',
                'title' => 'FAQ header',
            ],
            [
                'key' => 'faq_subheader',
                'value' => 'Here are the most common questions from our users.',
                'type' => 'string',
                'title' => 'FAQ subheader',
            ],
            [
                'key' => 'faqs',
                'value' => json_encode([
                    [
                        'q' => 'What is FoxProx and how does it work?',
                        'a' => 'FoxProx is a secure proxy service that hides your IP address, encrypts your traffic, and lets you browse the internet privately. It routes your connection through our servers, ensuring your data and identity remain safe.',
                    ],
                    [
                        'q' => 'Does FoxProx log my browsing activity?',
                        'a' => 'No. FoxProx follows a strict zero-log policy. We never monitor, track, or store your browsing history or online activity, so your privacy is always protected.',
                    ],
                    [
                        'q' => 'Can I access geo-restricted websites with FoxProx?',
                        'a' => 'Yes. By using FoxProx, you can bypass geo-restrictions and access content from anywhere in the world while keeping your location and identity hidden.',
                    ],
                    [
                        'q' => 'How is a proxy different from a VPN?',
                        'a' => 'A proxy routes your internet traffic through a server to hide your IP address. A VPN does this too, but also adds stronger encryption and system-wide protection. FoxProx is optimized for speed and anonymity.',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Frequently Asked Questions',
            ],
            // cta
            [
                'key' => 'cta_header',
                'value' => 'Your privacy matters. Secure it with FoxProx.',
                'type' => 'string',
                'title' => 'Call to Action header',
            ],
            // contact
            [
                'key' => 'contact_header',
                'value' => 'Let\'s talk',
                'type' => 'string',
                'title' => 'Contact Section Title',
            ],
            [
                'key' => 'contact_subheader',
                'value' => 'Your real IP address disappears so your online activity can’t be tracked. Our strict zero-log policy keeps your identity safe.',
                'type' => 'string',
                'title' => 'Contact Section Subtitle',
            ],
            [
                'key' => 'site_phone',
                'value' => '+1 (203) 302 - 9545',
                'type' => 'string',
                'title' => 'Contact Phone',
            ],
            [
                'key' => 'site_email',
                'value' => 'contactus@foxprox.com',
                'type' => 'string',
                'title' => 'Contact Email',
            ],
            [
                'key' => 'contact_img',
                'value' => '/web_assets/img/media/contact-img.png',
                'type' => 'image',
                'title' => 'Contact Image',
            ],
            [
                'key' => 'site_social',
                'value' => json_encode([
                    ['icon' => 'facebook', 'url' => 'https://www.facebook.com/'],
                    ['icon' => 'twitter', 'url' => 'https://twitter.com/'],
                    ['icon' => 'instagram', 'url' => 'https://www.instagram.com/'],
                ]),
                'type' => 'social_array',
                'title' => 'Contact Social Links',
            ],
            [
                'key' => 'footer_social_header',
                'value' => 'Stay Update',
                'type' => 'string',
                'title' => 'Footer Social Header',
            ],
            [
                'key' => 'footer_social_subheader',
                'value' => 'Reimagine interaction between we
                            guest for the first time.',
                'type' => 'string',
                'title' => 'Footer Social Subheader',
            ],
            // privacy
            [
                'key' => 'privacy_policy',
                'value' => '<h1>Privacy Policy</h1>
<p>Last updated: '.date('F j, Y').'</p>

<h2>1. Introduction</h2>
<p>At FoxProx, we respect your privacy and are committed to protecting your personal information. This Privacy Policy explains what data we collect, why we collect it, how we use it, and the choices you have regarding your information.</p>

<h2>2. Information We Collect</h2>
<ul>
    <li><strong>Usage Data:</strong> Non-personal data about how you use our services (pages visited, timestamps, etc.).</li>
    <li><strong>Account Data:</strong> If you create an account, we collect information such as email address and username.</li>
    <li><strong>Payment Data:</strong> When you purchase a plan, payment processors collect billing details; we do not store raw card numbers.</li>
</ul>

<h2>3. How We Use Your Data</h2>
<p>We use the information to provide and improve our services, process payments, detect abuse, and communicate important updates. We do <strong>not</strong> sell your personal data to third parties.</p>

<h2>4. No-Logging Policy</h2>
<p>FoxProx maintains a strict <strong>no-logs</strong> policy. We do not store your browsing history, connection destinations, or DNS queries that can be used to reconstruct your activity.</p>

<h2>5. Security</h2>
<p>We implement industry-standard security measures to protect your data. However, no service can guarantee 100% security—please take reasonable precautions when sharing sensitive information.</p>

<h2>6. Third-Party Services</h2>
<p>We may use trusted third-party services (e.g., payment processors, analytics). These providers are bound by their own privacy policies; please review them for details.</p>

<h2>7. Your Rights</h2>
<ul>
    <li><strong>Access:</strong> You can request the personal information we hold about you.</li>
    <li><strong>Correction:</strong> You may request updates to incorrect or incomplete information.</li>
    <li><strong>Deletion:</strong> You may request deletion of your account and associated personal data, subject to legal requirements.</li>
</ul>

<h2>8. Changes to This Policy</h2>
<p>We may update this policy occasionally. Material changes will be posted here with an updated “Last updated” date.</p>

<h2>9. Contact</h2>
<p>If you have questions about this Privacy Policy, contact us at <a href="mailto:contactus@foxprox.com">contactus@foxprox.com</a>.</p>',
                'type' => 'html',
                'title' => 'Privacy Policy (HTML)',
            ],
            // logo
            [
                'key' => 'logo',
                'value' => '/web_assets/img/logo.png',
                'type' => 'image',
                'title' => 'Logo',
            ],
            [
                'key' => 'logo_sticky',
                'value' => '/web_assets/img/sticky-logo.png',
                'type' => 'image',
                'title' => 'Logo sticky',
            ],
            // 404
            [
                'key' => 'title_404',
                'value' => '404 - FoxProx',
                'type' => 'string',
                'title' => '404 tab title',
            ],
            [
                'key' => 'image_404',
                'value' => '/web_assets/img/media/error-img.png',
                'type' => 'image',
                'title' => '404 image',
            ],
            [
                'key' => 'header_404',
                'value' => 'uh-oh page not found...',
                'type' => 'string',
                'title' => '404 page header',
            ],
            [
                'key' => 'subheader_404',
                'value' => 'Oops! The page you are looking for does not exist. Please return to the site’s homepage.',
                'type' => 'string',
                'title' => '404 page subheader',
            ],
            // clinic website settings
            [
                'key' => 'clinic_site_name',
                'value' => 'Clinic',
                'type' => 'string',
                'title' => 'Clinic Site Name',
            ],
            [
                'key' => 'clinic_logo',
                'value' => '/web_assets/img/logo.webp',
                'type' => 'image',
                'title' => 'Clinic Logo',
            ],
            [
                'key' => 'clinic_logo_alt',
                'value' => 'Clinic Logo',
                'type' => 'string',
                'title' => 'Clinic Logo Alt',
            ],
            [
                'key' => 'clinic_topbar_email',
                'value' => 'contact@example.com',
                'type' => 'string',
                'title' => 'Clinic Topbar Email',
            ],
            [
                'key' => 'clinic_topbar_phone',
                'value' => '+1 5589 55488 55',
                'type' => 'string',
                'title' => 'Clinic Topbar Phone',
            ],
            [
                'key' => 'clinic_topbar_social',
                'value' => json_encode([
                    ['icon' => 'twitter-x', 'url' => 'https://twitter.com/'],
                    ['icon' => 'facebook', 'url' => 'https://facebook.com/'],
                    ['icon' => 'instagram', 'url' => 'https://instagram.com/'],
                    ['icon' => 'linkedin', 'url' => 'https://linkedin.com/'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Topbar Social Links',
            ],
            [
                'key' => 'clinic_nav_links',
                'value' => json_encode([
                    ['label' => 'Home', 'route' => 'clinic.home'],
                    ['label' => 'About', 'route' => 'clinic.about'],
                    ['label' => 'Services', 'route' => 'clinic.services'],
                    ['label' => 'Contact', 'route' => 'clinic.contact'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Navigation Links',
            ],
            [
                'key' => 'clinic_breadcrumb_home',
                'value' => 'Home',
                'type' => 'string',
                'title' => 'Clinic Breadcrumb Home Label',
            ],
            [
                'key' => 'clinic_footer_description',
                'value' => 'Crafting exceptional digital experiences through thoughtful design and innovative solutions that elevate your brand presence.',
                'type' => 'string',
                'title' => 'Clinic Footer Description',
            ],
            [
                'key' => 'clinic_footer_address',
                'value' => '123 Creative Boulevard, Design District, NY 10012',
                'type' => 'string',
                'title' => 'Clinic Footer Address',
            ],
            [
                'key' => 'clinic_footer_phone',
                'value' => '+1 (555) 987-6543',
                'type' => 'string',
                'title' => 'Clinic Footer Phone',
            ],
            [
                'key' => 'clinic_footer_email',
                'value' => 'hello@designstudio.com',
                'type' => 'string',
                'title' => 'Clinic Footer Email',
            ],
            [
                'key' => 'clinic_footer_columns',
                'value' => json_encode([
                    [
                        'title' => 'Studio',
                        'links' => [
                            ['label' => 'Our Story', 'url' => '#'],
                            ['label' => 'Design Process', 'url' => '#'],
                            ['label' => 'Portfolio', 'url' => '#'],
                            ['label' => 'Case Studies', 'url' => '#'],
                            ['label' => 'Awards', 'url' => '#'],
                        ],
                    ],
                    [
                        'title' => 'Services',
                        'links' => [
                            ['label' => 'Brand Identity', 'url' => '#'],
                            ['label' => 'Web Design', 'url' => '#'],
                            ['label' => 'Mobile Apps', 'url' => '#'],
                            ['label' => 'Digital Strategy', 'url' => '#'],
                            ['label' => 'Consultation', 'url' => '#'],
                        ],
                    ],
                    [
                        'title' => 'Resources',
                        'links' => [
                            ['label' => 'Design Blog', 'url' => '#'],
                            ['label' => 'Style Guide', 'url' => '#'],
                            ['label' => 'Free Assets', 'url' => '#'],
                            ['label' => 'Tutorials', 'url' => '#'],
                            ['label' => 'Inspiration', 'url' => '#'],
                        ],
                    ],
                    [
                        'title' => 'Connect',
                        'links' => [
                            ['label' => 'Start Project', 'url' => '#'],
                            ['label' => 'Schedule Call', 'url' => '#'],
                            ['label' => 'Join Newsletter', 'url' => '#'],
                            ['label' => 'Follow Updates', 'url' => '#'],
                            ['label' => 'Partnership', 'url' => '#'],
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Footer Columns',
            ],
            [
                'key' => 'clinic_footer_copyright',
                'value' => '© Clinic. All rights reserved.',
                'type' => 'string',
                'title' => 'Clinic Footer Copyright',
            ],
            [
                'key' => 'clinic_footer_credits_html',
                'value' => '<a href="#">Privacy Policy</a>
<a href="#">Terms of Service</a>
<a href="#">Cookie Policy</a>
<div class="credits">
  Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>. Distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
</div>',
                'type' => 'html',
                'title' => 'Clinic Footer Credits HTML',
            ],
            [
                'key' => 'clinic_home_title',
                'value' => 'Home - Clinic',
                'type' => 'string',
                'title' => 'Clinic Home Title',
            ],
            [
                'key' => 'clinic_home_description',
                'value' => 'Clinic home page',
                'type' => 'string',
                'title' => 'Clinic Home Description',
            ],
            [
                'key' => 'clinic_home_keywords',
                'value' => 'clinic, healthcare, medical',
                'type' => 'string',
                'title' => 'Clinic Home Keywords',
            ],
            [
                'key' => 'clinic_home_hero_title',
                'value' => 'Excellence in <span class="highlight">Healthcare</span> With Compassionate Care',
                'type' => 'html',
                'title' => 'Clinic Home Hero Title',
            ],
            [
                'key' => 'clinic_home_hero_description',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'type' => 'string',
                'title' => 'Clinic Home Hero Description',
            ],
            [
                'key' => 'clinic_home_hero_badges',
                'value' => json_encode([
                    ['icon' => 'shield-check', 'label' => 'Accredited'],
                    ['icon' => 'clock', 'label' => '24/7 Emergency'],
                    ['icon' => 'star-fill', 'label' => '4.9/5 Rating'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Home Hero Badges',
            ],
            [
                'key' => 'clinic_home_hero_stats',
                'value' => json_encode([
                    ['value' => 15, 'suffix' => '+', 'label' => 'Years Experience'],
                    ['value' => 5000, 'suffix' => '+', 'label' => 'Patients Treated'],
                    ['value' => 50, 'suffix' => '+', 'label' => 'Medical Experts'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Home Hero Stats',
            ],
            [
                'key' => 'clinic_home_hero_primary_label',
                'value' => 'Book Appointment',
                'type' => 'string',
                'title' => 'Clinic Home Hero Primary Label',
            ],
            [
                'key' => 'clinic_home_hero_primary_url',
                'value' => '/contact',
                'type' => 'string',
                'title' => 'Clinic Home Hero Primary URL',
            ],
            [
                'key' => 'clinic_home_hero_secondary_label',
                'value' => 'Watch Our Story',
                'type' => 'string',
                'title' => 'Clinic Home Hero Secondary Label',
            ],
            [
                'key' => 'clinic_home_hero_secondary_url',
                'value' => 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
                'type' => 'string',
                'title' => 'Clinic Home Hero Secondary URL',
            ],
            [
                'key' => 'clinic_home_emergency_label',
                'value' => 'Emergency Hotline',
                'type' => 'string',
                'title' => 'Clinic Home Emergency Label',
            ],
            [
                'key' => 'clinic_home_emergency_phone',
                'value' => '+1 (555) 911-2468',
                'type' => 'string',
                'title' => 'Clinic Home Emergency Phone',
            ],
            [
                'key' => 'clinic_home_hero_image',
                'value' => '/web_assets/img/health/staff-10.webp',
                'type' => 'image',
                'title' => 'Clinic Home Hero Image',
            ],
            [
                'key' => 'clinic_home_hero_image_alt',
                'value' => 'Modern Healthcare Facility',
                'type' => 'string',
                'title' => 'Clinic Home Hero Image Alt',
            ],
            [
                'key' => 'clinic_home_hero_card_appointment_title',
                'value' => 'Next Available',
                'type' => 'string',
                'title' => 'Clinic Home Appointment Card Title',
            ],
            [
                'key' => 'clinic_home_hero_card_appointment_time',
                'value' => 'Today 2:30 PM',
                'type' => 'string',
                'title' => 'Clinic Home Appointment Card Time',
            ],
            [
                'key' => 'clinic_home_hero_card_appointment_doctor',
                'value' => 'Dr. Sarah Johnson',
                'type' => 'string',
                'title' => 'Clinic Home Appointment Card Doctor',
            ],
            [
                'key' => 'clinic_home_hero_card_rating_value',
                'value' => '4.9/5',
                'type' => 'string',
                'title' => 'Clinic Home Rating Card Value',
            ],
            [
                'key' => 'clinic_home_hero_card_rating_count',
                'value' => '1,234 Reviews',
                'type' => 'string',
                'title' => 'Clinic Home Rating Card Count',
            ],
            [
                'key' => 'clinic_home_about_heading',
                'value' => 'Compassionate Care, Advanced Medicine',
                'type' => 'string',
                'title' => 'Clinic Home About Heading',
            ],
            [
                'key' => 'clinic_home_about_lead',
                'value' => 'For over two decades, we have been dedicated to providing exceptional healthcare that combines cutting-edge medical technology with the personal touch our patients deserve.',
                'type' => 'string',
                'title' => 'Clinic Home About Lead',
            ],
            [
                'key' => 'clinic_home_about_body',
                'value' => 'Our multidisciplinary team of specialists works collaboratively to ensure every patient receives comprehensive care tailored to their unique needs. From preventive services to complex procedures, we maintain the highest standards of medical excellence while fostering an environment of trust and healing.',
                'type' => 'string',
                'title' => 'Clinic Home About Body',
            ],
            [
                'key' => 'clinic_home_about_stats',
                'value' => json_encode([
                    ['value' => 15000, 'label' => 'Patients Served'],
                    ['value' => 25, 'label' => 'Years of Excellence'],
                    ['value' => 50, 'label' => 'Medical Specialists'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Home About Stats',
            ],
            [
                'key' => 'clinic_home_about_cta_label',
                'value' => 'Learn More About Us',
                'type' => 'string',
                'title' => 'Clinic Home About CTA Label',
            ],
            [
                'key' => 'clinic_home_about_cta_url',
                'value' => '/about',
                'type' => 'string',
                'title' => 'Clinic Home About CTA URL',
            ],
            [
                'key' => 'clinic_home_about_image',
                'value' => '/web_assets/img/health/facilities-9.webp',
                'type' => 'image',
                'title' => 'Clinic Home About Image',
            ],
            [
                'key' => 'clinic_home_about_image_alt',
                'value' => 'Modern medical facility',
                'type' => 'string',
                'title' => 'Clinic Home About Image Alt',
            ],
            [
                'key' => 'clinic_home_about_floating_icon',
                'value' => 'heart-pulse',
                'type' => 'string',
                'title' => 'Clinic Home About Floating Icon',
            ],
            [
                'key' => 'clinic_home_about_floating_title',
                'value' => '24/7 Emergency Care',
                'type' => 'string',
                'title' => 'Clinic Home About Floating Title',
            ],
            [
                'key' => 'clinic_home_about_floating_text',
                'value' => 'Always here when you need us most',
                'type' => 'string',
                'title' => 'Clinic Home About Floating Text',
            ],
            [
                'key' => 'clinic_home_about_badge_years',
                'value' => '25+',
                'type' => 'string',
                'title' => 'Clinic Home About Badge Years',
            ],
            [
                'key' => 'clinic_home_about_badge_text',
                'value' => 'Years of Trusted Care',
                'type' => 'string',
                'title' => 'Clinic Home About Badge Text',
            ],
            [
                'key' => 'clinic_home_services_heading',
                'value' => 'Our Services',
                'type' => 'string',
                'title' => 'Clinic Home Services Heading',
            ],
            [
                'key' => 'clinic_home_services_subheading',
                'value' => 'Comprehensive medical services tailored to your needs.',
                'type' => 'string',
                'title' => 'Clinic Home Services Subheading',
            ],
            [
                'key' => 'clinic_services_cta_label',
                'value' => 'Learn More',
                'type' => 'string',
                'title' => 'Clinic Services CTA Label',
            ],
            [
                'key' => 'clinic_services_list',
                'value' => json_encode([
                    [
                        'title' => 'Cardiology',
                        'description' => 'Comprehensive heart care with advanced diagnostic tools and treatment options for cardiovascular conditions.',
                        'image' => '/web_assets/img/health/cardiology-2.webp',
                        'image_alt' => 'Cardiology Services',
                        'icon' => 'fas fa-heartbeat',
                        'features' => ['ECG Testing', 'Heart Surgery'],
                        'url' => '#',
                    ],
                    [
                        'title' => 'Neurology',
                        'description' => 'Expert neurological care for brain and nervous system disorders with state-of-the-art imaging technology.',
                        'image' => '/web_assets/img/health/neurology-3.webp',
                        'image_alt' => 'Neurology Services',
                        'icon' => 'fas fa-brain',
                        'features' => ['MRI Scans', 'Stroke Care'],
                        'url' => '#',
                    ],
                    [
                        'title' => 'Orthopedics',
                        'description' => 'Specialized bone and joint treatment including sports medicine and reconstructive surgery procedures.',
                        'image' => '/web_assets/img/health/orthopedics-1.webp',
                        'image_alt' => 'Orthopedics Services',
                        'icon' => 'fas fa-bone',
                        'features' => ['Joint Replacement', 'Sports Medicine'],
                        'url' => '#',
                    ],
                    [
                        'title' => 'Pediatrics',
                        'description' => 'Dedicated healthcare for children from infancy through adolescence with specialized treatment protocols.',
                        'image' => '/web_assets/img/health/pediatrics-4.webp',
                        'image_alt' => 'Pediatrics Services',
                        'icon' => 'fas fa-child',
                        'features' => ['Well-Child Visits', 'Immunizations'],
                        'url' => '#',
                    ],
                    [
                        'title' => 'Emergency Care',
                        'description' => '24/7 emergency medical services with rapid response teams and critical care capabilities.',
                        'image' => '/web_assets/img/health/emergency-2.webp',
                        'image_alt' => 'Emergency Services',
                        'icon' => 'fas fa-ambulance',
                        'features' => ['Trauma Center', 'Critical Care'],
                        'url' => '#',
                    ],
                    [
                        'title' => 'Laboratory Testing',
                        'description' => 'Advanced diagnostic laboratory services with comprehensive testing panels and rapid result delivery.',
                        'image' => '/web_assets/img/health/laboratory-3.webp',
                        'image_alt' => 'Laboratory Services',
                        'icon' => 'fas fa-microscope',
                        'features' => ['Blood Tests', 'Pathology'],
                        'url' => '#',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Services List',
            ],
            [
                'key' => 'clinic_about_title',
                'value' => 'About - Clinic',
                'type' => 'string',
                'title' => 'Clinic About Title',
            ],
            [
                'key' => 'clinic_about_description',
                'value' => 'About our clinic',
                'type' => 'string',
                'title' => 'Clinic About Description',
            ],
            [
                'key' => 'clinic_about_keywords',
                'value' => 'about, clinic',
                'type' => 'string',
                'title' => 'Clinic About Keywords',
            ],
            [
                'key' => 'clinic_about_page_title',
                'value' => 'About',
                'type' => 'string',
                'title' => 'Clinic About Page Title',
            ],
            [
                'key' => 'clinic_about_page_intro',
                'value' => 'Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.',
                'type' => 'string',
                'title' => 'Clinic About Page Intro',
            ],
            [
                'key' => 'clinic_about_heading',
                'value' => 'Compassionate Care for Every Family',
                'type' => 'string',
                'title' => 'Clinic About Heading',
            ],
            [
                'key' => 'clinic_about_lead',
                'value' => 'For over two decades, we have been dedicated to providing exceptional healthcare services to our community. Our commitment goes beyond medical treatment—we believe in building lasting relationships with our patients and their families.',
                'type' => 'string',
                'title' => 'Clinic About Lead',
            ],
            [
                'key' => 'clinic_about_body',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'type' => 'string',
                'title' => 'Clinic About Body',
            ],
            [
                'key' => 'clinic_about_stats',
                'value' => json_encode([
                    ['value' => 15000, 'label' => 'Patients Treated'],
                    ['value' => 25, 'label' => 'Years Experience'],
                    ['value' => 50, 'label' => 'Medical Specialists'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic About Stats',
            ],
            [
                'key' => 'clinic_about_image_main',
                'value' => '/web_assets/img/health/facilities-6.webp',
                'type' => 'image',
                'title' => 'Clinic About Main Image',
            ],
            [
                'key' => 'clinic_about_image_main_alt',
                'value' => 'Healthcare facility',
                'type' => 'string',
                'title' => 'Clinic About Main Image Alt',
            ],
            [
                'key' => 'clinic_about_image_float',
                'value' => '/web_assets/img/health/staff-8.webp',
                'type' => 'image',
                'title' => 'Clinic About Floating Image',
            ],
            [
                'key' => 'clinic_about_image_float_alt',
                'value' => 'Medical team',
                'type' => 'string',
                'title' => 'Clinic About Floating Image Alt',
            ],
            [
                'key' => 'clinic_about_values_heading',
                'value' => 'Our Core Values',
                'type' => 'string',
                'title' => 'Clinic About Values Heading',
            ],
            [
                'key' => 'clinic_about_values_description',
                'value' => 'These principles guide everything we do in our commitment to exceptional healthcare',
                'type' => 'string',
                'title' => 'Clinic About Values Description',
            ],
            [
                'key' => 'clinic_about_values',
                'value' => json_encode([
                    [
                        'icon' => 'heart-pulse',
                        'title' => 'Compassion',
                        'description' => 'Providing care with empathy and understanding for every patient\'s unique needs and circumstances.',
                    ],
                    [
                        'icon' => 'shield-check',
                        'title' => 'Excellence',
                        'description' => 'Maintaining the highest standards of medical care through continuous learning and innovation.',
                    ],
                    [
                        'icon' => 'people',
                        'title' => 'Integrity',
                        'description' => 'Building trust through honest communication and ethical practices in all our interactions.',
                    ],
                    [
                        'icon' => 'lightbulb',
                        'title' => 'Innovation',
                        'description' => 'Embracing cutting-edge technology and treatments to improve patient outcomes.',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic About Values',
            ],
            [
                'key' => 'clinic_about_certs_heading',
                'value' => 'Accreditations & Certifications',
                'type' => 'string',
                'title' => 'Clinic About Certifications Heading',
            ],
            [
                'key' => 'clinic_about_certs_description',
                'value' => 'Recognized by leading healthcare organizations for our commitment to quality care',
                'type' => 'string',
                'title' => 'Clinic About Certifications Description',
            ],
            [
                'key' => 'clinic_about_certs',
                'value' => json_encode([
                    ['image' => '/web_assets/img/clients/clients-1.webp', 'alt' => 'Healthcare certification'],
                    ['image' => '/web_assets/img/clients/clients-2.webp', 'alt' => 'Medical accreditation'],
                    ['image' => '/web_assets/img/clients/clients-3.webp', 'alt' => 'Healthcare certification'],
                    ['image' => '/web_assets/img/clients/clients-4.webp', 'alt' => 'Medical certification'],
                    ['image' => '/web_assets/img/clients/clients-5.webp', 'alt' => 'Healthcare accreditation'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic About Certifications',
            ],
            [
                'key' => 'clinic_services_title',
                'value' => 'Services - Clinic',
                'type' => 'string',
                'title' => 'Clinic Services Title',
            ],
            [
                'key' => 'clinic_services_description',
                'value' => 'Our medical services',
                'type' => 'string',
                'title' => 'Clinic Services Description',
            ],
            [
                'key' => 'clinic_services_keywords',
                'value' => 'services, clinic',
                'type' => 'string',
                'title' => 'Clinic Services Keywords',
            ],
            [
                'key' => 'clinic_services_page_title',
                'value' => 'Services',
                'type' => 'string',
                'title' => 'Clinic Services Page Title',
            ],
            [
                'key' => 'clinic_services_page_intro',
                'value' => 'Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.',
                'type' => 'string',
                'title' => 'Clinic Services Page Intro',
            ],
            [
                'key' => 'clinic_contact_title',
                'value' => 'Contact - Clinic',
                'type' => 'string',
                'title' => 'Clinic Contact Title',
            ],
            [
                'key' => 'clinic_contact_description',
                'value' => 'Contact us',
                'type' => 'string',
                'title' => 'Clinic Contact Description',
            ],
            [
                'key' => 'clinic_contact_keywords',
                'value' => 'contact, clinic',
                'type' => 'string',
                'title' => 'Clinic Contact Keywords',
            ],
            [
                'key' => 'clinic_contact_page_title',
                'value' => 'Contact',
                'type' => 'string',
                'title' => 'Clinic Contact Page Title',
            ],
            [
                'key' => 'clinic_contact_page_intro',
                'value' => 'Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.',
                'type' => 'string',
                'title' => 'Clinic Contact Page Intro',
            ],
            [
                'key' => 'clinic_contact_info_cards',
                'value' => json_encode([
                    [
                        'icon' => 'geo-alt',
                        'title' => 'Our Address',
                        'lines' => ['1842 Maple Avenue, Portland, Oregon 97204'],
                    ],
                    [
                        'icon' => 'envelope',
                        'title' => 'Email Address',
                        'lines' => ['info@example.com', 'contact@example.com'],
                    ],
                    [
                        'icon' => 'headset',
                        'title' => 'Hours of Operation',
                        'lines' => ['Sunday-Fri: 9 AM - 6 PM', 'Saturday: 9 AM - 4 PM'],
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic Contact Info Cards',
            ],
            [
                'key' => 'clinic_contact_form_heading',
                'value' => 'Send us a Message',
                'type' => 'string',
                'title' => 'Clinic Contact Form Heading',
            ],
            [
                'key' => 'clinic_contact_form_intro',
                'value' => 'Have questions or want to learn more? Reach out to us and our team will get back to you shortly.',
                'type' => 'string',
                'title' => 'Clinic Contact Form Intro',
            ],
            [
                'key' => 'clinic_contact_form_action',
                'value' => '#',
                'type' => 'string',
                'title' => 'Clinic Contact Form Action',
            ],
            [
                'key' => 'clinic_contact_form_name_placeholder',
                'value' => 'Your Name',
                'type' => 'string',
                'title' => 'Clinic Contact Form Name Placeholder',
            ],
            [
                'key' => 'clinic_contact_form_email_placeholder',
                'value' => 'Your Email',
                'type' => 'string',
                'title' => 'Clinic Contact Form Email Placeholder',
            ],
            [
                'key' => 'clinic_contact_form_subject_placeholder',
                'value' => 'Subject',
                'type' => 'string',
                'title' => 'Clinic Contact Form Subject Placeholder',
            ],
            [
                'key' => 'clinic_contact_form_message_placeholder',
                'value' => 'Your Message',
                'type' => 'string',
                'title' => 'Clinic Contact Form Message Placeholder',
            ],
            [
                'key' => 'clinic_contact_form_loading',
                'value' => 'Loading',
                'type' => 'string',
                'title' => 'Clinic Contact Form Loading Text',
            ],
            [
                'key' => 'clinic_contact_form_success',
                'value' => 'Your message has been sent. Thank you!',
                'type' => 'string',
                'title' => 'Clinic Contact Form Success Text',
            ],
            [
                'key' => 'clinic_contact_form_submit',
                'value' => 'Send Message',
                'type' => 'string',
                'title' => 'Clinic Contact Form Submit',
            ],
            [
                'key' => 'clinic_contact_map_url',
                'value' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus',
                'type' => 'string',
                'title' => 'Clinic Contact Map URL',
            ],
            [
                'key' => 'clinic_404_title',
                'value' => '404 - Clinic',
                'type' => 'string',
                'title' => 'Clinic 404 Title',
            ],
            [
                'key' => 'clinic_404_description',
                'value' => 'Page not found',
                'type' => 'string',
                'title' => 'Clinic 404 Description',
            ],
            [
                'key' => 'clinic_404_keywords',
                'value' => '404, not found',
                'type' => 'string',
                'title' => 'Clinic 404 Keywords',
            ],
            [
                'key' => 'clinic_404_page_title',
                'value' => '404',
                'type' => 'string',
                'title' => 'Clinic 404 Page Title',
            ],
            [
                'key' => 'clinic_404_page_intro',
                'value' => 'Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.',
                'type' => 'string',
                'title' => 'Clinic 404 Page Intro',
            ],
            [
                'key' => 'clinic_404_number',
                'value' => '404',
                'type' => 'string',
                'title' => 'Clinic 404 Number',
            ],
            [
                'key' => 'clinic_404_error_title',
                'value' => 'Page Not Found',
                'type' => 'string',
                'title' => 'Clinic 404 Error Title',
            ],
            [
                'key' => 'clinic_404_error_description',
                'value' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.',
                'type' => 'string',
                'title' => 'Clinic 404 Error Description',
            ],
            [
                'key' => 'clinic_404_primary_label',
                'value' => 'Back to Home',
                'type' => 'string',
                'title' => 'Clinic 404 Primary Label',
            ],
            [
                'key' => 'clinic_404_primary_url',
                'value' => '/',
                'type' => 'string',
                'title' => 'Clinic 404 Primary URL',
            ],
            [
                'key' => 'clinic_404_secondary_label',
                'value' => 'Search Site',
                'type' => 'string',
                'title' => 'Clinic 404 Secondary Label',
            ],
            [
                'key' => 'clinic_404_secondary_url',
                'value' => '#',
                'type' => 'string',
                'title' => 'Clinic 404 Secondary URL',
            ],
            [
                'key' => 'clinic_404_links_heading',
                'value' => 'You might be looking for:',
                'type' => 'string',
                'title' => 'Clinic 404 Links Heading',
            ],
            [
                'key' => 'clinic_404_links',
                'value' => json_encode([
                    ['icon' => 'info-circle', 'label' => 'About Us', 'url' => '/about'],
                    ['icon' => 'telephone', 'label' => 'Contact', 'url' => '/contact'],
                    ['icon' => 'grid-3x3-gap', 'label' => 'Services', 'url' => '/services'],
                    ['icon' => 'journal-text', 'label' => 'Blog', 'url' => '#'],
                    ['icon' => 'question-circle', 'label' => 'Support', 'url' => '#'],
                    ['icon' => 'shield-check', 'label' => 'Privacy Policy', 'url' => '#'],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'Clinic 404 Helpful Links',
            ],
        ];

        Setting::insert($settings);
    }
}
