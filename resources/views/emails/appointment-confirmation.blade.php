<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .appointment-details {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .detail-row {
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #1a1a1a;
            display: inline-block;
            width: 140px;
        }
        .value {
            color: #1a1a1a;
        }
        .important-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #1a1a1a;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e7f3ff;
            padding: 15px;
            margin: 20px 0;
        }
        .contact-info a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bansal Immigration</h1>
        <p style="margin: 5px 0 0 0;">Appointment Confirmation</p>
    </div>

    <div class="content">
        <p>Dear {{ $clientName }},</p>

        <p>Thank you for booking an appointment with Bansal Immigration. This email confirms your appointment details:</p>

        <div class="appointment-details">
            <h2 style="margin-top: 0; color: #1a1a1a;">Appointment Details</h2>
            
            <div class="detail-row">
                <span class="label">Date:</span>
                <span class="value">{{ $appointmentDate }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Time:</span>
                <span class="value">{{ $appointmentTime }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Location:</span>
                <span class="value">{{ $locationAddress }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Consultant:</span>
                <span class="value">{{ $consultant }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Service:</span>
                <span class="value">{{ $serviceType }}</span>
            </div>
        </div>

        @if($adminNotes)
        <div class="important-note">
            <strong>Important Notes:</strong>
            <p style="margin: 10px 0 0 0;">{{ $adminNotes }}</p>
        </div>
        @endif

        <div class="important-note">
            <strong>Please bring:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Valid photo identification (Passport, Driver's License)</li>
                <li>All relevant documents related to your visa inquiry</li>
                <li>Any previous correspondence from immigration authorities</li>
            </ul>
        </div>

        <div class="contact-info">
            <strong>Need to reschedule or have questions?</strong>
            <p style="margin: 10px 0 0 0;">
                Phone: <a href="tel:1300859368">1300 859 368</a><br>
                Email: <a href="mailto:info@bansalimmigration.com">info@bansalimmigration.com</a><br>
                Website: <a href="https://bansalimmigration.com">bansalimmigration.com</a>
            </p>
        </div>

        <p style="margin-top: 30px;">We look forward to assisting you with your immigration needs.</p>

        <p>Best regards,<br>
        <strong>Bansal Immigration Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated confirmation email. Please do not reply to this email.</p>
        <p style="font-size: 12px; color: #666666;">
            &copy; {{ date('Y') }} Bansal Immigration. All rights reserved.
        </p>
    </div>
</body>
</html>
