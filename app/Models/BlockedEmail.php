<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlockedEmail extends Model
{
    protected $fillable = [
        'match_type',
        'pattern',
        'note',
        'created_by',
    ];

    public static function isBlocked(?string $email): bool
    {
        if (! is_string($email) || trim($email) === '') {
            return false;
        }

        $email = Str::lower(trim($email));
        $parts = explode('@', $email);
        $domain = count($parts) === 2 ? $parts[1] : null;

        $rules = static::query()->get(['match_type', 'pattern']);

        foreach ($rules as $rule) {
            $type = (string) $rule->match_type;
            $pattern = Str::lower(trim((string) $rule->pattern));

            if ($type === 'email' && $pattern !== '' && $email === $pattern) {
                return true;
            }

            if ($type === 'domain' && $pattern !== '' && $domain !== null && $domain === $pattern) {
                return true;
            }
        }

        return false;
    }
}

