<template>
    <Head title="Profit & Loss Records" />
    <MSMESidebar title="Profit & Loss Records">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-center"
                        >
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    Profit & Loss Records
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    Track your business profitability over time
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <button
                                    @click="openGenerateModal"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Generate P&L Statement
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Generate Modal -->
                <div
                    v-if="showGenerateModal"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Generate Profit & Loss Statement
                        </h3>
                        <form @submit.prevent="generatePL" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Start Date *</label
                                    >
                                    <input
                                        v-model="generateForm.start_date"
                                        type="date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    />
                                    <p
                                        v-if="generateForm.errors.start_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ generateForm.errors.start_date }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >End Date *</label
                                    >
                                    <input
                                        v-model="generateForm.end_date"
                                        type="date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    />
                                    <p
                                        v-if="generateForm.errors.end_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ generateForm.errors.end_date }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button
                                    @click="closeGenerateModal"
                                    type="button"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="generateForm.processing"
                                    class="px-4 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <svg
                                        v-if="generateForm.processing"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    {{
                                        generateForm.processing
                                            ? "Generating..."
                                            : "Generate P&L"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- P&L Records List -->
                <div
                    v-if="profitLossRecords.data.length > 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Profit & Loss Records
                        </h3>
                        <div class="space-y-4">
                            <div
                                v-for="record in profitLossRecords.data"
                                :key="record.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4"
                                    >
                                        <svg
                                            class="w-5 h-5 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            P&L Statement
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{
                                                formatDate(record.period_start)
                                            }}
                                            -
                                            {{ formatDate(record.period_end) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Net Profit:
                                            {{
                                                formatCurrency(
                                                    record.net_profit,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <Link
                                        :href="
                                            route('profit-loss.show', record.id)
                                        "
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        View Details
                                    </Link>
                                    <button
                                        @click="deleteRecord(record.id)"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="w-16 h-16 mx-auto mb-4 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            ></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            No Profit & Loss Records
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Generate your first P&L statement to track your
                            business profitability.
                        </p>
                        <button
                            @click="openGenerateModal"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Generate Your First P&L Statement
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="profitLossRecords.last_page > 1" class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ profitLossRecords.from }} to
                            {{ profitLossRecords.to }} of
                            {{ profitLossRecords.total }} results
                        </div>
                        <div class="flex space-x-1">
                            <Link
                                v-for="link in profitLossRecords.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-2 text-sm border rounded',
                                    link.active
                                        ? 'bg-purple-600 text-white border-purple-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { formatDate } from "@/Components/Composables/useDateFormat";
import { ref } from "vue";

const props = defineProps({
    profitLossRecords: Object,
});

const { formatCurrency } = useFormatCurrency();

const showGenerateModal = ref(false);

const generateForm = useForm({
    start_date: "",
    end_date: "",
});

const openGenerateModal = () => {
    showGenerateModal.value = true;
    // Set default dates to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    generateForm.start_date = firstDay.toISOString().split("T")[0];
    generateForm.end_date = lastDay.toISOString().split("T")[0];
};

const closeGenerateModal = () => {
    showGenerateModal.value = false;
    generateForm.reset();
};

const generatePL = () => {
    generateForm.post(route("profit-loss.generate"), {
        onSuccess: () => {
            closeGenerateModal();
        },
    });
};

const deleteRecord = (recordId) => {
    if (confirm("Are you sure you want to delete this P&L record?")) {
        generateForm.delete(route("profit-loss.destroy", recordId));
    }
};
</script>
