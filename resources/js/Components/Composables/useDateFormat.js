// resources/js/composables/useDateFormat.js

export function formatDate(dateInput) {
    const date = new Date(dateInput);
    if (isNaN(date)) return "";

    const day = ("0" + date.getDate()).slice(-2);
    const month = ("0" + (date.getMonth() + 1)).slice(-2);
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
}
