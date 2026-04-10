@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
    <section class="dashboard-content" aria-label="{{ __('Dashboard') }}">
        @yield('dashboard_content')
    </section>
</div>
@endsection


