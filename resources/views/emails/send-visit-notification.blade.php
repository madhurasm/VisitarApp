<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #CD310F;
            color: #fff;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            margin: 5px;
        }
        .accept {
            background-color: #5cb85c;
            color: white !important;
            font-weight: bold;
        }
        .reject {
            background-color: #d9534f;
            color: white !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .passport-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">New Visitor Notification</div>
    <p>Dear {{ $host['name'] }},</p>
    <p>Your visitor <strong>{{ $visitor['name'] }}</strong> has checked in for your meeting.</p>

    <table>
        <tr>
            <th>Visitor Details</th>
            <th></th>
        </tr>
        <tr>
            <td><strong>Name</strong></td>
            <td>{{ $visitor['name'] }}</td>
        </tr>
        <tr>
            <td><strong>Phone</strong></td>
            <td>{{ $visitor['country_code'].' '.$visitor['mobile'] }}</td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><a href="mailto:{{ $visitor['email'] }}">{{ $visitor['email'] }}</a></td>
        </tr>
        <tr>
            <td><strong>Photo</strong></td>
            <td><img src="{{ url($visitor['profile_image']) }}" alt="Visitor Photo" class="passport-photo"></td>
        </tr>
        <tr>
            <td><strong>Purpose of Visit</strong></td>
            <td>{{ $visitor['purpose_of_visit'] ?? 'No additional message provided.' }}</td>
        </tr>
    </table>

    <div class="buttons">
        <a href="{{ route('accept_or_reject_notification', ['token' => $token, 'action' => 'accepted']) }}" class="btn accept">Accept</a>
        <a href="{{ route('accept_or_reject_notification', ['token' => $token, 'action' => 'rejected']) }}" class="btn reject">Reject</a>
    </div>

    <p>If you can't meet with your visitor, click the Reject button above and contact the Receptionist and/or the visitor to let them know you can't meet them.</p>

    <div class="footer">
        If you have questions about this email or the {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }} App, contact our <a href="mailto:{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SUPPORT_EMAIL') }}">support team</a>.<br>
{{--        &copy; {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}. All Rights Reserved.--}}
            &copy; VISITAR. All Rights Reserved.
    </div>
</div>
</body>
</html>
