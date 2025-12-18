<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice for Hubdoc Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px 20px;
        }
        h2 {
            color: #1a1a1a;
            font-size: 22px;
            margin-bottom: 20px;
        }
        p {
            color: #1a1a1a;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        ul {
            color: #1a1a1a;
            font-size: 15px;
            line-height: 1.8;
        }
        li {
            margin-bottom: 8px;
        }
        strong {
            color: #1a1a1a;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Invoice for Hubdoc Processing</h2>
        
        <p>Dear Hubdoc Team,</p>
        
        <p>Please find attached the invoice for processing.</p>
        
        <p><strong>Invoice Details:</strong></p>
        <ul>
            <li><strong>Invoice Number:</strong> {{ $invoiceData['invoice_no'] ?? 'N/A' }}</li>
            <li><strong>Client:</strong> {{ $invoiceData['client_name'] ?? 'N/A' }}</li>
            <li><strong>Date:</strong> {{ $invoiceData['invoice_date'] ?? 'N/A' }}</li>
            <li><strong>Amount:</strong> ${{ number_format($invoiceData['amount'] ?? 0, 2) }}</li>
        </ul>
        
        <p>Please process this invoice accordingly.</p>
        
        <p>Best regards,<br>
        <strong>Bansal Immigration Team</strong></p>
    </div>
</body>
</html>
