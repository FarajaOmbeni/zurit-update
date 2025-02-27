export function calculateMonthsLeft(startDate, endDate) {
    if (!startDate || !endDate) return 0; // Handle missing dates

    const start = new Date(startDate);
    const end = new Date(endDate);

    if (isNaN(start) || isNaN(end)) return 0; // Handle invalid dates

    const yearsDiff = end.getFullYear() - start.getFullYear();
    const monthsDiff = end.getMonth() - start.getMonth();

    return Math.max(0, yearsDiff * 12 + monthsDiff);
}
