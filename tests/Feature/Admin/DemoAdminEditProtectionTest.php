<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoAdminEditProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_admin_cannot_be_demoted_from_admin_panel(): void
    {
        $admin = User::factory()->create(['email' => 'boss@school.test', 'is_admin' => true]);
        $demo = User::factory()->create(['email' => 'admin@school.demo', 'name' => 'Demo']);

        $this->assertTrue($demo->is_admin);

        $this->actingAs($admin)->patch(route('admin.users.update', $demo), [
            'name' => 'Renamed',
        ]);

        $demo->refresh();
        $this->assertTrue($demo->is_admin);
        $this->assertSame('Renamed', $demo->name);
    }

    public function test_non_demo_admin_can_be_demoted_when_checkbox_omitted(): void
    {
        $admin = User::factory()->create(['email' => 'boss@school.test', 'is_admin' => true]);
        $user = User::factory()->create(['email' => 'student@school.test', 'is_admin' => true]);

        $this->actingAs($admin)->patch(route('admin.users.update', $user), [
            'name' => $user->name,
        ]);

        $user->refresh();
        $this->assertFalse($user->is_admin);
    }
}
