<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Signature Request</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #047857 0%, #10b981 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #047857;
            margin-bottom: 20px;
        }
        .message {
            color: #4b5563;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .custom-message {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .document-info {
            background: #f8fafc;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .document-info h3 {
            margin: 0 0 10px 0;
            color: #047857;
            font-size: 16px;
        }
        .document-info p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #92400e;
        }
        .attachments {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .attachments h4 {
            margin: 0 0 10px 0;
            color: #1e3a8a;
            font-size: 14px;
        }
        .email-footer {
            background: #f8fafc;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 13px;
        }
        .email-footer a {
            color: #10b981;
            text-decoration: none;
        }
        .footer-links {
            margin-top: 15px;
        }
        .footer-links a {
            color: #6b7280;
            text-decoration: none;
            margin: 0 10px;
            font-size: 12px;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .email-header h1 {
                font-size: 20px;
            }
            .cta-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">📋 Bansal Migration</div>
            <h1>Agreement Signature Request</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">Dear {{ $signerName }},</p>
            
            <!-- Custom Message -->
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

            <!-- Document Info -->
            <div class="document-info">
                <h3>📄 Agreement Details</h3>
                <p><strong>Document:</strong> {{ $documentTitle }}</p>
                <p><strong>Type:</strong> Cost Agreement</p>
                @if($dueDate)
                    <p><strong>Due Date:</strong> {{ $dueDate }}</p>
                @endif
            </div>

            <!-- Attachments Notice (if any) -->
            <div class="attachments">
                <h4>📎 Additional Information</h4>
                <p>This email may include additional documents and information related to your matter. 
                Please review all attached materials carefully before signing.</p>
            </div>

            <!-- Call to Action -->
            <div class="button-container">
                <a href="{{ $signingUrl }}" class="cta-button">
                    ✍️ Review & Sign Agreement
                </a>
            </div>

            <!-- Legal Note -->
            <div class="note">
                <strong>⚠️ Important Legal Notice:</strong> By signing this agreement, you acknowledge that you have 
                read, understood, and agree to the terms and conditions outlined in the document. This is a legally 
                binding agreement. Please contact us if you have any questions before signing.
            </div>

            <p class="message">
                If you have any questions about this agreement or need clarification on any terms, 
                please don't hesitate to contact our office before proceeding with the signature.
            </p>

            <p class="message" style="margin-top: 30px;">
                <strong>Warm regards,</strong><br>
                Bansal Migration Team<br>
                <em>Your trusted immigration partner</em>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Bansal Migration</strong></p>
            <p>Immigration & Visa Services</p>
            <p>📧 Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a></p>
            <p>📞 Phone: <a href="tel:+61292673945">+61 2 9267 3945</a></p>
            <p>🌐 Website: <a href="https://www.bansalimmigration.com.au">www.bansalimmigration.com.au</a></p>
            
            <div class="footer-links">
                <a href="https://www.bansalimmigration.com.au/privacy-policy">Privacy Policy</a> | 
                <a href="https://www.bansalimmigration.com.au/terms">Terms of Service</a> |
                <a href="https://www.bansalimmigration.com.au/contact">Contact Us</a>
            </div>
            
            <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                This is an automated message. Please do not reply to this email.<br>
                For inquiries, please contact us through our official channels.
            </p>
        </div>
    </div>
</body>
</html>

