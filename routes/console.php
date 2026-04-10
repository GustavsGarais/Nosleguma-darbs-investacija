<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('mail:test {email}', function (string $email) {
    $from = config('mail.from.address');
    $mailer = config('mail.default');

    Mail::raw(
        __('mail.test_body', [
            'app' => config('app.name'),
            'mailer' => $mailer,
            'from' => $from,
        ]),
        function ($message) use ($email) {
            $message->to($email)->subject(__('mail.test_subject', ['app' => config('app.name')]));
        }
    );

    $this->info(__('mail.test_sent', ['email' => $email, 'mailer' => $mailer]));

    if ($mailer === 'log') {
        $this->warn(__('mail.test_warn_log'));
    }
})->purpose('Send a test email to verify mail configuration (check MAIL_* in .env)');
