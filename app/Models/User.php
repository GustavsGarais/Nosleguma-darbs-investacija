<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * When a new account uses this email (any casing), grant admin automatically (classroom / demo convention).
     */
    public const DEMO_ADMIN_EMAIL = 'admin@school.demo';

    /**
     * Default / demo accounts that may use the app without completing email verification.
     *
     * @var list<string>
     */
    public const VERIFICATION_EXEMPT_EMAILS = [
        self::DEMO_ADMIN_EMAIL,
        'user@user.com',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'currency_preference',
        'password',
        'tutorial_completed',
        'is_admin',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'tutorial_completed' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (self::emailIsDemoAdmin($user->email)) {
                $user->is_admin = true;
            }
        });
    }

    public static function emailIsDemoAdmin(?string $email): bool
    {
        return $email !== null && strcasecmp(trim($email), self::DEMO_ADMIN_EMAIL) === 0;
    }

    public static function emailIsVerificationExempt(?string $email): bool
    {
        if ($email === null || trim($email) === '') {
            return false;
        }

        $normalized = strtolower(trim($email));

        foreach (self::VERIFICATION_EXEMPT_EMAILS as $exempt) {
            if ($normalized === strtolower($exempt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasVerifiedEmail(): bool
    {
        if (self::emailIsVerificationExempt($this->email)) {
            return true;
        }

        return parent::hasVerifiedEmail();
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Check if 2FA is enabled for this user
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get recovery codes array
     */
    public function getRecoveryCodes(): array
    {
        return $this->two_factor_recovery_codes ?? [];
    }

    /**
     * Check if a recovery code is valid and remove it if found
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getRecoveryCodes();
        $index = array_search($code, $codes);

        if ($index !== false) {
            unset($codes[$index]);
            $this->two_factor_recovery_codes = array_values($codes);
            $this->save();

            return true;
        }

        return false;
    }
}
