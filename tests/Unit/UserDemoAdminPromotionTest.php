<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDemoAdminPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_user_with_demo_admin_email_grants_admin(): void
    {
        $user = User::factory()->create(['email' => 'ADMIN@school.DEMO']);

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_similar_email_does_not_grant_admin(): void
    {
        $user = User::factory()->create(['email' => 'teacher@school.demo']);

        $this->assertFalse($user->fresh()->is_admin);
    }
}
