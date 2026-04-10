<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetInitiatedMail;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
        Mail::assertSent(PasswordResetInitiatedMail::class, function (PasswordResetInitiatedMail $mail) use ($user) {
            return $mail->user->is($user) && $mail->supportMessage === null;
        });
    }

    public function test_support_password_reset_includes_optional_message_in_security_mail(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
            'from_support' => '1',
            'support_message' => 'Locked out; requesting from Support.',
        ]);

        Mail::assertSent(PasswordResetInitiatedMail::class, function (PasswordResetInitiatedMail $mail) use ($user) {
            return $mail->user->is($user)
                && $mail->supportMessage === 'Locked out; requesting from Support.';
        });
    }

    public function test_report_unauthorized_password_reset_deletes_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);

        $table = config('auth.passwords.'.config('auth.defaults.passwords').'.table');
        $this->assertSame(1, (int) DB::table($table)->where('email', $user->email)->count());

        $url = URL::temporarySignedRoute(
            'password.reset.report-unauthorized',
            now()->addHour(),
            ['user' => $user->id],
        );

        $this->get($url)->assertRedirect(route('support.index'));

        $this->assertSame(0, (int) DB::table($table)->where('email', $user->email)->count());
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
