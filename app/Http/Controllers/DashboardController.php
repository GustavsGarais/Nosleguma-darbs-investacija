<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $simulations = auth()->user()->simulations()->latest()->get();
        $simulation = null;

        if ($request->has('simulation')) {
            $simulation = auth()->user()->simulations()->find($request->simulation);
        }

        return view('dashboard', compact('simulations', 'simulation'));
    }

    public function completeTutorial(Request $request)
    {
        $user = auth()->user();
        $user->tutorial_completed = true;
        $user->save();

        return response()->json(['success' => true]);
    }
}