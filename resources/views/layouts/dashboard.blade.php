@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
    <aside class="dashboard-sidebar auth-card" aria-label="{{ __('Sidebar navigation') }}">
        <nav aria-label="Main">
            <ul style="list-style:none; margin:0; padding:0; display:grid; gap:8px;">
                <li>
                    <a href="{{ route('simulations.index') }}" class="btn btn-primary dashboard-nav-link @if(request()->routeIs('simulations.*')) is-current @endif" style="width:100%; text-align:left;" @if(request()->routeIs('simulations.*')) aria-current="page" @endif>{{ __('Simulations') }}</a>
                </li>
                <li>
                    <a href="{{ url('/reports') }}" class="btn btn-primary dashboard-nav-link @if(request()->routeIs('tickets.*') || request()->is('reports')) is-current @endif" style="width:100%; text-align:left;" @if(request()->routeIs('tickets.*') || request()->is('reports')) aria-current="page" @endif>{{ __('Reports') }}</a>
                </li>
                <li>
                    <a href="{{ route('settings') }}" class="btn btn-primary dashboard-nav-link @if(request()->routeIs('settings*')) is-current @endif" style="width:100%; text-align:left;" @if(request()->routeIs('settings*')) aria-current="page" @endif>{{ __('Account') }}</a>
                </li>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <li style="border-top:1px solid var(--c-border); padding-top:8px; margin-top:8px;">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary dashboard-nav-link @if(request()->is('admin*')) is-current @endif" style="width:100%; text-align:left;" @if(request()->is('admin*')) aria-current="page" @endif>
                            <span style="display:flex; align-items:center; gap:8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                                {{ __('Admin Panel') }}
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


