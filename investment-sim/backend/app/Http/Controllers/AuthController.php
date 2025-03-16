<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register a new user and log them in
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        // Create user and store in database
        $user = User::create([
            'username' => $request->username,
            'password' => $request->password, // NO HASHING
        ]);

        // Log in the new user (sets session)
        Auth::login($user);

        return response()->json(['message' => 'Registered successfully'], 200);
    }

    // Login an existing user
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Find user manually (because we are NOT hashing passwords)
        $user = User::where('username', $credentials['username'])
                    ->where('password', $credentials['password'])
                    ->first();

        if ($user) {
            Auth::login($user); // Start a session
            return response()->json(['message' => 'Login successful'], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
