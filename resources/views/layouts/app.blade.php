<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', __('Investment Simulation')) | {{ config('app.name') }}</title>
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#07a05a" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    @include('components.fonts')

    <!-- Styles (tokens + site + simulation layout via Vite) -->
    @vite(['resources/css/app.css'])
    @stack('styles')

    <!-- Apply saved theme early -->
    <script>
    (function(){
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
        } catch (e) {}
    })();
    </script>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @stack('scripts')
</head>
<body>
    <x-navigation />

    <main>
        <x-flash-alerts />
        @yield('content')
    </main>

</body>
</html>