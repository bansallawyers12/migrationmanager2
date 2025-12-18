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
        .header-subtitle {
            margin-top: 8px;
            font-size: 14px;
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
        .document-info {
            background-color: #f8f9fa;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 25px 0;
        }
        .document-info h3 {
            margin: 0 0 15px 0;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: bold;
        }
        .document-info-item {
            margin: 10px 0;
            color: #1a1a1a;
            font-size: 15px;
            line-height: 1.6;
        }
        .document-info-item strong {
            color: #1a1a1a;
            font-weight: bold;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
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
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 25px 0;
            font-size: 14px;
            color: #856404;
            line-height: 1.6;
        }
        .note strong {
            color: #856404;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
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
        .footer-brand-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .footer-brand-tagline {
            font-size: 13px;
            color: #666666;
        }
        .footer-contact {
            margin: 15px 0;
            padding: 15px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        .footer-contact-item {
            margin: 8px 0;
            color: #1a1a1a;
            font-size: 14px;
        }
        .footer-contact-item a {
            color: #2563eb;
            text-decoration: none;
        }
        .footer-links {
            margin: 15px 0;
        }
        .footer-links a {
            color: #666666;
            text-decoration: none;
            font-size: 13px;
            margin: 0 10px;
        }
        .footer-disclaimer {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            font-size: 11px;
            color: #999999;
        }
        .email-signature-block {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Document Signature Request</h1>
            <div class="header-subtitle">Bansal Migration Immigration & Visa Services</div>
        </div>

        <div class="email-body">
            <p class="greeting">Dear {{ $signerName }},</p>
            
            <p class="message">
                {{ $emailMessage ?? 'Please review and sign the attached document.' }}
            </p>

            <div class="document-info">
                <h3>Document Details</h3>
                <div class="document-info-item">
                    <strong>Document:</strong> {{ $documentTitle }}
                </div>
                <div class="document-info-item">
                    <strong>Type:</strong> {{ ucfirst($documentType ?? 'General') }}
                </div>
                @if(isset($dueDate) && $dueDate)
                <div class="document-info-item">
                    <strong>Due Date:</strong> {{ $dueDate }}
                </div>
                @endif
            </div>

            <div class="button-container">
                <a href="{{ $signingUrl }}" class="cta-button" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Review & Sign Document
                </a>
            </div>

            <div class="note">
                <strong>Important:</strong> This link is unique to you and should not be shared. 
                It will expire once the document is signed or after the due date.
            </div>

            <p class="message">
                If you have any questions or need assistance, please don't hesitate to contact us.
            </p>

            <p class="message">
                Thank you for your prompt attention to this matter.
            </p>

            <div class="signature-section">
                <p class="signature-text"><strong>Regards,</strong></p>
                <p class="signature-name">Bansal Migration Team</p>
            </div>

            @if(isset($emailSignature) && !empty($emailSignature))
            <div class="email-signature-block">
                {!! $emailSignature !!}
            </div>
            @endif
        </div>

        <div class="email-footer">
            <div class="footer-brand-name">Bansal Migration</div>
            <div class="footer-brand-tagline">Immigration & Visa Services</div>
            
            <div class="footer-contact">
                <div class="footer-contact-item">
                    Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a>
                </div>
                <div class="footer-contact-item">
                    Website: <a href="https://www.bansalimmigration.com.au" target="_blank">www.bansalimmigration.com.au</a>
                </div>
            </div>
            
            <div class="footer-links">
                <a href="https://www.bansalimmigration.com.au/privacy-policy" target="_blank">Privacy Policy</a> |
                <a href="https://www.bansalimmigration.com.au/terms" target="_blank">Terms of Service</a>
            </div>
            
            <div class="footer-disclaimer">
                This is an automated message. Please do not reply to this email.
            </div>
        </div>
    </div>
</body>
</html>
