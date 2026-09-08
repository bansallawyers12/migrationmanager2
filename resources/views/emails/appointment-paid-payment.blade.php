<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complete Your Appointment Payment - Bansal Immigration</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f4; color:#1c2a3a; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f4f4;">
  <tr>
    <td align="center" style="padding:24px 12px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

        @include('emails.partials.appointment-branding-header')

        <tr>
          <td style="padding:8px 24px 18px 24px;">
            <p style="margin:0 0 10px 0; font-size:14px; line-height:1.7; color:#333;">Dear {{ $clientName }},</p>
            <p style="margin:0; font-size:14px; line-height:1.7; color:#333;">
              Thank you for booking an appointment with <strong>Bansal Immigration</strong>. Your appointment has been reserved, and payment is required to confirm it.
            </p>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e6eaf0; border-radius:8px; overflow:hidden;">
              <tr>
                <td style="background:#1c2a3a; padding:12px 16px;">
                  <p style="margin:0; font-size:15px; font-weight:bold; color:#f5a623;">
                    &#128197; Appointment Details
                  </p>
                </td>
              </tr>
              <tr>
                <td style="padding:0; background:#ffffff;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td width="28" valign="top" style="padding:12px 0 12px 14px; color:#1c2a3a; font-size:14px;">&#128197;</td>
                      <td style="padding:12px 14px 12px 6px; border-bottom:1px solid #eee; font-size:14px; color:#333;">
                        <strong style="color:#555;">Date:</strong> {{ $appointmentDate }}
                      </td>
                    </tr>
                    <tr>
                      <td width="28" valign="top" style="padding:12px 0 12px 14px; color:#1c2a3a; font-size:14px;">&#128336;</td>
                      <td style="padding:12px 14px 12px 6px; border-bottom:1px solid #eee; font-size:14px; color:#333;">
                        <strong style="color:#555;">Time:</strong> {{ $appointmentTime }}
                      </td>
                    </tr>
                    <tr>
                      <td width="28" valign="top" style="padding:12px 0 12px 14px; color:#1c2a3a; font-size:14px;">&#128196;</td>
                      <td style="padding:12px 14px 12px 6px; border-bottom:1px solid #eee; font-size:14px; color:#333;">
                        <strong style="color:#555;">Service Type:</strong> {{ $serviceType }}
                      </td>
                    </tr>
                    <tr>
                      <td width="28" valign="top" style="padding:12px 0 12px 14px; color:#1c2a3a; font-size:14px;">&#128100;</td>
                      <td style="padding:12px 14px 12px 6px; border-bottom:1px solid #eee; font-size:14px; color:#333;">
                        <strong style="color:#555;">Type:</strong>
                        <span style="display:inline-block; background:#f5a623; color:#1c2a3a; font-size:12px; font-weight:bold; padding:4px 12px; border-radius:20px; margin-left:4px;">{{ $meetingTypeLabel }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td width="28" valign="top" style="padding:12px 0 12px 14px; color:#1c2a3a; font-size:14px;">&#128205;</td>
                      <td style="padding:12px 14px 12px 6px; font-size:14px; color:#333;">
                        <strong style="color:#555;">Location:</strong> {{ $locationAddress }}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef6fb; border:1px solid #c5d9e8; border-radius:8px;">
              <tr>
                <td style="padding:18px 16px; text-align:center;">
                  <p style="margin:0 0 10px 0; font-size:14px; line-height:1.7; color:#1a4a6a;">Please complete your payment to confirm this appointment.</p>
                  <p style="margin:0 0 16px 0; font-size:26px; font-weight:bold; color:#1c2a3a;">${{ $amount }} AUD</p>
                  <a href="{{ $paymentUrl }}" target="_blank" rel="noopener" style="display:inline-block; background:#1c2a3a; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; font-weight:bold; font-size:15px;">Pay now securely</a>
                  <p style="margin:12px 0 0 0; font-size:12px; color:#5a7080; word-break:break-all;">{{ $paymentUrl }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff8e1; border:1px solid #f5d48a; border-radius:8px;">
              <tr>
                <td style="padding:14px 16px;">
                  <p style="margin:0; font-size:13px; line-height:1.7; color:#333;">
                    After payment is completed, you will receive a separate confirmation email with full appointment details and preparation instructions.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 16px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff; border:1px solid #e6eaf0; border-radius:8px;">
              <tr>
                <td valign="top" width="44" style="padding:16px 0 16px 16px;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" valign="middle" width="32" height="32" style="width:32px; height:32px; background:#1c2a3a; border-radius:16px; color:#ffffff; font-size:16px;">&#127911;</td>
                    </tr>
                  </table>
                </td>
                <td style="padding:16px 16px 16px 10px;">
                  <p style="margin:0 0 10px 0; font-size:14px; font-weight:bold; color:#1c2a3a;">Need help?</p>
                  <p style="margin:0 0 6px 0; font-size:13px; line-height:1.6; color:#333;">
                    &#128222; <a href="tel:{{ $locationPhoneTel }}" style="color:#1c2a3a; text-decoration:none; font-weight:bold;">{{ $locationPhone }}</a>
                  </p>
                  <p style="margin:0; font-size:13px; line-height:1.6; color:#333;">
                    &#128231; <a href="mailto:info@bansalimmigration.com.au" style="color:#1c2a3a; text-decoration:none; font-weight:bold;">info@bansalimmigration.com.au</a>
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:10px 24px 24px 24px;">
            <p style="margin:0; font-size:14px; line-height:1.7; color:#333;">Kind regards,</p>
            <p style="margin:6px 0 0 0; font-size:14px; font-weight:bold; color:#1c2a3a;">Bansal Immigration Team</p>
          </td>
        </tr>

        <tr>
          <td style="background:#1c2a3a; padding:16px 20px; text-align:center;">
            <p style="margin:0 0 4px 0; font-size:11px; color:#8fa3b3;">Secure payment powered by Stripe</p>
            <p style="margin:0; font-size:11px; color:#8fa3b3;">&copy; {{ date('Y') }} Bansal Immigration. All rights reserved.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
