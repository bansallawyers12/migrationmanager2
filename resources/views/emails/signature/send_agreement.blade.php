<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Signature Request</title>
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
        .custom-message {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .document-info {
            background-color: #f8f9fa;
            border-left: 4px solid #059669;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .document-info h3 {
            margin: 0 0 10px 0;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: bold;
        }
        .document-info p {
            margin: 5px 0;
            color: #1a1a1a;
            font-size: 14px;
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
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .attachments {
            background-color: #e7f3ff;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .attachments h4 {
            margin: 0 0 10px 0;
            color: #1a1a1a;
            font-size: 14px;
            font-weight: bold;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            margin: 5px 0;
            color: #1a1a1a;
            font-size: 13px;
        }
        .email-footer a {
            color: #059669;
            text-decoration: none;
        }
        .footer-links {
            margin-top: 15px;
        }
        .footer-links a {
            color: #666666;
            text-decoration: none;
            font-size: 12px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Agreement Signature Request</h1>
        </div>

        <div class="email-body">
            <p class="greeting">Dear {{ $signerName }},</p>
            
            @if(isset($emailMessage) && !empty($emailMessage))
            <div class="custom-message">
                {!! nl2br(e($emailMessage)) !!}
            </div>
            @else
            <p class="message">
                We have prepared an important agreement document that requires your review and signature.
                Please take a moment to carefully review the terms and conditions outlined in the document.
            </p>
            @endif

            <div class="document-info">
                <h3>Agreement Details</h3>
                <p><strong>Document:</strong> {{ $documentTitle }}</p>
                <p><strong>Type:</strong> Cost Agreement</p>
                @if($dueDate)
                    <p><strong>Due Date:</strong> {{ $dueDate }}</p>
                @endif
            </div>

            <div class="attachments">
                <h4>Additional Information</h4>
                <p>This email may include additional documents and information related to your matter. 
                Please review all attached materials carefully before signing.</p>
            </div>

            <div class="button-container">
                <a href="{{ $signingUrl }}" class="cta-button" style="display: inline-block; background-color: #059669; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Review & Sign Agreement
                </a>
            </div>

            <div class="note">
                <strong>Important Legal Notice:</strong> By signing this agreement, you acknowledge that you have 
                read, understood, and agree to the terms and conditions outlined in the document. This is a legally 
                binding agreement. Please contact us if you have any questions before signing.
            </div>

            <p class="message">
                If you have any questions about this agreement or need clarification on any terms, 
                please don't hesitate to contact our office before proceeding with the signature.
            </p>

            <p class="message" style="margin-top: 30px;">
                <strong>Warm regards,</strong><br>
                Bansal Migration Team
            </p>

            @if(isset($emailSignature) && !empty($emailSignature))
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    {!! $emailSignature !!}
                </div>
            @endif
        </div>

        <div class="email-footer">
            <p><strong>Bansal Migration</strong></p>
            <p>Immigration & Visa Services</p>
            <p>Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a></p>
            <p>Phone: <a href="tel:+61292673945">+61 2 9267 3945</a></p>
            <p>Website: <a href="https://www.bansalimmigration.com.au">www.bansalimmigration.com.au</a></p>
            
            <div class="footer-links">
                <a href="https://www.bansalimmigration.com.au/privacy-policy">Privacy Policy</a> | 
                <a href="https://www.bansalimmigration.com.au/terms">Terms of Service</a> |
                <a href="https://www.bansalimmigration.com.au/contact">Contact Us</a>
            </div>
            
            <p style="margin-top: 15px; font-size: 11px; color: #999999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
