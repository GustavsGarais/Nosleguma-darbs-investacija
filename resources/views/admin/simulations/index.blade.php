@extends('layouts.dashboard')

@section('title', 'Simulations Management')

@section('dashboard_content')
<section class="auth-card" aria-label="Simulations Management">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">All Simulations</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <form method="GET" action="{{ route('admin.simulations.index') }}" style="margin-top:16px; display:flex; gap:12px; flex-wrap:wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by simulation name..." 
            value="{{ request('search') }}"
            style="flex:1; min-width:200px; padding:8px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
        >
        <select 
            name="user_id" 
            style="padding:8px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
        >
            <option value="">All Users</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('search') || request('user_id'))
            <a href="{{ route('admin.simulations.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    @if($simulations->count())
        <div style="overflow:auto; margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Name</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">User</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Initial Investment</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Latest Value</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Created</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($simulations as $simulation)
                        @php
                            $snapshot = $simulation->data['snapshot'] ?? null;
                            $lastValue = $snapshot['value'] ?? ($simulation->settings['initialInvestment'] ?? 0);
                            $initialInvestment = $simulation->settings['initialInvestment'] ?? 0;
                        @endphp
                        <tr style="border-bottom:1px solid var(--c-border);">
                            <td style="padding:12px 8px;">
                                <a href="{{ route('admin.simulations.show', $simulation) }}" style="font-weight:600;">{{ $simulation->name }}</a>
                            </td>
                            <td style="padding:12px 8px;">
                                <a href="{{ route('admin.users.show', $simulation->user) }}" style="color:var(--c-primary);">{{ $simulation->user->name }}</a>
                            </td>
                            <td style="padding:12px 8px;">
                                <span class="currency-value" data-currency-value="{{ $initialInvestment }}">{{ '€'.number_format($initialInvestment, 2) }}</span>
                            </td>
                            <td style="padding:12px 8px;">
                                <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                            </td>
                            <td style="padding:12px 8px; color:var(--c-on-surface-2); font-size:13px;">
                                {{ $simulation->created_at->format('M d, Y') }}
                            </td>
                            <td style="padding:12px 8px;">
                                <div style="display:flex; gap:8px;">
                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.simulations.show', $simulation) }}">View</a>
                                    <form method="POST" action="{{ route('admin.simulations.delete', $simulation) }}" onsubmit="return confirm('Delete this simulation?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            {{ $simulations->links() }}
        </div>
    @else
        <p style="margin-top:16px; color:var(--c-on-surface-2);">No simulations found.</p>
    @endif
</section>
@endsection

@include('components.currency-script')

