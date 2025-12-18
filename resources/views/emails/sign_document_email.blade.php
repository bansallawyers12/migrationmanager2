<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Signature Request</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #2563eb;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        .message {
            color: #1a1a1a;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 15px;
        }
        .signing-link-box {
            background-color: #f8f9fa;
            border: 2px dashed #cccccc;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        .signing-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            word-break: break-all;
        }
        .cta-button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
        }
        .signature-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .signature-text {
            color: #1a1a1a;
            font-size: 15px;
            margin-bottom: 5px;
        }
        .signature-name {
            color: #1a1a1a;
            font-weight: bold;
            font-size: 15px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer-brand {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .footer-contact {
            margin: 15px 0;
            color: #1a1a1a;
            font-size: 13px;
        }
        .footer-contact a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Document Signature Request</h1>
        </div>
        
        <div class="email-body">
            <p class="greeting">Hi {{ $firstName }},</p>
            
            <p class="message">
                We have forwarded a document for signature. Please click the button below or use the link provided to access and sign the document.
            </p>
            
            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ $signingUrl }}" class="cta-button" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Sign Document Now
                </a>
            </div>
            
            <div class="signing-link-box">
                <div style="font-size: 13px; color: #666666; margin-bottom: 10px;">Or copy this link:</div>
                <a href="{{ $signingUrl }}" class="signing-link">{{ $signingUrl }}</a>
            </div>
            
            <div class="signature-section">
                <p class="signature-text"><strong>Regards,</strong></p>
                <p class="signature-name">Bansal Migration Team</p>
            </div>
        </div>
        
        <div class="email-footer">
            <div class="footer-brand">Bansal Migration</div>
            <div class="footer-contact">
                Immigration & Visa Services<br>
                Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a><br>
                Website: <a href="https://www.bansalimmigration.com.au" target="_blank">www.bansalimmigration.com.au</a>
            </div>
        </div>
    </div>
</body>
</html>
