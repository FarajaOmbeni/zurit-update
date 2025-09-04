<template>

    <Head title="Calendar" />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
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
            <div v-for="event in eventsForCurrentMonth" :key="event.id" :class="[
                getEventCardClass(event.type),
                { 'filter grayscale': isPastEvent(event) }
            ]" class="h-full flex flex-col p-5 rounded-lg shadow-lg transition-all hover:shadow-xl">
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

                <div class="mt-auto pt-4 text-center">
                    <a v-if="!isPastEvent(event)" :href="event.link" target="_blank" :class="[
                        'inline-block px-6 py-2 rounded font-bold transition-colors',
                        getEventButtonClass(event.type)
                    ]">
                        REGISTER
                    </a>
                    <div v-else
                        class="inline-block px-6 py-2 rounded font-bold bg-gray-500 text-gray-200 cursor-not-allowed">
                        EVENT ENDED
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import BackButton from '@/Components/Shared/BackButton.vue';

const allEvents = ref([]);
const loading = ref(true);
const currentDate = ref(new Date()); // Start with Jan 2025 as data is for 2025

// NEW: Function to check if an event is in the past
const isPastEvent = (event) => {
    // 'in_house' events are on-demand and never considered "past"
    if (event.type === 'in_house') {
        return false;
    }

    const today = new Date();
    // Set time to 00:00:00 to compare dates only
    today.setHours(0, 0, 0, 0);

    // Use the event's end date if available, otherwise use the start date.
    // For recurring webinars, the date_start_obj is the specific instance date.
    const eventEndDate = event.date_end_obj || event.date_start_obj;

    // If there's no valid date object, we can't determine if it's past.
    if (!eventEndDate) {
        return false;
    }

    // Return true if the event's end date is before today
    return eventEndDate < today;
};


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

// --- Filter events for the currently selected month ---
// This is a simplified filter. For events spanning months, you'd need more complex logic.
const eventsForCurrentMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth(); // 0-indexed

    const tuesdayWebinars = [];
    if (allEvents.value.find(e => e.type === 'webinar_bi_weekly')) {
        // Generate Tuesday webinars for the current month being viewed
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

    /* 👇 Modified: keep only the first event for any title seen this month, except for recurring webinars */
    const seen = new Set();
    const unique = [];
    for (const ev of combined) {
        // For recurring webinars, use a unique key that includes the date
        const key = ev.isRecurringInstance
            ? `${ev.title.trim().toLowerCase()}-${ev.date_start}`
            : ev.title.trim().toLowerCase();

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
    if (type === 'webinar_bi_weekly') {
        // For recurring webinars, show the actual date with time
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        const startDate = parseDate(start);
        if (startDate) {
            return `${startDate.toLocaleDateString('en-US', options)} at 7 PM`;
        }
        return 'Every Tuesday at 7 PM';
    }
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

// --- Fetch events ---
onMounted(async () => {
    try {
        // Replace with your actual API endpoint
        // const response = await axios.get('/api/events');
        // this.allEvents = response.data;

        const rawEvents = [
            // ──────────────────────── Prosperity Talks (monthly) ───────────────────────
            { id: 'corp1', title: "Prosperity Circles", topic: "Using NSSF / Pension to save on tax", date_start: "2025-05-31", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp2', title: "Prosperity Circles", topic: "How to Navigate Financial Changes", date_start: "2025-06-28", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp3', title: "Prosperity Circles", topic: "Debt-Detox – How to clean up your Finances and Prosper", date_start: "2025-07-26", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp4', title: "Prosperity Circles", topic: "The Timeless Principles of Wealth Creation", date_start: "2025-08-30", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp5', title: "Prosperity Circles", topic: "Back to the Drawing Board – How to Implement Your Financial Plan", date_start: "2025-09-27", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp6', title: "Prosperity Circles", topic: "Securing Your Retirement", date_start: "2025-10-31", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp7', title: "Prosperity Circles", topic: "How Businesses can successfully Invest in Financial Markets", date_start: "2025-11-29", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },
            { id: 'corp8', title: "Prosperity Circles", topic: "Strategies Businesses Can Adopt to Protect and Grow Wealth", date_start: "2025-12-20", type: "corporate_monthly", location: "Virtual / Physical", price: "Ksh 1,500 physical / 1,000 virtual", link: "https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share" },

            // ─────────────────────── Prosperity Masterclass (monthly cohorts) ───────────
            { id: 'mast1', title: "Prosperity Masterclass", date_start: "2025-04-10", date_end: "2025-05-08", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast2', title: "Prosperity Masterclass", date_start: "2025-05-10", date_end: "2025-06-07", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast3', title: "Prosperity Masterclass", date_start: "2025-06-12", date_end: "2025-07-10", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast4', title: "Prosperity Masterclass", date_start: "2025-07-12", date_end: "2025-08-09", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast5', title: "Prosperity Masterclass", date_start: "2025-08-14", date_end: "2025-09-11", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast6', title: "Prosperity Masterclass", date_start: "2025-09-13", date_end: "2025-10-11", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast7', title: "Prosperity Masterclass", date_start: "2025-10-16", date_end: "2025-11-13", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'mast8', title: "Prosperity Masterclass", date_start: "2025-11-15", date_end: "2025-12-13", type: "masterclass_quarterly", location: "Virtual", price: "Ksh 15,000", link: "https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },

            // ───────────────────────── Retirement-focused programmes ────────────────────
            { id: 'ret1', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2025-08-06", date_end: "2025-08-08", type: "retirement_planning", location: "Nairobi", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret2', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2025-09-08", date_end: "2025-09-10", type: "retirement_planning", location: "Nanyuki", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret3', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2025-10-01", date_end: "2025-10-03", type: "retirement_planning", location: "Machakos", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret4', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2025-11-05", date_end: "2025-11-07", type: "retirement_planning", location: "Nairobi", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret5', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-02-04", date_end: "2026-02-06", type: "retirement_planning", location: "Nairobi", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret6', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-03-04", date_end: "2026-03-06", type: "retirement_planning", location: "Nanyuki", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret7', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-04-01", date_end: "2026-04-03", type: "retirement_planning", location: "Machakos", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret8', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-05-06", date_end: "2026-05-08", type: "retirement_planning", location: "Nairobi", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret9', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-06-03", date_end: "2026-06-05", type: "retirement_planning", location: "Machakos", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },
            { id: 'ret10', title: "From Salary to Security: Your Path to a Prosperous Retirement", date_start: "2026-07-01", date_end: "2026-07-03", type: "retirement_planning", location: "Nairobi", price: "Ksh 50,000 excl. VAT", topic: "Taking Stock, Mental Preparedness for Retirement, Financial well-being in Retirement, Investment Options in Retirement, Health Considerations in Retirement", link: "https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },

            // ───────────────────────── Alternative Investments ──────────────────────────
            { id: 'alt1', title: "Alternative Investments", date_start: "2025-10-15", date_end: "2025-10-17", type: "alternative_investments", location: "Naivasha", price: "Ksh 79,000 excl. VAT", topic: "Foundations & Traditional Investments, Alternative Investments & Diversification, Real Assets, Risk & Performance Measurement, Designing a Business Model, Personal Wealth Building & Financial Leadership", link: "https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share" },
            { id: 'alt2', title: "Alternative Investments", date_start: "2025-12-01", date_end: "2025-12-05", type: "alternative_investments", location: "Mombasa", price: "Ksh 79,000 excl. VAT", topic: "Foundations & Traditional Investments, Alternative Investments & Diversification, Real Assets, Risk & Performance Measurement, Designing a Business Model, Personal Wealth Building & Financial Leadership", link: "https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share" },
            { id: 'alt3', title: "Alternative Investments", date_start: "2026-02-09", date_end: "2026-02-13", type: "alternative_investments", location: "Naivasha", price: "Ksh 79,000 excl. VAT", topic: "Foundations & Traditional Investments, Alternative Investments & Diversification, Real Assets, Risk & Performance Measurement, Designing a Business Model, Personal Wealth Building & Financial Leadership", link: "https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share" },
            { id: 'alt4', title: "Alternative Investments", date_start: "2026-07-13", date_end: "2026-07-17", type: "alternative_investments", location: "Mombasa", price: "Ksh 79,000 excl. VAT", topic: "Foundations & Traditional Investments, Alternative Investments & Diversification, Real Assets, Risk & Performance Measurement, Designing a Business Model, Personal Wealth Building & Financial Leadership", link: "https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share" },

            // ───────────────────── Fundamentals of Investments ─────────────────────────
            { id: 'fund1', title: "Fundamentals of Investments", date_start: "2025-08-18", date_end: "2025-08-22", type: "fundamentals_investments", location: "Machakos", price: "Ksh 79,000 excl. VAT", topic: "Laying the Foundation — Roles, Rules & Instruments; Understanding Traditional Asset Classes; Diversification, Oversight & Monitoring; Strategy Mapping with the Business Model Canvas; Personal Financial Wellness & Ethical Stewardship", link:"https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'fund2', title: "Fundamentals of Investments", date_start: "2025-11-10", date_end: "2025-11-14", type: "fundamentals_investments", location: "Kisumu", price: "Ksh 79,000 excl. VAT", topic: "Laying the Foundation — Roles, Rules & Instruments; Understanding Traditional Asset Classes; Diversification, Oversight & Monitoring; Strategy Mapping with the Business Model Canvas; Personal Financial Wellness & Ethical Stewardship", link:"https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'fund3', title: "Fundamentals of Investments", date_start: "2026-01-05", date_end: "2026-01-09", type: "fundamentals_investments", location: "Machakos", price: "Ksh 79,000 excl. VAT", topic: "Laying the Foundation — Roles, Rules & Instruments; Understanding Traditional Asset Classes; Diversification, Oversight & Monitoring; Strategy Mapping with the Business Model Canvas; Personal Financial Wellness & Ethical Stewardship", link:"https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'fund4', title: "Fundamentals of Investments", date_start: "2026-04-13", date_end: "2026-04-17", type: "fundamentals_investments", location: "Naivasha", price: "Ksh 79,000 excl. VAT", topic: "Laying the Foundation — Roles, Rules & Instruments; Understanding Traditional Asset Classes; Diversification, Oversight & Monitoring; Strategy Mapping with the Business Model Canvas; Personal Financial Wellness & Ethical Stewardship", link:"https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },
            { id: 'fund5', title: "Fundamentals of Investments", date_start: "2026-06-15", date_end: "2026-06-19", type: "fundamentals_investments", location: "Naivasha", price: "Ksh 79,000 excl. VAT", topic: "Laying the Foundation — Roles, Rules & Instruments; Understanding Traditional Asset Classes; Diversification, Oversight & Monitoring; Strategy Mapping with the Business Model Canvas; Personal Financial Wellness & Ethical Stewardship", link:"https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share" },

            // ────────────── Strategic Governance of Complex Investment Portfolios ──────
            { id: 'strat1', title: "Strategic Governance & Oversight of Complex Investment Portfolios", date_start: "2025-07-14", date_end: "2025-07-18", type: "strategic_governance", location: "Naivasha", price: "Ksh 79,000 excl. VAT", topic: "Strengthen governance, interrogate portfolio strategies, red-flag detection, ethical decision-making, stakeholder accountability, long-term wealth security", link: "https://dashboard.mailerlite.com/forms/1042116/156268647856211263/share" },
            { id: 'strat2', title: "Strategic Governance & Oversight of Complex Investment Portfolios", date_start: "2025-09-15", date_end: "2025-09-19", type: "strategic_governance", location: "Kisumu", price: "Ksh 79,000 excl. VAT", topic: "Strengthen governance, interrogate portfolio strategies, red-flag detection, ethical decision-making, stakeholder accountability, long-term wealth security", link: "https://dashboard.mailerlite.com/forms/1042116/156268647856211263/share" },
            { id: 'strat3', title: "Strategic Governance & Oversight of Complex Investment Portfolios", date_start: "2026-03-09", date_end: "2026-03-13", type: "strategic_governance", location: "Mombasa", price: "Ksh 79,000 excl. VAT", topic: "Strengthen governance, interrogate portfolio strategies, red-flag detection, ethical decision-making, stakeholder accountability, long-term wealth security", link: "https://dashboard.mailerlite.com/forms/1042116/156268647856211263/share" },
            { id: 'strat4', title: "Strategic Governance & Oversight of Complex Investment Portfolios", date_start: "2026-05-11", date_end: "2026-05-15", type: "strategic_governance", location: "Kisumu", price: "Ksh 79,000 excl. VAT", topic: "Strengthen governance, interrogate portfolio strategies, red-flag detection, ethical decision-making, stakeholder accountability, long-term wealth security", link: "https://dashboard.mailerlite.com/forms/1042116/156268647856211263/share" },

            // ────────────────────────────── Custom / In-House ───────────────────────────
            { id: 'inhouse1', title: "In-House Training - Employee Wellness", type: "in_house", price: "Ksh 5,000 per person or 45,000 for 10", details: "Custom / On-Demand", location: "Client Premises" },
            { id: 'inhouse2', title: "In-House Training - Retirement Planning", type: "in_house", price: "Tailor-made", details: "Custom / On-Demand", location: "Client Premises", link:"https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share" },

            // ────────────────────────────── Free Weekly Prosperity Talks ─────────────────
            { id: 'webinar_template', title: "Weekly Prosperity Talks", type: "webinar_bi_weekly", location: "Virtual", price: "Free", topic: "Asset-class discussions", details: "Every Tuesday at 7 PM", link: "https://us06web.zoom.us/meeting/register/HUXuDeA7QqWYs-UCtYlG2Q" },
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
</script>

<style scoped>
/* Add any additional custom styles if Tailwind isn't enough */
/* Ensure your tailwind.config.js includes purple-500, yellow-500, etc. */
/* Or that they are part of the default Tailwind palette */
</style>