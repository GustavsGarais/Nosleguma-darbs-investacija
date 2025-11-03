<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $simulations = auth()->user()->simulations()->latest()->get();

        return view('dashboard', compact('simulations'));
    }
}