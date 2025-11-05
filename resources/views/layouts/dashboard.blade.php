@extends('layouts.app')

@section('content')
<div class="dashboard-shell" style="display:grid; grid-template-columns: 240px 1fr; gap: 24px; align-items:start;">
    <aside class="dashboard-sidebar auth-card" aria-label="Sidebar navigation" style="position:sticky; top:16px; height:fit-content; padding:16px;">
        <nav aria-label="Main">
            <ul style="list-style:none; margin:0; padding:0; display:grid; gap:8px;">
                <li><a href="{{ route('dashboard') }}" class="btn btn-outline" style="width:100%; text-align:left;">Dashboard</a></li>
                <li><a href="{{ route('simulations.index') }}" class="btn btn-outline" style="width:100%; text-align:left;">Simulations</a></li>
                <li><a href="{{ url('/settings') }}" class="btn btn-outline" style="width:100%; text-align:left;">Settings</a></li>
                <li><a href="{{ url('/reports') }}" class="btn btn-outline" style="width:100%; text-align:left;">Reports</a></li>
                <li><a href="{{ url('/account') }}" class="btn btn-outline" style="width:100%; text-align:left;">Account</a></li>
            </ul>
        </nav>
    </aside>

    <section class="dashboard-content">
        @yield('dashboard_content')
    </section>
</div>
@endsection


