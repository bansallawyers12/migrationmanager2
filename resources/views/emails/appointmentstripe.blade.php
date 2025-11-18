<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title></title>
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <style>
    table, td, div, h1, p {font-family: Arial, sans-serif;}
  </style>
</head>
<body style="margin:0;padding:0;">
  <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
    <tr>
        <td align="center" style="padding:0;">
            <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                <tr>
                    <td align="center" style="padding:40px 0 30px 0;background:#70bbd9;">
                    <img src="{{URL::to('/public/img/logo_img/bansal-imm-logo-11_vrUFM77pu7.png')}}" alt="" width="300" style="height:auto;display:block;" />
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 30px 42px 30px;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                            <tr>
                                <td style="padding:0 0 36px 0;color:#153643;">
                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Dear {{ $details['fullname'] }} ,</p>
                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Thank you for booking a <strong>{{ $details['service'] }}</strong> with us! We look forward to assisting you.</p>
                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;"><strong>Here are the details of your appointment:</strong></p>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0;">
                                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                        <tr>
                                            <td style="width:260px;padding:0;vertical-align:top;color:#153643;">
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Service:</strong> {{ $details['service'] }}</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Client Name:</strong> {{ $details['fullname'] }}</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Appointment Type:</strong> {{ $details['appointment_details'] }}</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Date:</strong> {{ $details['date'] }}</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Time:</strong> {{ $details['time'] }}</p>

                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Location:</strong> {{ $details['inperson_address'] }}</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">• <strong>Preferred Language:</strong> {{ $details['preferred_language'] }}</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width:260px;padding:0;vertical-align:top;color:#153643;">

                                                <?php if( isset($details['service_type'])  && $details['service_type'] != 1 ) { ?>
                                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Your Payment is pending. Please use below payment link - <a target="_blank" href="http://{{$details['host']}}/stripe/{{$details['appointment_id']}}" style="color:#ee4c50;text-decoration:underline;">Payment link</a></p>
                                                <?php }?>

                                                <!--<p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">
                                                    Kindly click this link to confirm you email address. If you do not confirm your email, we may not confirm your appointment.
                                                    <a href="https://www.bansalimmigration.com.au/email-verify-token/<?php //echo base64_encode(convert_uuencode($details['client_id']));?>" style="background-color: #4CAF50;border: none;color: white;padding: 7px 16px;text-align: center;text-decoration: none;display: inline-block;font-size: 16p;cursor: pointer;border-radius: 4px;">Verify Email</a>
                                                    or if you are already verified,Pls ignore this
                                                </p>-->
                                                <!--<p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">
                                                    You can also update your details by clicking this link. <a href="https://www.bansalimmigration.com.au/verify-dob/<?php //echo base64_encode(convert_uuencode($details['client_id']));?>">Update Detail</a>
                                                </p>-->
                                            </td>
                                        </tr>


                                        <tr>
                                            <td style="width:260px;padding:0;vertical-align:top;color:#153643;">
                                                <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;"><strong>Contact Us:</strong></p>
                                                <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">If you need to update your appointment details or want to reschedule or cancel, kindly inform us at least 24 hours in advance.</p>
                                                <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">If you have any questions or need assistance before your appointment, feel free to contact us at 03 9602 1330 or at info@bansalimmigration.com.au</p>
                                                <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">We look forward to helping you with your migration process!</p>
                                                <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Best regards,</p>
                                                <p style="margin:0 0 12px 12px;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">Bansal Immigration Consultants</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px;background:#ee4c50;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
                            <tr>
                                <td style="padding:0;width:50%;" align="left">
                                    <p style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;"> <a href="{{URL::to('/')}}" style="color:#ffffff;text-decoration:underline;">Bansal Immigration Consultants @ {{date('Y')}}</a>
                                    </p>
                                </td>
                                <td style="padding:0;width:50%;" align="right">
                                    <table role="presentation" style="border-collapse:collapse;border:0;border-spacing:0;">
                                        <tr>
                                            <td style="padding:0 0 0 10px;width:38px;">
                                            <a href="https://twitter.com/Bansalimmi" style="color:#ffffff;"><img src="https://assets.codepen.io/210284/tw_1.png" alt="Twitter" width="38" style="height:auto;display:block;border:0;" /></a>
                                            </td>
                                            <td style="padding:0 0 0 10px;width:38px;">
                                            <a href="https://www.facebook.com/BANSALImmigration/" style="color:#ffffff;"><img src="https://assets.codepen.io/210284/fb_1.png" alt="Facebook" width="38" style="height:auto;display:block;border:0;" /></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
  </table>
</body>
</html>
