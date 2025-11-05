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

                <!-- Report Types Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Profit & Loss -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3"
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
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Profit & Loss
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">
                                Shows your business revenue, expenses, and
                                profit for a specific period.
                            </p>
                            <ul class="text-xs text-gray-500 space-y-1">
                                <li>• Revenue from sales</li>
                                <li>• Cost of goods sold</li>
                                <li>• Operating expenses</li>
                                <li>• Net profit/loss</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Balance Sheet -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3"
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
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Balance Sheet
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">
                                Shows your business assets, liabilities, and
                                equity at a specific point in time.
                            </p>
                            <ul class="text-xs text-gray-500 space-y-1">
                                <li>• Current assets (cash, inventory)</li>
                                <li>• Fixed assets (equipment, property)</li>
                                <li>• Liabilities (loans, payables)</li>
                                <li>• Owner's equity</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Cash Flow -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                        />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Cash Flow
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">
                                Shows how cash moves in and out of your business
                                over a period.
                            </p>
                            <ul class="text-xs text-gray-500 space-y-1">
                                <li>• Operating activities</li>
                                <li>• Investing activities</li>
                                <li>• Financing activities</li>
                                <li>• Net cash change</li>
                            </ul>
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

                <!-- Inline Result Rendering -->
                <div
                    v-if="$page.props.inlineResult"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <template
                            v-if="
                                $page.props.inlineResult.type === 'profit_loss'
                            "
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Profit & Loss ({{
                                    $page.props.inlineResult.period.start_date
                                }}
                                to
                                {{ $page.props.inlineResult.period.end_date }})
                            </h3>
                            <!-- Key totals -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4"
                            >
                                <div>
                                    <p class="text-sm text-gray-600">Revenue</p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.revenue ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">COGS</p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.cost_of_goods_sold ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Gross Profit
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.gross_profit ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Net Profit
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.net_profit ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Detailed breakdown -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <h4 class="font-semibold text-gray-900">
                                        Income & COGS
                                    </h4>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Revenue</span
                                        >
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.revenue ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">COGS</span>
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.cost_of_goods_sold ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between text-sm font-medium border-t pt-2"
                                    >
                                        <span>Gross Profit</span>
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.gross_profit ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <h4 class="font-semibold text-gray-900">
                                        Operating & Other Expenses
                                    </h4>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Operating Expenses</span
                                        >
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.operating_expenses ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Depreciation</span
                                        >
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.depreciation ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Interest Expense</span
                                        >
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.interest_expense ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Tax Expense</span
                                        >
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.tax_expense ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between text-sm font-medium border-t pt-2"
                                    >
                                        <span>Net Profit</span>
                                        <span>{{
                                            (
                                                $page.props.inlineResult
                                                    .profitLossRecord
                                                    ?.net_profit ?? 0
                                            ).toLocaleString?.()
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template
                            v-else-if="
                                $page.props.inlineResult.type ===
                                'balance_sheet'
                            "
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Balance Sheet (as of
                                {{
                                    $page.props.inlineResult.period.as_of_date
                                }})
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Total Assets
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .balanceSheetRecord
                                                    ?.total_assets ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Total Liabilities
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .balanceSheetRecord
                                                    ?.total_liabilities ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Total Equity
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .balanceSheetRecord
                                                    ?.total_equity ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>

                        <template
                            v-else-if="
                                $page.props.inlineResult.type === 'cashflow'
                            "
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Cash Flow ({{
                                    $page.props.inlineResult.period.start_date
                                }}
                                to
                                {{ $page.props.inlineResult.period.end_date }})
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Total Income
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .cashflowSummary
                                                    ?.total_income ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Total Expenses
                                    </p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .cashflowSummary
                                                    ?.total_expenses ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Net</p>
                                    <p class="text-2xl font-semibold">
                                        {{
                                            (
                                                $page.props.inlineResult
                                                    .cashflowSummary?.net ?? 0
                                            ).toLocaleString?.()
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>

                        <div class="mt-4 flex space-x-3">
                            <a
                                v-if="
                                    $page.props.inlineResult.type ===
                                    'profit_loss'
                                "
                                :href="`/msme/download-profit-loss?start_date=${$page.props.inlineResult.period.start_date}&end_date=${$page.props.inlineResult.period.end_date}`"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"
                            >
                                Download Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { ref, reactive, watch } from "vue";

// No props required; this page only renders the parameter form

const showGenerateModal = ref(false);

const reportForm = useForm({
    type: "",
    format: "excel", // used for balance_sheet/cashflow; ignored for profit_loss when viewing in-app
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
    // Build payload dynamically to avoid sending format for profit_loss (so the backend renders in-app)
    const payload = {
        type: reportForm.type,
        start_date: reportForm.start_date,
        end_date: reportForm.end_date,
    };

    if (reportForm.type !== "profit_loss" && reportForm.format) {
        payload.format = reportForm.format;
    }

    router.post(route("msme.generate-report"), payload, {
        preserveState: false,
        onSuccess: () => {
            closeGenerateModal();
            console.log("Report generated successfully");
        },
        onError: (errors) => {
            console.error("Report generation failed:", errors);
        },
    });
};

// No watcher forcing format for profit_loss; leaving format unset triggers in-app rendering

const download = (type, format) => {
    // Reuse the current period from inlineResult if present
    const res = window?.__inertiaPageProps?.inlineResult || undefined;
    let start = reportForm.start_date;
    let end = reportForm.end_date;
    if (res) {
        if (type === "profit_loss" || type === "cashflow") {
            start = res.period?.start_date || start;
            end = res.period?.end_date || end;
        }
        if (type === "balance_sheet") {
            end = res.period?.as_of_date || end;
        }
    }

    // Post again with format to trigger download via backend routing
    const payload = { type };
    if (type === "profit_loss" || type === "cashflow") {
        payload.start_date = start;
        payload.end_date = end;
    }
    if (type === "balance_sheet") {
        payload.start_date = start; // unused by backend but kept consistent
        payload.end_date = end;
    }
    payload.format = format;

    router.post(route("msme.generate-report"), payload);
};

// No download list on this page; downloads happen via form submit redirect
</script>
