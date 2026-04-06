<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fbfd;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .email-container {
            max-width: 580px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5eef7;
        }

        .header {
            background: linear-gradient(135deg, #007bff 0%, #0099ff 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header p {
            margin-top: 10px;
            font-size: 17px;
            opacity: 0.9;
        }

        .content {
            padding: 35px 45px;
            color: #333333;
            line-height: 1.7;
            font-size: 16px;
        }

        .content p {
            margin-bottom: 20px;
        }

        /* ✅ Updated Reset Box */
        .reset-box {
            padding: 30px 0;
            margin: 30px 0;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        .reset-box .label {
            font-size: 18px;
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 15px;
            display: block;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 17px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.25);
        }

        .button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .footer {
            background-color: #f0f5f9;
            color: #8898a9;
            text-align: center;
            padding: 30px;
            font-size: 14px;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            border-top: 1px solid #e5eef7;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
            <p>We received a request to reset your password.</p>
        </div>

        <div class="content">
            <p>Hi <strong>{{ $user->name }}</strong>,</p>
            <p>You are receiving this email because we received a password reset request for your account
                (<strong>{{ $user->email }}</strong>).</p>

            <div class="reset-box">
                <span class="label">Click the button below to reset your password:</span>
                <div class="button-container">
                    <a href="{{ $url }}">Reset Password</a>
                </div>
            </div>

            <p>This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.</p>

            <p>If you did not request a password reset, no further action is required.</p>

            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p><a href="{{ config('app.url') }}">Visit Our Website</a></p>
        </div>
    </div>
</body>

</html>
