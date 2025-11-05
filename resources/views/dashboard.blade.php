@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('dashboard_content')
<div class="dashboard">
    <section aria-label="Welcome" class="auth-card" style="margin-top:24px;">
        <h1 style="margin:0 0 8px;">Welcome {{ auth()->user()->name }}!</h1>
        <p style="margin:0; color: var(--c-on-surface);">You're signed in. Your data is loaded from the database.</p>
    </section>

    @if(isset($simulations) && $simulations->count())
    <section aria-label="Your simulations" class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 12px;">Your recent simulations</h2>
        <ul style="margin:0; padding-left:18px;">
            @foreach($simulations as $simulation)
                <li>
                    <a href="{{ route('simulations.show', $simulation) }}">{{ $simulation->name ?? 'Simulation #'.$simulation->id }}</a>
                </li>
            @endforeach
        </ul>
    </section>
    @else
    <section aria-label="Get started" class="auth-card" style="margin-top:24px;">
        <p style="margin:0;">No simulations yet. <a href="{{ route('simulations.create') }}">Create your first simulation</a>.</p>
    </section>
    @endif
</div>
@endsection


