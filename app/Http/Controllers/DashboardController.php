<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function completeTutorial(Request $request)
    {
        $user = auth()->user();
        $user->tutorial_completed = true;
        $user->save();

        return response()->json(['success' => true]);
    }
}
