function setOpen(sheet, open) {
    if (!sheet) return;
    sheet.dataset.open = open ? 'true' : 'false';
    sheet.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.documentElement.classList.toggle('helpSheet--noScroll', open);
    if (open) {
        const panel = sheet.querySelector('.helpSheet__panel');
        // Focus after CSS paint so transitions don’t steal focus.
        requestAnimationFrame(() => panel?.focus());
    }
}

function findSheet(id) {
    if (!id) return null;
    return document.getElementById(id);
}

function syncButtonAria(id, open) {
    document.querySelectorAll(`[data-help-sheet-open="${CSS.escape(id)}"]`).forEach((btn) => {
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
}

function openSheet(id) {
    const sheet = findSheet(id);
    if (!sheet) return;
    setOpen(sheet, true);
    syncButtonAria(id, true);
}

function closeSheet(id) {
    const sheet = findSheet(id);
    if (!sheet) return;
    setOpen(sheet, false);
    syncButtonAria(id, false);
}

document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-help-sheet-open]');
    if (openBtn) {
        const id = openBtn.getAttribute('data-help-sheet-open');
        openSheet(id);
        return;
    }
    const closeBtn = e.target.closest('[data-help-sheet-close]');
    if (closeBtn) {
        const id = closeBtn.getAttribute('data-help-sheet-close');
        closeSheet(id);
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const openSheetEl = document.querySelector('.helpSheet[data-open="true"]');
    if (!openSheetEl) return;
    const id = openSheetEl.id;
    closeSheet(id);
});

