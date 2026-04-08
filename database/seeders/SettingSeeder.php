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

        $settings = [
            // global
            [
                'key' => 'site_logo',
                'value' => '/web_assets/img/logo.webp',
                'type' => 'image',
                'title' => 'Site Logo',
            ],
            [
                'key' => 'site_email',
                'value' => 'support@quicksms.io',
                'type' => 'string',
                'title' => 'Site Email',
            ],
            [
                'key' => 'site_phone',
                'value' => '+1 (202) 555-0148',
                'type' => 'string',
                'title' => 'Site Phone',
            ],

            // footer
            [
                'key' => 'footer_description',
                'value' => 'Buy phone numbers for specific services, receive messages fast, and get instant refunds when numbers are unavailable or messages do not arrive in time.',
                'type' => 'string',
                'title' => 'Footer Description',
            ],


            // home seo
            [
                'key' => 'home_title',
                'value' => 'Buy SMS Numbers - QuickSMS',
                'type' => 'string',
                'title' => 'Home Title',
            ],
            [
                'key' => 'home_description',
                'value' => 'Buy phone numbers for any service, receive SMS instantly, and get refunded when numbers are unavailable or messages do not arrive.',
                'type' => 'string',
                'title' => 'Home Description',
            ],
            [
                'key' => 'home_keywords',
                'value' => 'sms, phone numbers, verification, refund, api',
                'type' => 'string',
                'title' => 'Home Keywords',
            ],

            // home hero
            [
                'key' => 'home_badge_1_icon',
                'value' => 'shield-check',
                'type' => 'string',
                'title' => 'Home Badge 1 Icon',
            ],
            [
                'key' => 'home_badge_1_label',
                'value' => 'Instant Refunds',
                'type' => 'string',
                'title' => 'Home Badge 1 Label',
            ],
            [
                'key' => 'home_badge_2_icon',
                'value' => 'clock',
                'type' => 'string',
                'title' => 'Home Badge 2 Icon',
            ],
            [
                'key' => 'home_badge_2_label',
                'value' => '15-Min Guarantee',
                'type' => 'string',
                'title' => 'Home Badge 2 Label',
            ],
            [
                'key' => 'home_badge_3_icon',
                'value' => 'code-square',
                'type' => 'string',
                'title' => 'Home Badge 3 Icon',
            ],
            [
                'key' => 'home_badge_3_label',
                'value' => 'API Ready',
                'type' => 'string',
                'title' => 'Home Badge 3 Label',
            ],
            [
                'key' => 'home_hero_title',
                'value' => 'Buy SMS Numbers for Any <span class="highlight">Service</span> in Seconds',
                'type' => 'html',
                'title' => 'Home Hero Title',
            ],
            [
                'key' => 'home_hero_description',
                'value' => 'Choose a service, buy the number, and receive your verification message instantly. If a message does not arrive within 15 minutes or numbers are unavailable, refunds are automatic.',
                'type' => 'string',
                'title' => 'Home Hero Description',
            ],
            [
                'key' => 'home_stat_1_value',
                'value' => '120',
                'type' => 'string',
                'title' => 'Home Stat 1 Value',
            ],
            [
                'key' => 'home_stat_1_suffix',
                'value' => '+',
                'type' => 'string',
                'title' => 'Home Stat 1 Suffix',
            ],
            [
                'key' => 'home_stat_1_label',
                'value' => 'Supported Services',
                'type' => 'string',
                'title' => 'Home Stat 1 Label',
            ],
            [
                'key' => 'home_stat_2_value',
                'value' => '99',
                'type' => 'string',
                'title' => 'Home Stat 2 Value',
            ],
            [
                'key' => 'home_stat_2_suffix',
                'value' => '%',
                'type' => 'string',
                'title' => 'Home Stat 2 Suffix',
            ],
            [
                'key' => 'home_stat_2_label',
                'value' => 'Delivery Success',
                'type' => 'string',
                'title' => 'Home Stat 2 Label',
            ],
            [
                'key' => 'home_stat_3_value',
                'value' => '15',
                'type' => 'string',
                'title' => 'Home Stat 3 Value',
            ],
            [
                'key' => 'home_stat_3_suffix',
                'value' => 'min',
                'type' => 'string',
                'title' => 'Home Stat 3 Suffix',
            ],
            [
                'key' => 'home_stat_3_label',
                'value' => 'Refund Window',
                'type' => 'string',
                'title' => 'Home Stat 3 Label',
            ],
            [
                'key' => 'home_primary_label',
                'value' => 'Start Buying Numbers',
                'type' => 'string',
                'title' => 'Home Primary Label',
            ],
            [
                'key' => 'home_secondary_label',
                'value' => 'View API Docs',
                'type' => 'string',
                'title' => 'Home Secondary Label',
            ],
            [
                'key' => 'home_secondary_icon',
                'value' => 'terminal',
                'type' => 'string',
                'title' => 'Home Secondary Icon',
            ],
            [
                'key' => 'home_highlight_icon',
                'value' => 'telephone-fill',
                'type' => 'string',
                'title' => 'Home Highlight Icon',
            ],
            [
                'key' => 'home_highlight_label',
                'value' => 'Support',
                'type' => 'string',
                'title' => 'Home Highlight Label',
            ],
            [
                'key' => 'home_highlight_value',
                'value' => '24/7 Live Chat',
                'type' => 'string',
                'title' => 'Home Highlight Value',
            ],
            [
                'key' => 'home_hero_image',
                'value' => '/web_assets/img/health/staff-10.webp',
                'type' => 'image',
                'title' => 'Home Hero Image',
            ],
            [
                'key' => 'home_hero_image_alt',
                'value' => 'SMS numbers dashboard preview',
                'type' => 'string',
                'title' => 'Home Hero Image Alt',
            ],
            [
                'key' => 'home_card_1_icon',
                'value' => 'cart-check',
                'type' => 'string',
                'title' => 'Home Card 1 Icon',
            ],
            [
                'key' => 'home_card_1_title',
                'value' => 'Numbers Ready',
                'type' => 'string',
                'title' => 'Home Card 1 Title',
            ],
            [
                'key' => 'home_card_1_value',
                'value' => 'Instant Delivery',
                'type' => 'string',
                'title' => 'Home Card 1 Value',
            ],
            [
                'key' => 'home_card_1_note',
                'value' => 'Refunded if unavailable',
                'type' => 'string',
                'title' => 'Home Card 1 Note',
            ],
            [
                'key' => 'home_card_2_value',
                'value' => 'API Enabled',
                'type' => 'string',
                'title' => 'Home Card 2 Value',
            ],
            [
                'key' => 'home_card_2_note',
                'value' => 'Automate at scale',
                'type' => 'string',
                'title' => 'Home Card 2 Note',
            ],

            // home about
            [
                'key' => 'home_about_image',
                'value' => '/web_assets/img/health/facilities-9.webp',
                'type' => 'image',
                'title' => 'Home About Image',
            ],
            [
                'key' => 'home_about_card_title',
                'value' => 'Automatic Refunds',
                'type' => 'string',
                'title' => 'Home About Card Title',
            ],
            [
                'key' => 'home_about_card_text',
                'value' => 'Unfulfilled numbers and expired waits return to your balance instantly.',
                'type' => 'string',
                'title' => 'Home About Card Text',
            ],
            [
                'key' => 'home_about_badge_value',
                'value' => '15 min',
                'type' => 'string',
                'title' => 'Home About Badge Value',
            ],
            [
                'key' => 'home_about_badge_text',
                'value' => 'Refund Timer',
                'type' => 'string',
                'title' => 'Home About Badge Text',
            ],

            // home services
            [
                'key' => 'home_services_heading',
                'value' => 'Popular Services',
                'type' => 'string',
                'title' => 'Home Services Heading',
            ],
            [
                'key' => 'home_services_subheading',
                'value' => 'Pick a service and get a phone number instantly.',
                'type' => 'string',
                'title' => 'Home Services Subheading',
            ],
            [
                'key' => 'service_price_label',
                'value' => 'Price:',
                'type' => 'string',
                'title' => 'Service Price Label',
            ],
            [
                'key' => 'service_card_cta',
                'value' => 'Buy Now',
                'type' => 'string',
                'title' => 'Service Card CTA',
            ],
            [
                'key' => 'service_card_url',
                'value' => '/services',
                'type' => 'string',
                'title' => 'Service Card URL',
            ],

            // about seo
            [
                'key' => 'about_title',
                'value' => 'About - QuickSMS',
                'type' => 'string',
                'title' => 'About Title',
            ],
            [
                'key' => 'about_description',
                'value' => 'Learn how QuickSMS delivers verification numbers and instant refunds.',
                'type' => 'string',
                'title' => 'About Description',
            ],
            [
                'key' => 'about_keywords',
                'value' => 'sms, numbers, refunds, api',
                'type' => 'string',
                'title' => 'About Keywords',
            ],
            [
                'key' => 'about_page_intro',
                'value' => 'We make SMS verification simple, fast, and fair for teams and individuals.',
                'type' => 'string',
                'title' => 'About Page Intro',
            ],
            [
                'key' => 'about_heading',
                'value' => 'Built for Reliable SMS Delivery',
                'type' => 'string',
                'title' => 'About Heading',
            ],
            [
                'key' => 'about_lead',
                'value' => 'QuickSMS provides phone numbers for specific services with a transparent refund policy so you only pay for successful messages.',
                'type' => 'string',
                'title' => 'About Lead',
            ],
            [
                'key' => 'about_body',
                'value' => 'Whether you buy one number or hundreds, we automatically refund any unavailable numbers and any message that does not arrive within 15 minutes. Our API helps you automate buying and fetching SMS at scale.',
                'type' => 'string',
                'title' => 'About Body',
            ],
            [
                'key' => 'about_stat_1_value',
                'value' => '500',
                'type' => 'string',
                'title' => 'About Stat 1 Value',
            ],
            [
                'key' => 'about_stat_1_label',
                'value' => 'Numbers Fulfilled',
                'type' => 'string',
                'title' => 'About Stat 1 Label',
            ],
            [
                'key' => 'about_stat_2_value',
                'value' => '15',
                'type' => 'string',
                'title' => 'About Stat 2 Value',
            ],
            [
                'key' => 'about_stat_2_label',
                'value' => 'Refund Timer',
                'type' => 'string',
                'title' => 'About Stat 2 Label',
            ],
            [
                'key' => 'about_stat_3_value',
                'value' => '100',
                'type' => 'string',
                'title' => 'About Stat 3 Value',
            ],
            [
                'key' => 'about_stat_3_label',
                'value' => 'Automation Ready',
                'type' => 'string',
                'title' => 'About Stat 3 Label',
            ],
            [
                'key' => 'about_image_main',
                'value' => '/web_assets/img/health/facilities-6.webp',
                'type' => 'image',
                'title' => 'About Main Image',
            ],
            [
                'key' => 'about_image_main_alt',
                'value' => 'Service reliability',
                'type' => 'string',
                'title' => 'About Main Image Alt',
            ],
            [
                'key' => 'about_image_float',
                'value' => '/web_assets/img/health/staff-8.webp',
                'type' => 'image',
                'title' => 'About Floating Image',
            ],
            [
                'key' => 'about_image_float_alt',
                'value' => 'Automation overview',
                'type' => 'string',
                'title' => 'About Floating Image Alt',
            ],
            [
                'key' => 'about_values_heading',
                'value' => 'Why Teams Use QuickSMS',
                'type' => 'string',
                'title' => 'About Values Heading',
            ],
            [
                'key' => 'about_values_description',
                'value' => 'We built a platform that prioritizes speed, transparency, and automation.',
                'type' => 'string',
                'title' => 'About Values Description',
            ],
            [
                'key' => 'about_value_1_icon',
                'value' => 'lightning',
                'type' => 'string',
                'title' => 'About Value 1 Icon',
            ],
            [
                'key' => 'about_value_1_title',
                'value' => 'Instant Delivery',
                'type' => 'string',
                'title' => 'About Value 1 Title',
            ],
            [
                'key' => 'about_value_1_description',
                'value' => 'Numbers are provisioned in seconds for supported services.',
                'type' => 'string',
                'title' => 'About Value 1 Description',
            ],
            [
                'key' => 'about_value_2_icon',
                'value' => 'arrow-repeat',
                'type' => 'string',
                'title' => 'About Value 2 Icon',
            ],
            [
                'key' => 'about_value_2_title',
                'value' => 'Automatic Refunds',
                'type' => 'string',
                'title' => 'About Value 2 Title',
            ],
            [
                'key' => 'about_value_2_description',
                'value' => 'Unavailable numbers or expired waits are refunded immediately.',
                'type' => 'string',
                'title' => 'About Value 2 Description',
            ],
            [
                'key' => 'about_value_3_icon',
                'value' => 'code-slash',
                'type' => 'string',
                'title' => 'About Value 3 Icon',
            ],
            [
                'key' => 'about_value_3_title',
                'value' => 'API Integration',
                'type' => 'string',
                'title' => 'About Value 3 Title',
            ],
            [
                'key' => 'about_value_3_description',
                'value' => 'Automate purchasing and message retrieval with our API.',
                'type' => 'string',
                'title' => 'About Value 3 Description',
            ],
            [
                'key' => 'about_value_4_icon',
                'value' => 'shield-check',
                'type' => 'string',
                'title' => 'About Value 4 Icon',
            ],
            [
                'key' => 'about_value_4_title',
                'value' => 'Transparent Billing',
                'type' => 'string',
                'title' => 'About Value 4 Title',
            ],
            [
                'key' => 'about_value_4_description',
                'value' => 'Pay only for numbers that work and messages that arrive.',
                'type' => 'string',
                'title' => 'About Value 4 Description',
            ],
            [
                'key' => 'about_certs_heading',
                'value' => 'Trusted by Growing Teams',
                'type' => 'string',
                'title' => 'About Certs Heading',
            ],
            [
                'key' => 'about_certs_description',
                'value' => 'Reliable SMS delivery for startups, agencies, and developers.',
                'type' => 'string',
                'title' => 'About Certs Description',
            ],
            // services seo
            [
                'key' => 'services_title',
                'value' => 'Services',
                'type' => 'string',
                'title' => 'Services Title',
            ],
            [
                'key' => 'services_description',
                'value' => 'Choose a service and buy a number instantly.',
                'type' => 'string',
                'title' => 'Services Description',
            ],
            [
                'key' => 'services_keywords',
                'value' => 'sms services, numbers, verification',
                'type' => 'string',
                'title' => 'Services Keywords',
            ],
            [
                'key' => 'services_page_intro',
                'value' => 'Pick a service to see current prices. We only show availability for the first six services here.',
                'type' => 'string',
                'title' => 'Services Page Intro',
            ],

            // contact seo
            [
                'key' => 'contact_title',
                'value' => 'Contact Us',
                'type' => 'string',
                'title' => 'Contact Title',
            ],
            [
                'key' => 'contact_description',
                'value' => 'Contact QuickSMS for help with numbers, refunds, and API access.',
                'type' => 'string',
                'title' => 'Contact Description',
            ],
            [
                'key' => 'contact_keywords',
                'value' => 'contact, support, sms, api',
                'type' => 'string',
                'title' => 'Contact Keywords',
            ],
            [
                'key' => 'contact_page_intro',
                'value' => 'Need help buying numbers or automating via API? Reach out and we will respond quickly.',
                'type' => 'string',
                'title' => 'Contact Page Intro',
            ],
          
            [
                'key' => 'contact_form_heading',
                'value' => 'Send us a Message',
                'type' => 'string',
                'title' => 'Contact Form Heading',
            ],
            [
                'key' => 'contact_form_intro',
                'value' => 'Have questions about refunds, availability, or API integration? We are here to help.',
                'type' => 'string',
                'title' => 'Contact Form Intro',
            ],
            [
                'key' => 'contact_form_success',
                'value' => 'Your message has been sent. Thank you!',
                'type' => 'string',
                'title' => 'Contact Form Success Text',
            ],
            // faq
            [
                'key' => 'faq_header',
                'value' => 'Questions? We have answers.',
                'type' => 'string',
                'title' => 'FAQ Header',
            ],
            [
                'key' => 'faq_subheader',
                'value' => 'Quick answers about number availability, refunds, and API automation.',
                'type' => 'string',
                'title' => 'FAQ Subheader',
            ],
            [
                'key' => 'faqs',
                'value' => json_encode([
                    [
                        'q' => 'What happens if the number I want is not available?',
                        'a' => 'If you request multiple numbers and only some are available, you will receive the available ones and the remaining balance is refunded automatically.',
                    ],
                    [
                        'q' => 'How long do I wait for an SMS message?',
                        'a' => 'We wait up to 15 minutes. If the SMS is not received in that window, your balance is refunded automatically.',
                    ],
                    [
                        'q' => 'Can I automate purchases and message retrieval?',
                        'a' => 'Yes. Our API lets you buy numbers, check availability, and fetch messages programmatically.',
                    ],
                    [
                        'q' => 'Do I get charged for failed orders?',
                        'a' => 'No. You only pay for numbers that are actually delivered and receive messages within the allowed time.',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'faq_array',
                'title' => 'FAQs',
            ],

            // privacy
            [
                'key' => 'privacy_title',
                'value' => 'Privacy - QuickSMS',
                'type' => 'string',
                'title' => 'Privacy Title',
            ],
            [
                'key' => 'privacy_description',
                'value' => 'Privacy policy for QuickSMS users.',
                'type' => 'string',
                'title' => 'Privacy Description',
            ],
            [
                'key' => 'privacy_keywords',
                'value' => 'privacy, policy, sms',
                'type' => 'string',
                'title' => 'Privacy Keywords',
            ],
            [
                'key' => 'privacy_page_intro',
                'value' => 'How we collect and use information when you buy phone numbers and receive messages.',
                'type' => 'string',
                'title' => 'Privacy Page Intro',
            ],
            [
                'key' => 'privacy_content',
                'value' => '<h2>1. Information We Collect</h2><p>We collect basic account information, payment confirmations, and service usage data needed to deliver phone numbers and messages.</p><h2>2. SMS Handling</h2><p>We store messages only as long as needed to display them in your account or API response. Messages are not sold or shared.</p><h2>3. Refund Logic</h2><p>If a number is unavailable or no message arrives within 15 minutes, your balance is refunded automatically.</p><h2>4. API Access</h2><p>API requests are logged for security and rate limiting. We do not store verification messages longer than necessary.</p><h2>5. Contact</h2><p>If you have privacy questions, contact us at support@quicksms.io.</p>',
                'type' => 'html',
                'title' => 'Privacy Content',
            ],

            // terms
            [
                'key' => 'terms_title',
                'value' => 'Terms - QuickSMS',
                'type' => 'string',
                'title' => 'Terms Title',
            ],
            [
                'key' => 'terms_description',
                'value' => 'Terms of service for QuickSMS.',
                'type' => 'string',
                'title' => 'Terms Description',
            ],
            [
                'key' => 'terms_keywords',
                'value' => 'terms, service, sms',
                'type' => 'string',
                'title' => 'Terms Keywords',
            ],
            [
                'key' => 'terms_page_intro',
                'value' => 'By using QuickSMS, you agree to the terms below.',
                'type' => 'string',
                'title' => 'Terms Page Intro',
            ],
            [
                'key' => 'terms_content',
                'value' => '<h2>1. Service Scope</h2><p>QuickSMS provides temporary phone numbers for receiving SMS messages for specific services.</p><h2>2. Availability</h2><p>Numbers are provided based on availability. If you request five numbers and only three are available, the remaining amount is refunded automatically.</p><h2>3. Message Delivery</h2><p>Messages are expected within 15 minutes. If no message arrives in that time window, the order is refunded automatically.</p><h2>4. API Use</h2><p>You may use the API to automate number purchases and message retrieval. Abuse or excessive requests may result in limits.</p><h2>5. Refunds</h2><p>Refunds are credited to your balance for any unavailable numbers or expired message waits.</p><h2>6. Contact</h2><p>For questions about these terms, contact support@quicksms.io.</p>',
                'type' => 'html',
                'title' => 'Terms Content',
            ],

            [
                'key'   => 'site_social',
                'value' => json_encode([
                    ['icon' => 'facebook', 'url' => 'https://www.facebook.com/'],
                    ['icon' => 'twitter', 'url' => 'https://twitter.com/'],
                    ['icon' => 'instagram', 'url' => 'https://www.instagram.com/'],
                ]),
                'type'  => 'social_array',
                'title' => 'Contact Social Links',
            ],
        ];

        Setting::insert($settings);
    }
}
