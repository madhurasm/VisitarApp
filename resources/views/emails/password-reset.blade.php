@extends('layouts.mail.app')
@section('content')
    <div class="header">Reset Your Password</div>
    <p>Hi {{$user->name}},</p>
    <p>We received a request to reset your password. Click the button below to set a new password.</p>

    <div class="buttons">
        <a href="{{ route('admin.password.reset', $user->reset_token) }}" class="btn accept">Reset Password</a>
    </div>

    <p>If you didn't request this, you can ignore this email</p>

    <div class="footer"> &copy; {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}. All Rights Reserved.<br>
        If you have questions about this email or the {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }} App, contact our <a href="mailto:{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SUPPORT_EMAIL') }}">support team</a>.
    </div>
@endsection
