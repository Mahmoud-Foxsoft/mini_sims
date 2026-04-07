<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        /* Reset & Base */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #374151;
        }

        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Header */
        .header {
            padding: 40px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .icon-wrapper {
            display: inline-block;
            background-color: #d1fae5; /* Soft success green */
            padding: 16px;
            border-radius: 50%;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.025em;
        }

        .header p {
            margin: 8px 0 0;
            font-size: 15px;
            color: #6b7280;
        }

        /* Content */
        .content {
            padding: 30px 40px;
            font-size: 16px;
            line-height: 1.6;
        }

        .content p {
            margin: 0 0 16px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        /* Amount Section */
        .amount-container {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 32px 20px;
            text-align: center;
            margin: 32px 0;
        }

        .amount-label {
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .amount-value {
            font-size: 42px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1;
            letter-spacing: -0.025em;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 32px 0;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5; /* Brand Indigo */
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s ease;
        }

        .button:hover {
            background-color: #4338ca;
        }

        /* Notice Box */
        .notice-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-radius: 0 4px 4px 0;
        }

        .notice-box p {
            margin: 0;
            font-size: 14px;
            color: #1e3a8a;
        }

        /* Footer */
        .footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 0 0 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 20px 10px;
            }
            .header {
                padding: 30px 20px 15px;
            }
            .content {
                padding: 20px;
            }
            .amount-container {
                padding: 24px 15px;
            }
            .amount-value {
                font-size: 36px;
            }
            .button {
                display: block;
                width: auto;
            }
            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="email-container">
            <div class="header">
                <div class="icon-wrapper">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h1>Payment Successful</h1>
                <p>Thank you for your purchase.</p>
            </div>

            <div class="content">
                <p class="greeting">Hi {{ $user->name }},</p>
                <p>We’re happy to let you know that your recent payment was processed successfully. Here are the details of your transaction:</p>

                <div class="amount-container">
                    <span class="amount-label">Amount Paid</span>
                    <p class="amount-value">{{ $amount }}</p>
                </div>

                <p>You can review your plan, check your usage, or download your detailed receipt anytime by visiting your account dashboard.</p>

                <div class="button-container">
                    <a href="{{ route('console') }}" class="button">Go to My Dashboard</a>
                </div>

                <div class="notice-box">
                    <p>ℹ️ If you didn’t make this payment or have any questions regarding this transaction, please contact our support team right away.</p>
                </div>

                <p style="margin-top: 30px; margin-bottom: 0;">
                    Thanks for choosing <strong>{{ config('app.name') }}</strong>!<br>
                    <strong>The {{ config('app.name') }} Team</strong>
                </p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p><a href="{{ config('app.url') }}">Visit Our Website</a></p>
            </div>
        </div>
    </div>
</body>
</html>