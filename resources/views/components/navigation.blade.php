<nav id="main-navigation" class="navigation">
    <div class="navigation__container">
        @php
            $showThemeToggle = auth()->check() && !request()->is('/');
        @endphp

        <a href="{{ url('/') }}">
            <div aria-label="{{ config('app.name') }}" class="navigation__logo">
                <div class="navigation__logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                            <path d="m19 9l-5 5l-4-4l-3 3"></path>
                        </g>
                    </svg>
                </div>
                <span class="navigation__logo-text">{{ config('app.name') }}</span>
                <div class="navigation__logo-glow"></div>
            </div>
        </a>

        <div class="navigation__layout" style="margin-left:auto; display:flex; align-items:center; gap:16px;">
            <div class="navigation__item navigation__item--dropdown" style="position:relative;">
                <button type="button" class="navigation__link navigation__link--dropdown" aria-expanded="false" aria-haspopup="true" style="display:flex; align-items:center; gap:6px; background:none; border:none; cursor:pointer; padding:8px;">
                    <span class="navigation__link-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </span>
                    <span class="navigation__link-text"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition:transform 0.2s;">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <ul class="navigation__dropdown" style="display:none; position:absolute; top:100%; right:0; z-index:1000; list-style:none; margin:8px 0 0 0; padding:0; min-width:220px; background:var(--c-surface); border:1px solid var(--c-border); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); overflow:hidden;">
                    <li>
                        <a href="#simulations" class="navigation__dropdown-link" style="display:flex; align-items:center; gap:10px; padding:12px 16px; color:var(--c-on-surface); text-decoration:none; transition:background 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path>
                            </svg>
                            <span>Simulations</span>
                        </a>
                    </li>
                    <li>
                        <a href="#features" class="navigation__dropdown-link" style="display:flex; align-items:center; gap:10px; padding:12px 16px; color:var(--c-on-surface); text-decoration:none; transition:background 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <g>
                                    <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                                    <path d="m19 9l-5 5l-4-4l-3 3"></path>
                                </g>
                            </svg>
                            <span>Features</span>
                        </a>
                    </li>
                    <li>
                        <a href="#about" class="navigation__dropdown-link" style="display:flex; align-items:center; gap:10px; padding:12px 16px; color:var(--c-on-surface); text-decoration:none; transition:background 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <g>
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </g>
                            </svg>
                            <span>About</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="navigation__actions">
                @if ($showThemeToggle)
                    <button
                        type="button"
                        class="theme-toggle theme-toggle--combined"
                        aria-pressed="false"
                        title="Toggle light or dark mode"
                        style="display:flex; align-items:center; gap:6px; border:1px solid var(--c-border); border-radius:999px; padding:6px 12px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); cursor:pointer;">
                        <span class="theme-toggle__icon theme-toggle__icon--light" aria-hidden="true" style="display:flex; align-items:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                            </svg>
                        </span>
                        <span class="theme-toggle__icon theme-toggle__icon--dark" aria-hidden="true" style="display:flex; align-items:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path>
                            </svg>
                        </span>
                        <span class="theme-toggle__label" style="font-size:13px; font-weight:600;">Theme</span>
                    </button>
                @endif

                @auth
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="display:flex; align-items:center; gap:8px; padding:8px 16px; border-color:var(--c-border); color:var(--c-on-surface);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">
                        <div class="navigation__action-link">
                            <span>Log In</span>
                        </div>
                    </a>
                    <a href="{{ route('register') }}">
                        <div class="btn btn-primary">
                            <span>Get Started</span>
                        </div>
                    </a>
                @endauth
            </div>
        </div>

        
        <div class="navigation__scanline"></div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navigationToggle = document.getElementById("navigation-toggle");
    const navigationMenu = document.getElementById("navigation-menu");

    if (navigationToggle && navigationMenu) {
        navigationToggle.addEventListener("click", () => {
            const isExpanded = navigationToggle.getAttribute("aria-expanded") === "true";

            navigationToggle.setAttribute("aria-expanded", (!isExpanded).toString());
            navigationMenu.classList.toggle("navigation__menu--active");

            document.body.style.overflow = !isExpanded ? "hidden" : "";
        });

        const navigationLinks = navigationMenu.querySelectorAll(".navigation__link, .navigation__action-link, .btn");
        navigationLinks.forEach((link) => {
            link.addEventListener("click", () => {
                navigationToggle.setAttribute("aria-expanded", "false");
                navigationMenu.classList.remove("navigation__menu--active");
                document.body.style.overflow = "";
            });
        });

        window.addEventListener("resize", () => {
            if (window.innerWidth > 991) {
                navigationToggle.setAttribute("aria-expanded", "false");
                navigationMenu.classList.remove("navigation__menu--active");
                document.body.style.overflow = "";
            }
        });
    }

    const dropdownToggle = document.querySelector('.navigation__link--dropdown');
    const dropdownMenu = document.querySelector('.navigation__dropdown');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isExpanded = dropdownToggle.getAttribute('aria-expanded') === 'true';
            dropdownToggle.setAttribute('aria-expanded', (!isExpanded).toString());
            dropdownMenu.style.display = isExpanded ? 'none' : 'block';

            const arrow = dropdownToggle.querySelector('svg:last-child');
            if (arrow) arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
        });

        document.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownMenu.style.display = 'none';
                const arrow = dropdownToggle.querySelector('svg:last-child');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        });

        dropdownMenu.querySelectorAll('.navigation__dropdown-link').forEach(link => {
            link.addEventListener('click', () => {
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownMenu.style.display = 'none';
                const arrow = dropdownToggle.querySelector('svg:last-child');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            });

            link.addEventListener('mouseenter', function() {
                this.style.background = 'color-mix(in srgb, var(--c-primary) 10%, var(--c-surface))';
            });
            link.addEventListener('mouseleave', function() {
                this.style.background = '';
            });
        });
    }

    const html = document.documentElement;
    function applyTheme(theme) {
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            try { localStorage.setItem('theme', 'dark'); } catch (e) {}
        } else {
            html.removeAttribute('data-theme');
            try { localStorage.removeItem('theme'); } catch (e) {}
        }

        document.querySelectorAll('.theme-toggle').forEach(btn => {
            if (btn.dataset.theme === 'dark' || btn.dataset.theme === 'light') {
                btn.setAttribute('aria-pressed', btn.dataset.theme === (theme || 'light') ? 'true' : 'false');
            } else if (btn.classList.contains('theme-toggle--combined')) {
                btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                btn.setAttribute('data-theme-active', theme);
            }
        });
    }

    function currentTheme() {
        return html.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    let storedTheme = null;
    try { storedTheme = localStorage.getItem('theme'); } catch (e) { storedTheme = null; }
    applyTheme(storedTheme === 'dark' ? 'dark' : 'light');

    document.querySelectorAll('.theme-toggle').forEach(btn => {
        if (btn.dataset.theme === 'dark' || btn.dataset.theme === 'light') {
            btn.addEventListener('click', () => applyTheme(btn.dataset.theme === 'dark' ? 'dark' : 'light'));
        } else {
            btn.addEventListener('click', () => {
                const nextTheme = currentTheme() === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
            });
        }
    });
});
</script>

