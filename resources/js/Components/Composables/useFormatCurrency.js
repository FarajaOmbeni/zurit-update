export function formatCurrency(value) {
    const amount = Number(value);
    if (isNaN(amount)) return "KES 0";

    return new Intl.NumberFormat("en-KE", {
        style: "currency",
        currency: "KES",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}
