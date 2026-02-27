<?php

namespace App\Notifications;

use App\Models\CompanySetting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailCustom extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        $companyName = (string) (CompanySetting::current()?->company_name ?: config('app.name'));

        return (new MailMessage)
            ->from((string) config('mail.from.address'), $companyName)
            ->subject(__('Verify Email Address'))
            ->greeting(__('Hello!'))
            ->line(__('Please click the button below to verify your email address.'))
            ->action(__('Verify Email Address'), $verificationUrl)
            ->line(__('If you did not create an account, no further action is required.'))
            ->salutation(__('Regards,') . "\n" . $companyName);
    }
}
