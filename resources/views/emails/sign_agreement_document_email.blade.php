<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Document Signature Request</title>
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
            background-color: #059669;
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
        .custom-message-box {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .custom-message-box p {
            margin: 0;
            color: #1a1a1a;
            line-height: 1.6;
            font-size: 15px;
        }
        .cta-button {
            display: inline-block;
            background-color: #059669;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
        }
        .attachments-notice {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 15px 20px;
            margin: 20px 0;
            font-size: 14px;
            color: #1a1a1a;
        }
        .attachments-notice strong {
            color: #1a1a1a;
            font-weight: bold;
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
            color: #059669;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Agreement Signature Request</h1>
        </div>
        
        <div class="email-body">
            <p class="greeting">Dear {{ $firstName }},</p>
            
            @if(!empty($emailmessage))
            <div class="custom-message-box">
                {!! nl2br(e($emailmessage)) !!}
            </div>
            @else
            <p class="message">
                We have prepared an important agreement document that requires your review and signature.
                Please take a moment to carefully review the terms and conditions outlined in the document.
            </p>
            @endif
            
            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ $signingUrl }}" class="cta-button" style="display: inline-block; background-color: #059669; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Review & Sign Agreement
                </a>
            </div>
            
            <div class="attachments-notice">
                <strong>Note:</strong> This email may contain important attachments related to your agreement. 
                Please review all documents before signing.
            </div>
            
            <div class="signature-section">
                <p class="signature-text"><strong>Regards,</strong></p>
                <p class="signature-name">Bansal Migration Team</p>
            </div>
            
            @if(isset($emailSignature) && !empty($emailSignature))
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                {!! $emailSignature !!}
            </div>
            @endif
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
