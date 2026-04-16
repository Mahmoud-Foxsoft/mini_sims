@php
    $baseUrl = rtrim(config('app.url'), '/') . '/api/v1';
@endphp

@extends('clinic.layout')

@section('title', 'API Docs')
@section('description', 'FasterVerify external API documentation')
@section('keywords', 'api, docs, sms, numbers')
@section('body_class', 'docs-page')

@section('content')
    <div class="page-title">
        <div class="heading">
            <div class="container">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1 class="heading-title">API Documentation</h1>
                        <p class="mb-0">Use the external API to fetch and manage services, orders, payments, and messages.</p>
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
                        <a href="#filtering">Filtering</a>
                        <a href="#endpoints">Endpoints</a>
                        <a href="#get-me">GET /me</a>
                        <a href="#get-orders">GET /orders</a>
                        <a href="#post-orders">POST /orders</a>
                        <a href="#get-phone-numbers">GET /phone-numbers</a>
                        <a href="#post-phone-numbers-cancel">POST /phone-numbers/{id}/cancel</a>
                        <a href="#post-phone-numbers-reuse">POST /phone-numbers/{id}/reuse</a>
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
                        <p><a href="/fasterverify-api.postman_collection.json" download>Download Postman Collection</a></p>
                    </div>

                    <div id="filtering">
                        <h2>Filtering</h2>
                        <p>Many of our list endpoints support filtering to help you find specific records. When an endpoint supports filtering, you can pass filters as query parameters using the <code>filters[field_name]=value</code> syntax.</p>
                        <p>To use multiple filters at once, simply chain them together with an ampersand (<code>&</code>).</p>
                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/endpoint?filters[status]=active&filters[type]=example" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>
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

                        <h4>Available Filters</h4>
                        <ul class="mb-3">
                            <li><code>filters[created_at]</code> - Filter orders by creation date (e.g., 2026-04-01).</li>
                            <li><code>filters[status]</code> - Filter orders by their status (e.g., completed).</li>
                        </ul>

                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/orders" \
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

                    <div id="post-orders">
                        <h3>POST /orders</h3>
                        <p>Create a new order for phone numbers.</p>

                        <h4>Request Body</h4>
                        <ul class="mb-3">
                            <li><code>service_code</code> (required, string) - The code of the service you want to order.</li>
                            <li><code>quantity</code> (required, integer) - The amount of numbers to order.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X POST "{{ $baseUrl }}/orders" \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{
    "service_code": "service_10",
    "quantity": 1
  }'</code></pre>

                        <h4>Success Response</h4>
                        <pre><code class="language-json">{
    "success": true,
    "data": {
        "order": {
            "user_id": 1,
            "total_cent_price": 1500,
            "status": "completed",
            "id": "019d9671-ad34-7289-95df-43241a8152ac",
            "updated_at": "2026-04-16T13:18:45.000000Z",
            "created_at": "2026-04-16T13:18:45.000000Z"
        },
        "total_price": 15,
        "items_count": 1,
        "numbers": [
            {
                "id": 25,
                "order_id": "019d9671-ad34-7289-95df-43241a8152ac",
                "user_id": 1,
                "service_name": "Service 10",
                "phone_number": "15550000004",
                "price_cents": 1500,
                "status": "pending",
                "created_at": "2026-04-16T13:18:45.000000Z",
                "updated_at": "2026-04-16T13:18:45.000000Z"
            }
        ]
    },
    "message": "Order created successfully."
}</code></pre>
                    </div>

                    <div id="get-phone-numbers">
                        <h3>GET /phone-numbers</h3>
                        <p>List order items and include received messages.</p>

                        <h4>Available Filters</h4>
                        <ul class="mb-3">
                            <li><code>filters[service_name]</code> - Filter by the service name (e.g., telegram).</li>
                            <li><code>filters[status]</code> - Filter by the order item status (e.g., pending).</li>
                            <li><code>filters[phone_number]</code> - Filter by a specific phone number.</li>
                            <li><code>filters[order_id]</code> - Filter by a specific order ID.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/phone-numbers" \
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

                    <div id="post-phone-numbers-cancel">
                        <h3>POST /phone-numbers/{id}/cancel</h3>
                        <p>Cancel a pending phone number order item.</p>

                        <h4>Path Parameters</h4>
                        <ul class="mb-3">
                            <li><code>id</code> (required, integer) - The ID of the phone number.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X POST "{{ $baseUrl }}/phone-numbers/25/cancel" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
                        <pre><code class="language-json">{
  "success": true,
  "data": null,
  "message": "Phone number cancelled successfully."
}</code></pre>
                    </div>

                    <div id="post-phone-numbers-reuse">
                        <h3>POST /phone-numbers/{id}/reuse</h3>
                        <p>Reuse a previously ordered phone number.</p>

                        <h4>Path Parameters</h4>
                        <ul class="mb-3">
                            <li><code>id</code> (required, integer) - The ID of the phone number.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X POST "{{ $baseUrl }}/phone-numbers/25/reuse" \
  -H "Authorization: Bearer &lt;token&gt;"</code></pre>

                        <h4>Success Response</h4>
                        <pre><code class="language-json">{
  "success": true,
  "data": null,
  "message": "Phone number reused successfully."
}</code></pre>
                    </div>

                    <div id="get-transactions">
                        <h3>GET /transactions</h3>
                        <p>Get your transaction history.</p>

                        <h4>Available Filters</h4>
                        <ul class="mb-3">
                            <li><code>filters[type]</code> - Filter by the transaction type (e.g., credit, debit).</li>
                            <li><code>filters[source]</code> - Filter by the source of the transaction (e.g., payment).</li>
                            <li><code>filters[reference]</code> - Filter by a specific reference identifier.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/transactions" \
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

                        <h4>Available Filters</h4>
                        <ul class="mb-3">
                            <li><code>filters[name]</code> - Search for a service by its name (e.g., Telegram, WhatsApp).</li>
                            <li><code>filters[code]</code> - Filter for a service using its exact unique identifier code.</li>
                            <li><code>filters[price]</code> - Filter for a service using its price.</li>
                        </ul>

                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/services" \
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

                        <h4>Available Filters</h4>
                        <ul class="mb-3">
                            <li><code>filters[status]</code> - Filter by the payment status (e.g., finished, pending).</li>
                            <li><code>filters[created_date]</code> - Filter payments by their creation date (e.g., 2026-04-01).</li>
                        </ul>

                        <pre><code class="language-bash">curl -X GET "{{ $baseUrl }}/payments" \
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