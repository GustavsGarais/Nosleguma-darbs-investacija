<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupSimilarAdminEmailsCommand extends Command
{
    protected $signature = 'users:cleanup-similar-admin-emails
                            {--dry-run : List matching users without deleting}
                            {--force : Actually delete non-admin matches}';

    protected $description = 'Delete non-admin users whose email contains "admin" (case-insensitive). Keeps real admin accounts.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (! $dry && ! $force) {
            $this->error('Specify --dry-run to preview or --force to delete.');

            return self::FAILURE;
        }

        $query = User::query()
            ->where('is_admin', false)
            ->whereRaw('LOWER(email) LIKE ?', ['%admin%'])
            ->orderBy('id');

        $users = $query->get(['id', 'name', 'email', 'is_admin', 'created_at']);

        if ($users->isEmpty()) {
            $this->info('No matching non-admin users found.');

            return self::SUCCESS;
        }

        $this->table(
            ['id', 'email', 'name', 'created_at'],
            $users->map(fn (User $u) => [
                $u->id,
                $u->email,
                $u->name,
                $u->created_at?->toDateTimeString(),
            ])->all()
        );

        if ($dry) {
            $this->warn('Dry run only. Re-run with --force to delete these rows.');

            return self::SUCCESS;
        }

        $ids = $users->pluck('id')->all();
        $deleted = User::query()->whereIn('id', $ids)->delete();
        $this->info("Deleted {$deleted} user(s).");

        return self::SUCCESS;
    }
}
