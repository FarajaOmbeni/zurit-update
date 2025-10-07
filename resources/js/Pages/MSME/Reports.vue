<template>
    <Head title="Financial Reports" />
    <MSMESidebar title="Financial Reports">
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
                                    Financial Reports
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    Generate and download your business
                                    financial reports
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <button
                                    @click="openGenerateModal"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Generate New Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Generation Form -->
                <div
                    v-if="showGenerateModal"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Generate Report
                        </h3>
                        <form
                            @submit.prevent="generateReport"
                            class="space-y-4"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Report Type *</label
                                    >
                                    <select
                                        v-model="reportForm.type"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">
                                            Select Report Type
                                        </option>
                                        <option value="profit_loss">
                                            Profit & Loss Statement
                                        </option>
                                        <option value="balance_sheet">
                                            Balance Sheet
                                        </option>
                                        <option value="cashflow">
                                            Cash Flow Statement
                                        </option>
                                    </select>
                                    <p
                                        v-if="reportForm.errors.type"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ reportForm.errors.type }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Format *</label
                                    >
                                    <select
                                        v-model="reportForm.format"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">Select Format</option>
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                    <p
                                        v-if="reportForm.errors.format"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ reportForm.errors.format }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Start Date *</label
                                    >
                                    <input
                                        v-model="reportForm.start_date"
                                        type="date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    />
                                    <p
                                        v-if="reportForm.errors.start_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ reportForm.errors.start_date }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >End Date *</label
                                    >
                                    <input
                                        v-model="reportForm.end_date"
                                        type="date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    />
                                    <p
                                        v-if="reportForm.errors.end_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ reportForm.errors.end_date }}
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
                                    :disabled="reportForm.processing"
                                    class="px-4 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                                >
                                    <svg
                                        v-if="reportForm.processing"
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
                                        reportForm.processing
                                            ? "Generating..."
                                            : "Generate Report"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Profit & Loss Reports -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Profit & Loss Reports
                        </h3>
                        <div
                            v-if="profitLossReports.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="report in profitLossReports"
                                :key="report.id"
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
                                                formatDate(report.period_start)
                                            }}
                                            -
                                            {{ formatDate(report.period_end) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Net Profit:
                                            {{
                                                formatCurrency(
                                                    report.net_profit,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="
                                            downloadReport(
                                                'profit_loss',
                                                report.id,
                                                'pdf',
                                            )
                                        "
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        Download PDF
                                    </button>
                                    <button
                                        @click="
                                            downloadReport(
                                                'profit_loss',
                                                report.id,
                                                'excel',
                                            )
                                        "
                                        class="text-green-600 hover:text-green-800 text-sm"
                                    >
                                        Download Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <svg
                                class="w-12 h-12 mx-auto mb-3 text-gray-300"
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
                            <p>No Profit & Loss reports generated yet</p>
                        </div>
                    </div>
                </div>

                <!-- Balance Sheet Reports -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Balance Sheet Reports
                        </h3>
                        <div
                            v-if="balanceSheetReports.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="report in balanceSheetReports"
                                :key="report.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4"
                                    >
                                        <svg
                                            class="w-5 h-5 text-blue-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            Balance Sheet
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            As of
                                            {{ formatDate(report.as_of_date) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Total Assets:
                                            {{
                                                formatCurrency(
                                                    report.total_assets,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="
                                            downloadReport(
                                                'balance_sheet',
                                                report.id,
                                                'pdf',
                                            )
                                        "
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        Download PDF
                                    </button>
                                    <button
                                        @click="
                                            downloadReport(
                                                'balance_sheet',
                                                report.id,
                                                'excel',
                                            )
                                        "
                                        class="text-green-600 hover:text-green-800 text-sm"
                                    >
                                        Download Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <svg
                                class="w-12 h-12 mx-auto mb-3 text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                ></path>
                            </svg>
                            <p>No Balance Sheet reports generated yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { formatDate } from "@/Components/Composables/useDateFormat";
import { ref, reactive } from "vue";

const props = defineProps({
    profitLossReports: Array,
    balanceSheetReports: Array,
});

const { formatCurrency } = useFormatCurrency();

const showGenerateModal = ref(false);

const reportForm = useForm({
    type: "",
    format: "",
    start_date: "",
    end_date: "",
});

const openGenerateModal = () => {
    showGenerateModal.value = true;
    // Set default dates
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    reportForm.start_date = firstDay.toISOString().split("T")[0];
    reportForm.end_date = lastDay.toISOString().split("T")[0];
};

const closeGenerateModal = () => {
    showGenerateModal.value = false;
    reportForm.reset();
};

const generateReport = () => {
    reportForm.post(route("msme.generate-report"), {
        onSuccess: (page) => {
            closeGenerateModal();
            // Handle success - could show a success message
            console.log("Report generated successfully");
        },
        onError: (errors) => {
            console.error("Report generation failed:", errors);
        },
    });
};

const downloadReport = (type, reportId, format) => {
    let url = null;
    if (type === "profit_loss") {
        url = route("profit-loss.download", {
            profitLossRecord: reportId,
            format,
        });
    } else if (type === "balance_sheet") {
        url = route("balance-sheet.download", {
            balanceSheetRecord: reportId,
            format,
        });
    }

    if (url) {
        window.location.href = url;
    }
};
</script>
