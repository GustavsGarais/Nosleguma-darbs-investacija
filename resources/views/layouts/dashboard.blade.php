@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
    <section class="dashboard-content" aria-label="{{ __('Dashboard') }}">
        <div class="dashboard-content-inner">
            @yield('dashboard_content')
        </div>
    </section>
</div>
@endsection


