<?php

namespace Database\Seeders;

use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Demo accounts (password for all: "password") — useful for empty installs / classroom demos.
     * Run: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@school.demo'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'tutorial_completed' => true,
                'is_admin' => true,
                'currency_preference' => 'EUR',
            ]
        );

        $demo = User::query()->updateOrCreate(
            ['email' => 'demo@school.demo'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'tutorial_completed' => false,
                'is_admin' => false,
                'currency_preference' => 'EUR',
            ]
        );

        // Sample simulations so the dashboard is not empty after first login
        $samples = [
            [
                'name' => 'EUR ETF — balanced 10y',
                'settings' => [
                    'initialInvestment' => 5000,
                    'monthlyContribution' => 150,
                    'growthRate' => 0.065,
                    'riskAppetite' => 0.45,
                    'marketInfluence' => 0.45,
                    'inflationRate' => 0.025,
                    'investors' => 1,
                ],
                'snapshot' => [
                    'month' => 48,
                    'value' => 12450.75,
                    'real_value' => 11020.30,
                    'contributions' => 12200.00,
                    'total_gain' => 250.75,
                    'captured_at' => now()->subHours(2)->toIso8601String(),
                ],
            ],
            [
                'name' => 'Stress test sandbox',
                'settings' => [
                    'initialInvestment' => 10000,
                    'monthlyContribution' => 200,
                    'growthRate' => 0.07,
                    'riskAppetite' => 0.75,
                    'marketInfluence' => 0.65,
                    'inflationRate' => 0.03,
                    'investors' => 1,
                ],
                'snapshot' => [
                    'month' => 24,
                    'value' => 16890.20,
                    'real_value' => 15100.00,
                    'contributions' => 14800.00,
                    'total_gain' => 2090.20,
                    'captured_at' => now()->subDays(1)->toIso8601String(),
                ],
            ],
        ];

        foreach ($samples as $row) {
            Simulation::query()->updateOrCreate(
                [
                    'user_id' => $demo->id,
                    'name' => $row['name'],
                ],
                [
                    'settings' => $row['settings'],
                    'data' => ['snapshot' => $row['snapshot']],
                ]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'tutorial_completed' => true,
                'is_admin' => false,
                'currency_preference' => 'EUR',
            ]
        );

        $this->call(SchoolProjectAccountsSeeder::class);
    }
}
