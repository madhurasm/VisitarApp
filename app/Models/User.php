<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\User_Password_Reset_Mail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function createCustomToken($name = 'API Token', $length = 60)
    {
        // Generate a random token string
        $token = bin2hex(random_bytes($length / 2));

        // Store the token in the database (this is optional, depending on your needs)
        $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $token),
            'abilities' => json_encode(['*']),
        ]);

        return $token; // Return the plain token
    }

    public function scopeDetails($query)
    {
        return $query->select(['id', 'entity_id', 'site_id', 'first_name', 'last_name', 'name', 'email', 'username', 'country_code', 'mobile', 'notification', 'profile_image', 'location', 'language', 'template_id', 'site_id', 'reset_token']);
    }

    public function scopeList($query)
    {
        return $query->select(['id', 'entity_id', 'site_id', 'name', 'country_code', 'mobile', 'profile_image', 'site_id']);
    }

    public function getNameAttribute($val)
    {
        return $val ?? '';
    }

    public function scopeType($query)
    {
        return $query->where('type', 'user');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getProfileImageAttribute($val)
    {
        return checkFileExist($val, 'no_user_image');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function site()
    {
        return $this->hasOne(EntitySite::class, 'id', 'site_id')->select('id', 'name', 'location');
    }

    public function entity()
    {
        return $this->hasOne(self::class, 'id', 'entity_id')->select('id', 'name', 'profile_image');
    }

    public static function AddTokenToUser()
    {
        $user = Auth::user();
        $device_id = request('device_id');

        // Not Allow multi device login
        Device::where('device_id', $device_id)->delete();

        $user->devices()->updateOrCreate(
            ['device_id' => $device_id], // Search for this device_id
            [
                'device_token' => request('device_token'),
                'type' => request('device_type'),
                'app_type' => request('app_type') ?? 'user',
                'os' => request('os') ?? '',
                'version' => request('version') ?? '',
                'time_zone' => request('time_zone') ?? '',
                'language' => request('language') ?? '',
            ]
        );
    }

    public static function passwordReset($email = "", $flash = true)
    {
        $user = User::where('email', $email)->first();

        // Check if user exists
        if (!$user) {
            $message = __('api.error_email_not_exits');
            return self::handleFlashOrReturn($flash, $message, false);
        }

        // Check if user account is active
        if ($user->status !== "active") {
            $message = __('api.error_account_disabled');
            return self::handleFlashOrReturn($flash, $message, false);
        }

        // Generate reset token and send email
        $user->update([
            'reset_token' => genUniqueStr('users', 'reset_token', '30', '', true)
        ]);
        Mail::to($user->email)->send(new User_Password_Reset_Mail($user));

        $message = __('api.success_email_sent');
        return self::handleFlashOrReturn($flash, $message, true);
    }

    private static function handleFlashOrReturn($flash, $message, $status)
    {
        if ($flash) {
            $status ? flash_session('success', $message) : flash_session('error', $message);
        } else {
            return ['status' => $status, 'message' => $message];
        }
    }
}
