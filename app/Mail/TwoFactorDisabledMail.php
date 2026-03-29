<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorDisabledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public int $ticketId
    ) {}

    public function build(): self
    {
        return $this
            ->subject(__('mail.2fa_disabled_subject', [], app()->getLocale()))
            ->view('emails.two_factor_disabled')
            ->with([
                'loginUrl' => url('/login'),
                'passwordResetUrl' => url('/forgot-password'),
            ]);
    }
}

