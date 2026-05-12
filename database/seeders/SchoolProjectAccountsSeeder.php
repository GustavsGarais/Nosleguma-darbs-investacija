<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolProjectAccountsSeeder extends Seeder
{
    /**
     * Same demo pair as bootstrap / DatabaseSeeder (run alone: php artisan db:seed --class=SchoolProjectAccountsSeeder).
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'tutorial_completed' => true,
                'is_admin' => true,
                'currency_preference' => 'EUR',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'User',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'tutorial_completed' => false,
                'is_admin' => false,
                'currency_preference' => 'EUR',
            ]
        );
    }
}
