/**
 * Paslēpj līmējo augšējo navigāciju, ritinot uz leju; parāda, ritinot uz augšu vai tuvu lapas augšai.
 */
(function initNavScrollHide() {
    const nav = document.getElementById('main-navigation');
    if (!nav) {
        return;
    }

    const menu = document.getElementById('navigation-menu');
    const dropdownToggle = document.querySelector('.navigation__link--dropdown');

    let lastY = window.scrollY;
    let ticking = false;

    const topRevealPx = 32;
    const directionDeltaPx = 8;

    function isOverlayOpen() {
        if (menu?.classList.contains('navigation__menu--active')) {
            return true;
        }
        if (dropdownToggle?.getAttribute('aria-expanded') === 'true') {
            return true;
        }
        return false;
    }

    function onFrame() {
        const y = window.scrollY;

        if (isOverlayOpen() || y <= topRevealPx) {
            nav.classList.remove('navigation--hidden');
        } else if (y > lastY + directionDeltaPx) {
            nav.classList.add('navigation--hidden');
        } else if (y < lastY - directionDeltaPx) {
            nav.classList.remove('navigation--hidden');
        }

        lastY = y;
        ticking = false;
    }

    function onScroll() {
        if (ticking) {
            return;
        }
        ticking = true;
        requestAnimationFrame(onFrame);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
})();
