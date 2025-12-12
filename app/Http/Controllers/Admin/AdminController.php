<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        
        // Ticket statistics
        $totalTickets = SupportTicket::count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        $inProgressTickets = SupportTicket::where('status', 'in_progress')->count();
        $urgentTickets = SupportTicket::where('priority', 'urgent')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
        
        // Recent users (last 7 days)
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))->count();
        
        // Recent tickets (last 7 days)
        $recentTickets = SupportTicket::where('created_at', '>=', now()->subDays(7))->count();
        
        // Latest users
        $latestUsers = User::latest()->take(5)->get();
        
        // Latest tickets
        $latestTickets = SupportTicket::with('user')->latest()->take(5)->get();
        
        // Unassigned tickets
        $unassignedTickets = SupportTicket::where('status', 'open')
            ->whereNull('assigned_to')
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'urgentTickets',
            'recentUsers',
            'recentTickets',
            'latestUsers',
            'latestTickets',
            'unassignedTickets'
        ));
    }

    /**
     * Display list of all users
     */
    public function users(Request $request): View
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by admin status
        if ($request->has('filter') && $request->get('filter') === 'admins') {
            $query->where('is_admin', true);
        } elseif ($request->has('filter') && $request->get('filter') === 'users') {
            $query->where('is_admin', false);
        }

        $users = $query->withCount('simulations')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display user details
     */
    public function showUser(User $user): View
    {
        $user->load('simulations');
        
        $tickets = SupportTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.show', compact('user', 'tickets'));
    }

    /**
     * Show form to edit user
     */
    public function editUser(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
            'password' => 'nullable|string|min:12|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_admin = $request->has('is_admin') ? (bool)$request->is_admin : false;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user): RedirectResponse
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
