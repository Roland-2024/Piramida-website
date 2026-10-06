<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;

class ResetPassword extends \Illuminate\Auth\Notifications\ResetPassword
{
    public function toMail($notifiable)
    {
        $message = parent::toMail($notifiable);
        $settings = SiteSetting::query()->first();
        if ($settings?->postmark_enabled) {
            config(['mail.mailers.dashboard_postmark' => $settings->postmarkTransport()]);
            Mail::purge('dashboard_postmark');
            $message->mailer('dashboard_postmark')->from($settings->mail_from_address, $settings->mail_from_name);
        }

        return $message;
    }
}
