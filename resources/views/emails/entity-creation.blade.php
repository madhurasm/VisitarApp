@extends('layouts.mail.app')
@section('content')
    <div class="header">Entity Account Created</div>
    <p>Hi {{$user->name}},</p>
    <p>Your account for {{$user->name}} has been successfully created in {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}, and you can now log in with the following credentials:</p>
    <table>
        <tr>
            <td><strong>Username</strong></td>
            <td>{{$user->username}}</td>
        </tr>
        <tr>
            <td><strong>Password</strong></td>
            <td>{{$password}}</td>
        </tr>
    </table>

    <div class="buttons">
        <a href="{{ route('admin.login') }}" class="btn accept">Login</a>
    </div>

    <p>If you didn't request this, you can ignore this email.</p>

    <div class="footer">
        If you have questions about this email or the {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }} App, contact our <a href="mailto:{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SUPPORT_EMAIL') }}">support team</a>.<br>
        &copy; {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}. All Rights Reserved.
    </div>
@endsection
