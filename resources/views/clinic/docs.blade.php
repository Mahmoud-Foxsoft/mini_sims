@php
    $baseUrl = rtrim(config('app.url'), '/') . '/api/v1';
@endphp

@extends('clinic.layout')

@section('title', 'API Docs')
@section('description', 'QuickSMS external API documentation')
@section('keywords', 'api, docs, sms, numbers')
@section('body_class', 'docs-page')

@section('content')
    <div class="page-title">
        <div class="heading">
            <div class="container">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1 class="heading-title">API Documentation</h1>
                        <p class="mb-0">Use the external API to fetch services, orders, payments, and messages.</p>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumbs">
            <div class="container">
                <ol>
                    <li><a href="{{ route('clinic.home') }}">Home</a></li>
                    <li class="current">API Docs</li>
                </ol>
            </div>
        </nav>
    </div>

    <section class="section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <h2>Base URL</h2>
            <p>All endpoints below use:</p>
            <pre><code class="language-bash">{{ $baseUrl }}</code></pre>

            <h2>API Key Management</h2>
            <p>Generate or rotate your API key from the user dashboard. Use the Console to manage keys.</p>

            <h2>Authentication</h2>
            <p>Use a bearer token</p>
            <pre><code class="language-bash">Authorization: Bearer &lt;token&gt;</code></pre>

            <h2>Endpoints</h2>

            <h3>GET /me</h3>
            <p>Returns the authenticated user profile.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/me" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": "2026-04-07T13:44:43.000000Z",
        "balance_cents": 38375,
        "webhook_url": null,
        "created_at": "2026-04-07T13:44:43.000000Z",
        "updated_at": "2026-04-07T13:44:44.000000Z"
    },
    "message": "User profile fetched successfully"
}</code></pre>

            <h3>GET /orders</h3>
            <p>List your orders.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/orders" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "267336c5-4c58-3f89-9cd1-e8835202da80",
                "user_id": 1,
                "total_cent_price": 90347,
                "status": "completed",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z"
            },
            {
                "id": "ebdc07d4-3c9f-3373-822a-e16bd8c26c07",
                "user_id": 1,
                "total_cent_price": 16191,
                "status": "completed",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z"
            },
            {
                "id": "da8787f6-0826-31af-9edb-7f5d1631a2f5",
                "user_id": 1,
                "total_cent_price": 91032,
                "status": "cancelled",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z"
            },
            {
                "id": "fd43050e-5680-37e2-92d9-0c73968c9c0a",
                "user_id": 1,
                "total_cent_price": 81145,
                "status": "pending",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z"
            }
        ],
        "first_page_url": "{{ $baseUrl }}/orders?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "{{ $baseUrl }}/orders?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "{{ $baseUrl }}/orders?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "{{ $baseUrl }}/orders",
        "per_page": 10,
        "prev_page_url": null,
        "to": 4,
        "total": 4
    },
    "message": "Orders retrieved successfully."
}</code></pre>

            <h3>GET /phone-numbers</h3>
            <p>List order items and include received messages.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/phone-numbers" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "order_id": "267336c5-4c58-3f89-9cd1-e8835202da80",
                "user_id": 1,
                "service_name": "Dietrich, Turcotte and Durgan",
                "phone_number": "651.410.9941",
                "price_cents": 6292,
                "status": "pending",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z",
                "messages": [
                    {
                        "id": 8,
                        "message": "Test message1",
                        "order_item_id": 1,
                        "created_at": "2026-04-07T13:44:44.000000Z",
                        "updated_at": "2026-04-07T13:44:44.000000Z"
                    },
                ]
            },
            {
                "id": 3,
                "order_id": "267336c5-4c58-3f89-9cd1-e8835202da80",
                "user_id": 1,
                "service_name": "Yost-Schmeler",
                "phone_number": "934.493.0724",
                "price_cents": 9043,
                "status": "completed",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z",
                "messages": [
                    {
                        "id": 1,
                        "message": "Test message2",
                        "order_item_id": 3,
                        "created_at": "2026-04-07T13:44:44.000000Z",
                        "updated_at": "2026-04-07T13:44:44.000000Z"
                    }
                ]
            },
            {
                "id": 7,
                "order_id": "267336c5-4c58-3f89-9cd1-e8835202da80",
                "user_id": 1,
                "service_name": "D'Amore-Bartell",
                "phone_number": "1-832-443-0326",
                "price_cents": 5334,
                "status": "timeout_refunded",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z",
                "messages": []
            },
            {
                "id": 8,
                "order_id": "267336c5-4c58-3f89-9cd1-e8835202da80",
                "user_id": 1,
                "service_name": "Treutel-Moore",
                "phone_number": "(520) 272-4363",
                "price_cents": 8778,
                "status": "cancelled",
                "created_at": "2026-04-07T13:44:44.000000Z",
                "updated_at": "2026-04-07T13:44:44.000000Z",
                "messages": []
            },
        ],
        "first_page_url": "{{ $baseUrl }}/phone-numbers?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "{{ $baseUrl }}/phone-numbers?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "{{ $baseUrl }}/phone-numbers?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "{{ $baseUrl }}/phone-numbers",
        "per_page": 20,
        "prev_page_url": null,
        "to": 4,
        "total": 4
    },
    "message": "Orders retrieved successfully."
}</code></pre>

            <h3>GET /transactions</h3>
            <p>Get your transaction history.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/transactions" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "transactions": {
            "current_page": 1,
            "data": [
                {
                    "id": 5,
                    "user_id": 1,
                    "type": "credit",
                    "amount_cents": 2963,
                    "description": "Test Credit2",
                    "reference_id": null,
                    "created_at": "2026-04-07T13:44:44.000000Z",
                    "updated_at": "2026-04-07T13:44:44.000000Z"
                },
                {
                    "id": 6,
                    "user_id": 1,
                    "type": "debit",
                    "amount_cents": 1000,
                    "description": "Test Debit2",
                    "reference_id": null,
                    "created_at": "2026-04-07T13:44:44.000000Z",
                    "updated_at": "2026-04-07T13:44:44.000000Z"
                },
            ],
            "first_page_url": "{{ $baseUrl }}/transactions?page=1",
            "from": 1,
            "last_page": 1,
            "last_page_url": "{{ $baseUrl }}/transactions?page=1",
            "links": [
                {
                    "url": null,
                    "label": "&laquo; Previous",
                    "page": null,
                    "active": false
                },
                {
                    "url": "{{ $baseUrl }}/transactions?page=1",
                    "label": "1",
                    "page": 1,
                    "active": true
                },
                {
                    "url": null,
                    "label": "Next &raquo;",
                    "page": null,
                    "active": false
                }
            ],
            "next_page_url": null,
            "path": "{{ $baseUrl }}/transactions",
            "per_page": 20,
            "prev_page_url": null,
            "to": 2,
            "total": 2
        },
        "credit_sum_cents": "2963",
        "debit_sum_cents": "1000"
    },
    "message": "Transactions fetched successfully"
}</code></pre>

            <h3>GET /payments</h3>
            <p>List payments.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/payments" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "amount": 89.47,
                "currency": "AOA",
                "transaction_id": "f922fbe5-c997-34f1-a8a9-fa9da63cbb5b",
                "status": "finished",
                "paid_amount": "29.85",
                "created_at": "2026-04-08T07:51:43.000000Z",
                "updated_at": "2026-04-08T07:51:43.000000Z"
            },
            {
                "id": 2,
                "user_id": 1,
                "amount": 43.63,
                "currency": "KHR",
                "transaction_id": "2f6e4796-f483-31e4-b715-275278e278a3",
                "status": "waiting",
                "paid_amount": "31.72",
                "created_at": "2026-04-08T07:51:43.000000Z",
                "updated_at": "2026-04-08T07:51:43.000000Z"
            },
            {
                "id": 3,
                "user_id": 1,
                "amount": 64.91,
                "currency": "INR",
                "transaction_id": "ff017ecc-61bb-3c25-ad01-9100f014f584",
                "status": "waiting",
                "paid_amount": "14.16",
                "created_at": "2026-04-08T07:51:43.000000Z",
                "updated_at": "2026-04-08T07:51:43.000000Z"
            }
        ],
        "first_page_url": "{{ $baseUrl }}/payments?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "{{ $baseUrl }}/payments?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "{{ $baseUrl }}/payments?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "{{ $baseUrl }}/payments",
        "per_page": 20,
        "prev_page_url": null,
        "to": 3,
        "total": 3
    },
    "message": "Payments fetched successfully"
}</code></pre>

            <h3>POST /payments</h3>
            <p>Create a payment request.</p>
            <pre><code class="language-bash">curl -X POST "{{ $baseUrl }}/payments" \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50,
    "currency": "USD",
    "paid_amount": 50
  }'</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {
    // Expected response data here
  }
}</code></pre>

            <h3>GET /payments/currencies</h3>
            <p>Returns supported currencies.</p>
            <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/payments/currencies" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "currencies": [
            "BTC",
            "LTC",
            "ETH",
            "MATIC",
            "BCH",
            "BNBMAINNET",
            "BUSDBSC",
            "SOL",
            "BUSD",
            "USDC",
            "USDTBSC",
            "USDTERC20",
            "USDCBSC"
        ]
    },
    "message": "currencies fetched successfully"
}</code></pre>

            <h3>POST /payments/estimate</h3>
            <p>Estimate the payment amount in a selected currency.</p>
            <pre><code class="language-bash">curl -X POST "{{ $baseUrl }}/payments/estimate" \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50,
    "currency": "USD"
  }'</code></pre>

            <h2>Success Responses</h2>
            <p>A standard successful response will return a 200 OK status code with the following JSON structure.</p>
            <pre><code class="language-json">{
    "success": true,
    "data": {
        "data": {
            "currency_from": "usd",
            "amount_from": 671.622,
            "currency_to": "btc",
            "estimated_amount": "0.00937731"
        }
    },
    "message": "estimated successfully"
}</code></pre>


            <h2>Error Responses</h2>
            <p>All responses use a standard JSON shape with a message and data payload. Errors return an error message and
                status code.</p>
            <pre><code class="language-json">{
  "success": false,
  "message": "Unauthorized",
  "data": {
    "error": "Unauthenticated."
  }
}</code></pre>
        </div>
    </section>
@endsection
