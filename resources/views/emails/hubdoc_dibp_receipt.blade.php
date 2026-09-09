<!DOCTYPE html>
<html>
<head>
    <title>Receipt for Hubdoc Processing</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Receipt for Hubdoc Processing</h2>

        <p>Dear Hubdoc Team,</p>

        <p>Please find attached the receipt for processing.</p>

        <p><strong>Receipt Details:</strong></p>
        <ul>
            <li><strong>Client:</strong> {{ $receiptData['client_name'] ?? 'N/A' }}</li>
            <li><strong>Checklist:</strong> {{ $receiptData['checklist'] ?? 'N/A' }}</li>
            <li><strong>File:</strong> {{ $receiptData['file_name'] ?? 'N/A' }}</li>
        </ul>

        <p>Please process this receipt accordingly.</p>

        <p>Best regards,<br>
        Bansal Immigration Team</p>
    </div>
</body>
</html>
