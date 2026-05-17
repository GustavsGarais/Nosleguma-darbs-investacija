<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationExemptAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_user_can_access_app_without_email_verified_at(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'user@user.com',
        ]);

        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasVerifiedEmail());

        $this->actingAs($user)
            ->get(route('simulations.index'))
            ->assertOk();
    }

    public function test_demo_admin_email_can_access_app_without_email_verified_at(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => User::DEMO_ADMIN_EMAIL,
            'is_admin' => true,
        ]);

        $this->assertTrue($user->hasVerifiedEmail());

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_other_users_still_require_verification(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'other@example.com',
        ]);

        $this->assertFalse($user->hasVerifiedEmail());

        $this->actingAs($user)
            ->get(route('simulations.index'))
            ->assertRedirect(route('verification.notice', absolute: false));
    }
}
