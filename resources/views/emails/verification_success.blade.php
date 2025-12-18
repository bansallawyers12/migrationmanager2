<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified Successfully</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
        }
        .success-container {
            background-color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            max-width: 550px;
            margin: 20px auto;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #059669;
            border-radius: 50%;
            display: inline-block;
            line-height: 80px;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #ffffff;
            font-weight: bold;
        }
        h1 {
            color: #059669;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }
        p {
            color: #1a1a1a;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .email-display {
            background-color: #f0fdf4;
            border: 2px solid #059669;
            padding: 15px 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 25px 0;
            font-size: 16px;
            word-break: break-all;
        }
        .success-message {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 15px 20px;
            margin: 25px 0;
            text-align: left;
        }
        .success-message p {
            margin: 0;
            color: #1a1a1a;
            font-size: 15px;
        }
        .close-button {
            margin-top: 25px;
            padding: 15px 40px;
            background-color: #059669;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .company-name {
            margin-top: 25px;
            color: #999999;
            font-size: 14px;
        }
        .company-name strong {
            color: #059669;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Email Verified Successfully!</h1>
        <p>Your email address has been verified:</p>
        <div class="email-display">{{ $clientEmail->email }}</div>
        
        <div class="success-message">
            <p><strong>Verification Complete</strong></p>
            <p style="margin-top: 10px;">You can now receive important updates and communications from Bansal Immigration.</p>
        </div>
        
        <p>Thank you for confirming your email address. You can now close this window.</p>
        <button class="close-button" onclick="window.close()" style="background-color: #059669; color: #ffffff; padding: 15px 40px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer;">Close Window</button>
        
        <div class="company-name">
            Verified by <strong>Bansal Immigration</strong>
        </div>
    </div>
</body>
</html>
