<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Investify') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-purple-800 to-black">
        @include('layouts.navigation')

        @if (app()->environment('local'))
            <!-- Dev banner: shows Vite dev server status when developing -->
            <div id="devBanner" class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-white/10 text-white px-3 py-2 rounded shadow backdrop-blur-sm text-sm" aria-live="polite">
                <svg id="devSpinner" class="w-4 h-4 animate-spin text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <div>
                    <div id="devBannerText">Checking dev server...</div>
                </div>
            </div>

            <script>
                (function pollVite() {
                    const statusEl = document.getElementById('devBannerText');
                    const spinner = document.getElementById('devSpinner');
                    const viteUrl = (location.protocol === 'https:' ? 'https://' : 'http://') + 'localhost:5173/@vite/client';
                    let attempts = 0;

                    function setStatus(msg, ready=false) {
                        statusEl.textContent = msg;
                        if (ready) {
                            spinner.style.display = 'none';
                            statusEl.textContent = 'Vite dev server: ready';
                        }
                    }

                    function tryFetch() {
                        attempts++;
                        // add timestamp to avoid caching
                        fetch(viteUrl + '?t=' + Date.now(), { mode: 'no-cors' })
                            .then(() => setStatus('Vite dev server: ready', true))
                            .catch(() => {
                                if (attempts === 1) setStatus('Starting vite (run `npm run dev`)...');
                                if (attempts < 30) {
                                    setTimeout(tryFetch, 1000);
                                } else {
                                    setStatus('Vite not running — assets will be served from build', false);
                                }
                            });
                    }

                    // start polling after small delay so developer can start vite
                    setTimeout(tryFetch, 300);
                })();
            </script>
        @endif

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white/10 shadow backdrop-blur-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
@stack('scripts')
</html>