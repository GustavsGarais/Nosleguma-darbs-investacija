@extends('layouts.dashboard')

@section('title', __('Simulations'))

@section('dashboard_content')
<div class="simulations-page">
    @include('simulations.partials.welcome')
    @include('simulations.partials.list')
</div>
@include('components.currency-script')
@include('components.tutorial', ['currentPage' => 'dashboard'])
@endsection
