<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('Admin Panel') }} - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/homepage.css') }}" />
    <!-- Apply saved theme so admin uses same light/dark as rest of site -->
    <script>
    (function(){
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
        } catch (e) {}
    })();
    </script>
    <style>
        /* Use main app theme variables so admin matches the rest of the site */
        :root {
            --admin-bg: var(--c-surface);
            --admin-surface: color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 6%);
            --admin-surface-light: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);
            --admin-border: var(--c-border);
            --admin-text: var(--c-on-surface);
            --admin-text-muted: var(--c-on-surface-2);
            --admin-primary: var(--c-primary);
            --admin-primary-hover: var(--c-primary-700, #068a4f);
            --admin-danger: #ef4444;
            --admin-success: #10b981;
            --admin-warning: #d98e12;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--admin-bg);
            color: var(--admin-text);
            font-family: var(--font-family-body, 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: var(--admin-surface);
            border-right: 1px solid var(--admin-border);
            padding: 24px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-sidebar-header {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-sidebar-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--admin-text);
            font-family: var(--font-family-heading, 'Orbitron', 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif);
        }

        .admin-sidebar-header p {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--admin-text-muted);
        }

        .admin-nav {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .admin-nav-item {
            margin-bottom: 8px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--admin-text-muted);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }

        .admin-nav-link:hover {
            background: var(--admin-surface-light);
            color: var(--admin-text);
        }

        .admin-nav-link.active {
            background: var(--admin-primary);
            color: white;
        }

        .admin-nav-link svg {
            width: 20px;
            height: 20px;
        }

        .admin-content {
            flex: 1;
            margin-left: 260px;
            padding: 32px;
            width: calc(100% - 260px);
            min-width: 0;
            max-width: min(1400px, calc(100vw - 260px));
            box-sizing: border-box;
        }

        @media (max-width: 1024px) {
            .admin-sidebar {
                width: 220px;
            }
            .admin-content {
                margin-left: 220px;
                width: calc(100% - 220px);
                max-width: calc(100vw - 220px);
                padding: 24px;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .admin-content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
                padding: 16px;
            }
        }

        .admin-header {
            margin-bottom: 32px;
        }

        .admin-header h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 700;
            font-family: var(--font-family-heading, 'Orbitron', 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif);
        }

        .admin-header p {
            margin: 0;
            color: var(--admin-text-muted);
            font-size: 14px;
        }

        .admin-card {
            background: var(--admin-surface);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .admin-btn-primary {
            background: var(--admin-primary);
            color: white;
        }

        .admin-btn-primary:hover {
            background: var(--admin-primary-hover);
        }

        .admin-btn-secondary {
            background: var(--admin-surface-light);
            color: var(--admin-text);
            border: 1px solid var(--admin-border);
        }

        .admin-btn-secondary:hover {
            background: var(--admin-border);
        }

        .admin-btn-danger {
            background: var(--admin-danger);
            color: white;
        }

        .admin-btn-danger:hover {
            background: #dc2626;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid var(--admin-border);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--admin-text-muted);
        }

        .admin-table td {
            padding: 16px 12px;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-table tbody tr:hover {
            background: var(--admin-surface-light);
        }

        .admin-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .admin-input {
            width: 100%;
            padding: 10px 12px;
            background: var(--admin-surface-light);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            color: var(--admin-text);
            font-size: 14px;
        }

        .admin-input:focus {
            outline: none;
            border-color: var(--admin-primary);
        }

        .admin-select {
            width: 100%;
            padding: 10px 12px;
            background: var(--admin-surface-light);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            color: var(--admin-text);
            font-size: 14px;
        }

        .admin-textarea {
            width: 100%;
            padding: 10px 12px;
            background: var(--admin-surface-light);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            color: var(--admin-text);
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
            align-items: stretch;
            width: 100%;
        }

        .admin-stat-card {
            background: var(--admin-surface);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 20px;
        }

        .admin-stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 8px 0;
        }

        .admin-stat-label {
            font-size: 13px;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border: 1px solid;
        }

        .admin-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--admin-success);
            color: var(--admin-success);
        }

        .admin-alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--admin-danger);
            color: var(--admin-danger);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h1>{{ __('Admin Panel') }}</h1>
                <p>{{ auth()->user()->name }}</p>
                <div style="margin-top:12px; display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start;">
                    <form id="admin-locale-form" method="POST" action="{{ route('language.switch') }}" style="flex:1; min-width:0;">
                        @csrf
                        <label for="admin-locale" style="font-size:12px; color:var(--admin-text-muted); text-transform:uppercase; letter-spacing:0.06em;">{{ __('Language') }}</label>
                        <select id="admin-locale" name="locale" class="admin-select" style="max-width:140px; margin-top:6px;">
                            <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                            <option value="lv" @selected(app()->getLocale() === 'lv')>{{ __('Latviešu') }}</option>
                        </select>
                    </form>
                    <div style="flex-shrink:0;">
                        <span style="font-size:12px; color:var(--admin-text-muted); text-transform:uppercase; letter-spacing:0.06em; display:block; margin-bottom:6px;">{{ __('Theme') }}</span>
                        <button type="button" id="admin-theme-toggle" class="admin-btn admin-btn-secondary" style="padding:8px 12px; display:inline-flex; align-items:center; gap:6px;" title="{{ __('Toggle light or dark mode') }}" aria-pressed="false">
                            <svg id="admin-theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                            <svg id="admin-theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>
                            <span id="admin-theme-label">{{ __('Light') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.tickets.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Support Tickets') }}
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ __('Users') }}
                        </a>
                    </li>
                    <li class="admin-nav-item" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                        <a href="{{ route('dashboard') }}" class="admin-nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('Back to Site') }}
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="admin-nav-link" style="width: 100%; background: none; border: none; cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            @if(session('success'))
                <div class="admin-alert admin-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="admin-alert admin-alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    <script>
    document.getElementById('admin-locale')?.addEventListener('change', function() {
        this.form.submit();
    });

    (function() {
        var html = document.documentElement;
        var btn = document.getElementById('admin-theme-toggle');
        var label = document.getElementById('admin-theme-label');
        var iconSun = document.getElementById('admin-theme-icon-sun');
        var iconMoon = document.getElementById('admin-theme-icon-moon');

        function applyAdminTheme(theme) {
            if (theme === 'dark') {
                html.setAttribute('data-theme', 'dark');
                try { localStorage.setItem('theme', 'dark'); } catch (e) {}
                if (label) label.textContent = '{{ __("Dark") }}';
                if (btn) btn.setAttribute('aria-pressed', 'true');
                if (iconSun) iconSun.style.display = 'none';
                if (iconMoon) iconMoon.style.display = 'block';
            } else {
                html.removeAttribute('data-theme');
                try { localStorage.removeItem('theme'); } catch (e) {}
                if (label) label.textContent = '{{ __("Light") }}';
                if (btn) btn.setAttribute('aria-pressed', 'false');
                if (iconSun) iconSun.style.display = 'block';
                if (iconMoon) iconMoon.style.display = 'none';
            }
        }

        var stored = '';
        try { stored = localStorage.getItem('theme'); } catch (e) {}
        applyAdminTheme(stored === 'dark' ? 'dark' : 'light');

        if (btn) {
            btn.addEventListener('click', function() {
                var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyAdminTheme(next);
            });
        }
    })();
    </script>
</body>
</html>

