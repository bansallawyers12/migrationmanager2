<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .verify-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: scale(1.05);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternative-link {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
            word-break: break-all;
        }
        .email-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .warning-text {
            color: #dc3545;
            font-size: 14px;
            margin-top: 20px;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📧 Verify Your Email Address</h1>
        </div>
        
        <div class="email-body">
            <p>Hello,</p>
            
            <p>Thank you for providing your email address <strong>{{ $clientEmail->email }}</strong> to <strong>{{ config('app.name') }}</strong>.</p>
            
            <p>To complete your email verification and ensure we can communicate with you effectively, please click the button below:</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✓ Verify My Email Address
                </a>
            </div>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>Important:</strong></p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>This link expires in <strong>{{ $expiresAt->diffForHumans() }}</strong> ({{ $expiresAt->format('M j, Y g:i A') }})</li>
                    <li>This link can only be used once</li>
                    <li>If you didn't request this verification, please ignore this email</li>
                </ul>
            </div>
            
            <div class="alternative-link">
                <p><strong>If the button doesn't work, copy and paste this link into your browser:</strong></p>
                <p><a href="{{ $verificationUrl }}" style="color: #667eea;">{{ $verificationUrl }}</a></p>
            </div>
            
            <p class="warning-text">
                ⚠️ Never share this link with anyone. Our staff will never ask you for this link.
            </p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated email from {{ config('app.name') }}.</p>
            <p>If you have any questions, please contact our office.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
