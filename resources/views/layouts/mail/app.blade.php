{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <style>--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            background-color: #f4f4f4;--}}
{{--            margin: 0;--}}
{{--            padding: 0;--}}
{{--        }--}}
{{--        .container {--}}
{{--            max-width: 600px;--}}
{{--            margin: 20px auto;--}}
{{--            background: #ffffff;--}}
{{--            padding: 20px;--}}
{{--            border-radius: 10px;--}}
{{--            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);--}}
{{--            text-align: center;--}}
{{--        }--}}
{{--        .header {--}}
{{--            padding: 20px;--}}
{{--            background: #CD310F;--}}
{{--            color: #ffffff;--}}
{{--            border-top-left-radius: 10px;--}}
{{--            border-top-right-radius: 10px;--}}
{{--        }--}}
{{--        .header img {--}}
{{--            max-width: 150px;--}}
{{--            margin-bottom: 10px;--}}
{{--        }--}}
{{--        h2 {--}}
{{--            color: #333;--}}
{{--        }--}}
{{--        p {--}}
{{--            color: #666;--}}
{{--            font-size: 16px;--}}
{{--            line-height: 1.5;--}}
{{--        }--}}
{{--        .button {--}}
{{--            display: inline-block;--}}
{{--            padding: 12px 24px;--}}
{{--            margin: 20px 0;--}}
{{--            font-size: 16px;--}}
{{--            color: #ffffff;--}}
{{--            background-color: #CD310F;--}}
{{--            text-decoration: none;--}}
{{--            border-radius: 5px;--}}
{{--        }--}}
{{--        .button:hover {--}}
{{--            background-color: #A8280D;--}}
{{--        }--}}
{{--        .footer {--}}
{{--            background: #f4f4f4;--}}
{{--            padding: 15px;--}}
{{--            font-size: 12px;--}}
{{--            color: #999;--}}
{{--            text-align: center;--}}
{{--            border-bottom-left-radius: 10px;--}}
{{--            border-bottom-right-radius: 10px;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}

{{--<div class="container">--}}
{{--    <!-- Header with Brand Logo -->--}}
{{--    <div class="header">--}}
{{--        <img src="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_LOGO'))}}" alt="{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }} logo">--}}
{{--        <h2>{{config('general_settings.SITE_NAME')}}</h2>--}}
{{--    </div>--}}

{{--    <!-- Email Body -->--}}
{{--    @yield('content')--}}

{{--    <!-- Footer -->--}}
{{--    <div class="footer">--}}
{{--        <p>&copy; <script>document.write(new Date().getFullYear())</script> {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}. All Rights Reserved.</p>--}}
{{--        <p>If you have any issues, contact our <a href="mailto:{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SUPPORT_EMAIL') }}">support team</a>.</p>--}}
{{--    </div>--}}
{{--</div>--}}

{{--</body>--}}
{{--</html>--}}


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
            background: #d9534f;
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
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
