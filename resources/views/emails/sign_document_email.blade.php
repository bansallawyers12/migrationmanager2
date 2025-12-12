<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Document Signature Request</title>
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
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
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
        
        .signing-link-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        .signing-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            word-break: break-all;
            display: inline-block;
            padding: 12px 20px;
            background: #ffffff;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        
        .signing-link:hover {
            background: #eff6ff;
            border-color: #3b82f6;
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
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
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
            color: #1e40af;
            margin-bottom: 15px;
        }
        
        .footer-contact {
            margin: 15px 0;
            color: #64748b;
            font-size: 13px;
        }
        
        .footer-contact a {
            color: #3b82f6;
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
                <h1>🔏 Document Signature Request</h1>
            </div>
            
            <div class="email-body">
                <p class="greeting">Hi {{ $firstName }},</p>
                
                <p class="message">
                    We have forwarded a document for signature. Please click the button below or use the link provided to access and sign the document.
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $signingUrl }}" class="cta-button">
                        ✍️ Sign Document Now
                    </a>
                </div>
                
                <div class="signing-link-box">
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 10px;">Or copy this link:</div>
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
                    📧 <a href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a><br>
                    🌐 <a href="https://www.bansalimmigration.com.au" target="_blank">www.bansalimmigration.com.au</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
