<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Appointment Reminder - Bansal Immigration</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; font-size: 14px; }
    .wrapper { max-width: 700px; margin: 30px auto; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.12); }

    .header { background: #1c2a3a; text-align: center; padding: 28px 20px; }
    .header .brand { color: #f5a623; font-size: 26px; font-weight: 900; letter-spacing: 2px; }
    .header h1 { color: #fff; font-size: 18px; font-weight: bold; margin-top: 4px; }
    .header .sub { color: #c8d4df; font-size: 13px; margin-top: 4px; }

    .reminder-banner { background: linear-gradient(135deg, #e74c3c, #c0392b); text-align: center; padding: 12px 20px; }
    .reminder-banner p { color: #fff; font-size: 14px; font-weight: bold; letter-spacing: 0.5px; }
    .reminder-banner span { background: rgba(255,255,255,0.2); padding: 2px 10px; border-radius: 12px; margin-left: 6px; font-size: 12px; }

    .body { padding: 28px 36px; }
    .greeting p { margin-bottom: 10px; line-height: 1.7; }

    .countdown-box { background: #fef9f0; border: 2px dashed #f5a623; border-radius: 8px; text-align: center; padding: 18px 20px; margin: 20px 0; }
    .countdown-box .label { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .countdown-box .days { font-size: 38px; font-weight: 900; color: #f5a623; line-height: 1; }
    .countdown-box .days-label { font-size: 13px; color: #c47d0e; margin-top: 4px; font-weight: bold; }
    .countdown-box .appt-date { margin-top: 10px; font-size: 15px; font-weight: bold; color: #1c2a3a; }

    .section-title { font-size: 15px; font-weight: bold; color: #1c2a3a; border-bottom: 2px solid #1c2a3a; padding-bottom: 6px; margin: 22px 0 14px; }

    .details-table { width: 100%; border-collapse: collapse; }
    .details-table tr { border-bottom: 1px solid #e8e8e8; }
    .details-table tr:last-child { border-bottom: none; }
    .details-table td { padding: 10px 8px; vertical-align: top; }
    .details-table td:first-child { font-weight: bold; color: #555; width: 130px; }

    .action-section { margin: 28px 0; text-align: center; }
    .action-section .action-title { font-size: 15px; font-weight: bold; color: #1c2a3a; margin-bottom: 6px; }
    .action-section .action-subtitle { font-size: 13px; color: #777; margin-bottom: 20px; }

    .btn { display: inline-block; padding: 13px 26px; border-radius: 6px; font-size: 14px; font-weight: bold; text-decoration: none; cursor: pointer; border: none; letter-spacing: 0.3px; }
    .btn-confirm { background: #27ae60; color: #fff; }
    .btn-cancel { background: #fff; color: #e74c3c; border: 2px solid #e74c3c; }
    .btn-icon { margin-right: 6px; }
    .btn-note { font-size: 12px; color: #999; margin-top: 14px; font-style: italic; }

    .bring-box { background: #fff8e1; border-left: 5px solid #f5a623; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
    .bring-box h3 { color: #c47d0e; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .bring-box a { color: #c47d0e; font-weight: bold; }

    .compliance-box { background: #f0faf4; border-left: 5px solid #27ae60; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
    .compliance-box h3 { color: #1e8449; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .compliance-box p { line-height: 1.7; }
    .compliance-box a { color: #1e8449; font-weight: bold; text-decoration: none; }

    .contact-box { background: #eef3f8; border-left: 5px solid #1c2a3a; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
    .contact-box h3 { color: #1c2a3a; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .contact-box p { margin-bottom: 5px; line-height: 1.6; }
    .contact-box a { color: #2980b9; text-decoration: none; font-weight: bold; }

    .closing { margin-top: 22px; line-height: 1.7; }
    .closing p { margin-bottom: 6px; }
    .closing .signature { font-weight: bold; color: #1c2a3a; margin-top: 8px; }

    .footer { background: #1c2a3a; text-align: center; padding: 18px 20px; color: #8fa3b3; font-size: 12px; }
    .footer p { margin-bottom: 5px; }
    .footer a { color: #a8c4d8; text-decoration: none; }
    .footer .copy { margin-top: 10px; color: #5a7080; font-size: 11px; }

    .divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }
  </style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="brand">🅱 BANSAL</div>
    <h1>Bansal Immigration</h1>
    <p class="sub">Appointment Reminder</p>
  </div>

  <div class="reminder-banner">
    <p>⏰ Friendly Reminder — Your Appointment is Coming Up! <span>Action Required</span></p>
  </div>

  <div class="body">

    <div class="greeting">
      <p>Dear {{ $clientName }},</p>
      <p>
        This is a friendly reminder from <strong>Bansal Immigration</strong> about your upcoming appointment. We want to ensure everything is set and you're well-prepared for your consultation.
      </p>
      <p>
        Please take a moment to <strong>confirm, reschedule, or cancel</strong> your appointment using the options below.
      </p>
    </div>

    <div class="countdown-box">
      <div class="label">Your appointment is in</div>
      <div class="days">{{ $daysUntil }}</div>
      <div class="days-label">@if($daysUntil === 1) DAY @else DAYS @endif</div>
      <div class="appt-date">📅 {{ $appointmentDate }} &nbsp;|&nbsp; 🕐 {{ $appointmentTime }}</div>
    </div>

    <div class="section-title">Appointment Details</div>
    <table class="details-table" role="presentation">
      <tr><td>Date:</td><td>{{ $appointmentDate }}</td></tr>
      <tr><td>Time:</td><td>{{ $appointmentTime }}</td></tr>
      <tr><td>Location:</td><td>{{ $locationAddress }}</td></tr>
    </table>

    <hr class="divider"/>

    <div class="action-section">
      <div class="action-title">Please Confirm Your Attendance</div>
      <div class="action-subtitle">Kindly respond at least <strong>24 hours before</strong> your appointment.</div>
      <table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 16px;">
        <tr>
          <td style="padding:6px;">
            <a href="{{ $confirmMailtoHref }}" class="btn btn-confirm" style="display:inline-block; padding:13px 26px; border-radius:6px; font-size:14px; font-weight:bold; text-decoration:none; background:#27ae60; color:#fff;">✅ Confirm Appointment</a>
          </td>
          <td style="padding:6px;">
            <a href="{{ $cancelMailtoHref }}" class="btn btn-cancel" style="display:inline-block; padding:13px 26px; border-radius:6px; font-size:14px; font-weight:bold; text-decoration:none; background:#fff; color:#e74c3c; border:2px solid #e74c3c;">❌ Cancel Appointment</a>
          </td>
        </tr>
      </table>
      <p class="btn-note">Clicking a button will open a pre-filled email for you to send. Alternatively, call us on {{ $locationPhone }}.</p>
    </div>

    <hr class="divider"/>

    <div class="bring-box">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
        <tr>
          <td style="width:36px; vertical-align:top; font-size:22px; line-height:1;">📄</td>
          <td>
            <div style="display:inline-block; background:#f5a623; color:#fff; font-size:10px; font-weight:bold; padding:2px 9px; border-radius:20px; letter-spacing:0.8px; margin-bottom:8px; text-transform:uppercase;">🔖 First-Time Clients Only</div>
            <p style="line-height:1.7; color:#5a3e00; margin:0;">
              <strong>Haven't sent us your CV yet?</strong> If this is your first appointment with us, please email your resume to
              <a href="{{ $resumeMailtoHref }}">info@bansalimmigration.com</a>
              before your visit — it helps our consultant prepare tailored advice just for you.
              <span style="font-style:italic; color:#888;"> (Returning clients can disregard this note.)</span>
            </p>
          </td>
        </tr>
      </table>
    </div>

    <div class="compliance-box">
      <h3>✅ Our Commitment — Registered Migration Agent Code of Conduct</h3>
      <p>
        Bansal Immigration operates in full compliance with the <strong>Office of the Migration Agents Registration Authority (OMARA)</strong> and strictly adheres to the <strong>Code of Conduct for Registered Migration Agents</strong>. We are dedicated to acting in your best interests with complete transparency and confidentiality.
      </p>
      <p style="margin-top:8px;">
        Review your rights as a consumer:
        <a href="https://www.mara.gov.au/get-help-visa-subsite/Files/consumer_guide_english.pdf" target="_blank">MARA Consumer Guide (English)</a>
      </p>
    </div>

    <div class="contact-box">
      <h3>📞 Need Help or Have Questions?</h3>
      <p>📱 <strong>Phone:</strong> <a href="tel:{{ $locationPhoneTel }}">{{ $locationPhone }}</a></p>
      <p>📧 <strong>Email:</strong> <a href="mailto:info@bansalimmigration.com">info@bansalimmigration.com</a></p>
      <p>🌐 <strong>Website:</strong> <a href="https://bansalimmigration.com" target="_blank">bansalimmigration.com</a></p>
    </div>

    <div class="closing">
      <p>We look forward to seeing you and assisting with your immigration needs. If there is anything you need before your appointment, our team is always happy to help.</p>
      <p>Warm regards,</p>
      <p class="signature">Bansal Immigration Team</p>
    </div>

  </div>

  <div class="footer">
    <p>This is an automated reminder email. Please do not reply directly to this email.</p>
    <p>Consumer guide: <a href="https://www.mara.gov.au/get-help-visa-subsite/Files/consumer_guide_english.pdf" target="_blank">https://www.mara.gov.au/get-help-visa-subsite/Files/consumer_guide_english.pdf</a></p>
    <p class="copy">© {{ date('Y') }} Bansal Immigration. All rights reserved.</p>
  </div>

</div>
</body>
</html>
