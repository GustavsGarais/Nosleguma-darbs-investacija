<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Izveido fiksētos demo kontus vienreiz vidē (aizsargāti ar kešu + DB pārbaudi).
 * Ieslēdz ar BOOTSTRAP_DEFAULT_ACCOUNTS=true .env (piem., Coolify / klase).
 */
final class DefaultAccountsBootstrap
{
    private const CACHE_FLAG = 'app.default_accounts_bootstrapped';

    private const LOCK_KEY = 'app.default_accounts_bootstrapping';

    public static function ensureIfEnabled(): void
    {
        if (! config('app.bootstrap_default_accounts')) {
            return;
        }

        if (app()->environment('testing')) {
            return;
        }

        if (! Schema::hasTable('users')) {
            return;
        }

        if (Cache::get(self::CACHE_FLAG)) {
            return;
        }

        $emails = ['admin@school.com', 'user@user.com'];
        if (User::query()->whereIn('email', $emails)->count() >= 2) {
            Cache::forever(self::CACHE_FLAG, true);

            return;
        }

        $run = function () use ($emails): void {
            if (User::query()->whereIn('email', $emails)->count() >= 2) {
                Cache::forever(self::CACHE_FLAG, true);

                return;
            }

            User::query()->updateOrCreate(
                ['email' => 'admin@school.com'],
                [
                    'name' => 'Demo Admin',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'tutorial_completed' => true,
                    'is_admin' => true,
                    'currency_preference' => 'EUR',
                ],
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
                ],
            );

            Cache::forever(self::CACHE_FLAG, true);
        };

        try {
            Cache::lock(self::LOCK_KEY, 30)->block(5, $run);
        } catch (LockTimeoutException) {
            // Cits workeris veido kontus; šo pieprasījumu izlaiž.
        } catch (\Throwable) {
            // Keša krātuve var neatbalstīt lock; tomēr nodrošina, ka konti pastāv.
            $run();
        }
    }
}
