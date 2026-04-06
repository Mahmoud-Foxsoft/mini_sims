<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fbfd; /* Light blue-grey background */
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
            background: linear-gradient(135deg, #4CAF50 0%, #68B45E 100%); /* Green gradient */
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
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
            background-color: #e6ffed; /* Very light green */
            border: 2px solid #66bb6a; /* Success green border */
            border-radius: 12px;
            padding: 30px 20px;
            margin: 30px 0;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.8s ease-out; /* Simple fade-in animation */
        }
        .amount-display .label {
            font-size: 18px;
            color: #2e7d32;
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
        }
        .amount-display .amount {
            font-size: 48px; /* Large for impact */
            font-weight: 800;
            color: #1b5e20; /* Darker green */
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
            background-color: #007bff; /* Primary action blue */
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
        
        /* Responsive Styles */
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
                font-size: 28px;
            }
            .content {
                padding: 25px 30px;
            }
            .amount-display {
                padding: 25px 15px;
                margin: 25px 0;
            }
            .amount-display .amount {
                font-size: 40px;
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
        
        /* Keyframe for fade-in effect */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>💰 Payment Alert! 💰</h1>
            <p>A new payment has just been successfully processed.</p>
        </div>
        <div class="content">
            <p>Dear Admin,</p>
            <p>This is an automated notification to inform you of a recent successful transaction. The following amount has been received:</p>
            
            <div class="amount-display">
                <span class="label">Total Payment Amount:</span>
                <span class="amount">{{ $amount }}</span>
            </div>
            
            <p>You can find more detailed information about this transaction and others by logging into your admin dashboard.</p>
            
            <div class="button-container">
                <a href="{{ route('admin') }}" class="button">Go to Admin Dashboard</a>
            </div>
            
            <p>Thank you for your attention to this matter.</p>
            
            <p>Sincerely,<br>Your Application Team</p>
        </div>
        <div class="footer">
            <p>&copy;FoxProx. All rights reserved.</p>
            <p> <a href="{{ config('app.url')}}">Visit Our Website</a> </p>
        </div>
    </div>
</body>
</html>