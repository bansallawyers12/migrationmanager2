<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Agreement Document Signature Request</title>
    <style>
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
        
        .email-header {
            background: linear-gradient(135deg, #047857 0%, #10b981 50%, #34d399 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .email-body {
            padding: 45px 35px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
        }
        
        .message {
            color: #475569;
            margin-bottom: 24px;
            line-height: 1.75;
            font-size: 15px;
        }
        
        .custom-message-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 5px solid #10b981;
            padding: 20px 24px;
            margin: 25px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
        }
        
        .custom-message-box p {
            margin: 0;
            color: #065f46;
            line-height: 1.75;
            font-size: 15px;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 48px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        
        .attachments-notice {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 22px;
            margin: 25px 0;
            font-size: 14px;
            color: #475569;
        }
        
        .attachments-notice strong {
            color: #1e293b;
        }
        
        .signature-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        
        .signature-text {
            color: #475569;
            font-size: 15px;
            margin-bottom: 8px;
        }
        
        .signature-name {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
        }
        
        .email-footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-brand {
            font-size: 18px;
            font-weight: 700;
            color: #047857;
            margin-bottom: 15px;
        }
        
        .footer-contact {
            margin: 15px 0;
            color: #64748b;
            font-size: 13px;
        }
        
        .footer-contact a {
            color: #10b981;
            text-decoration: none;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .cta-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>📋 Agreement Signature Request</h1>
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
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $signingUrl }}" class="cta-button">
                        ✍️ Review & Sign Agreement
                    </a>
                </div>
                
                <div class="attachments-notice">
                    <strong>📎 Note:</strong> This email may contain important attachments related to your agreement. 
                    Please review all documents before signing.
                </div>
                
                <div class="signature-section">
                    <p class="signature-text"><strong>Regards,</strong></p>
                    <p class="signature-name">Bansal Migration Team</p>
                </div>
                
                @if(isset($emailSignature) && !empty($emailSignature))
                <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
                    {!! $emailSignature !!}
                </div>
                @endif
            </div>
            
            <div class="email-footer">
                <div class="footer-brand">Bansal Migration</div>
                <div class="footer-contact">
                    Immigration & Visa Services<br>
                    📧 <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a><br>
                    🌐 <a href="https://www.bansalimmigration.com.au" target="_blank">www.bansalimmigration.com.au</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
