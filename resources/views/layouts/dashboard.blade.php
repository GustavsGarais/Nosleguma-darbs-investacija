@extends('layouts.app')

@section('content')
<div class="dashboard-shell" style="display:grid; grid-template-columns: 240px 1fr; gap: 24px; align-items:start;">
    <aside class="dashboard-sidebar auth-card" aria-label="Sidebar navigation" style="position:sticky; top:16px; height:fit-content; padding:16px;">
        <nav aria-label="Main">
            <ul style="list-style:none; margin:0; padding:0; display:grid; gap:8px;">
                <li><a href="{{ route('dashboard') }}" class="btn btn-outline" style="width:100%; text-align:left;">Dashboard</a></li>
                <li><a href="{{ route('simulations.index') }}" class="btn btn-outline" style="width:100%; text-align:left;">Simulations</a></li>
                <li><a href="{{ url('/reports') }}" class="btn btn-outline" style="width:100%; text-align:left;">Reports</a></li>
                <li><a href="{{ route('settings') }}" class="btn btn-outline" style="width:100%; text-align:left;">Account</a></li>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <li style="border-top:1px solid var(--c-border); padding-top:8px; margin-top:8px;">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="width:100%; text-align:left; background:color-mix(in srgb, var(--c-primary) 10%, transparent); border-color:var(--c-primary);">
                            <span style="display:flex; align-items:center; gap:8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                                Admin Panel
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </aside>

    <section class="dashboard-content">
        @yield('dashboard_content')
    </section>
</div>
@endsection


