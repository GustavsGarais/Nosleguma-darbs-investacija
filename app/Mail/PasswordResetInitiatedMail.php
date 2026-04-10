<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PasswordResetInitiatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $supportMessage,
    ) {}

    public function build(): self
    {
        $reportUnauthorizedUrl = URL::temporarySignedRoute(
            'password.reset.report-unauthorized',
            now()->addDays(7),
            ['user' => $this->user->id],
        );

        return $this
            ->subject(__('mail.password_reset_initiated_subject', [], app()->getLocale()))
            ->view('emails.password_reset_initiated')
            ->with([
                'userName' => $this->user->name,
                'supportMessage' => $this->supportMessage,
                'reportUnauthorizedUrl' => $reportUnauthorizedUrl,
                'supportUrl' => url('/support'),
            ]);
    }
}
