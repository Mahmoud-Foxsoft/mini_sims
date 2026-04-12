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
            <div class="docs-layout">
                <aside class="docs-sidebar">
                    <h6>On This Page</h6>
                    <nav>
                        <a href="#base-url">Base URL</a>
                        <a href="#auth">Authentication</a>
                        <a href="#api-key">API Key</a>
                        <a href="#downloads">Downloads</a>
                        <a href="#endpoints">Endpoints</a>
                        <a href="#get-me">GET /me</a>
                        <a href="#get-orders">GET /orders</a>
                        <a href="#get-phone-numbers">GET /phone-numbers</a>
                        <a href="#get-transactions">GET /transactions</a>
                        <a href="#get-services">GET /services</a>
                        <a href="#get-payments">GET /payments</a>
                        <a href="#errors">Error Responses</a>
                    </nav>
                </aside>

                <div class="docs-content">
                    <div id="base-url">
                        <h2>Base URL</h2>
                        <p>All endpoints below use:</p>
                        <pre><code class="language-bash">{{ $baseUrl }}</code></pre>
                    </div>

                    <div id="auth">
                        <h2>Authentication</h2>
                        <p>Use a bearer token with the <strong>external-api</strong> scope.</p>
                        <pre><code class="language-bash">Authorization: Bearer &lt;token&gt;</code></pre>
                    </div>

                    <div id="api-key">
                        <h2>API Key Management</h2>
                        <p>Generate or rotate your API key from the user dashboard. Use the Console to manage keys.</p>
                    </div>

                    <div id="downloads">
                        <h2>Downloads</h2>
                        <p><a href="/quicksms-api.postman_collection.json" download>Download Postman Collection</a></p>
                    </div>

                    <div id="endpoints">
                        <h2>Endpoints</h2>
                    </div>

                    <div id="get-me">
                        <h3>GET /me</h3>
                        <p>Returns the authenticated user profile.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/me" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
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
                    </div>

                    <div id="get-orders">
                        <h3>GET /orders</h3>
                        <p>List your orders.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/orders?filters[created_at]=2026-04-01" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
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
    "to": 1,
    "total": 1
  },
  "message": "Orders retrieved successfully."
}</code></pre>
                    </div>

                    <div id="get-phone-numbers">
                        <h3>GET /phone-numbers</h3>
                        <p>List order items and include received messages.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/phone-numbers?filters[service_name]=telegram&filters[status]=pending&filters[phone_number]=+1555" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
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
          }
        ]
      }
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
    "to": 1,
    "total": 1
  },
  "message": "Orders retrieved successfully."
}</code></pre>
                    </div>

                    <div id="get-transactions">
                        <h3>GET /transactions</h3>
                        <p>Get your transaction history.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/transactions?per_page=20&filters[type]=credit&filters[source]=payment&filters[reference]=abc" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
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
        }
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
      "to": 1,
      "total": 1
    },
    "credit_sum_cents": "2963",
    "debit_sum_cents": "1000"
  },
  "message": "Transactions fetched successfully"
}</code></pre>
                    </div>
                    <div id="get-services">
                        <h3>GET /services</h3>
                        <p>Get available phone services.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/services?filters[name]=service 1" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
                        <pre><code class="language-json">{
    "success": true,
    "data": {
        "services": [
            {
                "name": "Service 1",
                "code": "service_1",
                "price": 1.5,
                "available": true
            },
            {
                "name": "Service 10",
                "code": "service_10",
                "price": 15,
                "available": true
            }
          ]
    },
    "message": "Phone services retrieved successfully"
}</code></pre>
                    </div>
                    <div id="get-payments">
                        <h3>GET /payments</h3>
                        <p>List payments.</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/payments?filters[status]=finished&filters[created_date]=2026-04-01" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
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
    "to": 1,
    "total": 1
  },
  "message": "Payments fetched successfully"
}</code></pre>
                    </div>

                    <div id="errors">
                        <h2>Error Responses</h2>
                        <p>Errors return a JSON response with a message and error details.</p>
                        <pre><code class="language-json">{
  "success": false,
  "message": "Unauthorized",
  "data": {
    "error": "Unauthenticated."
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
