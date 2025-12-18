<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
        }
        .error-container {
            background-color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            max-width: 550px;
            margin: 20px auto;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background-color: #dc2626;
            border-radius: 50%;
            display: inline-block;
            line-height: 80px;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #ffffff;
            font-weight: bold;
        }
        h1 {
            color: #dc2626;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }
        p {
            color: #1a1a1a;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .error-message {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px 20px;
            margin: 25px 0;
            text-align: left;
        }
        .error-message p {
            margin: 0;
            color: #856404;
            font-weight: bold;
            font-size: 15px;
        }
        .reasons-box {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        .reasons-box p {
            margin-bottom: 15px;
            font-weight: bold;
            color: #1a1a1a;
            font-size: 16px;
        }
        .reasons-box ul {
            margin: 0;
            padding-left: 20px;
            color: #1a1a1a;
        }
        .reasons-box li {
            margin-bottom: 10px;
            font-size: 15px;
            line-height: 1.6;
        }
        .help-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin-top: 25px;
            text-align: left;
        }
        .help-box p {
            margin: 0;
            color: #1a1a1a;
        }
        .help-box p:first-child {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .company-name {
            margin-top: 25px;
            color: #999999;
            font-size: 14px;
        }
        .company-name strong {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">✗</div>
        <h1>Verification Failed</h1>
        
        <div class="error-message">
            <p>{{ $message }}</p>
        </div>
        
        <div class="reasons-box">
            <p>Possible reasons:</p>
            <ul>
                <li>The verification link has expired (links are valid for 24 hours)</li>
                <li>The link has already been used</li>
                <li>The link is invalid or corrupted</li>
            </ul>
        </div>
        
        <div class="help-box">
            <p>Need help?</p>
            <p>Please contact Bansal Immigration office to request a new verification email.</p>
        </div>
        
        <div class="company-name">
            <strong>Bansal Immigration</strong>
        </div>
    </div>
</body>
</html>
