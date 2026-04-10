<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportUnauthorizedPasswordResetController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $broker = config('auth.defaults.passwords');
        $table = config("auth.passwords.{$broker}.table");

        if (is_string($table) && $table !== '') {
            DB::table($table)->where('email', $user->email)->delete();
        }

        return redirect()
            ->route('support.index')
            ->with('password_reset_reported', true);
    }
}
