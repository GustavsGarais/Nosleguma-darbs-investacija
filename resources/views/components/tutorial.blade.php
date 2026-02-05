@php
    $isFirstTime = !auth()->user()->tutorial_completed;
    $tutorialId = 'simulation-tutorial';
@endphp

<div id="{{ $tutorialId }}" class="tutorial-overlay" style="display:none; position:fixed; inset:0; z-index:9999; pointer-events:none;">
    <!-- Dark background overlay -->
    <div class="tutorial-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.85); pointer-events:auto;"></div>
    
    <!-- Tutorial popup - appears next to highlighted element -->
    <div class="tutorial-popup" style="position:absolute; background:var(--c-surface); border:3px solid var(--c-primary); border-radius:16px; padding:24px; max-width:420px; box-shadow:0 12px 48px rgba(0,0,0,0.6); z-index:10000; pointer-events:auto;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <h3 style="margin:0; font-size:22px; font-weight:700; color:var(--c-primary);">Tutorial</h3>
            <button type="button" class="tutorial-close" aria-label="Close tutorial" style="background:none; border:none; cursor:pointer; padding:4px 8px; color:var(--c-on-surface-2); font-size:24px; line-height:1; border-radius:4px; transition:background 0.2s;">&times;</button>
        </div>
        <div class="tutorial-content" style="margin-bottom:20px; min-height:60px;">
            <p style="margin:0; color:var(--c-on-surface); line-height:1.7; font-size:15px;"></p>
        </div>
        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
            <button type="button" class="tutorial-prev btn btn-outline" style="display:none;">Previous</button>
            <div style="flex:1;"></div>
            <button type="button" class="tutorial-next btn btn-primary">Next</button>
            <button type="button" class="tutorial-finish btn btn-primary" style="display:none;">Finish</button>
        </div>
        <div style="margin-top:16px; text-align:center;">
            <span class="tutorial-step-indicator" style="color:var(--c-on-surface-2); font-size:13px; font-weight:500;">Step 1 of 3</span>
        </div>
    </div>
</div>

<script>
(function() {
    const tutorialId = '{{ $tutorialId }}';
    const isFirstTime = {{ $isFirstTime ? 'true' : 'false' }};
    const currentPage = '{{ $currentPage ?? 'dashboard' }}';
    
    const tutorialSteps = {
        dashboard: [
            {
                target: 'h1',
                position: 'bottom',
                content: 'This short tour will show you where to find your simulations and how to start a new one. Use Next and Previous to move between steps, or Finish to close the tour at any time.',
            },
            {
                target: 'section[aria-label="Simulations"] h2, .auth-card h2',
                position: 'bottom',
                content: 'Here you\'ll see “Your Simulations” – a list of all scenarios you have created, with their latest saved values and quick access to open or edit them.',
            },
            {
                target: 'a[href*="simulations.create"], .auth-card a[href*="simulations.create"]',
                position: 'bottom',
                content: 'Click “New Simulation” to create a fresh scenario. You\'ll choose an initial amount, monthly contributions, and risk settings to see how your investments could behave over time.',
                navigate: true
            },
            {
                target: 'aside a[href*="simulations"]',
                position: 'right',
                content: 'Use the sidebar to move between Dashboard, Simulations, and Account settings. If you ever feel lost, come back to the Dashboard and start the tour again.',
            }
        ],
        create: [
            {
                target: 'input[name="name"]',
                position: 'bottom',
                content: 'Start by giving your simulation a clear name, like “Retirement plan 30 years” or “High-risk test”. This makes it easy to recognize later.',
            },
            {
                target: 'input[name="initial_investment"]',
                position: 'right',
                content: 'Initial Investment is the amount of money you put in at the very beginning, in euros. Think of it as your starting balance.',
            },
            {
                target: 'input[name="monthly_contribution"]',
                position: 'right',
                content: 'Monthly Contribution is how much you add every month. Regular contributions are one of the most powerful drivers of long‑term growth.',
            },
            {
                target: 'input[name="growth_rate"]',
                position: 'right',
                content: 'Growth Rate is the expected yearly return in percent. For example, “7” means 7% per year. Higher values mean more potential growth, but also more risk.',
            },
            {
                target: 'input[name="risk_appetite"]',
                position: 'right',
                content: 'Risk Appetite (0–100%) controls how “bumpy” your simulation is. Lower values are calmer and more stable, higher values swing up and down more aggressively.',
            },
            {
                target: 'input[name="market_influence"]',
                position: 'right',
                content: 'Market Influence (0–100%) tells the simulator how strongly external market movements should affect your portfolio. Higher values create more realistic ups and downs.',
            },
            {
                target: 'input[name="inflation_rate"]',
                position: 'right',
                content: 'Inflation Rate is the yearly price increase in percent. For example, “2” means prices grow about 2% per year. This helps you see the difference between nominal and real value.',
            },
            {
                target: 'button[type="submit"]',
                position: 'top',
                content: 'When you are happy with the settings, click “Create” to save this simulation. You can always come back later and edit these values if you change your mind.',
            }
        ]
    };

    const steps = tutorialSteps[currentPage] || [];
    let currentStep = 0;
    let overlay = null;
    let popup = null;
    let highlightedElement = null;

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
        }

        function highlightElement(el) {
            clearHighlight();

            // Store original inline styles so we can restore them later
            if (!el.dataset.tutorialOriginalStyle) {
                el.dataset.tutorialOriginalStyle = el.getAttribute('style') || '';
            }

            highlightedElement = el;

            // Make element stand out above the dark overlay
            el.style.position = 'relative';
            el.style.zIndex = '10001';
            el.style.transition = 'all 0.3s ease';

            // Add glowing highlight effect
            el.style.boxShadow = '0 0 0 4px rgba(7, 160, 90, 0.4), 0 0 20px rgba(7, 160, 90, 0.6), 0 4px 12px rgba(0,0,0,0.3)';
            el.style.outline = '3px solid var(--c-primary)';
            el.style.outlineOffset = '6px';
            el.style.borderRadius = '8px';

            // Brighten buttons/links slightly without blurring text
            if (el.tagName === 'BUTTON' || el.tagName === 'A' || el.classList.contains('btn')) {
                el.style.filter = 'brightness(1.15) saturate(1.1)';
                if (el.classList.contains('btn-primary')) {
                    const computed = getComputedStyle(el);
                    el.style.backgroundColor = computed.backgroundColor || 'var(--c-primary)';
                }
            } else {
                // For text elements, add a subtle background to improve contrast
                el.style.backgroundColor = 'rgba(7, 160, 90, 0.08)';
                el.style.padding = '4px 8px';
            }
        }

        function showStep(stepIndex) {
            if (stepIndex < 0 || stepIndex >= steps.length) {
                hideTutorial();
                return;
            }

            const step = steps[stepIndex];
            currentStep = stepIndex;

            // Find target element
            let targetEl = null;
            if (step.target) {
                targetEl = document.querySelector(step.target);
            }

            if (!targetEl) {
                console.warn('Tutorial step target not found:', step.target);
                if (stepIndex < steps.length - 1) {
                    setTimeout(() => showStep(stepIndex + 1), 100);
                }
                return;
            }

            // Highlight the element
            highlightElement(targetEl);

            // Update content
            if (contentEl) {
                contentEl.textContent = step.content;
            }

            // Update indicator
            if (indicatorEl) {
                indicatorEl.textContent = `Step ${stepIndex + 1} of ${steps.length}`;
            }

            // Show/hide buttons
            if (prevBtn) prevBtn.style.display = stepIndex > 0 ? 'inline-block' : 'none';
            if (nextBtn) nextBtn.style.display = stepIndex < steps.length - 1 ? 'inline-block' : 'none';
            if (finishBtn) finishBtn.style.display = stepIndex === steps.length - 1 ? 'inline-block' : 'none';

            // Position popup next to highlighted element
            const rect = targetEl.getBoundingClientRect();
            const popupRect = popup.getBoundingClientRect();
            let top = 0, left = 0;
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

            // Keep popup in viewport
            const padding = 20;
            top = Math.max(padding, Math.min(top, window.innerHeight - popupRect.height - padding));
            left = Math.max(padding, Math.min(left, window.innerWidth - popupRect.width - padding));

            popup.style.top = top + 'px';
            popup.style.left = left + 'px';
            popup.style.transform = 'none';

            // Handle navigation
            if (step.navigate && targetEl.tagName === 'A') {
                targetEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    sessionStorage.setItem('continueTutorial', 'true');
                    window.location.href = targetEl.getAttribute('href');
                }, { once: true });
            }

            // Show overlay
            overlay.style.display = 'block';
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
