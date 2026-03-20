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
            ->subject('Your 2FA has been disabled')
            ->view('emails.two_factor_disabled');
    }
}

