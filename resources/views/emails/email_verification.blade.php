<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #4f46e5;
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-body p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #1a1a1a;
            line-height: 1.6;
        }
        .email-highlight {
            color: #4f46e5;
            font-weight: bold;
        }
        .verify-button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternative-link {
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 2px dashed #cccccc;
            font-size: 13px;
            word-break: break-all;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            color: #1a1a1a;
            font-weight: bold;
        }
        .alternative-link a {
            color: #4f46e5;
            text-decoration: none;
        }
        .email-footer {
            padding: 25px 20px;
            background-color: #f8f9fa;
            text-align: center;
            font-size: 14px;
            color: #1a1a1a;
            border-top: 1px solid #e0e0e0;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin-top: 25px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .info-box {
            background-color: #e7f3ff;
            border: 1px solid #2563eb;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0 0 12px 0;
            color: #1a1a1a;
            font-weight: bold;
            font-size: 15px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #1a1a1a;
        }
        .info-box li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-box li strong {
            color: #1a1a1a;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Verify Your Email Address</h1>
        </div>
        
        <div class="email-body">
            <p>Hello,</p>
            
            <p>Thank you for providing your email address <strong class="email-highlight">{{ $clientEmail->email }}</strong> to <strong>Bansal Immigration</strong>.</p>
            
            <p>To complete your email verification and ensure we can communicate with you effectively, please click the button below:</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button" style="display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Verify My Email Address
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
                <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
            </div>
            
            <div class="warning-box">
                <p>
                    <strong>Important:</strong> Never share this link with anyone. Our staff will never ask you for this link.
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            <p>This is an automated email from <strong>Bansal Immigration</strong>.</p>
            <p>If you have any questions, please contact our office.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} Bansal Immigration. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
