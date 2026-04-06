<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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
            background: linear-gradient(135deg, #007bff 0%, #00bcd4 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 30px;
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
        .amount-display {
            background-color: #e6f2ff;
            border: 2px solid #007bff;
            border-radius: 12px;
            padding: 30px 20px;
            margin: 30px 0;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.8s ease-out;
        }
        .amount-display .label {
            font-size: 18px;
            color: #004085;
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
        }
        .amount-display .amount {
            font-size: 46px;
            font-weight: 800;
            color: #002b70;
            line-height: 1;
            letter-spacing: -1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }
        .button-container {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 17px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.25);
        }
        .button:hover {
            background-color: #388e3c;
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
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 12px;
            }
            .header {
                padding: 30px 20px;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
            }
            .header h1 {
                font-size: 26px;
            }
            .content {
                padding: 25px 30px;
            }
            .amount-display {
                padding: 25px 15px;
                margin: 25px 0;
            }
            .amount-display .amount {
                font-size: 38px;
            }
            .button {
                padding: 12px 25px;
                font-size: 16px;
            }
            .footer {
                padding: 25px;
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Payment Successful!</h1>
            <p>Thank you for your purchase — your payment has been received.</p>
        </div>

        <div class="content">
            <p>Dear {{ $user->name }},</p>

            <p>We’re happy to let you know that your payment was processed successfully.  
               Here are the details of your transaction:</p>

            <div class="amount-display">
                <span class="label">Amount Paid</span>
                <span class="amount">{{ $amount }}</span>
            </div>

            <p>You can review your plan, usage, or download details anytime by visiting your dashboard.</p>

            <div class="button-container">
                <a href="{{ route('console') }}" class="button">Go to My Dashboard</a>
            </div>

            <p>If you didn’t make this payment or have any questions, please contact our support team right away.</p>

            <p>Thanks for choosing <strong>{{ config('app.name') }}</strong>!<br>
            — The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p><a href="{{ config('app.url') }}">Visit Our Website</a></p>
        </div>
    </div>
</body>
</html>
