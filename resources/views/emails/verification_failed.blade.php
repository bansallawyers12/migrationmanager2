<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .error-container {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .error-message {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            color: #856404;
        }
        .help-text {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">✗</div>
        <h1>Verification Failed</h1>
        <div class="error-message">
            {{ $message }}
        </div>
        <p>Possible reasons:</p>
        <ul style="text-align: left; color: #666;">
            <li>The verification link has expired</li>
            <li>The link has already been used</li>
            <li>The link is invalid</li>
        </ul>
        <div class="help-text">
            <p><strong>Need help?</strong></p>
            <p>Please contact our office to request a new verification email.</p>
        </div>
    </div>
</body>
</html>
