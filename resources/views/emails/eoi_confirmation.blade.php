<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EOI Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-label {
            font-weight: 600;
            width: 180px;
            color: #555;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
        }
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-amend {
            background-color: #f8f9fa;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-amend:hover {
            background-color: #667eea;
            color: #ffffff;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            font-size: 14px;
            color: #888;
            text-align: center;
        }
        .important-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .important-notice p {
            margin: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>EOI Details Confirmation</h1>
        </div>

        <p>Dear {{ $client->first_name }} {{ $client->last_name }},</p>

        <p>Please review and confirm the following Expression of Interest (EOI) details we have on record for you:</p>

        <div class="section">
            <h2>EOI Information</h2>
            <div class="detail-row">
                <div class="detail-label">EOI Number:</div>
                <div class="detail-value">{{ $eoiReference->EOI_number ?? 'N/A' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Subclass(es):</div>
                <div class="detail-value">{{ $eoiReference->formatted_subclasses }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">State(s):</div>
                <div class="detail-value">{{ $eoiReference->formatted_states }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Occupation:</div>
                <div class="detail-value">{{ $eoiReference->EOI_occupation ?? 'N/A' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Points:</div>
                <div class="detail-value">{{ $eoiReference->EOI_point ?? 'N/A' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Submission Date:</div>
                <div class="detail-value">
                    @if($eoiReference->EOI_submission_date)
                        {{ $eoiReference->EOI_submission_date->format('d/m/Y') }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">{{ ucfirst($eoiReference->eoi_status ?? 'N/A') }}</div>
            </div>
        </div>

        <div class="important-notice">
            <p><strong>Important:</strong> Please carefully review all the information above. If everything is correct, click the "Confirm Details" button. If you need to make any changes, click "Request Amendment" and provide details.</p>
        </div>

        <div class="button-container">
            <a href="{{ $confirmUrl }}" class="btn btn-confirm">Confirm Details</a>
            <a href="{{ $amendUrl }}" class="btn btn-amend">Request Amendment</a>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>If you have any questions, please contact your migration agent.</p>
        </div>
    </div>
</body>
</html>
