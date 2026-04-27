<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountEmailBlockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $blockedEmail,
        public ?string $adminNote = null,
    ) {}

    public function build(): self
    {
        return $this
            ->subject(__('mail.account_blocked_subject', [], app()->getLocale()))
            ->view('emails.account_blocked')
            ->with([
                'supportUrl' => url('/support'),
                'appealNote' => $this->adminNote,
            ]);
    }
}
