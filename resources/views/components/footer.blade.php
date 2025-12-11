<footer id="footer-main" class="footer-container">
    <div class="footer-background-layer"></div>
    <div class="footer-scanline-overlay"></div>
    <div class="footer-content-wrapper">
        <div class="footer-top-section">
            <div class="footer-brand-column">
                <div class="footer-logo-wrapper">
                    <div class="footer-logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                        </svg>
                    </div>
                    <span class="footer-logo-text">{{ config('app.name') }}</span>
                </div>
                <p class="footer-brand-description">
                    {{ __('Experience the future of investment education through interactive simulations and advanced analytics. Learn, experiment, and master the markets risk-free.') }}
                </p>
                <div class="footer-trust-badges">
                    <div class="footer-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                        </svg>
                        <span>{{ __('Bank-Grade Security') }}</span>
                    </div>
                    <div class="footer-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </g>
                        </svg>
                        <span>{{ __('256-bit Encrypted') }}</span>
                    </div>
                </div>
            </div>
            <div class="footer-newsletter-column">
                <div class="footer-newsletter-card">
                    <h3 class="footer-newsletter-title">{{ __('Stay Updated') }}</h3>
                    <p class="footer-newsletter-subtitle">
                        {{ __('Get market insights and platform updates delivered to your inbox') }}
                    </p>
                    <form id="newsletter-form" class="footer-newsletter-form">
                        <div class="footer-input-group">
                            <input type="email" placeholder="{{ __('Enter your email') }}" required class="footer-email-input" />
                            <button type="submit" class="footer-subscribe-btn btn btn-primary">{{ __('Subscribe') }}</button>
                        </div>
                    </form>
                    <p class="footer-newsletter-note">{{ __('Join 50,000+ investors already learning smarter') }}</p>
                </div>
            </div>
        </div>

        <div class="footer-links-section">
            <div class="footer-links-grid">
                <div class="footer-links-column">
                    <h4 class="footer-column-title">{{ __('Platform') }}</h4>
                    <ul class="footer-links-list">
                        <li><a href="#"><div class="footer-link"><span>{{ __('Simulations') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Dashboard') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Analytics') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Market Data') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Educational Hub') }}</span></div></a></li>
                    </ul>
                </div>
                
                <div class="footer-links-column">
                    <h4 class="footer-column-title">{{ __('Resources') }}</h4>
                    <ul class="footer-links-list">
                        <li><a href="#"><div class="footer-link"><span>{{ __('Getting Started') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Tutorials') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Documentation') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Community Forum') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Blog') }}</span></div></a></li>
                    </ul>
                </div>

                <div class="footer-links-column">
                    <h4 class="footer-column-title">{{ __('Company') }}</h4>
                    <ul class="footer-links-list">
                        <li><a href="#"><div class="footer-link"><span>{{ __('About Us') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Contact') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Press Kit') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Careers') }}</span></div></a></li>
                    </ul>
                </div>

                <div class="footer-links-column">
                    <h4 class="footer-column-title">{{ __('Legal') }}</h4>
                    <ul class="footer-links-list">
                        <li><a href="#"><div class="footer-link"><span>{{ __('Terms of Service') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Privacy Policy') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Cookie Policy') }}</span></div></a></li>
                        <li><a href="#"><div class="footer-link"><span>{{ __('Disclaimer') }}</span></div></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom-section">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
                    <p class="footer-disclaimer">{{ __('Investment simulations are for educational purposes only. Past performance does not guarantee future results.') }}</p>
                </div>
                <div class="footer-social-links">
                    <a href="#" aria-label="Twitter">
                        <div class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6c2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4c-.9-4.2 4-6.6 7-3.8c1.1 0 3-1.2 3-1.2"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <div class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2a2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"></path>
                                    <circle cx="4" cy="4" r="2"></circle>
                                </g>
                            </svg>
                        </div>
                    </a>
                    <a href="https://github.com/GustavsGarais/Nosleguma-darbs-investacija.git" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                        <div class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5c.08-1.25-.27-2.48-1-3.5c.28-1.15.28-2.35 0-3.5c0 0-1 0-3 1.5c-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.4 5.4 0 0 0 4 9c0 3.5 3 5.5 6 5.5c-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"></path>
                                    <path d="M9 18c-4.51 2-5-2-7-2"></path>
                                </g>
                            </svg>
                        </div>
                    </a>
                </div>
                <button id="back-to-top" aria-label="{{ __('Back to top') }}" class="footer-back-to-top">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="footer-tech-grid"></div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById("back-to-top");
    const newsletterForm = document.getElementById("newsletter-form");

    if (backToTopBtn) {
        backToTopBtn.addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }

    if (newsletterForm) {
        newsletterForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const emailInput = this.querySelector(".footer-email-input");

            if (emailInput && emailInput.value) {
                const originalText = emailInput.value;
                emailInput.value = "{{ __('Thank you for subscribing!') }}";
                emailInput.disabled = true;

                setTimeout(function() {
                    emailInput.value = "";
                    emailInput.disabled = false;
                    emailInput.placeholder = "{{ __('Enter your email') }}";
                }, 3000);
            }
        });
    }
});
</script>