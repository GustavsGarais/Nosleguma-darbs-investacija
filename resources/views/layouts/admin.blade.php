<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('Admin Panel') }} - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <style>
        :root {
            --admin-bg: #0f172a;
            --admin-surface: #1e293b;
            --admin-surface-light: #334155;
            --admin-border: #475569;
            --admin-text: #f1f5f9;
            --admin-text-muted: #94a3b8;
            --admin-primary: #3b82f6;
            --admin-primary-hover: #2563eb;
            --admin-danger: #ef4444;
            --admin-success: #10b981;
            --admin-warning: #f59e0b;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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
            font-family: 'Orbitron', 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
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
        }

        .admin-header {
            margin-bottom: 32px;
        }

        .admin-header h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 700;
            font-family: 'Orbitron', 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
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
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
            align-items: stretch;
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
                <form method="POST" action="{{ route('language.switch') }}" style="margin-top:12px; display:flex; gap:8px; align-items:center;">
                    @csrf
                    <label for="admin-locale" style="font-size:12px; color:var(--admin-text-muted); text-transform:uppercase; letter-spacing:0.06em;">{{ __('Language') }}</label>
                    <select id="admin-locale" name="locale" class="admin-select" style="max-width:140px;">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                        <option value="lv" @selected(app()->getLocale() === 'lv')>{{ __('Latviešu') }}</option>
                    </select>
                    <button type="submit" class="admin-btn admin-btn-secondary" style="padding:8px 12px;">OK</button>
                </form>
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
</body>
</html>

