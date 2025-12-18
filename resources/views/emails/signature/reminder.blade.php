<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Signature Reminder</title>
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
            background-color: #dc2626;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .reminder-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.3);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            margin-top: 10px;
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
        .urgent-notice {
            background-color: #fee2e2;
            border: 2px solid #dc2626;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .urgent-notice h3 {
            margin: 10px 0;
            color: #dc2626;
            font-size: 18px;
            font-weight: bold;
        }
        .urgent-notice p {
            margin: 5px 0;
            color: #991b1b;
            font-size: 14px;
        }
        .document-info {
            background-color: #f8f9fa;
            border-left: 4px solid #f59e0b;
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
        .due-date-warning {
            color: #dc2626;
            font-weight: bold;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
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
        .help-section {
            background-color: #e7f3ff;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .help-section h4 {
            margin: 0 0 10px 0;
            color: #1a1a1a;
            font-size: 14px;
            font-weight: bold;
        }
        .help-section p {
            margin: 5px 0;
            color: #1a1a1a;
            font-size: 13px;
        }
        .help-section a {
            color: #2563eb;
            text-decoration: none;
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
            color: #dc2626;
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
            <h1>Document Signature Reminder</h1>
            <div class="reminder-badge">Reminder #{{ $reminderNumber }}</div>
        </div>

        <div class="email-body">
            <p class="greeting">Dear {{ $signerName }},</p>
            
            <div class="urgent-notice">
                <h3>Action Required: Document Awaiting Your Signature</h3>
                <p>This is a friendly reminder that a document is still pending your signature.</p>
            </div>

            <p class="message">
                We previously sent you a document that requires your signature, but we haven't received 
                it yet. Your prompt attention to this matter would be greatly appreciated.
            </p>

            <div class="document-info">
                <h3>Document Details</h3>
                <p><strong>Document:</strong> {{ $documentTitle }}</p>
                <p><strong>Reminder:</strong> #{{ $reminderNumber }} of 3</p>
                @if($dueDate)
                    <p class="due-date-warning"><strong>Due Date:</strong> {{ $dueDate }}</p>
                @endif
            </div>

            <div class="button-container">
                <a href="{{ $signingUrl }}" class="cta-button" style="display: inline-block; background-color: #dc2626; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; font-size: 16px;">
                    Sign Now
                </a>
            </div>

            <p class="message">
                <strong>Why is this important?</strong><br>
                Your signature is required to proceed with your matter. Any delays in signing this document 
                may impact the progress of your case or application.
            </p>

            <div class="help-section">
                <h4>Need Help?</h4>
                <p>If you're experiencing any issues with the signing process or have questions about 
                the document, please contact us immediately:</p>
                <p>Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a></p>
                <p>Phone: <a href="tel:+61292673945">+61 2 9267 3945</a></p>
            </div>

            <p class="message">
                We understand that you may have a busy schedule, but your timely response is crucial 
                for the progress of your immigration matter.
            </p>

            <p class="message" style="margin-top: 30px;">
                <strong>Thank you for your cooperation,</strong><br>
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
                This is an automated reminder. Please do not reply to this email.<br>
                You will receive up to 3 reminders.
            </p>
        </div>
    </div>
</body>
</html>
