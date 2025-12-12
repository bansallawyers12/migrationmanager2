<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Document Signature Request</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Main styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f5f7fa;
            padding: 20px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 20s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .logo-icon {
            font-size: 48px;
            margin-bottom: 12px;
            display: block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-subtitle {
            margin-top: 8px;
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
        }
        
        /* Body */
        .email-body {
            padding: 45px 35px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        
        .message {
            color: #475569;
            margin-bottom: 24px;
            line-height: 1.75;
            font-size: 15px;
        }
        
        /* Document Info Card */
        .document-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-left: 5px solid #3b82f6;
            padding: 24px;
            margin: 28px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        
        .document-info-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .document-icon {
            font-size: 24px;
            margin-right: 12px;
        }
        
        .document-info h3 {
            margin: 0;
            color: #1e40af;
            font-size: 17px;
            font-weight: 600;
        }
        
        .document-info-item {
            margin: 10px 0;
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .document-info-item strong {
            color: #1e293b;
            font-weight: 600;
            min-width: 90px;
            display: inline-block;
        }
        
        /* CTA Button */
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 48px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .cta-button:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.45);
            transform: translateY(-2px);
        }
        
        .cta-button:active {
            transform: translateY(0);
        }
        
        /* Warning Note */
        .note {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 5px solid #f59e0b;
            padding: 18px 22px;
            margin: 28px 0;
            border-radius: 8px;
            font-size: 14px;
            color: #92400e;
            line-height: 1.6;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.1);
        }
        
        .note strong {
            color: #78350f;
            font-weight: 600;
        }
        
        .note-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 18px;
            vertical-align: middle;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 35px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }
        
        .signature-text {
            color: #475569;
            font-size: 15px;
            line-height: 1.75;
            margin-bottom: 8px;
        }
        
        .signature-name {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
            margin-top: 4px;
        }
        
        /* Footer */
        .email-footer {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 35px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-brand {
            margin-bottom: 20px;
        }
        
        .footer-brand-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }
        
        .footer-brand-tagline {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
        
        .footer-contact {
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .footer-contact-item {
            margin: 10px 0;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .footer-contact-item a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .footer-contact-item a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        
        .footer-links {
            margin: 20px 0;
            padding: 0;
        }
        
        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
            margin: 0 12px;
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: #3b82f6;
        }
        
        .footer-disclaimer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
        
        /* Email Signature Block */
        .email-signature-block {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px 0;
            }
            
            .email-container {
                border-radius: 0;
                margin: 0;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 22px;
            }
            
            .logo-icon {
                font-size: 40px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .cta-button {
                padding: 14px 36px;
                font-size: 15px;
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
            
            .document-info {
                padding: 20px;
            }
            
            .email-footer {
                padding: 25px 20px;
            }
            
            .footer-links a {
                display: block;
                margin: 8px 0;
            }
        }
        
        /* Dark mode support (for email clients that support it) */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #ffffff;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="header-content">
                    <span class="logo-icon">🔏</span>
                    <h1>Document Signature Request</h1>
                    <div class="header-subtitle">Bansal Migration Immigration & Visa Services</div>
                </div>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p class="greeting">Dear {{ $signerName }},</p>
                
                <p class="message">
                    {{ $emailMessage ?? 'Please review and sign the attached document.' }}
                </p>

                <!-- Document Info Card -->
                <div class="document-info">
                    <div class="document-info-header">
                        <span class="document-icon">📄</span>
                        <h3>Document Details</h3>
                    </div>
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

                <!-- Call to Action Button -->
                <div class="button-container">
                    <a href="{{ $signingUrl }}" class="cta-button">
                        ✍️ Review & Sign Document
                    </a>
                </div>

                <!-- Important Note -->
                <div class="note">
                    <span class="note-icon">⚠️</span>
                    <strong>Important:</strong> This link is unique to you and should not be shared. 
                    It will expire once the document is signed or after the due date.
                </div>

                <p class="message">
                    If you have any questions or need assistance, please don't hesitate to contact us.
                </p>

                <p class="message">
                    Thank you for your prompt attention to this matter.
                </p>

                <!-- Signature -->
                <div class="signature-section">
                    <p class="signature-text"><strong>Regards,</strong></p>
                    <p class="signature-name">Bansal Migration Team</p>
                </div>

                <!-- Email Signature Block -->
                @if(isset($emailSignature) && !empty($emailSignature))
                <div class="email-signature-block">
                    {!! $emailSignature !!}
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <div class="footer-brand">
                    <div class="footer-brand-name">Bansal Migration</div>
                    <div class="footer-brand-tagline">Immigration & Visa Services</div>
                </div>
                
                <div class="footer-contact">
                    <div class="footer-contact-item">
                        📧 Email: <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a>
                    </div>
                    <div class="footer-contact-item">
                        🌐 Website: <a href="https://www.bansalimmigration.com.au" target="_blank">www.bansalimmigration.com.au</a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <a href="https://www.bansalimmigration.com.au/privacy-policy" target="_blank">Privacy Policy</a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="https://www.bansalimmigration.com.au/terms" target="_blank">Terms of Service</a>
                </div>
                
                <div class="footer-disclaimer">
                    This is an automated message. Please do not reply to this email.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
