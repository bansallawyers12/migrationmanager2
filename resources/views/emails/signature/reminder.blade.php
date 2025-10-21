<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Signature Reminder</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
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
        .reminder-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 20px;
        }
        .message {
            color: #4b5563;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .urgent-notice {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .urgent-notice .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .urgent-notice h3 {
            margin: 10px 0;
            color: #dc2626;
            font-size: 18px;
        }
        .urgent-notice p {
            margin: 5px 0;
            color: #991b1b;
            font-size: 14px;
        }
        .document-info {
            background: #f8fafc;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .document-info h3 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 16px;
        }
        .document-info p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .due-date-warning {
            color: #dc2626;
            font-weight: 600;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 50px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 18px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 38, 38, 0.4);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .help-section {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .help-section h4 {
            margin: 0 0 10px 0;
            color: #1e3a8a;
            font-size: 14px;
        }
        .help-section p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 13px;
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
            color: #dc2626;
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
                padding: 14px 40px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">⏰ Bansal Migration</div>
            <h1>Document Signature Reminder</h1>
            <div class="reminder-badge">Reminder #{{ $reminderNumber }}</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">Dear {{ $signerName }},</p>
            
            <!-- Urgent Notice -->
            <div class="urgent-notice">
                <div class="icon">⚠️</div>
                <h3>Action Required: Document Awaiting Your Signature</h3>
                <p>This is a friendly reminder that a document is still pending your signature.</p>
            </div>

            <p class="message">
                We previously sent you a document that requires your signature, but we haven't received 
                it yet. Your prompt attention to this matter would be greatly appreciated.
            </p>

            <!-- Document Info -->
            <div class="document-info">
                <h3>📄 Document Details</h3>
                <p><strong>Document:</strong> {{ $documentTitle }}</p>
                <p><strong>Reminder:</strong> #{{ $reminderNumber }} of 3</p>
                @if($dueDate)
                    <p class="due-date-warning"><strong>⏰ Due Date:</strong> {{ $dueDate }}</p>
                @endif
            </div>

            <!-- Call to Action -->
            <div class="button-container">
                <a href="{{ $signingUrl }}" class="cta-button">
                    ✍️ Sign Now
                </a>
            </div>

            <p class="message">
                <strong>Why is this important?</strong><br>
                Your signature is required to proceed with your matter. Any delays in signing this document 
                may impact the progress of your case or application.
            </p>

            <!-- Help Section -->
            <div class="help-section">
                <h4>🆘 Need Help?</h4>
                <p>If you're experiencing any issues with the signing process or have questions about 
                the document, please contact us immediately:</p>
                <p>📧 <a href="mailto:info@bansalimmigration.com.au" style="color: #3b82f6;">info@bansalimmigration.com.au</a></p>
                <p>📞 <a href="tel:+61292673945" style="color: #3b82f6;">+61 2 9267 3945</a></p>
            </div>

            <p class="message">
                We understand that you may have a busy schedule, but your timely response is crucial 
                for the progress of your immigration matter.
            </p>

            <p class="message" style="margin-top: 30px;">
                <strong>Thank you for your cooperation,</strong><br>
                Bansal Migration Team
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
                This is an automated reminder. Please do not reply to this email.<br>
                You will receive up to 3 reminders, with 24 hours between each reminder.
            </p>
        </div>
    </div>
</body>
</html>

