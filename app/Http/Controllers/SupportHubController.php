<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SupportHubController extends Controller
{
    /**
     * Combined support: signed-in ticket reports + public account / 2FA recovery.
     */
    public function index(): View
    {
        return view('support.index');
    }
}
