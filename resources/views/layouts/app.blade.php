<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name') }} - @yield('title', 'Investment Simulation')</title>
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/homepage.css') }}" />
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
        @yield('content')
    </main>
    
    <x-footer />
</body>
</html>