@php
    $isFirstTime = !auth()->user()->tutorial_completed;
    $tutorialId = 'simulation-tutorial';

    // All tutorial text is generated in PHP so it can be translated via JSON lang files.
    $tutorialSteps = [
        'dashboard' => [
            [
                'target' => 'main',
                'position' => 'bottom',
                'heading' => __('tutorial.dashboard.h1'),
                'content' => __('tutorial.dashboard.0'),
            ],
            [
                'target' => 'h1',
                'position' => 'bottom',
                'heading' => __('tutorial.dashboard.h2'),
                'content' => __('tutorial.dashboard.1'),
            ],
            [
                'target' => 'section[aria-label="Simulations"] h2, .auth-card[aria-label="Simulations"] h2',
                'position' => 'bottom',
                'heading' => __('tutorial.dashboard.h3'),
                'content' => __('tutorial.dashboard.2'),
            ],
            [
                'target' => 'a[href*="simulations.create"], .auth-card a[href*="simulations.create"]',
                'position' => 'bottom',
                'heading' => __('tutorial.dashboard.h4'),
                'content' => __('tutorial.dashboard.3'),
                'navigate' => true,
            ],
            [
                'target' => 'table tbody a[href*="simulations/"]:not([href*="edit"]):not([href*="create"])',
                'position' => 'bottom',
                'heading' => __('tutorial.dashboard.h5'),
                'content' => __('tutorial.dashboard.4'),
            ],
        ],
        'create' => [
            [
                'target' => 'main',
                'position' => 'bottom',
                'heading' => __('tutorial.create.h1'),
                'content' => __('tutorial.create.0'),
            ],
            [
                'target' => 'input[name="name"]',
                'position' => 'bottom',
                'heading' => __('tutorial.create.h2'),
                'content' => __('tutorial.create.1'),
            ],
            [
                'target' => 'input[name="initial_investment"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h3'),
                'content' => __('tutorial.create.2'),
            ],
            [
                'target' => 'input[name="monthly_contribution"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h4'),
                'content' => __('tutorial.create.3'),
            ],
            [
                'target' => 'input[name="growth_rate"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h5'),
                'content' => __('tutorial.create.4'),
            ],
            [
                'target' => 'input[name="inflation_rate"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h6'),
                'content' => __('tutorial.create.5'),
            ],
            [
                'target' => 'input[name="risk_appetite"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h7'),
                'content' => __('tutorial.create.6'),
            ],
            [
                'target' => 'input[name="market_influence"]',
                'position' => 'right',
                'heading' => __('tutorial.create.h8'),
                'content' => __('tutorial.create.7'),
            ],
            [
                'target' => 'button[type="submit"]',
                'position' => 'top',
                'heading' => __('tutorial.create.h9'),
                'content' => __('tutorial.create.8'),
            ],
        ],
        'show' => [
            [
                'target' => 'main',
                'position' => 'bottom',
                'heading' => __('tutorial.show.h1'),
                'content' => __('tutorial.show.0'),
            ],
            [
                'target' => 'section[aria-label="Simulation details"] h1, .sim-run-shell h1',
                'position' => 'bottom',
                'heading' => __('tutorial.show.h2'),
                'content' => __('tutorial.show.1'),
            ],
            [
                'target' => '.sim-dash-toolbar-actions',
                'position' => 'bottom',
                'heading' => __('tutorial.show.h2b'),
                'content' => __('tutorial.show.1b'),
            ],
            [
                'target' => 'section[aria-label="Run controls"]',
                'position' => 'bottom',
                'heading' => __('tutorial.show.h3'),
                'content' => __('tutorial.show.2'),
            ],
            [
                'target' => '#months-input',
                'position' => 'right',
                'heading' => __('tutorial.show.h4'),
                'content' => __('tutorial.show.3'),
            ],
            [
                'target' => '#speed-input',
                'position' => 'right',
                'heading' => __('tutorial.show.h5'),
                'content' => __('tutorial.show.4'),
            ],
            [
                'target' => '#preset-select',
                'position' => 'right',
                'heading' => __('tutorial.show.h6'),
                'content' => __('tutorial.show.5'),
            ],
            [
                'target' => '#secondary-scenario',
                'position' => 'right',
                'heading' => __('tutorial.show.h6b'),
                'content' => __('tutorial.show.5b'),
            ],
            [
                'target' => '#btn-run',
                'position' => 'top',
                'heading' => __('tutorial.show.h7'),
                'content' => __('tutorial.show.6'),
            ],
            [
                'target' => '#btn-step',
                'position' => 'top',
                'heading' => __('tutorial.show.h8'),
                'content' => __('tutorial.show.7'),
            ],
            [
                'target' => '#btn-save',
                'position' => 'top',
                'heading' => __('tutorial.show.h9'),
                'content' => __('tutorial.show.8'),
            ],
            [
                'target' => '#sim-chart',
                'position' => 'top',
                'heading' => __('tutorial.show.h10'),
                'content' => __('tutorial.show.9'),
            ],
            [
                'target' => '#current-value',
                'position' => 'top',
                'heading' => __('tutorial.show.h11'),
                'content' => __('tutorial.show.10'),
            ],
            [
                'target' => '#total-contributed',
                'position' => 'top',
                'heading' => __('tutorial.show.h12'),
                'content' => __('tutorial.show.11'),
            ],
            [
                'target' => '#real-value',
                'position' => 'top',
                'heading' => __('tutorial.show.h13'),
                'content' => __('tutorial.show.12'),
            ],
            [
                'target' => '#drawdown',
                'position' => 'top',
                'heading' => __('tutorial.show.h14'),
                'content' => __('tutorial.show.13'),
            ],
            [
                'target' => '#cagr',
                'position' => 'top',
                'heading' => __('tutorial.show.h14b'),
                'content' => __('tutorial.show.13b'),
            ],
            [
                'target' => '#event-log',
                'position' => 'top',
                'heading' => __('tutorial.show.h15'),
                'content' => __('tutorial.show.14'),
            ],
            [
                'target' => 'main',
                'position' => 'bottom',
                'heading' => __('tutorial.show.h16'),
                'content' => __('tutorial.show.15'),
            ],
        ],
    ];
@endphp

<div id="{{ $tutorialId }}" class="tutorial-overlay" style="display:none; position:fixed; inset:0; z-index:9999; pointer-events:none;">
    <!-- Dark background overlay (used when no target to spotlight) -->
    <div class="tutorial-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.0); pointer-events:auto;"></div>

    <!-- Spotlight "hole" that reveals the highlighted element area -->
    <div class="tutorial-spotlight" style="position:fixed; top:0; left:0; width:0; height:0; border-radius:12px; pointer-events:none; box-shadow:0 0 0 9999px rgba(0,0,0,0.85); opacity:0; transition:opacity 0.15s ease;"></div>
    
    <!-- Tutorial popup - appears next to highlighted element -->
    <div class="tutorial-popup" style="position:absolute; background:var(--c-surface); border:3px solid var(--c-primary); border-radius:16px; padding:24px; max-width:420px; box-shadow:0 12px 48px rgba(0,0,0,0.6); z-index:10000; pointer-events:auto;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <h3 style="margin:0; font-size:22px; font-weight:700; color:var(--c-primary);">{{ __('tutorial.title') }}</h3>
            <button type="button" class="tutorial-close" aria-label="{{ __('tutorial.close') }}" style="background:none; border:none; cursor:pointer; padding:4px 8px; color:var(--c-on-surface-2); font-size:24px; line-height:1; border-radius:4px; transition:background 0.2s;">&times;</button>
        </div>
        <div class="tutorial-content" style="margin-bottom:20px; min-height:80px;">
            <h4 class="tutorial-step-title" style="margin:0 0 10px; font-size:16px; font-weight:900; color:var(--c-on-surface);"></h4>
            <p style="margin:0; color:var(--c-on-surface); line-height:1.7; font-size:15px;"></p>
        </div>
        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
            <button type="button" class="tutorial-prev btn btn-outline" style="display:none;">{{ __('tutorial.prev') }}</button>
            <div style="flex:1;"></div>
            <button type="button" class="tutorial-next btn btn-primary">{{ __('tutorial.next') }}</button>
            <button type="button" class="tutorial-finish btn btn-primary" style="display:none;">{{ __('tutorial.finish') }}</button>
        </div>
        <div style="margin-top:16px; text-align:center;">
            <span class="tutorial-step-indicator" style="color:var(--c-on-surface-2); font-size:13px; font-weight:500;">{{ __('tutorial.step_x_of_y', ['x' => 1, 'y' => 1]) }}</span>
        </div>
    </div>
</div>

<script>
(function() {
    const tutorialId = '{{ $tutorialId }}';
    const isFirstTime = {{ $isFirstTime ? 'true' : 'false' }};
    const currentPage = '{{ $currentPage ?? 'dashboard' }}';
    
    const tutorialSteps = @json($tutorialSteps);

    const steps = tutorialSteps[currentPage] || [];
    let currentStep = 0;
    let overlay = null;
    let popup = null;
    let highlightedElement = null;
    const stepIndicatorTemplate = @json(__('tutorial.step_x_of_y', ['x' => ':x', 'y' => ':y']));

    function initTutorial() {
        overlay = document.getElementById(tutorialId);
        if (!overlay) return;

        popup = overlay.querySelector('.tutorial-popup');
        if (!popup) return;

        const closeBtn = overlay.querySelector('.tutorial-close');
        const nextBtn = overlay.querySelector('.tutorial-next');
        const prevBtn = overlay.querySelector('.tutorial-prev');
        const finishBtn = overlay.querySelector('.tutorial-finish');
        const contentEl = overlay.querySelector('.tutorial-content p');
        const titleEl = overlay.querySelector('.tutorial-step-title');
        const indicatorEl = overlay.querySelector('.tutorial-step-indicator');

        function clearHighlight() {
            if (!highlightedElement) return;

            // Restore original inline styles if we modified them
            const originalStyle = highlightedElement.dataset.tutorialOriginalStyle;
            if (originalStyle !== undefined) {
                if (originalStyle) {
                    highlightedElement.setAttribute('style', originalStyle);
                } else {
                    highlightedElement.removeAttribute('style');
                }
                delete highlightedElement.dataset.tutorialOriginalStyle;
            }

            highlightedElement = null;

            // Hide spotlight and restore full-dim backdrop
            if (spotlightEl) spotlightEl.style.opacity = '0';
        }

        const spotlightEl = overlay.querySelector('.tutorial-spotlight');
        const backdropEl = overlay.querySelector('.tutorial-backdrop');

        function highlightElement(el) {
            clearHighlight();

            // Store original inline styles so we can restore them later
            if (!el.dataset.tutorialOriginalStyle) {
                el.dataset.tutorialOriginalStyle = el.getAttribute('style') || '';
            }

            highlightedElement = el;

            // Add a subtle highlight (the spotlight does the heavy lifting)
            el.style.transition = 'box-shadow 0.15s ease, outline 0.15s ease';
            el.style.boxShadow = '0 0 0 3px rgba(7, 160, 90, 0.55)';
            el.style.outline = '2px solid rgba(7, 160, 90, 0.85)';
            el.style.outlineOffset = '3px';

            // Position spotlight to reveal the target element
            if (spotlightEl) {
                const rect = el.getBoundingClientRect();
                const pad = 10;
                const top = Math.max(6, rect.top - pad);
                const left = Math.max(6, rect.left - pad);
                const width = Math.min(window.innerWidth - left - 6, rect.width + pad * 2);
                const height = Math.min(window.innerHeight - top - 6, rect.height + pad * 2);

                spotlightEl.style.top = top + 'px';
                spotlightEl.style.left = left + 'px';
                spotlightEl.style.width = width + 'px';
                spotlightEl.style.height = height + 'px';

                const computed = getComputedStyle(el);
                const br = computed.borderRadius && computed.borderRadius !== '0px' ? computed.borderRadius : '12px';
                spotlightEl.style.borderRadius = br;
                spotlightEl.style.opacity = '1';
            }

            // When spotlight is active, keep backdrop transparent (still blocks clicks)
            if (backdropEl) {
                backdropEl.style.background = 'rgba(0,0,0,0.0)';
            }
        }

        function showStep(stepIndex) {
            if (stepIndex < 0 || stepIndex >= steps.length) {
                hideTutorial();
                return;
            }

            const step = steps[stepIndex];
            currentStep = stepIndex;

            // Make overlay measurable before we calculate popup positioning.
            // If we measure while display:none, getBoundingClientRect() returns 0 sizes,
            // which can place the popup off-screen and "freeze" the UI under the backdrop.
            overlay.style.display = 'block';
            popup.style.visibility = 'hidden';
            popup.style.top = '0px';
            popup.style.left = '0px';
            popup.style.transform = 'none';

            // Find target element
            let targetEl = null;
            if (step.target) {
                targetEl = document.querySelector(step.target);
            }

            if (targetEl) {
                // Highlight the element
                highlightElement(targetEl);
                try {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                } catch (e) {}
            } else {
                clearHighlight();
                if (spotlightEl) spotlightEl.style.opacity = '0';
                if (backdropEl) backdropEl.style.background = 'rgba(0,0,0,0.85)';
            }

            // Update content
            if (titleEl) {
                titleEl.textContent = step.heading || '';
                titleEl.style.display = step.heading ? 'block' : 'none';
            }
            if (contentEl) {
                contentEl.textContent = step.content;
            }

            // Update indicator
            if (indicatorEl) {
                indicatorEl.textContent = stepIndicatorTemplate
                    .replace(':x', String(stepIndex + 1))
                    .replace(':y', String(steps.length));
            }

            // Show/hide buttons
            if (prevBtn) prevBtn.style.display = stepIndex > 0 ? 'inline-block' : 'none';
            if (nextBtn) nextBtn.style.display = stepIndex < steps.length - 1 ? 'inline-block' : 'none';
            if (finishBtn) finishBtn.style.display = stepIndex === steps.length - 1 ? 'inline-block' : 'none';

            // Position popup next to highlighted element (or center if no target).
            // Force a layout pass now that overlay is visible.
            const popupRect = popup.getBoundingClientRect();
            let top = (window.innerHeight / 2) - (popupRect.height / 2);
            let left = (window.innerWidth / 2) - (popupRect.width / 2);

            if (targetEl) {
                const rect = targetEl.getBoundingClientRect();
                const spacing = 20;

                switch (step.position) {
                    case 'top':
                        top = rect.top - popupRect.height - spacing;
                        left = rect.left + (rect.width / 2) - (popupRect.width / 2);
                        break;
                    case 'bottom':
                        top = rect.bottom + spacing;
                        left = rect.left + (rect.width / 2) - (popupRect.width / 2);
                        break;
                    case 'left':
                        top = rect.top + (rect.height / 2) - (popupRect.height / 2);
                        left = rect.left - popupRect.width - spacing;
                        break;
                    case 'right':
                        top = rect.top + (rect.height / 2) - (popupRect.height / 2);
                        left = rect.right + spacing;
                        break;
                    default:
                        top = rect.bottom + spacing;
                        left = rect.left + (rect.width / 2) - (popupRect.width / 2);
                }
            }

            const padding = 18;
            top = Math.max(padding, Math.min(top, window.innerHeight - popupRect.height - padding));
            left = Math.max(padding, Math.min(left, window.innerWidth - popupRect.width - padding));

            popup.style.top = top + 'px';
            popup.style.left = left + 'px';
            popup.style.transform = 'none';
            popup.style.visibility = 'visible';

            // Handle navigation
            if (step.navigate && targetEl.tagName === 'A') {
                targetEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    sessionStorage.setItem('continueTutorial', 'true');
                    window.location.href = targetEl.getAttribute('href');
                }, { once: true });
            }

            // Overlay already shown above for measurements
        }

        function hideTutorial() {
            clearHighlight();
            if (overlay) overlay.style.display = 'none';
        }

        function nextStep() {
            if (currentStep < steps.length - 1) {
                showStep(currentStep + 1);
            } else {
                completeTutorial();
            }
        }

        function prevStep() {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        }

        function completeTutorial() {
            hideTutorial();
            fetch('{{ route("tutorial.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            }).catch(err => console.error('Failed to mark tutorial complete:', err));
        }

        // Event listeners
        if (closeBtn) {
            closeBtn.addEventListener('click', hideTutorial);
            closeBtn.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(0,0,0,0.1)';
            });
            closeBtn.addEventListener('mouseleave', function() {
                this.style.background = '';
            });
        }
        // Allow ESC to close the tutorial if something goes wrong with positioning.
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hideTutorial();
        });
        if (nextBtn) nextBtn.addEventListener('click', nextStep);
        if (prevBtn) prevBtn.addEventListener('click', prevStep);
        if (finishBtn) finishBtn.addEventListener('click', completeTutorial);

        // Start tutorial
        const shouldStart = isFirstTime || sessionStorage.getItem('continueTutorial') === 'true';
        if (shouldStart && steps.length > 0) {
            sessionStorage.removeItem('continueTutorial');
            setTimeout(() => showStep(0), 300);
        }

        // Manual start button
        const manualStartBtn = document.getElementById('start-tutorial');
        if (manualStartBtn) {
            manualStartBtn.addEventListener('click', () => {
                if (steps.length > 0) {
                    showStep(0);
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTutorial);
    } else {
        initTutorial();
    }
})();
</script>
