<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
            background-color: #eef2ff;
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

        .highlight {
            font-weight: 600;
            color: #111827;
        }

        /* OTP Section */
        .otp-container {
            background-color: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 32px 20px;
            text-align: center;
            margin: 32px 0;
        }

        .otp-label {
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 42px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 8px;
            margin: 0;
        }

        /* Notice */
        .notice-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-radius: 0 4px 4px 0;
        }

        .notice-box p {
            margin: 0;
            font-size: 14px;
            color: #991b1b;
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
            .otp-container {
                padding: 24px 15px;
            }
            .otp-code {
                font-size: 36px;
                letter-spacing: 6px;
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
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <h1>Verify Your Email</h1>
            </div>

            <div class="content">
                <p class="greeting">Hi {{ $user->name }},</p>
                <p>Welcome to <strong>{{ config('app.name') }}</strong>! To complete your registration and secure your account, please verify your email address (<span class="highlight">{{ $user->email }}</span>).</p>

                <div class="otp-container">
                    <span class="otp-label">Verification Code</span>
                    <p class="otp-code">{{ $user->otp }}</p>
                </div>

                <div class="notice-box">
                    <p>⏱️ This code will expire in <strong>30 minutes</strong>.</p>
                </div>

                <p>If you didn't attempt to register an account with us, you can safely ignore and delete this email.</p>

                <p style="margin-top: 30px; margin-bottom: 0;">
                    Best regards,<br>
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