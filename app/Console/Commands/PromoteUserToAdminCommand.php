<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdminCommand extends Command
{
    protected $signature = 'user:promote-admin {email : Email of the user to grant admin access}';

    protected $description = 'Set is_admin=true for an existing user (e.g. first production admin when seeders were not run)';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("No user found with email: {$email}");

            return self::FAILURE;
        }

        if ($user->is_admin) {
            $this->warn("{$email} is already an admin.");

            return self::SUCCESS;
        }

        $user->is_admin = true;
        $user->save();

        $this->info("Admin access granted to {$email}. Sign in and open /admin/dashboard.");

        return self::SUCCESS;
    }
}
