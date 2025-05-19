<template>

    <Head title="Calendar" />
    <div class="p-4 bg-gray-100 min-h-screen">
        <h1 class="text-3xl font-bold text-purple-700 mb-6 text-center">Zurit Consulting Events</h1>

        <div class="flex justify-center items-center mb-6 space-x-4">
            <button @click="prevMonth"
                class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors">
                &lt; Prev
            </button>
            <h2 class="text-2xl font-semibold text-gray-700 w-48 text-center">{{ currentMonthYear }}</h2>
            <button @click="nextMonth"
                class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors">
                Next &gt;
            </button>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Loading events...</div>
        <div v-else-if="eventsForCurrentMonth.length === 0" class="text-center text-gray-500">
            No events scheduled for this month.
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="event in eventsForCurrentMonth" :key="event.id" :class="getEventCardClass(event.type)"
                class="h-full flex flex-col p-5 rounded-lg shadow-lg transition-all hover:shadow-xl">
                <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                    <div>
                        <h3 class="text-xl font-bold mb-1">{{ event.title }}</h3>
                        <p v-if="event.topic" class="text-sm opacity-90 mb-2">{{ event.topic }}</p>
                    </div>
                    <div :class="getEventPriceClass(event.type)"
                        class="px-3 py-1 rounded-full text-sm font-semibold mt-2 sm:mt-0 whitespace-nowrap">
                        {{ event.price }}
                    </div>
                </div>

                <hr :class="getEventHrClass(event.type)" class="my-3">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 opacity-75" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ formatDateRange(event.date_start, event.date_end, event.details, event.type) }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 opacity-75" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ event.location }}</span>
                    </div>
                    <div v-if="event.details && event.type !== 'webinar_bi_weekly'"
                        class="md:col-span-2 flex items-start mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 opacity-75 flex-shrink-0 mt-0.5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ event.details }}</span>
                    </div>
                </div>
                <!-- inside your v-for card -->
                <div class="mt-4 text-center">
                    <a :href="event.link" target="_blank" :class="[
                        'inline-block px-4 py-2 rounded font-bold transition-colors',
                        getEventButtonClass(event.type)
                    ]">
                        REGISTER
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const allEvents = ref([]);
const loading = ref(true);
const currentDate = ref(new Date()); // Start with Jan 2025 as data is for 2025

/* put this next to getEventPriceClass etc. */
const getEventButtonClass = (type) => {
    switch (type) {
        case 'corporate_monthly':      // card: bg-purple-500
            return 'bg-yellow-400 text-purple-800 hover:bg-yellow-500';

        case 'masterclass_quarterly':  // card: bg-yellow-500
            return 'bg-purple-600 text-yellow-200 hover:bg-purple-700';

        case 'retirement_planning':    // card: bg-purple-700
            return 'bg-yellow-300 text-purple-900 hover:bg-yellow-400';

        case 'alternative_investments':// card: bg-yellow-300
            return 'bg-purple-700 text-yellow-200 hover:bg-purple-800';

        case 'in_house':               // card: bg-gray-700
            return 'bg-white text-gray-800 hover:bg-gray-200';

        case 'webinar_bi_weekly':      // card: bg-purple-400
            return 'bg-yellow-500 text-purple-800 hover:bg-yellow-600';

        default:
            return 'bg-slate-400 text-white hover:bg-slate-500';
    }
};

// --- Helper to parse dates, assuming YYYY-MM-DD from API ---
const parseDate = (dateString) => {
    if (!dateString) return null;
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day); // month is 0-indexed
};

// --- Reactive properties for month navigation ---
const currentMonthYear = computed(() => {
    return currentDate.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

// --- Fetch events ---
onMounted(async () => {
    try {
        // Replace with your actual API endpoint
        // const response = await axios.get('/api/events');
        // this.allEvents = response.data;

        // Using provided data directly for this example since I can't make an API call
        const rawEvents = [
            // Corporate Trainings
            { id: 'corp1', title: "Wealth Wave Talks", topic: "Using NSSF / Pension to save on tax", date_start: "2025-04-05", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp2', title: "Wealth Wave Talks", topic: "How to Navigate Financial Changes", date_start: "2025-05-31", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp3', title: "Wealth Wave Talks", topic: "Developing Long-term Wealth Strategies", date_start: "2025-06-28", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp4', title: "Wealth Wave Talks", topic: "The Timeless Principles of Wealth Creation", date_start: "2025-07-26", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp5', title: "Wealth Wave Talks", topic: "Back to the Drawing Board - How to Implement Your Financial Plan", date_start: "2025-08-30", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp6', title: "Wealth Wave Talks", topic: "Securing Your Retirement", date_start: "2025-09-27", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp7', title: "Wealth Wave Talks", topic: "How Businesses Can Successfully Invest in Financial Markets", date_start: "2025-10-31", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp8', title: "Wealth Wave Talks", topic: "Strategies Businesses Can Adopt to Protect and Grow Wealth", date_start: "2025-11-29", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },
            { id: 'corp9', title: "Wealth Wave Talks", topic: "Scaling Businesses Effectively to Increase Market Value", date_start: "2025-12-20", type: "corporate_monthly", location: "Nairobi County", price: "Ksh 3,000", link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share' },

            // Quarterly Trainings – Prosperity Masterclass
            { id: 'mast1', title: "Prosperity Masterclass", date_start: "2025-04-10", date_end: "2025-05-08", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share' },
            { id: 'mast2', title: "Prosperity Masterclass", date_start: "2025-05-10", date_end: "2025-06-07", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast3', title: "Prosperity Masterclass", date_start: "2025-06-12", date_end: "2025-07-10", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast4', title: "Prosperity Masterclass", date_start: "2025-07-12", date_end: "2025-08-09", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast5', title: "Prosperity Masterclass", date_start: "2025-08-14", date_end: "2025-09-11", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast6', title: "Prosperity Masterclass", date_start: "2025-09-13", date_end: "2025-10-11", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast7', title: "Prosperity Masterclass", date_start: "2025-10-16", date_end: "2025-11-13", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},
            { id: 'mast8', title: "Prosperity Masterclass", date_start: "2025-11-15", date_end: "2025-12-13", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", details: "Each cohort runs for 1 month. Includes: Wealth-building principles, Best practices, Investment options, Systemizing your investments", link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'},

            // Retirement Planning
            { id: 'ret1', title: "Retirement Planning", date_start: "2025-04-14", date_end: "2025-04-18", location: "Mombasa", type: "retirement_planning", price: "Ksh 79,000 excl. VAT", topic: "Taking Stock, Mental Preparedness, Financial Well-being, Health, Investment Options in Retirement" },
            { id: 'ret2', title: "Retirement Planning", date_start: "2025-07-14", date_end: "2025-07-18", location: "Nakuru", type: "retirement_planning", price: "Ksh 79,000 excl. VAT", topic: "Taking Stock, Mental Preparedness, Financial Well-being, Health, Investment Options in Retirement" },
            { id: 'ret3', title: "Retirement Planning", date_start: "2025-10-13", date_end: "2025-10-17", location: "Kisumu", type: "retirement_planning", price: "Ksh 79,000 excl. VAT", topic: "Taking Stock, Mental Preparedness, Financial Well-being, Health, Investment Options in Retirement" },
            { id: 'ret4', title: "Retirement Planning", date_start: "2025-12-08", date_end: "2025-12-12", location: "Mombasa", type: "retirement_planning", price: "Ksh 79,000 excl. VAT", topic: "Taking Stock, Mental Preparedness, Financial Well-being, Health, Investment Options in Retirement" },

            // Alternative Investments
            { id: 'alt1', title: "Alternative Investments", date_start: "2025-05-12", date_end: "2025-05-16", location: "Kisumu", type: "alternative_investments", price: "Ksh 79,000 excl. VAT", topic: "Investment Landscape, Fixed Income, Equities, Real Estate, Offshore, Private Equity, Portfolio & Risk Mgmt, Personal Finance", link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share' },
            { id: 'alt2', title: "Alternative Investments", date_start: "2025-06-09", date_end: "2025-06-13", location: "Kisumu", type: "alternative_investments", price: "Ksh 79,000 excl. VAT", topic: "Investment Landscape, Fixed Income, Equities, Real Estate, Offshore, Private Equity, Portfolio & Risk Mgmt, Personal Finance", link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share' },
            { id: 'alt3', title: "Alternative Investments", date_start: "2025-08-18", date_end: "2025-08-22", location: "Mombasa", type: "alternative_investments", price: "Ksh 79,000 excl. VAT", topic: "Investment Landscape, Fixed Income, Equities, Real Estate, Offshore, Private Equity, Portfolio & Risk Mgmt, Personal Finance", link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share' },
            { id: 'alt4', title: "Alternative Investments", date_start: "2025-09-15", date_end: "2025-09-19", location: "Kiambu County", type: "alternative_investments", price: "Ksh 79,000 excl. VAT", topic: "Investment Landscape, Fixed Income, Equities, Real Estate, Offshore, Private Equity, Portfolio & Risk Mgmt, Personal Finance", link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share' },
            { id: 'alt5', title: "Alternative Investments", date_start: "2025-11-10", date_end: "2025-11-14", location: "Nakuru", type: "alternative_investments", price: "Ksh 79,000 excl. VAT", topic: "Investment Landscape, Fixed Income, Equities, Real Estate, Offshore, Private Equity, Portfolio & Risk Mgmt, Personal Finance", link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share' },

            // In-House Trainings
            { id: 'inhouse1', title: "In-House Training: Employee Wellness", type: "in_house", price: "Ksh 5,000/person or 45,000 for 10", details: "Custom/On-Demand", location: "Client Premises" }, // Date is on-demand
            { id: 'inhouse2', title: "In-House Training: Retirement Planning", type: "in_house", price: "Tailor-made", details: "Custom/On-Demand", location: "Client Premises" }, // Date is on-demand

            // Bi-Weekly Webinars
            { id: 'webinar1', title: "Bi-Weekly Webinar", type: "webinar_bi_weekly", price: "Free", location: "Virtual", topic: "Virtual asset class discussions", details: "Every Tuesday at 7 PM" }
        ];

        // Sort all events by start date (if available) for chronological order if needed elsewhere
        // For this monthly view, filtering by month is key.
        allEvents.value = rawEvents.map(event => ({
            ...event,
            // Ensure date_start_obj and date_end_obj are actual Date objects for comparison
            date_start_obj: event.date_start ? parseDate(event.date_start) : null,
            date_end_obj: event.date_end ? parseDate(event.date_end) : null,
        }));

    } catch (error) {
        console.error("Failed to fetch events:", error);
        // Handle error appropriately in UI
    } finally {
        loading.value = false;
    }
});

// --- Filter events for the currently selected month ---
// This is a simplified filter. For events spanning months, you'd need more complex logic.
const eventsForCurrentMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth(); // 0-indexed

    const tuesdayWebinars = [];
    if (allEvents.value.find(e => e.type === 'webinar_bi_weekly')) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        for (let day = new Date(firstDay); day <= lastDay; day.setDate(day.getDate() + 1)) {
            if (day.getDay() === 2) { // 0 = Sunday, 1 = Monday, 2 = Tuesday
                const webinarBase = allEvents.value.find(e => e.type === 'webinar_bi_weekly');
                tuesdayWebinars.push({
                    ...webinarBase,
                    id: `webinar-${year}-${month + 1}-${day.getDate()}`, // Unique ID for each instance
                    date_start: `${year}-${String(month + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`,
                    date_start_obj: new Date(day.getFullYear(), day.getMonth(), day.getDate(), 19, 0, 0), // 7 PM
                    isRecurringInstance: true
                });
            }
        }
    }

    const monthEvents = allEvents.value.filter(event => {
        if (event.type === 'webinar_bi_weekly' || event.type === 'in_house') { // Handle these separately or always show
            return false; // Don't include the template, instances are generated
        }
        if (!event.date_start_obj) return false; // Skip events without a start date for monthly view

        const eventStartMonth = event.date_start_obj.getMonth();
        const eventStartYear = event.date_start_obj.getFullYear();

        // Check if the event starts in the current month
        if (eventStartYear === year && eventStartMonth === month) {
            return true;
        }

        // Check if the event spans into the current month (started before, ends in or after)
        if (event.date_end_obj) {
            const eventEndMonth = event.date_end_obj.getMonth();
            const eventEndYear = event.date_end_obj.getFullYear();
            if (
                (eventStartYear < year || (eventStartYear === year && eventStartMonth < month)) &&
                (eventEndYear > year || (eventEndYear === year && eventEndMonth >= month))
            ) {
                return true;
            }
        }
        return false;
    });

    const inHouseEvents = allEvents.value.filter(event => event.type === 'in_house');


    // Combine, sort, and then ***dedupe by title***
    const combined = [...monthEvents, ...tuesdayWebinars, ...inHouseEvents].sort((a, b) => {
        /* sorting logic exactly as you wrote it */
        if (!a.date_start_obj && b.date_start_obj) return 1;
        if (a.date_start_obj && !b.date_start_obj) return -1;
        if (!a.date_start_obj && !b.date_start_obj) return a.title.localeCompare(b.title);
        return a.date_start_obj - b.date_start_obj;
    });

    /* 👇 NEW: keep only the first event for any title seen this month */
    const seen = new Set();
    const unique = [];
    for (const ev of combined) {
        const key = ev.title.trim().toLowerCase();
        if (!seen.has(key)) {
            unique.push(ev);
            seen.add(key);
        }
    }
    return unique;
});


// --- Navigation methods ---
const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

// --- Date Formatting ---
const formatDateRange = (start, end, details, type) => {
    if (type === 'webinar_bi_weekly') return `Every Tuesday at 7 PM`;
    if (type === 'in_house') return 'On-Demand / Custom Date';

    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    const startDate = parseDate(start);

    if (!startDate) return 'Date TBD';

    let dateStr = startDate.toLocaleDateString('en-US', options);

    if (end) {
        const endDate = parseDate(end);
        if (endDate && startDate.toDateString() !== endDate.toDateString()) {
            dateStr += ` - ${endDate.toLocaleDateString('en-US', options)}`;
        }
    }
    return dateStr;
};


// --- Styling based on event type ---
const getEventCardClass = (type) => {
    let baseClass = 'text-white'; // Default text color
    switch (type) {
        case 'corporate_monthly':
            return `${baseClass} bg-purple-500`;
        case 'masterclass_quarterly':
            return `${baseClass} bg-yellow-500`; // Using yellow-500 for card, text will be white.
        // Consider text-black for yellow-500 if white is not readable.
        case 'retirement_planning':
            return `${baseClass} bg-purple-700`; // Darker purple
        case 'alternative_investments':
            return `text-gray-800 bg-yellow-300`; // Lighter yellow, darker text for contrast
        case 'in_house':
            return `${baseClass} bg-gray-700`;
        case 'webinar_bi_weekly':
            return `${baseClass} bg-purple-400`;
        default:
            return `${baseClass} bg-gray-500`;
    }
};

const getEventPriceClass = (type) => {
    // Inverse or complementary colors for the price badge
    switch (type) {
        case 'corporate_monthly':
            return 'bg-yellow-400 text-purple-700';
        case 'masterclass_quarterly':
            return 'bg-purple-500 text-yellow-300';
        case 'retirement_planning':
            return 'bg-yellow-300 text-purple-800';
        case 'alternative_investments':
            return 'bg-purple-600 text-yellow-200';
        case 'in_house':
            return 'bg-white text-gray-700';
        case 'webinar_bi_weekly':
            return 'bg-yellow-500 text-purple-600';
        default:
            return 'bg-white text-gray-600';
    }
};

const getEventHrClass = (type) => {
    switch (type) {
        case 'corporate_monthly':
            return 'border-yellow-300';
        case 'masterclass_quarterly':
            return 'border-purple-300';
        case 'retirement_planning':
            return 'border-yellow-200';
        case 'alternative_investments':
            return 'border-purple-400';
        case 'in_house':
            return 'border-gray-400';
        case 'webinar_bi_weekly':
            return 'border-yellow-400';
        default:
            return 'border-gray-300';
    }
}

</script>

<style scoped>
/* Add any additional custom styles if Tailwind isn't enough */
/* Ensure your tailwind.config.js includes purple-500, yellow-500, etc. */
/* Or that they are part of the default Tailwind palette */
</style>