// Direct export for backward compatibility
export function formatCurrency(amount, currency = 'KES') {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return `${currency} 0.00`;
    }

    const number = parseFloat(amount);
    
    // Format the number with commas and 2 decimal places
    const formatted = number.toLocaleString('en-KE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    return `${currency} ${formatted}`;
}

// Composable export for new usage
export function useFormatCurrency() {
    const formatCurrencyShort = (amount, currency = 'KES') => {
        if (amount === null || amount === undefined || isNaN(amount)) {
            return `${currency} 0`;
        }

        const number = parseFloat(amount);
        
        if (number >= 1000000) {
            return `${currency} ${(number / 1000000).toFixed(1)}M`;
        } else if (number >= 1000) {
            return `${currency} ${(number / 1000).toFixed(1)}K`;
        }
        
        return `${currency} ${number.toLocaleString('en-KE', { maximumFractionDigits: 0 })}`;
    };

    const parseCurrency = (currencyString) => {
        if (!currencyString) return 0;
        
        // Remove currency symbol and commas, then parse
        const cleaned = currencyString.toString().replace(/[^\d.-]/g, '');
        return parseFloat(cleaned) || 0;
    };

    return {
        formatCurrency,
        formatCurrencyShort,
        parseCurrency
    };
}
