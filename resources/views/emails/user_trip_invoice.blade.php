<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <meta charset="utf-8">
        <!-- utf-8 works for most cases -->
        <meta name="viewport" content="width=device-width">
        <!-- Forcing initial-scale shouldn't be necessary -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Use the latest (edge) version of IE rendering engine -->
        <meta name="x-apple-disable-message-reformatting">
        <!-- Disable auto-scale in iOS 10 Mail entirely -->
        <title></title>
        <!-- The title tag shows in email notifications, like Android 4.4. -->
        <!-- Web Font / @font-face : BEGIN -->
        <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->
        <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
        <!--[if mso]>
        <style>
            * {
            font-family: sans-serif !important;
            }
        </style>
        <![endif]-->
        <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
        <!--[if !mso]><!-->
        <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
        <!--<![endif]-->
        <!-- Web Font / @font-face : END -->
        <!-- CSS Reset : BEGIN -->
        <style>
            /* What it does: Remove spaces around the email design added by some email clients. */
            /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
            html,
            body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            }
            /* What it does: Stops email clients resizing small text. */
            * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            }
            /* What it does: Centers email on Android 4.4 */
            div[style*="margin: 16px 0"] {
            margin: 0 !important;
            }
            /* What it does: Stops Outlook from adding extra spacing to tables. */
            table,
            td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            }
            /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
            table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
            }
            table table table {
            table-layout: auto;
            }
            /* What it does: Uses a better rendering method when resizing images in IE. */
            img {
            -ms-interpolation-mode:bicubic;
            }
            /* What it does: A work-around for email clients meddling in triggered links. */
            *[x-apple-data-detectors],  /* iOS */
            .x-gmail-data-detectors,    /* Gmail */
            .x-gmail-data-detectors *,
            .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            }
            /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
            .a6S {
            display: none !important;
            opacity: 0.01 !important;
            }
            /* If the above doesn't work, add a .g-img class to any image in question. */
            img.g-img + div {
            display: none !important;
            }
            /* What it does: Prevents underlining the button text in Windows 10 */
            .button-link {
            text-decoration: none !important;
            }
            /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
            /* Create one of these media queries for each additional viewport size you'd like to fix */
            /* Thanks to Eric Lepetit (@ericlepetitsf) for help troubleshooting */
            @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
            min-width: 375px !important;
            }
            }
            @media screen and (max-width: 480px) {
            /* What it does: Forces Gmail app to display email full width */
            u ~ div .email-container {
            min-width: 100vw;
            width: 100% !important;
            }
            }
        </style>
        <!-- CSS Reset : END -->
        <!-- Progressive Enhancements : BEGIN -->
        <style>
            /* What it does: Hover styles for buttons */
            .button-td,
            .button-a {
            transition: all 100ms ease-in;
            }
            .button-td:hover,
            .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
            }
            /* Media Queries */
            @media screen and (max-width: 600px) {
            .email-container {
           /*  width: 100% !important; */
            margin: auto !important;
            }
            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
            max-width: 100% !important;
            height: auto !important;
            margin-left: auto !important;
            margin-right: auto !important;
            }
            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
            text-align: center !important;
            }
            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
            text-align: center !important;
            display: block !important;
            margin-left: auto !important;
            margin-right: auto !important;
            float: none !important;
            }
            table.center-on-narrow {
            display: inline-block !important;
            }
            /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
            font-size: 17px !important;
            }
            }
        </style>
        <!-- Progressive Enhancements : END -->
        <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
        <!--[if gte mso 9]>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
    </head>
    <body width="100%" bgcolor="#222222" style="margin: 0; mso-line-height-rule: exactly;">
        <center style="width: 100%; background: #222222; text-align: left;">
            
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <tr>
                    <td colspan="2" style="font-family: Baskerville;font-size: 29px;font-style: normal;font-variant: normal;font-weight: 700;line-height: 26.4px;text-align: center;padding: 20px;background: linear-gradient( 135deg, rgba(60, 8, 118, 0.8) 0%, rgba(250, 0, 118, 0.8) 100%);">
                        <label style="top: 5px;position: relative;font-size: 30px;font-weight: 700;color: white;">
                            <!-- <img src="{{$website_logo_url}}" width="200" height="50" alt="alt_text" border="0" style="width:50px;height: auto; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;vertical-align:middle"> -->
                            {{$website_name}}
                        </label>
                    </td>
                </tr>
            </table>



            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <!-- 1 Column Text + Button : BEGIN -->
                <tr>
                    <td bgcolor="#ffffff" style="padding:10px 40px 5px 40px">
                        <h5 style="margin: 0; font-family: sans-serif; line-height: 125%; color: #333333; font-weight: normal;">{{$userTrip->created_at->format('d D, Y')}}</h5>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding:10px 40px 5px 40px;text-align:center;font-weight: 700;">
                        <h1 style="margin: 0; font-family: sans-serif; line-height: 125%; color: #333333; font-weight: normal;">{{$currency_symbol}}{{$invoice->total}}</h1>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding:0px 40px 5px 40px;text-align:center;font-weight: 700;">
                        <h5 style="margin: 0; font-family: sans-serif; line-height: 125%; color: #a59d9d; font-weight: normal;">Booking Id : {{$booking->booking_id}}</h5>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding:0px 40px 20px 40px;text-align:center;font-weight: 700;">
                        <h3 style="margin: 0; font-family: sans-serif; line-height: 125%; color: black; font-weight: normal;border-bottom: 1px solid #b9a7a7;padding-bottom: 10px;">Thanks for booking with us, {{$user->fname}}</h3>
                    </td>
                </tr>
            </table>


            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">


                <tr style="">
                    <td colspan="2" bgcolor="#ffffff" style="vertical-align:top;padding: 0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                        
                        <p style="margin-top:0">
                            <a target="_blank" href="{{$booking->trackBookingUrl()}}" style="background: #222222; border: 15px solid #222222; font-family: sans-serif; font-size: 13px; line-height: 110%; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ffffff;">Track Your Booking By Clicking Here</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    </a>
                        </p>

                        
                    </td>
                </tr>

            </table>

            

            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">


                <tr style="text-align:center">
                    <!-- <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                        <img src="{{$invoice->getStaticMapUrl()}}" style="width:100%">
                    </td> -->
                    <td colspan="2" bgcolor="#ffffff" style="vertical-align:top;padding: 0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                        
                        <table style="width:100%;border-top: 1px solid #b1a8a8;">
                            <tr style="background-color: #d2d0d0;color:#353535">
                                <td style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%;">Booking Fare</td>
                                <td  style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%;">{{$currency_symbol}}{{$invoice->ride_fare}}</td>
                            </tr>
                            <tr style="">
                                <td bgcolor="#ffffff" style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">Access Fee</td>
                                <td bgcolor="#ffffff" style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">{{$currency_symbol}}{{$invoice->access_fee}}</td>
                            </tr>
                            <tr style="">
                                <td bgcolor="#ffffff" style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">IGST</td>
                                <td bgcolor="#ffffff" style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">{{$currency_symbol}}{{$invoice->tax}}</td>
                            </tr>
                            @if($invoice->referral_bonus_discount != 0)
                            <tr style="">
                                <td bgcolor="#ffffff" style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">Referral Bonus Discount</td>
                                <td bgcolor="#ffffff" style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">-{{$currency_symbol}}{{$invoice->referral_bonus_discount}}</td>
                            </tr>
                            @endif
                            @if($invoice->coupon_discount != 0)
                            <tr style="">
                                <td bgcolor="#ffffff" style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">Coupon Discount</td>
                                <td bgcolor="#ffffff" style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">-{{$currency_symbol}}{{$invoice->coupon_discount}}</td>
                            </tr>
                            @endif
                            @if($invoice->cancellation_charge != 0)
                            <tr style="">
                                <td bgcolor="#ffffff" style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">Cancellation Charge</td>
                                <td bgcolor="#ffffff" style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">{{$currency_symbol}}{{$invoice->cancellation_charge}}</td>
                            </tr>
                            @endif
                            <tr style="background-color: #d2d0d0;">
                                <td style="text-align:left;font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;"><span style="color: black;font-weight: 700;">Total Fare</span><span style="font-size: 10px;">(rounded off)</span></td>
                                <td style="text-align:right;font-family: sans-serif; font-size: 15px; line-height: 140%; color: black;font-weight: 700;">{{$currency_symbol}}{{$invoice->total}}</td>
                            </tr>
                        </table>
                        


                    </td>
                </tr>

            </table>




            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <tr>
                    
                    <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%;">
                        <div style="margin-top:50px;display: flex;">
                            <img src="{{$driver->profilePhotoUrl()}}" style="width: 50px;margin-right: 10px;border-radius: 100px;height: 50px;border: 2px solid black;">
                            <span style="vertical-align:middle">
                                <label>Driver Name : <span>{{$driver->fname.' '.$driver->lname}}</span></label>
                                <br>
                                <label>Vehicle Number : <span>{{$driver->vehicle_number}}</span></label>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>


            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <tr style="">
                    <td bgcolor="#ffffff" style="padding:10px 40px 10px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: black;font-weight:700">
                    Source Address
                    </td>
                    <td bgcolor="#ffffff" style="padding:10px 40px 10px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color:black;font-weight:700">
                    Destination Address
                    </td>
                </tr>
                <tr style="background-color: #d2d0d0;color:#353535">
                    <td bgcolor="#ffffff" style="padding:0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                    {{$userTrip->trip->from}}({{$userTrip->boardingPoint->address}})
                    </td>
                    <td bgcolor="#ffffff" style="padding:0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                    {{$userTrip->trip->to}}({{$userTrip->destPoint->address}})
                    </td>
                </tr>
            </table>

            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <tr style="">
                    <td bgcolor="#ffffff" style="padding:10px 40px 10px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: black;font-weight:700">
                    Booking Date
                    </td>
                    <td bgcolor="#ffffff" style="padding:10px 40px 10px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color:black;font-weight:700">
                    Trip Date
                    </td>
                </tr>
                <tr style="background-color: #d2d0d0;color:#353535">
                    <td bgcolor="#ffffff" style="padding:0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                    {{$userTrip->tripFormatedCratedTimestamp()}}
                    </td>
                    <td bgcolor="#ffffff" style="padding:0px 40px 0px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                    {{$userTrip->trip->tripFormatedDateString()}} at {{$userTrip->trip->tripFormatedTimeString()}}
                    </td>
                </tr>
            </table>


            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
              
                <tr>
                    <td bgcolor="#ffffff" style="padding:40px 40px 5px 40px;text-align:center;font-weight: 700;">
                        <h2 style="border-bottom: 1px solid #b1a8a8;margin: 0; font-family: sans-serif; line-height: 125%; color: #333333; font-weight: normal;">Payment</h2>
                    </td>
                </tr>
                
            </table>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                <tr>
                    <td bgcolor="#ffffff" style="padding:10px 40px 5px 40px;text-align:center;font-weight: 700;">
                        <label style="margin: 0; font-family: sans-serif; line-height: 125%; color: #333333; font-weight: normal;">Paid by {{$invoice->payment_mode}}</label>
                    </td>
                    <td bgcolor="#ffffff" style="padding:10px 40px 5px 40px;text-align:center;font-weight: 700;">
                        <label style="margin: 0; font-family: sans-serif; line-height: 125%; color: #333333; font-weight: normal;">{{$currency_symbol}}{{$invoice->total}}</label>
                    </td>
                </tr>
            </table>


            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
                
                <tr>
                    <td bgcolor="#ffffff" style="padding: 40px 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                        <p style="margin: 0;">We tried our best to take you to you destination place. Thank you for using our service. In case you have any questions, feel free to reach out to us at {{$website_contact_email}}</p>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #555555;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: auto">
                            <tr>
                                <td style="border-radius: 3px; background: #222222; text-align: center;" class="button-td">
                                    <a href="{{url('')}}" style="background: #222222; border: 15px solid #222222; font-family: sans-serif; font-size: 13px; line-height: 110%; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ffffff;">Visit Website</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <!-- Button : END -->
                    </td>
                </tr>
                <!-- 1 Column Text + Button : END -->
            </table>

            
            <!-- Email Body : END -->
        </center>
    </body>
</html>