@once
@push('scripts')
<script>
(function() {
    if (window.NosCurrencyFormatter) {
        window.NosCurrencyFormatter.render();
        return;
    }

    const currencyRates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    const currencySymbols = {
        EUR: '€',
        USD: '$',
        GBP: '£',
        JPY: '¥',
    };

    function getPreferredCurrency() {
        try {
            const stored = localStorage.getItem('nosleguma-currency-preference');
            if (stored && currencyRates[stored]) {
                return stored;
            }
        } catch (e) {}
        return 'EUR';
    }

    function formatAmount(amount, currency) {
        const symbol = currencySymbols[currency] ?? '€';
        const sign = amount < 0 ? '-' : '';
        const formatted = Math.abs(amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return `${sign}${symbol}${formatted}`;
    }

    function render() {
        const currency = getPreferredCurrency();
        const rate = currencyRates[currency] ?? 1;
        document.querySelectorAll('[data-currency-value]').forEach((node) => {
            const raw = parseFloat(node.dataset.currencyValue);
            if (!Number.isFinite(raw)) return;
            node.textContent = formatAmount(raw * rate, currency);
        });
    }

    window.NosCurrencyFormatter = { render };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', render, { once: true });
    } else {
        render();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'nosleguma-currency-preference') {
            render();
        }
    });
})();
</script>
@endpush
@endonce

