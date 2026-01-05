@once
@push('scripts')
<script>
(function() {
    if (window.NosCurrencyFormatter) {
        window.NosCurrencyFormatter.render();
        return;
    }

    // Default fallback rates (used if API fails)
    const defaultRates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    let currencyRates = { ...defaultRates };

    const currencySymbols = {
        EUR: '€',
        USD: '$',
        GBP: '£',
        JPY: '¥',
    };

    // Fetch real-time exchange rates from API
    async function fetchExchangeRates() {
        try {
            // Using exchangerate-api.com (free tier, no API key required for EUR base)
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/EUR');
            if (!response.ok) throw new Error('API request failed');
            
            const data = await response.json();
            if (data && data.rates) {
                // Update rates for supported currencies
                currencyRates = {
                    EUR: 1,
                    USD: data.rates.USD || defaultRates.USD,
                    GBP: data.rates.GBP || defaultRates.GBP,
                    JPY: data.rates.JPY || defaultRates.JPY,
                };
                
                // Store rates with timestamp in localStorage for caching
                try {
                    localStorage.setItem('nosleguma-currency-rates', JSON.stringify({
                        rates: currencyRates,
                        timestamp: Date.now()
                    }));
                } catch (e) {}
                
                return true;
            }
        } catch (error) {
            console.warn('Failed to fetch exchange rates from API, using cached or default rates:', error);
            // Try to use cached rates if available
            try {
                const cached = localStorage.getItem('nosleguma-currency-rates');
                if (cached) {
                    const parsed = JSON.parse(cached);
                    // Use cached rates if less than 24 hours old
                    if (parsed.timestamp && (Date.now() - parsed.timestamp) < 24 * 60 * 60 * 1000) {
                        currencyRates = parsed.rates || defaultRates;
                        return true;
                    }
                }
            } catch (e) {}
            // Fall back to default rates
            currencyRates = { ...defaultRates };
            return false;
        }
        return false;
    }

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

    window.NosCurrencyFormatter = { 
        render,
        updateRates: fetchExchangeRates,
        getRates: () => ({ ...currencyRates })
    };

    // Fetch rates on load and render
    fetchExchangeRates().then(() => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', render, { once: true });
        } else {
            render();
        }
    });

    // Re-fetch rates every hour to keep them updated
    setInterval(fetchExchangeRates, 60 * 60 * 1000);

    window.addEventListener('storage', (event) => {
        if (event.key === 'nosleguma-currency-preference') {
            render();
        }
    });
})();
</script>
@endpush
@endonce
