<?php

namespace App\Mail;

use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Send_Visit_Notification_Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $host;

    public $token;

    public $visitor;

    public function __construct($host, $token, $visitor)
    {
        $this->host = $host;
        $this->token = $token;
        $this->visitor = $visitor;
    }

    public function build()
    {
        $siteLogo = GeneralSetting::getSiteSettingValue($this->host->user_id, 'SITE_LOGO');
        $siteName = GeneralSetting::getSiteSettingValue($this->host->user_id, 'SITE_NAME');
        $supportEmail = GeneralSetting::getSiteSettingValue($this->host->user_id, 'SUPPORT_EMAIL');

        return $this->subject('Meeting Request: Please Accept or Reject')
            ->view('emails.send-visit-notification', ['host' => $this->host, 'token' => $this->token, 'visitor' => $this->visitor, 'siteLogo' => $siteLogo, 'siteName' => $siteName, 'supportEmail' => $supportEmail]);
    }
}
